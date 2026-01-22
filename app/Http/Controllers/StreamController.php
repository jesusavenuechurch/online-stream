<?php

namespace App\Http\Controllers;

use App\Models\Attendee;
use App\Models\Attendance;
use App\Models\StreamEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class StreamController extends Controller
{
    /**
     * Show the stream page
     */
    public function show(Request $request, StreamEvent $event)
    {
        $attendeeId = Session::get('attendee_id');
        $sessionEventId = Session::get('event_id');

        // 1. Check if user is authenticated for this event
        if (!$attendeeId || (int)$sessionEventId !== (int)$event->id) {
            return view('stream.auth', compact('event'));
        }

        $attendee = Attendee::find($attendeeId);

        // 2. Safety check: If attendee doesn't exist or doesn't have pastor access
        if (!$attendee || ($event->isPastorsOnly() && !$attendee->isPastor())) {
            Session::forget(['attendee_id', 'event_id']);
            return redirect('/')->with('error', 'Access denied.');
        }

        // 3. SMART SESSION MANAGEMENT
        // Added 'joined_at' to prevent SQLSTATE[HY000] General error: 1364
        Attendance::updateOrCreate(
            [
                'event_id' => $event->id,
                'attendee_id' => $attendee->id,
                'left_at' => null, 
            ],
            [
                'joined_at' => now(), // Critical fix: provides the missing field value
                'last_ping' => now(), 
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'session_id' => Session::getId(),
            ]
        );

        // 4. Get stream URL
        $streamUrl = $this->getStreamUrl($event);

        return view('stream.player', compact('event', 'attendee', 'streamUrl'));
    }

    /**
     * Heartbeat: Called via JS every 30 seconds
     */
    public function heartbeat(Request $request, StreamEvent $event)
    {
        $attendeeId = Session::get('attendee_id');

        if ($attendeeId) {
            Attendance::where('event_id', $event->id)
                ->where('attendee_id', $attendeeId)
                ->whereNull('left_at')
                ->update(['last_ping' => now()]);
        }

        return response()->json(['status' => 'active']);
    }

    /**
     * Log when attendee leaves
     */
    public function leave(Request $request, StreamEvent $event)
    {
        $attendeeId = Session::get('attendee_id');

        if ($attendeeId) {
            Attendance::where('event_id', $event->id)
                ->where('attendee_id', $attendeeId)
                ->whereNull('left_at')
                ->update(['left_at' => now()]);
        }

        Session::forget(['attendee_id', 'event_id']);

        return response()->json(['success' => true]);
    }

    /**
     * Get the HLS stream URL for the event
     */
    private function getStreamUrl(StreamEvent $event): ?string
    {
        if ($event->isLive()) {
            $settings = \App\Models\StreamSettings::current();
            if ($settings && $settings->stream_key) {
                return asset('storage/hls/' . $settings->stream_key . '.m3u8');
            }
        }
        
        if ($event->recording_path) {
            return asset('storage/' . $event->recording_path);
        }
        
        return null;
    }
}
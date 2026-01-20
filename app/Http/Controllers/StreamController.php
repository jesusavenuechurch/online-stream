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

        // Check if user is authenticated for this event
        if (!$attendeeId || $sessionEventId != $event->id) {
            return view('stream.auth', compact('event'));
        }

        $attendee = Attendee::findOrFail($attendeeId);

        // Verify access permissions
        if ($event->isPastorsOnly() && !$attendee->isPastor()) {
            Session::forget(['attendee_id', 'event_id']);
            return redirect()->route('stream.show', $event)
                ->with('error', 'This stream is for pastors only.');
        }

        // Check for existing active session
        $existingSession = Attendance::where('event_id', $event->id)
            ->where('attendee_id', $attendee->id)
            ->whereNull('left_at')
            ->first();

        if ($existingSession) {
            // Check if it's the same browser session
            if ($existingSession->session_id !== Session::getId()) {
                return redirect()->route('stream.show', $event)
                    ->with('error', 'You are already watching this stream in another browser. Please close that session first.');
            }
        } else {
            // Create new attendance record with session tracking
            $attendance = Attendance::create([
                'event_id' => $event->id,
                'attendee_id' => $attendee->id,
                'joined_at' => now(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'session_id' => Session::getId(), // Add this field
            ]);
        }

        // Get stream URL (HLS)
        $streamUrl = $this->getStreamUrl($event);

        return view('stream.player', compact('event', 'attendee', 'streamUrl'));
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
        // If event is live, return live stream URL with the actual stream key
        if ($event->isLive()) {
            $settings = \App\Models\StreamSettings::current();
            if ($settings && $settings->stream_key) {
                // Point to Laravel storage URL, not nginx
                return asset('storage/hls/' . $settings->stream_key . '.m3u8');
            }
        }
        
        // If event has ended and has recording, return recording URL
        if ($event->recording_path) {
            return asset('storage/' . $event->recording_path);
        }
        
        return null;
    }
}
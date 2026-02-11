<?php

namespace App\Http\Controllers;

use App\Models\Attendee;
use App\Models\Attendance;
use App\Models\StreamEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class StreamController extends Controller
{
    /**
     * Show the stream page
     */
    public function show(Request $request, StreamEvent $event)
    {
        $attendeeId = Session::get('attendee_id');

        // 1. Check if user is authenticated
        if (!$attendeeId) {
            return view('stream.auth', compact('event'));
        }

        $attendee = Attendee::find($attendeeId);

        // 2. Safety check
        if (!$attendee) {
            Session::forget(['attendee_id', 'event_id']);
            return redirect('/')->with('error', 'Access denied.');
        }

        // 3. Smart redirect to live event
        $liveEvent = StreamEvent::where('status', 'live')->first();
        
        if ($liveEvent && $liveEvent->id !== $event->id) {
            Session::put('event_id', $liveEvent->id);
            Log::info('Redirecting to live event', [
                'from_event' => $event->id,
                'to_event' => $liveEvent->id,
                'attendee_id' => $attendee->id,
            ]);
            return redirect()->route('stream.show', $liveEvent);
        }

        // 4. Check pastor access
        if ($event->isPastorsOnly() && !$attendee->isPastor()) {
            Session::forget(['attendee_id', 'event_id']);
            return redirect('/')->with('error', 'This stream is for pastors only.');
        }

        // 5. Update session
        Session::put('event_id', $event->id);

        // 6. ALWAYS CREATE NEW ATTENDANCE RECORD
        $this->recordAttendance($event, $attendee, $request);

        // 7. Get stream URL (not used for external stream, but kept for compatibility)
        $streamUrl = $this->getStreamUrl($event);
        
        Log::info('Stream page loaded', [
            'event_id' => $event->id,
            'attendee_id' => $attendee->id,
            'attendee_name' => $attendee->full_name,
            'event_status' => $event->status,
        ]);

        return view('stream.player', compact('event', 'attendee', 'streamUrl'));
    }

    /**
     * ALWAYS create new attendance record (no duplicate checking)
     */
    private function recordAttendance(StreamEvent $event, Attendee $attendee, Request $request)
    {
        try {
            // ALWAYS CREATE NEW - More data is better!
            $attendance = Attendance::create([
                'event_id' => $event->id,
                'attendee_id' => $attendee->id,
                'joined_at' => now(),
                'last_ping' => now(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'session_id' => Session::getId(),
            ]);
            
            Log::info('New attendance created', [
                'attendance_id' => $attendance->id,
                'event_id' => $event->id,
                'attendee_id' => $attendee->id,
                'attendee_name' => $attendee->full_name,
                'joined_at' => $attendance->joined_at,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to create attendance', [
                'error' => $e->getMessage(),
                'event_id' => $event->id,
                'attendee_id' => $attendee->id,
            ]);
        }
    }

    /**
     * Heartbeat: Called via JS every 30 seconds
     */
    public function heartbeat(Request $request, StreamEvent $event)
    {
        $attendeeId = Session::get('attendee_id');
        $sessionId = Session::getId();

        if ($attendeeId) {
            // Update the most recent attendance for this user/event/session
            $updated = Attendance::where('event_id', $event->id)
                ->where('attendee_id', $attendeeId)
                ->where('session_id', $sessionId)
                ->whereNull('left_at')
                ->update(['last_ping' => now()]);
            
            if ($updated) {
                return response()->json(['status' => 'active']);
            } else {
                Log::warning('Heartbeat failed: no active attendance found', [
                    'event_id' => $event->id,
                    'attendee_id' => $attendeeId,
                    'session_id' => $sessionId,
                ]);
                return response()->json(['status' => 'not_found'], 404);
            }
        }

        return response()->json(['status' => 'unauthorized'], 401);
    }

    /**
     * Log when attendee leaves
     */
    public function leave(Request $request, StreamEvent $event)
    {
        $attendeeId = Session::get('attendee_id');
        $sessionId = Session::getId();

        if ($attendeeId) {
            $updated = Attendance::where('event_id', $event->id)
                ->where('attendee_id', $attendeeId)
                ->where('session_id', $sessionId)
                ->whereNull('left_at')
                ->update(['left_at' => now()]);
            
            Log::info('Attendee left stream', [
                'event_id' => $event->id,
                'attendee_id' => $attendeeId,
                'records_updated' => $updated,
            ]);
        }

        Session::forget(['attendee_id', 'event_id']);

        return response()->json(['success' => true]);
    }

    /**
     * Get the HLS stream URL for the event
     */
    private function getStreamUrl(StreamEvent $event): ?string
    {
        // This is kept for compatibility but not used with external stream
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
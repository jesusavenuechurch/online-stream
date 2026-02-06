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
        $sessionEventId = Session::get('event_id');

        // 1. Check if user is authenticated for ANY event
        if (!$attendeeId) {
            return view('stream.auth', compact('event'));
        }

        $attendee = Attendee::find($attendeeId);

        // 2. Safety check
        if (!$attendee) {
            Session::forget(['attendee_id', 'event_id']);
            return redirect('/')->with('error', 'Access denied.');
        }

        // 3. **SMART REDIRECT: Always show the LIVE event if one exists**
        $liveEvent = StreamEvent::where('status', 'live')->first();
        
        if ($liveEvent && $liveEvent->id !== $event->id) {
            // There's a different live event - redirect to it
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

        // 5. Update session to this event
        Session::put('event_id', $event->id);

        // 6. Record attendance (improved to prevent duplicates)
        $this->recordAttendance($event, $attendee, $request);

        // 7. Get stream URL
        $streamUrl = $this->getStreamUrl($event);
        
        Log::info('Stream page loaded', [
            'event_id' => $event->id,
            'attendee_id' => $attendee->id,
            'stream_url' => $streamUrl,
            'event_status' => $event->status,
        ]);

        return view('stream.player', compact('event', 'attendee', 'streamUrl'));
    }

    /**
     * Record attendance with duplicate prevention
     */
    private function recordAttendance(StreamEvent $event, Attendee $attendee, Request $request)
    {
        $sessionId = Session::getId();
        
        // Check for existing active attendance
        $existingAttendance = Attendance::where('event_id', $event->id)
            ->where('attendee_id', $attendee->id)
            ->whereNull('left_at')
            ->first();
        
        if ($existingAttendance) {
            // Update existing record
            $existingAttendance->update([
                'last_ping' => now(),
                'session_id' => $sessionId, // Update session ID in case it changed
            ]);
            
            Log::info('Updated existing attendance', [
                'attendance_id' => $existingAttendance->id,
                'attendee_id' => $attendee->id,
            ]);
        } else {
            // Create new attendance record
            Attendance::create([
                'event_id' => $event->id,
                'attendee_id' => $attendee->id,
                'joined_at' => now(),
                'last_ping' => now(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'session_id' => $sessionId,
            ]);
            
            Log::info('Created new attendance', [
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

        if ($attendeeId) {
            $updated = Attendance::where('event_id', $event->id)
                ->where('attendee_id', $attendeeId)
                ->whereNull('left_at')
                ->update(['last_ping' => now()]);
            
            if ($updated) {
                return response()->json(['status' => 'active']);
            } else {
                Log::warning('Heartbeat failed: no active attendance found', [
                    'event_id' => $event->id,
                    'attendee_id' => $attendeeId,
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

        if ($attendeeId) {
            $updated = Attendance::where('event_id', $event->id)
                ->where('attendee_id', $attendeeId)
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
        // Priority 1: Live stream
        if ($event->isLive()) {
            $settings = \App\Models\StreamSettings::current();
            if ($settings && $settings->stream_key) {
                $hlsUrl = asset('storage/hls/' . $settings->stream_key . '.m3u8');
                
                // Verify HLS file exists
                $hlsPath = storage_path('app/public/hls/' . $settings->stream_key . '.m3u8');
                if (!file_exists($hlsPath)) {
                    Log::warning('HLS file not found', [
                        'expected_path' => $hlsPath,
                        'event_id' => $event->id,
                    ]);
                    return null;
                }
                
                return $hlsUrl;
            }
        }
        
        // Priority 2: Recording (for ended events)
        if ($event->recording_path) {
            $recordingPath = storage_path('app/public/' . $event->recording_path);
            
            // Verify recording exists
            if (!file_exists($recordingPath)) {
                Log::warning('Recording file not found', [
                    'expected_path' => $recordingPath,
                    'event_id' => $event->id,
                ]);
                return null;
            }
            
            return asset('storage/' . $event->recording_path);
        }
        
        return null;
    }
}
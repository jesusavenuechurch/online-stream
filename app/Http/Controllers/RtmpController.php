<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\StreamEvent;
use App\Models\StreamSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class RtmpController extends Controller
{
    /**
     * Validate stream key before allowing publish
     */
    public function validate(Request $request)
    {
        Log::info('RTMP Validate Request', $request->all());
        
        $streamKey = $request->input('name'); // Nginx sends stream key as 'name'
        
        if (!$streamKey) {
            Log::warning('RTMP: No stream key provided');
            return response('', 403);
        }
        
        $settings = StreamSettings::current();
        
        if (!$settings) {
            Log::error('RTMP: No stream settings found');
            return response('', 403);
        }
        
        // Validate stream key
        if ($streamKey !== $settings->stream_key) {
            Log::warning('RTMP: Invalid stream key', [
                'provided' => $streamKey,
                'expected' => $settings->stream_key
            ]);
            return response('', 403);
        }
        
        Log::info('RTMP: Stream key validated successfully');
        return response('', 200);
    }
    
    /**
     * Handle stream start
     */
    public function start(Request $request)
    {
        Log::info('RTMP Stream Started', $request->all());
        
        $streamKey = $request->input('name');
        
        // Find the most recent scheduled event and mark it as live
        $event = StreamEvent::where('status', 'scheduled')
            ->orderBy('created_at', 'desc')
            ->first();
        
        if ($event) {
            // Delete previous recording if retention is "until_next_stream"
            $this->deletePreviousRecordings($event);
            
            $event->update([
                'status' => 'live',
                'started_at' => now(),
            ]);
            
            Log::info('RTMP: Event marked as live', ['event_id' => $event->id]);
        } else {
            Log::warning('RTMP: No scheduled event found to mark as live');
        }
        
        return response('', 200);
    }
    
    /**
     * Handle stream end
     */
    public function end(Request $request)
    {
        Log::info('RTMP Stream Ended', $request->all());
        
        // Find the current live event and mark it as ended
        $event = StreamEvent::where('status', 'live')->first();
        
        if ($event) {
            $event->update([
                'status' => 'ended',
                'ended_at' => now(),
            ]);
            
            // If recording was enabled, save the recording path
            $this->processRecording($event);
            
            Log::info('RTMP: Event marked as ended', ['event_id' => $event->id]);
            
            // Update all active attendance records to set left_at
            $event->attendance()
                ->whereNull('left_at')
                ->update(['left_at' => now()]);
        }
        
        return response('', 200);
    }
    
    /**
     * Delete previous recordings based on retention policy
     */
    private function deletePreviousRecordings(StreamEvent $currentEvent)
    {
        // Find events with "until_next_stream" retention that have recordings
        $previousEvents = StreamEvent::where('id', '!=', $currentEvent->id)
            ->where('recording_retention', 'until_next_stream')
            ->whereNotNull('recording_path')
            ->get();
        
        foreach ($previousEvents as $event) {
            if ($event->recording_path && Storage::disk('public')->exists($event->recording_path)) {
                Storage::disk('public')->delete($event->recording_path);
                Log::info('RTMP: Deleted previous recording', [
                    'event_id' => $event->id,
                    'path' => $event->recording_path
                ]);
            }
            
            $event->update(['recording_path' => null]);
        }
    }
    
    /**
     * Process and save recording after stream ends
     */
    private function processRecording(StreamEvent $event)
    {
        // Check if recording exists in the recordings directory
        $recordingsPath = storage_path('app/public/recordings');
        
        if (!is_dir($recordingsPath)) {
            Log::warning('RTMP: Recordings directory does not exist');
            return;
        }
        
        // Find the most recent recording file
        $files = glob($recordingsPath . '/*.flv');
        
        if (empty($files)) {
            Log::warning('RTMP: No recording file found');
            return;
        }
        
        // Get the most recent file
        usort($files, function($a, $b) {
            return filemtime($b) - filemtime($a);
        });
        
        $recordingFile = $files[0];
        $filename = basename($recordingFile);
        
        // Update event with recording path
        $event->update([
            'recording_path' => 'recordings/' . $filename
        ]);
        
        Log::info('RTMP: Recording saved', [
            'event_id' => $event->id,
            'file' => $filename
        ]);
        
        // Schedule deletion based on retention policy
        if ($event->recording_retention === 'days' && $event->recording_retention_days) {
            // You can implement a scheduled job to handle this
            Log::info('RTMP: Recording will be deleted in ' . $event->recording_retention_days . ' days');
        }
    }
}
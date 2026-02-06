<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\StreamEvent;
use App\Models\StreamSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class RtmpController extends Controller
{
    /**
     * Validate stream key before allowing publish
     */
    public function validate(Request $request)
    {
        Log::info('RTMP Validate Request', [
            'all_params' => $request->all(),
            'headers' => $request->headers->all(),
            'ip' => $request->ip(),
        ]);
        
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
        
        Log::info('RTMP: Stream key validated successfully', ['stream_key' => $streamKey]);
        return response('', 200);
    }
    
    /**
     * Handle stream start
     */
    public function start(Request $request)
    {
        Log::info('RTMP Stream Started', [
            'params' => $request->all(),
            'timestamp' => now()->toDateTimeString(),
        ]);
        
        $streamKey = $request->input('name');
        
        try {
            // End any currently live streams first (cleanup)
            $currentlyLive = StreamEvent::where('status', 'live')->get();
            foreach ($currentlyLive as $liveEvent) {
                Log::warning('RTMP: Found orphaned live event, cleaning up', ['event_id' => $liveEvent->id]);
                $this->endEvent($liveEvent);
            }
            
            // Find the most recent scheduled event and mark it as live
            $event = StreamEvent::where('status', 'scheduled')
                ->orderBy('created_at', 'desc')
                ->first();
            
            if ($event) {
                // Delete previous recordings if retention is "until_next_stream"
                $this->deletePreviousRecordings($event);
                
                $event->update([
                    'status' => 'live',
                    'started_at' => now(),
                ]);
                
                Log::info('RTMP: Event marked as live', [
                    'event_id' => $event->id,
                    'title' => $event->title,
                ]);
            } else {
                Log::warning('RTMP: No scheduled event found to mark as live');
            }
            
            return response('', 200);
            
        } catch (\Exception $e) {
            Log::error('RTMP: Error in start handler', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response('', 500);
        }
    }
    
    /**
     * Handle stream end
     */
    public function end(Request $request)
    {
        Log::info('RTMP Stream Ended', [
            'params' => $request->all(),
            'timestamp' => now()->toDateTimeString(),
        ]);
        
        try {
            // Find the current live event and mark it as ended
            $event = StreamEvent::where('status', 'live')->first();
            
            if ($event) {
                $this->endEvent($event);
            } else {
                Log::warning('RTMP: No live event found to mark as ended');
            }
            
            return response('', 200);
            
        } catch (\Exception $e) {
            Log::error('RTMP: Error in end handler', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response('', 500);
        }
    }
    
    /**
     * End an event and clean up
     */
    private function endEvent(StreamEvent $event)
    {
        $event->update([
            'status' => 'ended',
            'ended_at' => now(),
        ]);
        
        // Process recording BEFORE updating attendance
        // This gives the recording time to finish writing
        sleep(2); // Wait 2 seconds for recording to finalize
        $this->processRecording($event);
        
        Log::info('RTMP: Event marked as ended', [
            'event_id' => $event->id,
            'duration' => $event->started_at ? $event->started_at->diffForHumans(now(), true) : 'unknown',
        ]);
        
        // Update all active attendance records to set left_at
        $affectedRows = $event->attendance()
            ->whereNull('left_at')
            ->update(['left_at' => now()]);
            
        Log::info('RTMP: Closed attendance records', ['count' => $affectedRows]);
        
        // Clean up old HLS segments (optional - keeps disk clean)
        $this->cleanupHlsSegments($event);
    }
    
    /**
     * Delete previous recordings based on retention policy
     */
    private function deletePreviousRecordings(StreamEvent $currentEvent)
    {
        Log::info('RTMP: Checking for previous recordings to delete');
        
        // Find events with "until_next_stream" retention that have recordings
        $previousEvents = StreamEvent::where('id', '!=', $currentEvent->id)
            ->where('recording_retention', 'until_next_stream')
            ->whereNotNull('recording_path')
            ->get();
        
        foreach ($previousEvents as $event) {
            if ($event->recording_path && Storage::disk('public')->exists($event->recording_path)) {
                try {
                    Storage::disk('public')->delete($event->recording_path);
                    Log::info('RTMP: Deleted previous recording', [
                        'event_id' => $event->id,
                        'path' => $event->recording_path,
                    ]);
                } catch (\Exception $e) {
                    Log::error('RTMP: Failed to delete recording', [
                        'event_id' => $event->id,
                        'path' => $event->recording_path,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
            
            $event->update(['recording_path' => null]);
        }
    }
    
    /**
     * Process and save recording after stream ends
     */
    private function processRecording(StreamEvent $event)
    {
        Log::info('RTMP: Processing recording for event', ['event_id' => $event->id]);
        
        // Check if recording exists in the recordings directory
        $recordingsPath = storage_path('app/public/recordings');
        
        if (!is_dir($recordingsPath)) {
            Log::warning('RTMP: Recordings directory does not exist', ['path' => $recordingsPath]);
            return;
        }
        
        // Find recording files created in the last 5 minutes
        // This is more reliable than "most recent" across all time
        $files = glob($recordingsPath . '/*.{flv,mp4}', GLOB_BRACE);
        
        if (empty($files)) {
            Log::warning('RTMP: No recording files found', ['path' => $recordingsPath]);
            return;
        }
        
        // Filter files modified in last 5 minutes (likely from this stream)
        $recentFiles = array_filter($files, function($file) {
            return (time() - filemtime($file)) < 300; // 5 minutes
        });
        
        if (empty($recentFiles)) {
            Log::warning('RTMP: No recent recording files found');
            return;
        }
        
        // Get the most recent file
        usort($recentFiles, function($a, $b) {
            return filemtime($b) - filemtime($a);
        });
        
        $recordingFile = $recentFiles[0];
        $filename = basename($recordingFile);
        $filesize = filesize($recordingFile);
        
        // Verify file is not empty
        if ($filesize < 1024) { // Less than 1KB is probably corrupt
            Log::warning('RTMP: Recording file too small, likely corrupt', [
                'file' => $filename,
                'size' => $filesize,
            ]);
            return;
        }
        
        // Update event with recording path
        $event->update([
            'recording_path' => 'recordings/' . $filename
        ]);
        
        Log::info('RTMP: Recording saved', [
            'event_id' => $event->id,
            'file' => $filename,
            'size' => round($filesize / 1024 / 1024, 2) . ' MB',
        ]);
        
        // Schedule deletion based on retention policy
        if ($event->recording_retention === 'days' && $event->recording_retention_days) {
            Log::info('RTMP: Recording will be deleted in ' . $event->recording_retention_days . ' days');
            // You could dispatch a job here to handle this
            // DeleteRecordingJob::dispatch($event)->delay(now()->addDays($event->recording_retention_days));
        }
    }
    
    /**
     * Clean up HLS segments after stream ends
     */
    private function cleanupHlsSegments(StreamEvent $event)
    {
        $settings = StreamSettings::current();
        if (!$settings || !$settings->stream_key) {
            return;
        }
        
        $hlsPath = storage_path('app/public/hls/' . $settings->stream_key);
        
        if (is_dir($hlsPath)) {
            try {
                // Delete .ts and .m3u8 files
                $files = glob($hlsPath . '/*.{ts,m3u8}', GLOB_BRACE);
                foreach ($files as $file) {
                    if (is_file($file)) {
                        unlink($file);
                    }
                }
                Log::info('RTMP: Cleaned up HLS segments', ['count' => count($files)]);
            } catch (\Exception $e) {
                Log::error('RTMP: Failed to clean HLS segments', ['error' => $e->getMessage()]);
            }
        }
    }
}
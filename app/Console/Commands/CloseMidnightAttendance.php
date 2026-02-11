<?php

namespace App\Console\Commands;

use App\Models\Attendance;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CloseMidnightAttendance extends Command
{
    protected $signature = 'attendance:close-midnight';
    protected $description = 'Close all active attendance sessions at midnight';

    public function handle()
    {
        $this->info('Closing all active attendance sessions at midnight...');
        
        // Find all attendance records with no left_at
        $activeAttendance = Attendance::whereNull('left_at')->get();
        
        $count = $activeAttendance->count();
        
        if ($count === 0) {
            $this->info('No active attendance sessions to close.');
            return 0;
        }
        
        // Update all to set left_at
        Attendance::whereNull('left_at')->update([
            'left_at' => now()
        ]);
        
        Log::info('Midnight attendance closure', [
            'closed_sessions' => $count,
            'timestamp' => now()
        ]);
        
        $this->info("✅ Closed {$count} active attendance sessions.");
        
        return 0;
    }
}
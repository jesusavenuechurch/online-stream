<?php

use App\Http\Controllers\RtmpController;
use Illuminate\Support\Facades\Route;
use App\Models\Group;

// RTMP callbacks from Nginx
Route::post('/rtmp/validate', [RtmpController::class, 'validate'])->name('rtmp.validate');
Route::post('/rtmp/start', [RtmpController::class, 'start'])->name('rtmp.start');
Route::post('/rtmp/end', [RtmpController::class, 'end'])->name('rtmp.end');
Route::get('/groups', function () {
    return Group::active()
        ->with('zone:id,name')
        ->get(['id', 'zone_id', 'name', 'order']);
});
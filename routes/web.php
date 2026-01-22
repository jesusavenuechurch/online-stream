<?php

use App\Http\Controllers\StreamController;
use App\Http\Controllers\AttendeeAuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Stream routes
Route::get('/watch/{event}', [StreamController::class, 'show'])->name('stream.show');
Route::post('/watch/{event}/authenticate', [AttendeeAuthController::class, 'authenticate'])->name('attendee.authenticate');
Route::post('/watch/{event}/register', [AttendeeAuthController::class, 'register'])->name('attendee.register');
Route::post('/watch/{event}/leave', [StreamController::class, 'leave'])->name('stream.leave');
Route::post('/watch/{event}/heartbeat', [StreamController::class, 'heartbeat'])->name('stream.heartbeat');

// Check if username exists (for UX)
Route::post('/check-username', [AttendeeAuthController::class, 'checkUsername'])->name('attendee.check-username');
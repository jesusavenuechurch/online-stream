<?php

use App\Http\Controllers\StreamController;
use App\Http\Controllers\AttendeeAuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AvatarController;

Route::get('/', function () {
    return view('welcome');
});

/**
 * JOINT/UNIVERSAL ROUTES
 * These handle registration when the user doesn't have a specific event link.
 * We add {event?} so the same page can handle specific events too.
 */
Route::get('/join/{event?}', [AttendeeAuthController::class, 'showJoinPage'])->name('join');
Route::post('/join/authenticate', [AttendeeAuthController::class, 'authenticateDirect'])->name('join.authenticate');
Route::post('/join/register', [AttendeeAuthController::class, 'registerDirect'])->name('join.register');

/**
 * STREAM ROUTES
 * Used when the user is actually inside the player.
 */
Route::get('/watch/{event}', [StreamController::class, 'show'])->name('stream.show');
Route::post('/watch/{event}/authenticate', [AttendeeAuthController::class, 'authenticate'])->name('attendee.authenticate');
Route::post('/watch/{event}/register', [AttendeeAuthController::class, 'register'])->name('attendee.register');

// Tracking
Route::post('/watch/{event}/leave', [StreamController::class, 'leave'])->name('stream.leave');
Route::post('/watch/{event}/heartbeat', [StreamController::class, 'heartbeat'])->name('stream.heartbeat');

Route::post('/check-username', [AttendeeAuthController::class, 'checkUsername'])->name('attendee.check-username');

Route::get('/avatar', [AvatarController::class, 'index'])->name('avatar.index');
Route::get('/avatar/template', [AvatarController::class, 'getTemplate'])->name('avatar.template');


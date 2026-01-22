<?php

namespace App\Http\Controllers;

use App\Models\Attendee;
use App\Models\StreamEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class AttendeeAuthController extends Controller
{
    /**
     * Authenticate existing attendee
     * No more generic validation errors - we send clear messages for stubborn users.
     */
    public function authenticate(Request $request, StreamEvent $event)
    {
        // 1. Validate that something was entered
        if (!$request->username) {
            return response()->json(['message' => 'Please enter a username.'], 422);
        }

        // 2. Look for the user
        $attendee = Attendee::where('username', $request->username)->first();

        // 3. Clear error if user doesn't exist
        if (!$attendee) {
            return response()->json([
                'message' => 'This username is not registered yet. Please click the Register tab to create an account.'
            ], 404);
        }

        // 4. Pastor-only check
        if ($event->isPastorsOnly() && !$attendee->isPastor()) {
            return response()->json([
                'message' => 'This event is restricted to Pastors only.'
            ], 403);
        }

        // 5. Success - Set Session
        Session::put('attendee_id', $attendee->id);
        Session::put('event_id', $event->id);

        return response()->json([
            'success' => true,
            'redirect' => route('stream.show', $event),
        ]);
    }

    /**
     * Register new attendee
     */
    public function register(Request $request, StreamEvent $event)
    {
        $validator = Validator::make($request->all(), [
            'title'      => 'required|string',
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'username'   => 'required|string|max:255|unique:attendees,username',
            'zone_id'    => 'required|exists:zones,id',
            'group_id'   => 'required|exists:groups,id',
            'type'       => 'required|in:pastor,member',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 422);
        }

        $attendee = Attendee::create([
            'title'       => $request->title,
            'first_name'  => $request->first_name,
            'last_name'   => $request->last_name,
            'username'    => $request->username,
            'zone_id'     => $request->zone_id,
            'group_id'    => $request->group_id,
            'type'        => $request->type,
        ]);

        Session::put('attendee_id', $attendee->id);
        Session::put('event_id', $event->id);

        // LOGIC: Is the stream actually live right now?
        $isLive = $event->isLive() || $event->recording_path;

        return response()->json([
            'success' => true,
            'is_live' => $isLive,
            'redirect' => $isLive ? route('stream.show', $event) : null,
            'message' => $isLive ? 'Joining...' : 'Registration Successful!'
        ]);
    }
}
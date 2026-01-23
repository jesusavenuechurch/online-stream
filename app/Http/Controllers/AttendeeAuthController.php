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
     * Show join page without specific event
     */
    public function showJoinPage()
    {
        $event = StreamEvent::where('status', 'live')->first();
        return view('stream.join', compact('event'));
    }

    /**
     * Authenticate from direct join page (/join)
     */
    public function authenticateDirect(Request $request)
    {
        if (!$request->username) {
            return response()->json(['message' => 'Please enter a username.'], 422);
        }

        $attendee = Attendee::where('username', $request->username)->first();

        if (!$attendee) {
            return response()->json([
                'message' => 'This username is not registered. Please register first.'
            ], 404);
        }

        Session::put('attendee_id', $attendee->id);

        $liveEvent = StreamEvent::where('status', 'live')->first();

        if ($liveEvent) {
            Session::put('event_id', $liveEvent->id);
            return response()->json([
                'success' => true,
                'is_live' => true,
                'redirect' => route('stream.show', $liveEvent->id),
            ]);
        }

        return response()->json([
            'success' => true,
            'is_live' => false,
            'message' => 'Signed in successfully! No live stream at the moment.',
        ]);
    }

    /**
     * Register from direct join page (/join)
     */
    public function registerDirect(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title'      => 'required|string',
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'username'   => 'required|string|max:255|unique:attendees,username',
            'email'      => 'required|email|max:255|unique:attendees,email',
            'phone'      => 'required|string|max:20',
            'zone_id'    => 'required|exists:zones,id',
            'group_id'   => 'required|exists:groups,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 422);
        }

        $attendee = Attendee::create([
            'title'       => $request->title,
            'first_name'  => $request->first_name,
            'last_name'   => $request->last_name,
            'username'    => $request->username,
            'email'       => $request->email,
            'phone'       => $request->phone,
            'zone_id'     => $request->zone_id,
            'group_id'    => $request->group_id,
            'type'        => 'member', // Hardcoded as member
        ]);

        Session::put('attendee_id', $attendee->id);

        $liveEvent = StreamEvent::where('status', 'live')->first();

        if ($liveEvent) {
            Session::put('event_id', $liveEvent->id);
            return response()->json([
                'success' => true,
                'is_live' => true,
                'redirect' => route('stream.show', $liveEvent->id),
            ]);
        }

        return response()->json([
            'success' => true,
            'is_live' => false,
            'message' => 'Registration successful! The broadcast will start shortly.',
        ]);
    }

    /**
     * Authenticate existing attendee (event-specific /watch/{event})
     */
    public function authenticate(Request $request, StreamEvent $event)
    {
        if (!$request->username) {
            return response()->json(['message' => 'Please enter a username.'], 422);
        }

        $attendee = Attendee::where('username', $request->username)->first();

        if (!$attendee) {
            return response()->json([
                'message' => 'This username is not registered yet.'
            ], 404);
        }

        if ($event->isPastorsOnly() && !$attendee->isPastor()) {
            return response()->json([
                'message' => 'This event is restricted to Pastors only.'
            ], 403);
        }

        Session::put('attendee_id', $attendee->id);
        Session::put('event_id', $event->id);

        $isLive = ($event->status === 'live' || $event->recording_path);

        return response()->json([
            'success' => true,
            'is_live' => $isLive,
            'redirect' => $isLive ? route('stream.show', $event->id) : null,
            'message' => $isLive ? 'Joining...' : 'Signed in! Please wait for the stream.'
        ]);
    }

    /**
     * Register new attendee (event-specific /watch/{event})
     */
    public function register(Request $request, StreamEvent $event)
    {
        $validator = Validator::make($request->all(), [
            'title'      => 'required|string',
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'username'   => 'required|string|max:255|unique:attendees,username',
            'email'      => 'required|email|max:255|unique:attendees,email',
            'phone'      => 'required|string|max:20',
            'zone_id'    => 'required|exists:zones,id',
            'group_id'   => 'required|exists:groups,id',
            // REMOVED 'type' => 'required' here because the form doesn't send it
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 422);
        }

        $attendee = Attendee::create([
            'title'       => $request->title,
            'first_name'  => $request->first_name,
            'last_name'   => $request->last_name,
            'username'    => $request->username,
            'email'       => $request->email,
            'phone'       => $request->phone,
            'zone_id'     => $request->zone_id,
            'group_id'    => $request->group_id,
            'type'        => 'member', // Defaulting to member
        ]);

        Session::put('attendee_id', $attendee->id);
        Session::put('event_id', $event->id);

        $isLive = ($event->status === 'live' || $event->recording_path);

        return response()->json([
            'success' => true,
            'is_live' => $isLive,
            'redirect' => $isLive ? route('stream.show', $event->id) : null,
            'message' => $isLive ? 'Joining...' : 'Registration Successful!'
        ]);
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Attendee;
use App\Models\StreamEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;

class AttendeeAuthController extends Controller
{
    /**
     * Check if username exists
     */
    public function checkUsername(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
        ]);

        $attendee = Attendee::where('username', $request->username)->first();

        return response()->json([
            'exists' => (bool) $attendee,
            'attendee' => $attendee ? [
                'first_name' => $attendee->first_name,
                'last_name' => $attendee->last_name,
                'type' => $attendee->type,
            ] : null,
        ]);
    }

    /**
     * Authenticate existing attendee
     */
    public function authenticate(Request $request, StreamEvent $event)
    {
        $request->validate([
            'username' => 'required|string|exists:attendees,username',
        ]);

        $attendee = Attendee::where('username', $request->username)->firstOrFail();

        // Pastor-only check
        if ($event->isPastorsOnly() && !$attendee->isPastor()) {
            return response()->json([
                'message' => 'This stream is for pastors only.'
            ], 403);
        }

        Session::put('attendee_id', $attendee->id);
        Session::put('event_id', $event->id);

        return response()->json([
            'success' => true,
            'redirect' => route('stream.show', $event),
        ]);
    }

    /**
     * Register new attendee with Zone and Group
     */
    public function register(Request $request, StreamEvent $event)
    {
        $validated = $request->validate([
            'title'      => 'required|string',
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'username'   => 'required|string|max:255|unique:attendees,username',
            'zone_id'    => 'required|exists:zones,id',
            'group_id'   => 'required|exists:groups,id',
            'type'       => 'required|in:pastor,member',
        ]);

        // Create attendee using the new model fields
        $attendee = Attendee::create([
            'title'       => $validated['title'],
            'first_name'  => $validated['first_name'],
            'last_name'   => $validated['last_name'],
            'username'    => $validated['username'],
            'zone_id'     => $validated['zone_id'],
            'group_id'    => $validated['group_id'],
            'type'        => $validated['type'],
            // Optional fields if you still want them
            'email'       => $request->email,
            'phone'       => $request->phone,
            'church_name' => $request->church_name,
        ]);

        // Double check pastor permissions if they just registered for a pastor-only event
        if ($event->isPastorsOnly() && !$attendee->isPastor()) {
            return response()->json(['message' => 'Unauthorized type for this event.'], 403);
        }

        Session::put('attendee_id', $attendee->id);
        Session::put('event_id', $event->id);

        return response()->json([
            'success' => true,
            'redirect' => route('stream.show', $event),
        ]);
    }
}
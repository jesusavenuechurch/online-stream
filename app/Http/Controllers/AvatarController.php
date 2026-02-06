<?php

namespace App\Http\Controllers;

use App\Models\AvatarTemplate;
use Illuminate\Http\Request;

class AvatarController extends Controller
{
    public function index()
    {
        $template = AvatarTemplate::getActive();

        if (!$template) {
            abort(404, 'No active avatar template found');
        }

        return view('avatar.index', compact('template'));
    }

    public function getTemplate()
    {
        $template = AvatarTemplate::getActive();

        if (!$template) {
            return response()->json(['error' => 'No active template'], 404);
        }

        return response()->json([
            'poster_url' => asset('storage/' . $template->poster_path),
            'frame_x' => $template->frame_x,
            'frame_y' => $template->frame_y,
            'frame_size' => $template->frame_size,
            'frame_shape' => $template->frame_shape,
        ]);
    }
}
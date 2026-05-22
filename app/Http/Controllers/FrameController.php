<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Frame;
use Illuminate\Support\Facades\Storage;

class FrameController extends Controller
{
    public function show(string $slug)
    {
        $frame = Frame::active()
            ->where('slug', $slug)
            ->firstOrFail();

            $frameUrl = Storage::disk('public')->url($frame->file_path);
 
        return view('frames.show', compact('frame', 'frameUrl'));
    }
}

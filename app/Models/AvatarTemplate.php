<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class AvatarTemplate extends Model
{
    protected $fillable = [
        'title',
        'poster_path',
        'frame_x',
        'frame_y',
        'frame_size',
        'frame_shape',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function getPosterUrlAttribute(): string
    {
        return Storage::url($this->poster_path);
    }

    public static function getActive(): ?self
    {
        return self::where('is_active', true)->first();
    }
}
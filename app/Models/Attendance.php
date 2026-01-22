<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    use HasFactory;

    protected $table = 'attendance';

    protected $fillable = [
        'event_id',
        'attendee_id',
        'joined_at',
        'left_at',
        'ip_address',
        'user_agent',
        'session_id',
    ];

    protected $casts = [
        'joined_at' => 'datetime',
        'left_at' => 'datetime',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(StreamEvent::class, 'event_id');
    }

    public function attendee(): BelongsTo
    {
        return $this->belongsTo(Attendee::class);
    }

    public function getDuration(): ?int
    {
        if (!$this->left_at) {
            return null;
        }

        return $this->joined_at->diffInMinutes($this->left_at);
    }

    public function scopeActive($query)
    {
        return $query->whereNull('left_at');
    }
}
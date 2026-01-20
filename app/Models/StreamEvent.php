<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StreamEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'visibility',
        'status',
        'started_at',
        'ended_at',
        'recording_path',
        'recording_retention',
        'recording_retention_days',
        'created_by',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    protected static function booted()
    {
        static::creating(function ($event) {
            if (auth()->check()) {
                $event->created_by = auth()->id();
            }
        });
    }

    public function attendance(): HasMany
    {
        return $this->hasMany(Attendance::class, 'event_id');
    }

    public function isPastorsOnly(): bool
    {
        return $this->visibility === 'pastors_only';
    }

    public function isPublic(): bool
    {
        return $this->visibility === 'public';
    }

    public function isLive(): bool
    {
        return $this->status === 'live';
    }

    public function scopeLive($query)
    {
        return $query->where('status', 'live');
    }

    public function scopeEnded($query)
    {
        return $query->where('status', 'ended');
    }

    public function getCurrentViewersCount(): int
    {
        return $this->attendance()
            ->whereNull('left_at')
            ->count();
    }

    public function getTotalAttendanceCount(): int
    {
        return $this->attendance()->count();
    }
}
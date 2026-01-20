<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Zone extends Model
{
    protected $fillable = ['name', 'order', 'active'];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function groups(): HasMany
    {
        return $this->hasMany(Group::class)->orderBy('order');
    }

    public function attendees(): HasMany
    {
        return $this->hasMany(Attendee::class);
    }

    public function scopeActive($query)
    {
        return $query->where('active', true)->orderBy('order');
    }
}
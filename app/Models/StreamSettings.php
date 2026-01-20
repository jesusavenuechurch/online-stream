<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class StreamSettings extends Model
{
    use HasFactory;

    protected $fillable = [
        'stream_key',
        'rtmp_url',
        'updated_by',
    ];

    protected $casts = [
        'stream_key' => 'encrypted',
    ];

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public static function current(): ?self
    {
        return self::first();
    }

    public static function generateNewKey(): string
    {
        return 'stream_' . Str::random(32);
    }

    public function regenerateKey(int $userId): void
    {
        $this->update([
            'stream_key' => self::generateNewKey(),
            'updated_by' => $userId,
        ]);
    }
}
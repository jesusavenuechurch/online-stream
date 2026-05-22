<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Frame extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'frame_path',
        'thumbnail_path',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // ─── Relationships ───────────────────────────────────────────────────────

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function generatedAvatars(): HasMany
    {
        return $this->hasMany(GeneratedAvatar::class);
    }

    // ─── Accessors ───────────────────────────────────────────────────────────

    /**
     * Full public URL to the frame PNG — used by the Canvas on the frontend.
     */
    public function getFrameUrlAttribute(): string
    {
        return Storage::url($this->frame_path);
    }

    /**
     * Full public URL to the thumbnail, falls back to the frame itself.
     */
    public function getThumbnailUrlAttribute(): string
    {
        return $this->thumbnail_path
            ? Storage::url($this->thumbnail_path)
            : $this->frame_url;
    }

    // ─── Scopes ──────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    /**
     * Auto-generate a slug from the name, ensuring uniqueness.
     */
    public static function generateSlug(string $name): string
    {
        $slug = Str::slug($name);
        $count = static::withTrashed()->where('slug', 'like', "{$slug}%")->count();

        return $count ? "{$slug}-{$count}" : $slug;
    }
}
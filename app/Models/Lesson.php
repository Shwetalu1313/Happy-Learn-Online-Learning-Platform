<?php

namespace App\Models;

use App\Services\LessonVideoService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lesson extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'body',
        'creator_id',
        'course_id',
        'video_provider',
        'video_source',
        'video_id',
        'video_start_at',
        'video_is_preview',
    ];

    protected $casts = [
        'video_start_at' => 'integer',
        'video_is_preview' => 'boolean',
    ];

    public static function getModelName(): string
    {
        return 'Lesson';
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id', 'id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id', 'id');
    }

    public function exercises(): HasMany
    {
        return $this->hasMany(Exercise::class, 'lesson_id');
    }

    public function forums(): HasMany
    {
        return $this->hasMany(Forum::class, 'lesson_id');
    }

    public function hasVideo(): bool
    {
        return is_string($this->video_id) && $this->video_id !== '';
    }

    public function getVideoEmbedUrlAttribute(): ?string
    {
        return app(LessonVideoService::class)->embedUrl(
            $this->video_provider,
            $this->video_id,
            $this->video_start_at
        );
    }
}

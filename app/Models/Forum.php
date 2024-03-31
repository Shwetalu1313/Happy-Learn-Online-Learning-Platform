<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Forum extends Model
{
    use HasFactory;

    protected $fillable = ['text','user_id','lesson_id'];

    public static function getModelName(): string
    {
        return 'Forum';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class,'user_id');
    }
    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class,'lesson_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class,'forum_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lesson extends Model
{
    use HasFactory;
    protected $fillable = ['title','body','creator_id','course_id'];

    public static function getModelName(): string
    {
        return 'Lesson';
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class,'course_id','id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class,'creator_id','id');
    }

    public function exercises(): HasMany
    {
        return $this->hasMany(Exercise::class);
    }
    public function forums(): HasMany
    {
        return $this->hasMany(Forum::class,'lesson_id');
    }
}

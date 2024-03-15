<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Lesson extends Model
{
    use HasFactory;
    protected $fillable = ['title','body','creator_id','course_id'];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class,'course_id','id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class,'creator_id','id');
    }
}

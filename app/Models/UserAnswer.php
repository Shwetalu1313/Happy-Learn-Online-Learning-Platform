<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'exercise_id',
        'question_id',
        'answer_id',
        'answer_text',
        'is_correct',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class,'user_id');
    }
    public function exercise(): BelongsTo
    {
        return $this->belongsTo(Exercise::class,'exercise_id');
    }
    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class,'question_id');
    }

    public function answer(): BelongsTo
    {
        return $this->belongsTo(Answer::class,'answer_id');
    }
}

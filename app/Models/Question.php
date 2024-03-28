<?php

namespace App\Models;

use App\Enums\QuestionTypeEnums;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model
{
    use HasFactory;

    protected $fillable = ['exercise_id', 'text', 'question_type'];

    protected $casts =['question_type' => QuestionTypeEnums::class];
    public function exercise(): BelongsTo
    {
        return $this->belongsTo(Exercise::class,'exercise_id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class);
    }
}

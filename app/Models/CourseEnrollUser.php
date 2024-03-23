<?php

namespace App\Models;

use App\Enums\CoursePaymentTypeEnums;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseEnrollUser extends Model
{
    use HasFactory; use HasUuids;

    protected $fillable = [
        'user_id',
        'course_id',
        'amount',
        'payment_type',
        'card_number',
        'expired_date',
        'cardHolderName',
    ];

    protected $casts = [
        'payment_type' => CoursePaymentTypeEnums::class,
        'amount' => 'integer',
        'expired_date' => 'date',
    ];

    public function user(): BelongsTo {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function course(): BelongsTo {
        return $this->belongsTo(Course::class, 'course_id');
    }
}

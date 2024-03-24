<?php

namespace App\Models;

use App\Enums\CoursePaymentTypeEnums;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

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
        'cvv',
        'cardHolderName',
    ];

    protected $casts = [
        'payment_type' => CoursePaymentTypeEnums::class,
        'amount' => 'integer',
        'cvv' => 'integer'
    ];

    public function user(): BelongsTo {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function course(): BelongsTo {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public static function PopularCourses(){
        $startDate = Carbon::now()->subMonth(3)->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();

        $topCourses = CourseEnrollUser::select('course_id', DB::raw('count(*) as enrollments'))
            ->where('created_at', '>=', now()->subMonths(3))
            ->groupBy('course_id')
            ->orderByDesc('enrollments')
            ->limit(6)
            ->get();

        return $topCourses;
    }
}

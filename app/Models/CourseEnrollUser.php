<?php

namespace App\Models;

use App\Enums\CoursePaymentTypeEnums;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use App\Models\CurrencyExchange;

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

    public static function getModelName(): string
    {
        return 'CourseEnrollUser';
    }

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

    public static function generateReport()
    {
        // Get the current month and the previous month
        $currentMonth = Carbon::now()->startOfMonth();
        $previousMonth = Carbon::now()->subMonth()->startOfMonth();

        // Get enrollments for the current month
        $currentMonthEnrollments = CourseEnrollUser::whereBetween('created_at', [$currentMonth, Carbon::now()->endOfMonth()])->count();

        // Get enrollments for the previous month
        $previousMonthEnrollments = CourseEnrollUser::whereBetween('created_at', [$previousMonth, $previousMonth->endOfMonth()])->count();

        // Calculate percentage change
        $percentageChange = 0;
        if ($previousMonthEnrollments != 0) {
            $percentageChange = (($currentMonthEnrollments - $previousMonthEnrollments) / $previousMonthEnrollments) * 100;
        }

        // Determine if the enrollment has improved
        $improved = $currentMonthEnrollments > $previousMonthEnrollments;

        // Create the report
        $report = [
            'current_month_enrollments' => $currentMonthEnrollments,
            'previous_month_enrollments' => $previousMonthEnrollments,
            'percentage_change' => $percentageChange,
            'improved' => $improved,
        ];

        return $report;
    }

    public static function incomeReport()
    {
        // Get the current month and the previous month
        $currentMonth = Carbon::now()->startOfMonth();
        $previousMonth = Carbon::now()->subMonth()->startOfMonth();

        // Get total income for the current month
        $currentMonthIncome = CourseEnrollUser::whereBetween('created_at', [$currentMonth, Carbon::now()->endOfMonth()])
            ->sum('amount');

        // Get total income for the previous month
        $previousMonthIncome = CourseEnrollUser::whereBetween('created_at', [$previousMonth, $previousMonth->endOfMonth()])
            ->sum('amount');

        // Convert to UsDollar
        $us_avg = CurrencyExchange::getUSD();
        $currentMonthUsDollar = MoneyExchange($currentMonthIncome, $us_avg);
        $previousMonthUsDollar = MoneyExchange($previousMonthIncome, $us_avg);

        // Convert income amounts to K format if greater than or equal to 1000
        $currentMonthIncomeFormatted = $currentMonthUsDollar >= 1000 ? number_format($currentMonthUsDollar / 1000, 1) . 'K' : $currentMonthUsDollar;
        $previousMonthIncomeFormatted = $previousMonthUsDollar >= 1000 ? number_format($previousMonthUsDollar / 1000, 1) . 'K' : $previousMonthUsDollar;

        // Calculate percentage change
        $percentageChange = 0;
        $increased = true;
        if ($previousMonthIncome != 0) {
            $percentageChange = (($currentMonthIncome - $previousMonthIncome) / $previousMonthIncome) * 100;
            $increased = $currentMonthIncome > $previousMonthIncome;
        }

        // Create the income report
        return [
            'current_month_income' => $currentMonthIncomeFormatted,
            'previous_month_income' => $previousMonthIncomeFormatted,
            'percentage_change' => $percentageChange,
            'increased' => $increased,
        ];
    }

}

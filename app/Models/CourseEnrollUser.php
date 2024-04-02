<?php

namespace App\Models;

use App\Enums\CoursePaymentTypeEnums;
use App\Enums\UserRoleEnums;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;
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

    public static function getEnrollmentsForUser($user)
    {
        if ($user->role->value != UserRoleEnums::ADMIN->value) {
            // Retrieve direct enrollments for the user
            $directEnrollments = self::whereHas('course.creator', function ($query) use ($user) {
                $query->where('createdUser_id', $user->id);
            })->get();

            // Retrieve indirect enrollments for the user
            $indirectEnrollments = $user->contributor->flatMap(function ($contributor) {
                return $contributor->course->enrollCourses;
            })->all();

            // Merge the two collections
            $mergedEnrollments = new Collection(array_merge($directEnrollments->toArray(), $indirectEnrollments));

            // Limit access based on roles or other conditions as needed

            // Arrange enrollments from newest to oldest
            $sortedEnrollments = $mergedEnrollments->sortByDesc('created_at');

            return $sortedEnrollments;
        } else {
            // If user is an admin, return all enrollments
            return self::orderByDesc('created_at')->get();
        }
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

    private static function format_percentage($value): string
    {
        if (abs($value - 100) < 1e-6) {  // Check for close to 100%
            return "100%";
        } else {
            return number_format($value , 2); // Multiply by 100 for percentage, format with 2 decimals
        }
    }

    public static function generateReport()
    {

        $now = Carbon::now();
        $currentMonthEnrollments = CourseEnrollUser::whereMonth('created_at', $now->month)
                                    ->whereYear('created_at', $now->year)
                                    ->count();

        // Get enrollments for the previous month
        $lastMonthEnrollments = CourseEnrollUser::whereMonth('created_at',$now->subMonth()->month)
                                    ->whereYear('created_at', $now->year)->count();

        // Calculate percentage change
        $percentage = 0;
        if ($lastMonthEnrollments != 0) {
            $percentage = (($currentMonthEnrollments - $lastMonthEnrollments) / $lastMonthEnrollments) * 100;
        }

        // Format the percentage to two decimal places
        //$percentageChange = number_format($percentage, 2);
        $percentageChange = self::format_percentage($percentage);

        // Determine if the enrollment has improved
        $improved = $currentMonthEnrollments > $lastMonthEnrollments;

        // Create the report
        $report = [
            'current_month_enrollments' => $currentMonthEnrollments,
            'previous_month_enrollments' => $lastMonthEnrollments,
            'percentage_change' => $percentageChange,
            'improved' => $improved,
        ];

        return $report;
    }

    public static function incomeReport()
    {
        // Get the current month and the previous month
        /*$currentMonth = Carbon::now()->startOfMonth();
        $previousMonth = Carbon::now()->subMonth()->startOfMonth();

        // Get total income for the current month
        $currentMonthIncome = CourseEnrollUser::whereBetween('created_at', [$currentMonth, Carbon::now()->endOfMonth()])
            ->sum('amount');

        // Get total income for the previous month
        $previousMonthIncome = CourseEnrollUser::whereBetween('created_at', [$previousMonth, $previousMonth->endOfMonth()])
            ->sum('amount');*/

        $now = Carbon::now();
        $currentMonthIncome = CourseEnrollUser::whereMonth('created_at', $now->month)
            ->whereYear('created_at', $now->year)
            ->sum('amount');

        // Get enrollments for the previous month
        $previousMonthIncome = CourseEnrollUser::whereMonth('created_at',$now->subMonth()->month)
            ->whereYear('created_at', $now->year)->sum('amount');

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
            'percentage_change' => self::format_percentage($percentageChange),
            'increased' => $increased,
        ];
    }

    public static function getEnrollmentsChartData()
    {
        $startDate = Carbon::now()->subDays(30)->startOfDay();
        $endDate = Carbon::now()->endOfDay();

        $enrollmentsData = self::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as enrollments')
        )
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return $enrollmentsData;
    }

    public static function getIncomeChartData()
    {
        $startDate = Carbon::now()->subDays(30)->startOfDay();
        $endDate = Carbon::now()->endOfDay();

        $incomeData = self::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('SUM(amount) as income')
        )
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return $incomeData;
    }
}

<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Enums\UserRoleEnums;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;
    use \Illuminate\Auth\Passwords\CanResetPassword; //note:: this is important when you want to enable reset password method.

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['name','email','phone','birthdate','points','avatar','role','about','password'];

    protected $table = 'users';

    public static function getModelName(): string
    {
        return 'User';
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'role' => UserRoleEnums::class,
    ];

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class, 'createdUser_id')
            ->orWhere('approvedUser_id', $this->id);
    }

    public function contributor(): HasMany {
        return $this->hasMany(CourseContributor::class);
    }

    public function lessons(): HasMany {
        return $this->hasMany(Lesson::class,'creator_id');
    }

    public static function students() {
        return User::where('role', UserRoleEnums::STUDENT)->orderBy('points', 'desc')->get();
    }

    public static function teachers() {
        return User::where('role', UserRoleEnums::TEACHER->value)->get();
    }

    public function enrollCourses(): HasMany {
        return $this->hasMany(CourseEnrollUser::class, 'user_id');
    }

    public function user_answers(): HasMany {
        return $this->hasMany(UserAnswer::class,'user_id');
    }

    public function comments(): HasMany {
        return $this->hasMany(Comment::class,'user_id');
    }

    public function forum(): HasMany {
        return $this->hasMany(Comment::class,'user_id');
    }

    public function systemActivities(): HasMany
    {
        return $this->hasMany(SystemActivity::class,'user_id');
    }

    /**
     * Get the registration count and rate for the last three months.
     *
     * @return array
     */
    public static function usersRegisterCount()
    {
        $currentMonth = Carbon::now()->startOfMonth();
        $previousThreeMonths = Carbon::now()->subMonths(3)->startOfMonth();

        // Count of registrations in the current month
        $currentMonthCount = self::whereBetween('created_at', [$currentMonth, Carbon::now()->endOfMonth()])->count();

        // Count of registrations in the last three months
        $previousThreeMonthsCount = self::whereBetween('created_at', [$previousThreeMonths, $currentMonth])->count();

        // Calculate percentage change
        $percentageChange = 0;
        if ($previousThreeMonthsCount != 0) {
            $percentageChange = (($currentMonthCount - $previousThreeMonthsCount) / $previousThreeMonthsCount) * 100;
        }

        // Determine if the registration rate increased or decreased
        $increased = $currentMonthCount > $previousThreeMonthsCount;

        return [
            'current_month_count' => $currentMonthCount,
            'previous_three_months_count' => $previousThreeMonthsCount,
            'percentage_change' => $percentageChange,
            'increased' => $increased,
        ];
    }

    public static function getRegisterUserChartData()
    {
        $startDate = Carbon::now()->subDays(30)->startOfDay();
        $endDate = Carbon::now()->endOfDay();

        $userRegistrationData = User::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as registrations')
        )
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return $userRegistrationData;
    }
}

<?php

namespace App\Models;

use App\Enums\UserRoleEnums;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'description', 'image', 'courseType', 'fees', 'state', 'createdUser_id', 'approvedUser_id', 'sub_category_id'];

    public static function getModelName(): string
    {
        return 'Course';
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'createdUser_id', 'id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approvedUser_id', 'id');
    }

    public function contribute_courses(): HasMany
    {
        return $this->hasMany(CourseContributor::class, 'course_id');
    }

    public static function getCoursesForUser($user)
    {
        if ($user->role->value != UserRoleEnums::ADMIN->value) {
            $directCourses = $user->courses;
            $indirectCourses = $user->contributor->map(function ($contributor) {
                return $contributor->course;
            });

            // Merge the two collections
            $mergedCourses = $directCourses->merge($indirectCourses);

            // Sort the merged courses by created_at attribute in descending order
            $sortedCourses = $mergedCourses->sortByDesc('created_at');

            // Unique courses based on course ID
            return $sortedCourses->unique('id');
        } else {
            // If user is an admin, return all courses ordered by created_at in descending order
            return self::orderBy('created_at', 'desc')->get();
        }
    }

    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class);
    }

    public function enrollCourses(): HasMany
    {
        return $this->hasMany(CourseEnrollUser::class, 'course_id');
    }

    /**
     * @return mixed
     *               if there is completely no course that is created within 7 days, show the lasted 6 item.
     *               but if there is/are item/s within that day, just show.
     *               but item is less than 1 or equal, add the last item to 3.
     *               we don't show the course which has no lessons.
     */
    public static function getNewCourseLimitSix()
    {
        $limit = 6;
        $sevenDaysAgo = Carbon::now()->subDays(7);
        $baseQuery = self::query()
            ->select(['id', 'title', 'courseType', 'fees', 'state', 'createdUser_id', 'created_at'])
            ->has('lessons')
            ->with([
                'creator:id,name,role',
                'contribute_courses' => fn ($query) => $query
                    ->select(['id', 'course_id', 'user_id'])
                    ->with(['user:id,name,role']),
            ])
            ->withCount('lessons');

        $newCourses = (clone $baseQuery)
            ->where('created_at', '>=', $sevenDaysAgo)
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();

        if ($newCourses->count() >= $limit) {
            return $newCourses;
        }

        $remainingLimit = $limit - $newCourses->count();
        $fallbackQuery = (clone $baseQuery)
            ->orderByDesc('created_at');

        if ($newCourses->isNotEmpty()) {
            $fallbackQuery->whereNotIn('id', $newCourses->pluck('id'));
        }

        return $newCourses->concat($fallbackQuery->limit($remainingLimit)->get());

    }

    public function sub_category(): BelongsTo
    {
        return $this->belongsTo(SubCategory::class, 'sub_category_id');
    }
}

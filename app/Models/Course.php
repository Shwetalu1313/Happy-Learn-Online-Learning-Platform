<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Enums\UserRoleEnums;

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

    public function sub_category(): BelongsTo
    {
        return $this->belongsTo(SubCategory::class, 'sub_category_id');
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

            // Unique courses based on course ID
            return $mergedCourses->unique('id');
        } else {
            return self::all();
        }
    }


    /**
     * @return mixed
     * if there is completely no course that is created within 7 days, show the lasted 6 item.
     * but if there is/are item/s within that day, just show.
     * but item is less than 1 or equal, add the last item to 3.
     * we don't show the course which has no lessons.
     */
    public static function getNewCourseLimitSix(){
        $sevenDaysAgo = Carbon::now()->subDays(7);
        $newCourses = Course::where('created_at', '>=', $sevenDaysAgo)
                            ->has('lessons')
                            ->orderBy('created_at', 'desc')
                            ->get();

        if ($newCourses->isNotEmpty()){
            if ($newCourses->count() <= 1){
                $remainingLimit = 6 - $newCourses->count();
                $lastedCourses = Course::has('lessons')
                                        ->orderBy('created_at','desc')
                                        ->limit($remainingLimit)
                                        ->get();
                $newCourses = $newCourses->merge($lastedCourses);
            }
            return $newCourses;
        }
        else{
            return Course::has('lessons')
                            ->orderBy('id','desc')
                            ->limit(6)
                            ->get();
        }

    }

    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class);
    }

    public function enrollCourses(): HasMany {
        return $this->hasMany(CourseEnrollUser::class, 'course_id');
    }
}

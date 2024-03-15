<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Enums\UserRoleEnums;

class Course extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'description', 'image', 'courseType', 'fees', 'state', 'createdUser_id', 'approvedUser_id', 'sub_category_id'];

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

    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class);
    }
}

<?php

namespace Database\Factories;
use App\Enums\UserRoleEnums;
use App\Models\CourseContributor;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class CourseContributorFactory extends Factory
{
    protected $model = CourseContributor::class;

    public function definition(): array
    {
        return [
            'user_id' => function (array $attributes) {
                $creatorId = $attributes['creator_id'] ?? null; // Get creator_id from attributes or set to null if not provided
                $otherContributorIds = CourseContributor::where('course_id', $attributes['course_id'])
                    ->where('user_id', '!=', $creatorId)
                    ->pluck('user_id')
                    ->toArray();

                if (count($otherContributorIds) > 0) {
                    return $this->faker->randomElement($otherContributorIds);
                } else {
                    $teacherAdminIds = User::whereIn('role', [UserRoleEnums::TEACHER->value, UserRoleEnums::ADMIN->value])
                        ->where('id', '!=', $creatorId)
                        ->pluck('id')
                        ->toArray();

                    if (count($teacherAdminIds) === 0) {
                        return null;
                    }

                    return $this->faker->randomElement($teacherAdminIds);
                }
            },
            'course_id' => $this->faker->numberBetween(1, 20),
        ];
    }
}

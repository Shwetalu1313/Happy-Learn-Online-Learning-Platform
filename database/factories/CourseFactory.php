<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Enums\UserRoleEnums;
use App\Enums\CourseStateEnums;
use App\Enums\CourseTypeEnums;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Course>
 */
class CourseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $teacherAdminIds = User::whereIn('role', [UserRoleEnums::TEACHER->value, UserRoleEnums::ADMIN->value])->pluck('id')->toArray();
        return [
            'title' => $this->faker->sentence($nbWords = 3, $variableNbWords = true),
            'description' => $this->faker->paragraph,
            'image' => 'webstyle/owl.png',
            'courseType' => $this->faker->randomElement([CourseTypeEnums::ADVANCED->value, CourseTypeEnums::BASIC->value]),
            'fees' => rand(5000,100000),
            'state' => $this->faker->randomElement([CourseStateEnums::PENDING->value, CourseStateEnums::APPROVED->value]),
            'createdUser_id' => $this->faker->randomElement($teacherAdminIds),
            'approvedUser_id' => function (array $attributes) use ($teacherAdminIds) {
                // If the course state is pending, approvedUser_id should be null
                if ($attributes['state'] === CourseStateEnums::PENDING->value) {
                    return null;
                }
                // Otherwise, approvedUser_id should be different from createdUser_id
                $excludedIds = [$attributes['createdUser_id']];
                return $this->faker->randomElement(array_diff($teacherAdminIds, $excludedIds));
            },
            'sub_category_id' => rand(1,75),
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }
}

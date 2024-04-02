<?php

namespace Database\Factories;

use App\Enums\UserRoleEnums;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Comment>
 */
class CommentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $teacherStudentIds = User::whereIn('role', [UserRoleEnums::TEACHER->value, UserRoleEnums::STUDENT->value])->pluck('id')->toArray();
        return [
            'text' => fake()->sentence,
            'user_id' => fake()->randomElement($teacherStudentIds),
            'forum_id' => rand(1,30),
            'created_at' => fake()->dateTimeBetween('-1 year', 'now'),
        ];
    }
}

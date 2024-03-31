<?php

namespace Database\Factories;

use App\Enums\UserRoleEnums;
use App\Models\Answer;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Course;
use App\Models\CourseContributor;
use App\Models\CourseEnrollUser;
use App\Models\CurrencyExchange;
use App\Models\Exercise;
use App\Models\Forum;
use App\Models\JobPost;
use App\Models\Lesson;
use App\Models\Question;
use App\Models\SubCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SystemActivity>
 */
class SystemActivityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $tableNames = [
            Answer::getModelName(),
            Course::getModelName(),
            Category::getModelName(),
            Comment::getModelName(),
            CourseContributor::getModelName(),
            CourseEnrollUser::getModelName(),
            CurrencyExchange::getModelName(),
            Exercise::getModelName(),
            Forum::getModelName(),
            JobPost::getModelName(),
            Lesson::getModelName(),
            Question::getModelName(),
            SubCategory::getModelName(),
            User::getModelName()];

        $teacherAdminIds = User::whereIn('role', [UserRoleEnums::TEACHER->value, UserRoleEnums::ADMIN->value])->pluck('id')->toArray();

        return [
            'table_name' => fake()->randomElement($tableNames),
            'ip_address' => fake()->ipv4,
            'user_agent' => fake()->userAgent,
            'user_id' => fake()->randomElement($teacherAdminIds),
            'short' => fake()->randomElement(['Course','Lesson', 'Exercise', 'Question', 'Enroll']). ' '. rand(1,30).' is '.fake()->randomElement(['updated','created','deleted']),
            'about' => fake()->sentence,
            'target' => fake()->randomElement([UserRoleEnums::ADMIN->value,null]),
            'route_name' => '#',
            'created_at' => fake()->dateTimeBetween('-1 year', 'now'),
            'updated_at' => fake()->dateTimeBetween('-1 year', 'now'),
        ];
    }
}

<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\CourseContributor;
use App\Models\Lesson;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class LessonFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Lesson::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence,
            'body' => $this->faker->paragraph,
            'creator_id' => function (array $attributes) {
                // Retrieve the course creator ID and the contributor (approver) ID
                $course = Course::find($attributes['course_id']);
                $contributor = CourseContributor::where('course_id', $attributes['course_id'])->first();

                // Choose the creator ID based on the availability of the contributor and the course creator
                $creatorId = $contributor ? $contributor->user_id : ($course ? $course->createdUser_id : null);

                return $creatorId;
            },
            'course_id' => fake()->numberBetween(1,20), // You can adjust this according to your database schema
        ];
    }
}

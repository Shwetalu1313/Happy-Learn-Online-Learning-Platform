<?php

namespace Database\Factories;

use App\Enums\QuestionTypeEnums;
use App\Models\Exercise;
use App\Models\Question;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Answer>
 */
class AnswerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'question_id' => Question::factory(),
            'text' => $this->faker->sentence,
            'is_correct' => $this->faker->boolean(50), // 50% chance of being correct
        ];
    }
}

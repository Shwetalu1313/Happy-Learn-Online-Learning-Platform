<?php

namespace Database\Factories;

use App\Enums\QuestionTypeEnums;
use App\Models\Exercise;
use App\Models\Question;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Question>
 */
class QuestionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'exercise_id' => Exercise::factory(),
            'text' => $this->faker->sentence,
            'question_type' => $this->faker->randomElement([QuestionTypeEnums::MULTIPLE_CHOICE->value, QuestionTypeEnums::BLANK->value, QuestionTypeEnums::TRUEorFALSE->value]),
        ];
    }
}

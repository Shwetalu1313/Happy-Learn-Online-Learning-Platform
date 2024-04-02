<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Exercise>
 */
class ExerciseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        return [
            'content' => fake()->randomElement(['Answer the following question\.','Challenging Questions are here.', 'Prove that you are smart enough.'] ),
            'lesson_id' => rand(1,40),
        ];
    }
}

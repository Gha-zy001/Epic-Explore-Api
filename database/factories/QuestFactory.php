<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Quest>
 */
class QuestFactory extends Factory
{
    protected $model = \App\Models\Quest::class;

    public function definition(): array
    {
        return [
            'title' => fake()->unique()->words(3, true),
            'description' => fake()->sentence(),
            'reward_xp' => fake()->numberBetween(50, 500),
            'requirement_type' => 'visits',
            'requirement_count' => fake()->numberBetween(1, 10),
        ];
    }
}

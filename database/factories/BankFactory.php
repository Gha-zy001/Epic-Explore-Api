<?php

namespace Database\Factories;

use App\Models\State;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Bank>
 */
class BankFactory extends Factory
{
    protected $model = \App\Models\Bank::class;

    public function definition(): array
    {
        return [
            'state_id' => State::factory(),
            'name' => fake()->company() . ' Bank',
            'rate' => fake()->randomFloat(2, 1, 5),
            'location' => fake()->address(),
        ];
    }
}

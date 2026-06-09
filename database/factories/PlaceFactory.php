<?php

namespace Database\Factories;

use App\Models\State;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Place>
 */
class PlaceFactory extends Factory
{
    protected $model = \App\Models\Place::class;

    public function definition(): array
    {
        return [
            'state_id' => State::factory(),
            'name' => fake()->unique()->company() . ' ' . fake()->word(),
            'description' => fake()->paragraph(),
            'address' => fake()->streetAddress() . ', ' . fake()->city(),
        ];
    }
}

<?php

namespace Database\Factories;

use App\Models\State;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Restaurant>
 */
class RestaurantFactory extends Factory
{
    protected $model = \App\Models\Restaurant::class;

    public function definition(): array
    {
        return [
            'state_id' => State::factory(),
            'name' => fake()->company() . ' Restaurant',
            'rate' => fake()->randomFloat(1, 1, 5),
            'address' => fake()->streetAddress() . ', ' . fake()->city(),
        ];
    }
}

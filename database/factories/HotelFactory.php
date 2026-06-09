<?php

namespace Database\Factories;

use App\Models\State;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Hotel>
 */
class HotelFactory extends Factory
{
    protected $model = \App\Models\Hotel::class;

    public function definition(): array
    {
        return [
            'state_id' => State::factory(),
            'name' => fake()->company() . ' Hotel',
            'address' => fake()->streetAddress() . ', ' . fake()->city(),
            'price' => fake()->randomFloat(2, 50, 1000),
            'rate' => fake()->randomFloat(1, 1, 5),
        ];
    }
}

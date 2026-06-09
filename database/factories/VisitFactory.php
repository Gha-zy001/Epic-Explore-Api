<?php

namespace Database\Factories;

use App\Models\Place;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Visit>
 */
class VisitFactory extends Factory
{
    protected $model = \App\Models\Visit::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'place_id' => Place::factory(),
            'latitude' => fake()->latitude(),
            'longitude' => fake()->longitude(),
            'points_awarded' => 50,
        ];
    }
}

<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class MovieFactory extends Factory
{
    public function definition(): array
    {
        return [
            'tmdb_id' => $this->faker->unique()->randomNumber(6),
            'title' => $this->faker->sentence(3),
            'director' => $this->faker->name(),
            'poster_url' => $this->faker->imageUrl(),
            'teaser_url' => $this->faker->url(),
            'duration_minutes' => $this->faker->numberBetween(90, 180),
            'status' => $this->faker->randomElement(['coming_soon', 'showing', 'stopped']),
        ];
    }
}

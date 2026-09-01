<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class SnackFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(2, true),
            'price' => $this->faker->randomElement([20000, 35000, 50000, 65000, 80000]),
            'image_url' => $this->faker->imageUrl(),
            'is_active' => $this->faker->boolean(90),
        ];
    }
}

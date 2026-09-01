<?php

namespace Database\Factories;

use App\Models\Movie;
use App\Models\Room;
use Illuminate\Database\Eloquent\Factories\Factory;

class ShowFactory extends Factory
{
    public function definition(): array
    {
        $startTime = $this->faker->dateTimeBetween('now', '+1 month');
        $endTime = (clone $startTime)->modify('+2 hours');

        return [
            'movie_id' => Movie::factory(),
            'room_id' => Room::factory(),
            'start_time' => $startTime,
            'end_time' => $endTime,
            'price' => $this->faker->randomElement([50000, 60000, 70000, 80000, 90000, 100000]),
        ];
    }
}

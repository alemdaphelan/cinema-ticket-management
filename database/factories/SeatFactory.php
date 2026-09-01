<?php

namespace Database\Factories;

use App\Models\Room;
use Illuminate\Database\Eloquent\Factories\Factory;

class SeatFactory extends Factory
{
    public function definition(): array
    {
        return [
            'room_id' => Room::factory(),
            'row_label' => $this->faker->randomElement(['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J']),
            'seat_number' => $this->faker->numberBetween(1, 20),
            'type' => $this->faker->randomElement(['normal', 'vip']),
        ];
    }
}

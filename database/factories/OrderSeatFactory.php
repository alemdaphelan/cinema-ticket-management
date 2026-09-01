<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Seat;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderSeatFactory extends Factory
{
    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'seat_id' => Seat::factory(),
            'price' => $this->faker->randomElement([50000, 60000, 70000]),
        ];
    }
}

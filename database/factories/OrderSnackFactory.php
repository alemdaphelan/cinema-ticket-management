<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Snack;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderSnackFactory extends Factory
{
    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'snack_id' => Snack::factory(),
            'quantity' => $this->faker->numberBetween(1, 5),
            'price' => $this->faker->randomElement([20000, 35000, 50000]),
        ];
    }
}

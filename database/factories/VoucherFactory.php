<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class VoucherFactory extends Factory
{
    public function definition(): array
    {
        return [
            'code' => $this->faker->unique()->lexify('VOUCHER?????'),
            'discount_value' => $this->faker->numberBetween(10000, 50000),
            'discount_type' => $this->faker->randomElement(['percent', 'fixed']),
            'min_order_value' => $this->faker->numberBetween(100000, 200000),
            'max_uses_per_user' => $this->faker->numberBetween(1, 5),
            'valid_from' => now(),
            'valid_to' => $this->faker->dateTimeBetween('+1 week', '+1 month'),
            'is_active' => $this->faker->boolean(80),
        ];
    }
}

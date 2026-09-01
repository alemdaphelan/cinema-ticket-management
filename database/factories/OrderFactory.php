<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Show;
use App\Models\Voucher;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'show_id' => Show::factory(),
            'voucher_id' => $this->faker->boolean(20) ? Voucher::factory() : null,
            'total_amount' => $this->faker->numberBetween(100000, 500000),
            'status' => $this->faker->randomElement(['pending', 'paid', 'completed', 'cancelled']),
            'payment_method' => $this->faker->randomElement(['vnpay', 'momo', 'cash']),
            'qr_code' => $this->faker->uuid(),
            'hold_expires_at' => $this->faker->dateTimeBetween('now', '+10 minutes'),
        ];
    }
}

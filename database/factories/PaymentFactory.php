<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Payment;
use App\Models\Booking;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'booking_id' => Booking::inRandomOrder()->first()?->id ?? Booking::factory(),
            'amount' => fake()->randomFloat(2, 100, 1000),
            'payment_method' => fake()->randomElement(['card', 'cash', 'bank_transfer', 'online']),
            'status' => 'completed',
            'transaction_id' => fake()->uuid(),
            'created_at' => fake()->dateTimeBetween('-2 months', 'now'),
        ];
    }
}

<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Booking;
use App\Models\User;
use App\Models\Room;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Booking>
 */
class BookingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $checkIn = fake()->dateTimeBetween('-1 month', '+1 month');
        return [
            'user_id' => User::factory(),
            'room_id' => Room::inRandomOrder()->first()?->id ?? Room::factory(),
            'check_in_date' => $checkIn,
            'check_out_date' => (clone $checkIn)->modify('+' . rand(1, 5) . ' days'),
            'total_amount' => fake()->randomFloat(2, 100, 1000),
            'status' => fake()->randomElement(['pending', 'confirmed', 'checked_in', 'checked_out', 'cancelled']),
            'adults' => fake()->numberBetween(1, 3),
            'children' => fake()->numberBetween(0, 2),
            'special_requests' => fake()->optional()->sentence(),
        ];
    }
}

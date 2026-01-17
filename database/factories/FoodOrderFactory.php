<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\FoodOrder;
use App\Models\Food;
use App\Models\Booking;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FoodOrder>
 */
class FoodOrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $booking = Booking::inRandomOrder()->first();
        $user = $booking ? $booking->user : User::factory()->create();
        
        $food = Food::inRandomOrder()->first() ?? Food::factory()->create();
        $quantity = fake()->numberBetween(1, 5);

        return [
            'food_id' => $food->id,
            'booking_id' => $booking?->id,
            'user_id' => $user->id,
            'guest_name' => $user->name,
            'guest_email' => $user->email,
            'guest_phone' => $user->phone ?? fake()->phoneNumber(),
            'quantity' => $quantity,
            'total_price' => $food->price * $quantity,
            'status' => fake()->randomElement(['pending', 'preparing', 'ready', 'delivered', 'cancelled']),
            'order_time' => fake()->dateTimeBetween('-2 days', 'now'),
            'kot_number' => 'KOT-' . date('Y') . '-' . fake()->unique()->numerify('######'),
            'special_instructions' => fake()->optional(0.3)->sentence(),
            'order_type' => fake()->randomElement(['room_service', 'restaurant']),
        ];
    }
}

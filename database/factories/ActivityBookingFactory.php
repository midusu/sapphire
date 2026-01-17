<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\ActivityBooking;
use App\Models\Activity;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ActivityBooking>
 */
class ActivityBookingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'activity_id' => Activity::inRandomOrder()->first()?->id ?? Activity::factory(),
            'user_id' => User::where('role_id', \App\Models\Role::where('slug', 'guest')->value('id') ?? 7)->inRandomOrder()->first()?->id ?? User::factory(),
            'scheduled_time' => fake()->dateTimeBetween('-1 month', '+1 month'),
            'participants' => fake()->numberBetween(1, 4),
            'status' => fake()->randomElement(['pending', 'confirmed', 'completed', 'cancelled']),
            'total_price' => fake()->randomFloat(2, 50, 300),
        ];
    }
}

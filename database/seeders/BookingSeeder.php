<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Booking;
use App\Models\User;
use App\Models\Room;

class BookingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get guests
        $guestRole = \App\Models\Role::where('slug', 'guest')->first();
        $guests = User::where('role_id', $guestRole->id)->get();
        
        if ($guests->count() === 0) {
            // Should be covered by GuestSeeder, but failsafe
            return;
        }

        // Create past bookings (Checked Out)
        Booking::factory()->count(50)->create([
            'status' => 'checked_out',
            'user_id' => fn() => $guests->random()->id,
            'room_id' => fn() => Room::inRandomOrder()->first()->id,
            'check_in_date' => fake()->dateTimeBetween('-3 months', '-1 week'),
            'check_out_date' => function (array $attributes) {
                return \Carbon\Carbon::parse($attributes['check_in_date'])->addDays(rand(1, 7));
            },
        ]);

        // Create current bookings (Checked In)
        Booking::factory()->count(10)->create([
            'status' => 'checked_in',
            'user_id' => fn() => $guests->random()->id,
            'room_id' => fn() => Room::inRandomOrder()->first()->id,
            'check_in_date' => fake()->dateTimeBetween('-5 days', 'now'),
            'check_out_date' => fake()->dateTimeBetween('+1 days', '+7 days'),
        ]);

        // Create future bookings (Confirmed)
        Booking::factory()->count(15)->create([
            'status' => 'confirmed',
            'user_id' => fn() => $guests->random()->id,
            'room_id' => fn() => Room::inRandomOrder()->first()->id,
            'check_in_date' => fake()->dateTimeBetween('+1 days', '+1 month'),
            'check_out_date' => function (array $attributes) {
                return \Carbon\Carbon::parse($attributes['check_in_date'])->addDays(rand(1, 7));
            },
        ]);
    }
}

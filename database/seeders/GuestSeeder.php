<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;

class GuestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $guestRole = Role::where('slug', 'guest')->first();

        // Create 50 guests
        User::factory()->count(50)->create([
            'role_id' => $guestRole->id,
        ])->each(function ($user) {
            // Add some realistic data occasionally
            if (rand(0, 1)) {
                $user->update([
                    'phone' => fake()->phoneNumber(),
                    'address' => fake()->address(),
                    'nationality' => fake()->country(),
                    'date_of_birth' => fake()->date('Y-m-d', '-18 years'),
                ]);
            }
        });
    }
}

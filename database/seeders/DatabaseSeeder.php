<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            RoomTypeSeeder::class,
            ActivitySeeder::class,
            RoomSeeder::class,
            GuestSeeder::class,
            BookingSeeder::class,
            ActivityBookingSeeder::class,
            PaymentSeeder::class,
            FoodSeeder::class,
            FoodOrderSeeder::class,
        ]);

        // Create admin user
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@sapphire.com',
            'password' => bcrypt('password'),
            'role_id' => Role::where('slug', 'admin')->first()->id,
        ]);

        // Create test user
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'user@sapphire.com',
            'password' => bcrypt('password'),
            'role_id' => Role::where('slug', 'guest')->first()->id,
        ]);
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\FoodOrder;
use App\Models\Booking;

class FoodOrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure there are bookings to attach orders to
        if (Booking::count() > 0) {
            // Create pending/preparing orders (Active Kitchen)
            FoodOrder::factory()->count(10)->create([
                'status' => 'pending',
                'order_time' => now()->subMinutes(rand(1, 30)),
            ]);

            FoodOrder::factory()->count(5)->create([
                'status' => 'preparing',
                'order_time' => now()->subMinutes(rand(10, 45)),
            ]);

            // Create ready orders
            FoodOrder::factory()->count(5)->create([
                'status' => 'ready',
                'order_time' => now()->subMinutes(rand(20, 60)),
                'kitchen_completed_time' => now(),
            ]);

            // Create past orders
            FoodOrder::factory()->count(30)->create([
                'status' => 'delivered',
                'order_time' => fake()->dateTimeBetween('-7 days', '-2 hours'),
                'kitchen_completed_time' => function ($attributes) {
                    return \Carbon\Carbon::parse($attributes['order_time'])->addMinutes(rand(15, 45));
                },
            ]);
        }
    }
}

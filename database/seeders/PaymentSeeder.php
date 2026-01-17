<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Payment;
use App\Models\Booking;

class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Add payments for existing bookings
        $bookings = Booking::whereIn('status', ['checked_in', 'checked_out', 'confirmed'])->get();

        foreach ($bookings as $booking) {
            Payment::factory()->create([
                'booking_id' => $booking->id,
                'amount' => $booking->total_amount ?? rand(100, 500),
                'status' => 'completed',
                'created_at' => $booking->created_at, // Use booking time or random
                'updated_at' => $booking->created_at,
            ]);
        }
    }
}

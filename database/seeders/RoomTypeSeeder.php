<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\RoomType;

class RoomTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roomTypes = [
            [
                'name' => 'Standard Room',
                'description' => 'Comfortable room with basic amenities',
                'base_price' => 99.99,
                'max_occupancy' => 2,
                'amenities' => json_encode(['AC', 'TV', 'WiFi', 'Mini Bar']),
                'image_url' => 'images/rooms/standard.jpg'
            ],
            [
                'name' => 'Deluxe Room',
                'description' => 'Spacious room with premium amenities',
                'base_price' => 149.99,
                'max_occupancy' => 3,
                'amenities' => json_encode(['AC', 'TV', 'WiFi', 'Mini Bar', 'Balcony', 'Safe']),
                'image_url' => 'images/rooms/deluxe.jpg'
            ],
            [
                'name' => 'Suite',
                'description' => 'Luxury suite with separate living area',
                'base_price' => 249.99,
                'max_occupancy' => 4,
                'amenities' => json_encode(['AC', 'TV', 'WiFi', 'Mini Bar', 'Balcony', 'Safe', 'Living Room', 'Kitchenette']),
                'image_url' => 'images/rooms/suite.jpg'
            ],
            [
                'name' => 'Presidential Suite',
                'description' => 'Ultimate luxury experience',
                'base_price' => 499.99,
                'max_occupancy' => 6,
                'amenities' => json_encode(['AC', 'TV', 'WiFi', 'Mini Bar', 'Balcony', 'Safe', 'Living Room', 'Kitchen', 'Jacuzzi', 'Butler Service']),
                'image_url' => 'images/rooms/presidential.jpg'
            ],
        ];

        foreach ($roomTypes as $roomType) {
            RoomType::create($roomType);
        }
    }
}

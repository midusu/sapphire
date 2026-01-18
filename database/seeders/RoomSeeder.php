<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Room;
use App\Models\RoomType;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roomTypes = RoomType::all();
        $roomCounter = 1;

        // Create sample rooms for each room type
        foreach ($roomTypes as $type) {
            $roomCount = match($type->name) {
                'Standard Room' => 10,
                'Deluxe Room' => 8,
                'Suite' => 5,
                'Presidential Suite' => 2,
                default => 5,
            };

            for ($i = 1; $i <= $roomCount; $i++) {
                $floor = match($type->name) {
                    'Standard Room' => rand(1, 3),
                    'Deluxe Room' => rand(2, 4),
                    'Suite' => rand(3, 5),
                    'Presidential Suite' => 5,
                    default => 1,
                };

                $roomNumber = str_pad($roomCounter++, 4, '0', STR_PAD_LEFT);
                if (Room::where('room_number', $roomNumber)->exists()) {
                    continue;
                }

                Room::create([
                    'room_number' => $roomNumber,
                    'room_type_id' => $type->id,
                    'status' => 'available',
                    'floor' => $floor,
                    'notes' => null,
                ]);
            }
        }
    }
}

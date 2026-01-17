<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Activity;

class ActivitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $activities = [
            [
                'name' => 'Zipline Adventure',
                'description' => 'Experience the thrill of ziplining through our scenic course',
                'type' => 'zipline',
                'price' => 49.99,
                'max_participants' => 10,
                'duration' => '02:00:00',
                'requirements' => json_encode(['Min weight: 40kg', 'Max weight: 120kg', 'Age: 12+', 'Comfortable clothing']),
                'is_active' => true
            ],
            [
                'name' => 'Swimming Pool Access',
                'description' => 'Full day access to our Olympic-size swimming pool',
                'type' => 'swimming',
                'price' => 19.99,
                'max_participants' => 50,
                'duration' => '04:00:00',
                'requirements' => json_encode(['Swimwear required', 'Towel provided', 'Age: 5+']),
                'is_active' => true
            ],
            [
                'name' => 'Night Zipline',
                'description' => 'Experience ziplining under the stars with LED lights',
                'type' => 'zipline',
                'price' => 69.99,
                'max_participants' => 8,
                'duration' => '02:30:00',
                'requirements' => json_encode(['Min weight: 40kg', 'Max weight: 120kg', 'Age: 16+', 'Comfortable clothing']),
                'is_active' => true
            ],
            [
                'name' => 'Swimming Lessons',
                'description' => 'Professional swimming lessons for all levels',
                'type' => 'swimming',
                'price' => 39.99,
                'max_participants' => 6,
                'duration' => '01:00:00',
                'requirements' => json_encode(['Swimwear required', 'Towel provided', 'All ages welcome']),
                'is_active' => true
            ],
        ];

        foreach ($activities as $activity) {
            Activity::create($activity);
        }
    }
}

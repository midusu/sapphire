<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            ['name' => 'Admin', 'slug' => 'admin', 'description' => 'System administrator with full access'],
            ['name' => 'Manager', 'slug' => 'manager', 'description' => 'Hotel manager with operational control'],
            ['name' => 'Receptionist', 'slug' => 'receptionist', 'description' => 'Front desk staff for check-in/out'],
            ['name' => 'Accountant', 'slug' => 'accountant', 'description' => 'Financial management and billing'],
            ['name' => 'Housekeeping', 'slug' => 'housekeeping', 'description' => 'Room cleaning and maintenance'],
            ['name' => 'Activity Staff', 'slug' => 'activity-staff', 'description' => 'Zipline and swimming pool activities'],
            ['name' => 'Guest', 'slug' => 'guest', 'description' => 'Hotel guests with limited access'],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}

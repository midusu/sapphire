<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminRoomAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_admin_can_access_rooms_page()
    {
        // Find or create admin user
        $adminRole = Role::where('slug', 'admin')->first();
        if (!$adminRole) {
            $this->fail('Admin role not found in database');
        }

        $admin = User::where('role_id', $adminRole->id)->first();
        if (!$admin) {
            $admin = User::factory()->create([
                'role_id' => $adminRole->id,
                'email' => 'admin_test_access@example.com'
            ]);
        }

        $response = $this->actingAs($admin)->get('/admin/rooms');
        
        if ($response->status() !== 200) {
            dump($response->getContent());
        }

        $response->assertStatus(200);
        $response->assertViewIs('admin.rooms.index');
        $response->assertSee('Room Management');
        $response->assertSee('Total Rooms');
    }
}

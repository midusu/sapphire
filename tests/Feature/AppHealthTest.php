<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppHealthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_public_routes_are_accessible()
    {
        $routes = [
            '/',
            '/booking/rooms',
            '/gallery',
            '/amenities',
            '/contact',
            '/login',
            '/register',
        ];

        foreach ($routes as $route) {
            $response = $this->get($route);
            $response->assertStatus(200);
        }
    }

    public function test_admin_dashboard_is_accessible()
    {
        $admin = User::where('email', 'admin@sapphire.com')->first();
        
        $response = $this->actingAs($admin)->get('/admin/dashboard');
        $response->assertStatus(200);
        
        $response = $this->actingAs($admin)->get('/admin/bookings');
        $response->assertStatus(200);

        $response = $this->actingAs($admin)->get('/admin/payments');
        $response->assertStatus(200);
    }

    public function test_guest_dashboard_is_accessible()
    {
        $guest = User::where('email', 'user@sapphire.com')->first();
        
        $response = $this->actingAs($guest)->get('/guest/dashboard');
        $response->assertStatus(200);
        
        $response = $this->actingAs($guest)->get('/dashboard');
        $response->assertRedirect('/guest/dashboard');
    }

    public function test_guest_can_view_food_menu()
    {
        $guest = User::where('email', 'user@sapphire.com')->first();
        
        $response = $this->actingAs($guest)->get('/guest/food-menu');
        $response->assertStatus(200);
    }
}

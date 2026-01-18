<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminGuestLoyaltyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_admin_can_override_guest_loyalty_status()
    {
        // Find or create admin user
        $adminRole = Role::where('slug', 'admin')->first();
        $admin = User::where('role_id', $adminRole->id)->first();
        if (!$admin) {
            $admin = User::factory()->create([
                'role_id' => $adminRole->id,
                'email' => 'admin_loyalty@example.com'
            ]);
        }

        // Create a guest
        $guestRole = Role::where('slug', 'guest')->first();
        $guest = User::factory()->create([
            'role_id' => $guestRole->id,
            'email' => 'guest_loyalty@example.com'
        ]);

        // Default status should be bronze (0 bookings, 0 spent)
        $this->assertEquals('bronze', $guest->getLoyaltyStatus());

        // Override loyalty status to Platinum
        $response = $this->actingAs($admin)->put(route('admin.guests.update', $guest), [
            'name' => $guest->name,
            'email' => $guest->email,
            'loyalty_level_override' => 'platinum'
        ]);

        $response->assertRedirect(route('admin.guests.show', $guest));
        
        $guest->refresh();
        $this->assertEquals('platinum', $guest->loyalty_level_override);
        $this->assertEquals('platinum', $guest->getLoyaltyStatus());

        // Override loyalty status to Gold
        $response = $this->actingAs($admin)->put(route('admin.guests.update', $guest), [
            'name' => $guest->name,
            'email' => $guest->email,
            'loyalty_level_override' => 'gold'
        ]);

        $guest->refresh();
        $this->assertEquals('gold', $guest->loyalty_level_override);
        $this->assertEquals('gold', $guest->getLoyaltyStatus());
        
        // Remove override (Auto-Calculate)
        $response = $this->actingAs($admin)->put(route('admin.guests.update', $guest), [
            'name' => $guest->name,
            'email' => $guest->email,
            'loyalty_level_override' => null
        ]);

        $guest->refresh();
        $this->assertNull($guest->loyalty_level_override);
        $this->assertEquals('bronze', $guest->getLoyaltyStatus());
    }
}

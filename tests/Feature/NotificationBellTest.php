<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\AccountDeactivated;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class NotificationBellTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
    }

    public function test_practitioner_dashboard_shows_notification_bell(): void
    {
        $user = $this->practitioner();
        $user->notify(new AccountDeactivated());

        $this->actingAs($user)->get('/en/practitioner/dashboard')
            ->assertOk()
            ->assertSee('notification-bell');
    }

    public function test_customer_dashboard_shows_notification_bell(): void
    {
        $user = User::factory()->create();
        $user->assignRole('customer');
        $user->customerProfile()->create(['country' => 'CM']);

        $this->actingAs($user)->get('/en/customer/dashboard')
            ->assertOk()
            ->assertSee('notification-bell');
    }

    private function practitioner(): User
    {
        $user = User::factory()->create();
        $user->assignRole('practitioner');
        $user->practitionerProfile()->create(['profession' => 'doctor', 'workplace_country' => 'CM']);
        return $user;
    }
}

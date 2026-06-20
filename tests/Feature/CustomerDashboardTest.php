<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class CustomerDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
    }

    public function test_customer_dashboard_loads_and_counts_active_licenses(): void
    {
        $user = User::factory()->create();
        $user->assignRole('customer');
        $user->customerProfile()->create(['country' => 'CM']);

        \App\Models\License::create([
            'user_id'      => $user->id,
            'issued_by'    => $user->id,
            'product_slug' => 'ohos',
            'product_name' => 'OPES Health OS',
            'license_key'  => 'LIC-TEST-0001',
            'status'       => 'active',
            'start_date'   => now()->toDateString(),
            'end_date'     => now()->addYear()->toDateString(),
        ]);

        // Regression: dashboard queried licenses.customer_id (non-existent column).
        // On MySQL that 500s; on SQLite it silently returns 0. Asserting the count is 1
        // catches both — the active-license stat must reflect the user's own licenses.
        $this->actingAs($user)->get('/en/customer/dashboard')
            ->assertOk()
            ->assertViewHas('activeLicenses', 1);
    }
}

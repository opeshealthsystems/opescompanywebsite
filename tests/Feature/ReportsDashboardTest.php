<?php

namespace Tests\Feature;

use App\Models\Invoice;
use App\Models\License;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class ReportsDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
    }

    public function test_view_reports_permission_exists(): void
    {
        $this->assertDatabaseHas('permissions', ['name' => 'view_reports']);
    }

    public function test_admin_has_view_reports_permission(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $this->assertTrue($admin->hasPermissionTo('view_reports'));
    }

    public function test_admin_can_access_reports_dashboard(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin)
            ->get('/admin/reports-dashboard')
            ->assertOk();
    }

    public function test_customer_cannot_access_reports_dashboard(): void
    {
        $customer = User::factory()->create();
        $customer->assignRole('customer');

        $this->actingAs($customer)
            ->get('/admin/reports-dashboard')
            ->assertForbidden();
    }

    public function test_reports_dashboard_shows_customer_count(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $customer = User::factory()->create();
        $customer->assignRole('customer');

        $this->actingAs($admin)
            ->get('/admin/reports-dashboard')
            ->assertOk()
            ->assertSee('Customers');
    }

    public function test_reports_dashboard_shows_ticket_metrics(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $customer = User::factory()->create();
        $customer->assignRole('customer');

        Ticket::create([
            'user_id'     => $customer->id,
            'subject'     => 'Test ticket',
            'description' => 'Test',
            'type'        => 'support',
            'status'      => 'open',
            'priority'    => 'medium',
        ]);

        $this->actingAs($admin)
            ->get('/admin/reports-dashboard')
            ->assertOk()
            ->assertSee('Tickets');
    }
}

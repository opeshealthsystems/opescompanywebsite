<?php
namespace Tests\Feature;

use App\Models\Ticket;
use App\Models\TesterAssignment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class TesterPortalEnhancedTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
    }

    private function testerUser(): User
    {
        $user = User::factory()->create();
        $user->assignRole('tester');
        return $user;
    }

    public function test_dashboard_shows_kpi_counts(): void
    {
        $user = $this->testerUser();

        TesterAssignment::create([
            'assigned_to'  => $user->id,
            'assigned_by'  => null,
            'product_slug' => 'opes-clinic',
            'product_name' => 'OPES Clinic',
            'title'        => 'Test login flow',
            'description'  => 'Verify login works',
            'status'       => 'completed',
        ]);
        TesterAssignment::create([
            'assigned_to'  => $user->id,
            'assigned_by'  => null,
            'product_slug' => 'opes-hospital',
            'product_name' => 'OPES Hospital',
            'title'        => 'Test dashboard',
            'description'  => 'Verify dashboard loads',
            'status'       => 'in_progress',
        ]);
        Ticket::create([
            'user_id'     => $user->id,
            'subject'     => 'Bug in login',
            'description' => 'Login fails',
            'type'        => 'bug_report',
            'status'      => 'open',
            'priority'    => 'medium',
        ]);

        $this->actingAs($user)
            ->get(route('tester.dashboard', ['locale' => 'en']))
            ->assertOk()
            ->assertViewHas('totalAssigned', 2)
            ->assertViewHas('completedCount', 1)
            ->assertViewHas('activeCount', 1)
            ->assertViewHas('bugReportsCount', 1);
    }

    public function test_dashboard_is_blocked_for_non_tester(): void
    {
        $customer = User::factory()->create();
        $customer->assignRole('customer');

        $this->actingAs($customer)
            ->get(route('tester.dashboard', ['locale' => 'en']))
            ->assertForbidden();
    }
}

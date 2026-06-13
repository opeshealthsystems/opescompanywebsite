<?php

namespace Tests\Feature;

use App\Models\Ticket;
use App\Models\TesterAssignment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class TesterAssignmentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
    }

    public function test_tester_assignments_table_exists(): void
    {
        $this->assertTrue(Schema::hasTable('tester_assignments'));
    }

    public function test_tickets_table_has_tester_assignment_id_column(): void
    {
        $this->assertTrue(Schema::hasColumn('tickets', 'tester_assignment_id'));
    }

    public function test_assignment_can_be_created(): void
    {
        $admin  = User::factory()->create();
        $admin->assignRole('admin');
        $tester = User::factory()->create();
        $tester->assignRole('tester');

        $assignment = TesterAssignment::create([
            'assigned_to'  => $tester->id,
            'assigned_by'  => $admin->id,
            'product_slug' => 'opescare',
            'product_name' => 'OPESCare',
            'title'        => 'Test patient registration flow',
            'description'  => 'Verify that new patients can be registered without errors.',
            'status'       => 'pending',
            'due_date'     => now()->addWeek()->toDateString(),
        ]);

        $this->assertDatabaseHas('tester_assignments', [
            'title'        => 'Test patient registration flow',
            'status'       => 'pending',
            'assigned_to'  => $tester->id,
        ]);

        $this->assertEquals($tester->id, $assignment->tester->id);
        $this->assertEquals($admin->id, $assignment->assigner->id);
    }

    public function test_assign_testers_permission_exists(): void
    {
        $this->assertDatabaseHas('permissions', ['name' => 'assign_testers']);
    }

    public function test_admin_has_assign_testers_permission(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $this->assertTrue($admin->hasPermissionTo('assign_testers'));
    }

    public function test_tester_has_view_tester_dashboard_permission(): void
    {
        $tester = User::factory()->create();
        $tester->assignRole('tester');
        $this->assertTrue($tester->hasPermissionTo('view_tester_dashboard'));
    }

    public function test_tester_can_view_their_assignments(): void
    {
        $admin  = User::factory()->create();
        $admin->assignRole('admin');
        $tester = User::factory()->create();
        $tester->assignRole('tester');

        TesterAssignment::create([
            'assigned_to'  => $tester->id,
            'assigned_by'  => $admin->id,
            'product_slug' => 'opescare',
            'product_name' => 'OPESCare',
            'title'        => 'Smoke test login',
            'description'  => 'Test the login flow.',
            'status'       => 'pending',
            'due_date'     => now()->addWeek()->toDateString(),
        ]);

        $this->actingAs($tester)
            ->get('/en/tester/assignments')
            ->assertOk()
            ->assertSee('Smoke test login');
    }

    public function test_tester_cannot_view_another_testers_assignment(): void
    {
        $admin   = User::factory()->create();
        $admin->assignRole('admin');
        $tester1 = User::factory()->create();
        $tester1->assignRole('tester');
        $tester2 = User::factory()->create();
        $tester2->assignRole('tester');

        $assignment = TesterAssignment::create([
            'assigned_to'  => $tester1->id,
            'assigned_by'  => $admin->id,
            'product_slug' => 'opescare',
            'product_name' => 'OPESCare',
            'title'        => 'Private test',
            'description'  => 'Only tester1 should see this.',
            'status'       => 'pending',
            'due_date'     => now()->addWeek()->toDateString(),
        ]);

        $this->actingAs($tester2)
            ->get('/en/tester/assignments/' . $assignment->id)
            ->assertForbidden();
    }

    public function test_tester_can_update_assignment_status(): void
    {
        $admin  = User::factory()->create();
        $admin->assignRole('admin');
        $tester = User::factory()->create();
        $tester->assignRole('tester');

        $assignment = TesterAssignment::create([
            'assigned_to'  => $tester->id,
            'assigned_by'  => $admin->id,
            'product_slug' => 'opescare',
            'product_name' => 'OPESCare',
            'title'        => 'Test flow',
            'description'  => 'Test it.',
            'status'       => 'pending',
            'due_date'     => now()->addWeek()->toDateString(),
        ]);

        $this->actingAs($tester)
            ->patch('/en/tester/assignments/' . $assignment->id . '/status', ['status' => 'in_progress'])
            ->assertRedirect();

        $this->assertDatabaseHas('tester_assignments', [
            'id'     => $assignment->id,
            'status' => 'in_progress',
        ]);
    }

    public function test_bug_report_can_be_filed_linked_to_assignment(): void
    {
        $admin  = User::factory()->create();
        $admin->assignRole('admin');
        $tester = User::factory()->create();
        $tester->assignRole('tester');

        $assignment = TesterAssignment::create([
            'assigned_to'  => $tester->id,
            'assigned_by'  => $admin->id,
            'product_slug' => 'opescare',
            'product_name' => 'OPESCare',
            'title'        => 'Test patient flow',
            'description'  => 'Test.',
            'status'       => 'in_progress',
            'due_date'     => now()->addWeek()->toDateString(),
        ]);

        $this->actingAs($tester)
            ->post('/en/tester/assignments/' . $assignment->id . '/bug-reports', [
                'subject'     => 'Registration fails with special characters',
                'description' => 'Steps to reproduce: enter special chars in name field.',
                'priority'    => 'high',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('tickets', [
            'type'                  => 'bug_report',
            'tester_assignment_id'  => $assignment->id,
            'user_id'               => $tester->id,
            'priority'              => 'high',
        ]);
    }
}

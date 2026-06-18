<?php

namespace Tests\Feature;

use App\Models\TesterAssignment;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TesterAssignmentPolicyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    private function makeUser(string $role): User
    {
        $user = User::factory()->create();
        $user->assignRole($role);
        return $user;
    }

    private function makeAssignment(int $assignedTo): TesterAssignment
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        return TesterAssignment::create([
            'assigned_to'  => $assignedTo,
            'assigned_by'  => $admin->id,
            'product_slug' => 'opescare',
            'product_name' => 'OPESCare',
            'title'        => 'Policy test assignment',
            'description'  => 'Testing the policy.',
            'status'       => 'pending',
            'due_date'     => now()->addWeek()->toDateString(),
        ]);
    }

    public function test_assigned_tester_can_view_assignment(): void
    {
        $tester     = $this->makeUser('tester');
        $assignment = $this->makeAssignment($tester->id);
        $this->assertTrue($tester->can('view', $assignment));
    }

    public function test_other_tester_cannot_view_assignment(): void
    {
        $tester     = $this->makeUser('tester');
        $other      = $this->makeUser('tester');
        $assignment = $this->makeAssignment($tester->id);
        $this->assertFalse($other->can('view', $assignment));
    }

    public function test_admin_can_view_any_assignment(): void
    {
        $tester     = $this->makeUser('tester');
        $admin      = $this->makeUser('admin');
        $assignment = $this->makeAssignment($tester->id);
        $this->assertTrue($admin->can('view', $assignment));
    }

    public function test_assigned_tester_can_update_assignment(): void
    {
        $tester     = $this->makeUser('tester');
        $assignment = $this->makeAssignment($tester->id);
        $this->assertTrue($tester->can('update', $assignment));
    }

    public function test_other_tester_cannot_update_assignment(): void
    {
        $tester     = $this->makeUser('tester');
        $other      = $this->makeUser('tester');
        $assignment = $this->makeAssignment($tester->id);
        $this->assertFalse($other->can('update', $assignment));
    }

    public function test_assignment_show_uses_policy(): void
    {
        $tester     = $this->makeUser('tester');
        $other      = $this->makeUser('tester');
        $assignment = $this->makeAssignment($tester->id);

        $response = $this->actingAs($other)->get("/en/tester/assignments/{$assignment->id}");
        $response->assertStatus(403);
    }
}

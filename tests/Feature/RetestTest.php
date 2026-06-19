<?php

namespace Tests\Feature;

use App\Models\Cohort;
use App\Models\CohortMember;
use App\Models\DeveloperTask;
use App\Models\IssueReport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class RetestTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
    }

    /** Reporter + their ready_for_retest issue + a fixed task. */
    private function scenario(): array
    {
        $user = User::factory()->create();
        $user->assignRole('practitioner');
        $cohort = Cohort::factory()->create();
        $member = CohortMember::factory()->create(['cohort_id' => $cohort->id, 'user_id' => $user->id, 'status' => 'active']);
        $issue  = IssueReport::factory()->create(['cohort_member_id' => $member->id, 'status' => 'ready_for_retest']);
        $task   = DeveloperTask::factory()->create(['issue_report_id' => $issue->id, 'status' => 'fixed']);
        return compact('user', 'member', 'issue', 'task');
    }

    public function test_reporter_can_pass_retest(): void
    {
        ['user' => $user, 'issue' => $issue] = $this->scenario();

        $this->actingAs($user)
            ->post("/en/practitioner/validation/issues/{$issue->id}/retests", [
                'result' => 'passed', 'notes' => 'Confirmed fixed.',
            ])->assertRedirect();

        $this->assertEquals('retest_passed', $issue->fresh()->status);
        $this->assertDatabaseHas('retests', ['issue_report_id' => $issue->id, 'result' => 'passed']);
    }

    public function test_reporter_failed_retest_reopens_task(): void
    {
        ['user' => $user, 'issue' => $issue, 'task' => $task] = $this->scenario();

        $this->actingAs($user)
            ->post("/en/practitioner/validation/issues/{$issue->id}/retests", [
                'result' => 'failed', 'notes' => 'Still broken.',
            ])->assertRedirect();

        $this->assertEquals('retest_failed', $issue->fresh()->status);
        $this->assertEquals('reopened', $task->fresh()->status);
    }

    public function test_other_practitioner_cannot_retest(): void
    {
        ['issue' => $issue] = $this->scenario();
        $other = User::factory()->create();
        $other->assignRole('practitioner');

        $this->actingAs($other)
            ->post("/en/practitioner/validation/issues/{$issue->id}/retests", [
                'result' => 'passed', 'notes' => 'x',
            ])->assertForbidden();
    }

    public function test_cannot_retest_issue_not_ready(): void
    {
        ['user' => $user, 'member' => $member] = $this->scenario();
        $notReady = IssueReport::factory()->create(['cohort_member_id' => $member->id, 'status' => 'submitted']);

        $this->actingAs($user)
            ->post("/en/practitioner/validation/issues/{$notReady->id}/retests", [
                'result' => 'passed', 'notes' => 'x',
            ])->assertStatus(422);
    }

    public function test_non_practitioner_forbidden(): void
    {
        ['issue' => $issue] = $this->scenario();
        $customer = User::factory()->create();
        $customer->assignRole('customer');

        $this->actingAs($customer)
            ->post("/en/practitioner/validation/issues/{$issue->id}/retests", [
                'result' => 'passed', 'notes' => 'x',
            ])->assertForbidden();
    }
}

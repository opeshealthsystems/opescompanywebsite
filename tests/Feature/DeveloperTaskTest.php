<?php

namespace Tests\Feature;

use App\Models\IssueReport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class DeveloperTaskTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
    }

    private function reviewer(): User
    {
        $u = User::factory()->create();
        $u->assignRole('admin');
        return $u;
    }

    public function test_sent_to_development_auto_creates_one_task(): void
    {
        $issue = IssueReport::factory()->create(['status' => 'product_review', 'severity' => 'high']);
        $issue->recordProductReview($this->reviewer()->id, 'sent_to_development', 'Routing to dev');

        $this->assertEquals('sent_to_development', $issue->fresh()->status);
        $this->assertNotNull($issue->fresh()->developerTask);
        $this->assertEquals('open', $issue->fresh()->developerTask->status);
        $this->assertEquals('high', $issue->fresh()->developerTask->priority);

        // Re-running the decision must NOT create a second task.
        $issue->recordProductReview($this->reviewer()->id, 'sent_to_development');
        $this->assertDatabaseCount('developer_tasks', 1);
    }

    public function test_accepted_decision_does_not_create_task(): void
    {
        $issue = IssueReport::factory()->create(['status' => 'product_review']);
        $issue->recordProductReview($this->reviewer()->id, 'accepted');
        $this->assertDatabaseCount('developer_tasks', 0);
    }

    public function test_mark_fixed_moves_issue_to_ready_for_retest(): void
    {
        $issue = IssueReport::factory()->create(['status' => 'sent_to_development']);
        $task  = \App\Models\DeveloperTask::factory()->create(['issue_report_id' => $issue->id, 'status' => 'in_progress']);

        $task->markFixed('Patched the save handler');

        $this->assertEquals('fixed', $task->fresh()->status);
        $this->assertNotNull($task->fresh()->fixed_at);
        $this->assertEquals('ready_for_retest', $issue->fresh()->status);
    }

    public function test_mark_in_progress_and_reopen(): void
    {
        $task = \App\Models\DeveloperTask::factory()->create(['status' => 'open']);
        $task->markInProgress();
        $this->assertEquals('in_progress', $task->fresh()->status);
        $this->assertNotNull($task->fresh()->started_at);

        $task->update(['status' => 'fixed']);
        $task->reopen();
        $this->assertEquals('reopened', $task->fresh()->status);
    }

    public function test_wont_fix_rejects_issue(): void
    {
        $issue = IssueReport::factory()->create(['status' => 'sent_to_development']);
        $task  = \App\Models\DeveloperTask::factory()->create(['issue_report_id' => $issue->id, 'status' => 'open']);

        $task->markWontFix('Out of scope');

        $this->assertEquals('wont_fix', $task->fresh()->status);
        $this->assertEquals('rejected', $issue->fresh()->status);
    }

    public function test_record_retest_pass_and_fail(): void
    {
        $issue = IssueReport::factory()->create(['status' => 'ready_for_retest']);
        $task  = \App\Models\DeveloperTask::factory()->create(['issue_report_id' => $issue->id, 'status' => 'fixed']);

        // FAIL → retest_failed + task reopened
        $issue->recordRetest($issue->cohort_member_id, 'failed', 'Still broken');
        $this->assertEquals('retest_failed', $issue->fresh()->status);
        $this->assertEquals('reopened', $task->fresh()->status);
        $this->assertDatabaseHas('retests', ['issue_report_id' => $issue->id, 'result' => 'failed', 'developer_task_id' => $task->id]);

        // Re-fix → ready again → PASS → retest_passed
        $task->markFixed();
        $issue->fresh()->recordRetest($issue->cohort_member_id, 'passed', 'Works now');
        $this->assertEquals('retest_passed', $issue->fresh()->status);
        $this->assertDatabaseCount('retests', 2);
    }
}

<?php

namespace Tests\Feature;

use App\Models\CohortMember;
use App\Models\DeveloperTask;
use App\Models\IssueReport;
use App\Models\User;
use App\Notifications\IssueClosed;
use App\Notifications\IssueReadyForRetest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class N2IssueResolutionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
    }

    private function reporterMember(): CohortMember
    {
        $user = User::factory()->create();
        $user->assignRole('practitioner');

        return CohortMember::factory()->create(['user_id' => $user->id]);
    }

    public function test_marking_a_dev_task_fixed_notifies_the_reporter_to_retest(): void
    {
        Notification::fake();
        $member = $this->reporterMember();
        $issue  = IssueReport::factory()->create(['cohort_member_id' => $member->id, 'status' => 'sent_to_development']);
        $task   = DeveloperTask::factory()->create(['issue_report_id' => $issue->id, 'status' => 'in_progress']);

        $task->markFixed('Patched the rounding bug.');

        Notification::assertSentTo($member->user, IssueReadyForRetest::class);
    }

    public function test_marking_fixed_on_a_non_dev_issue_does_not_notify(): void
    {
        Notification::fake();
        $member = $this->reporterMember();
        // Issue already closed → markFixed must NOT resurrect it or notify.
        $issue  = IssueReport::factory()->create(['cohort_member_id' => $member->id, 'status' => 'closed']);
        $task   = DeveloperTask::factory()->create(['issue_report_id' => $issue->id, 'status' => 'in_progress']);

        $task->markFixed();

        Notification::assertNotSentTo($member->user, IssueReadyForRetest::class);
    }

    public function test_closing_an_issue_notifies_the_reporter(): void
    {
        Notification::fake();
        $member = $this->reporterMember();
        $issue  = IssueReport::factory()->create(['cohort_member_id' => $member->id, 'status' => 'accepted']);

        $issue->closeIssue();

        Notification::assertSentTo($member->user, IssueClosed::class);
    }
}

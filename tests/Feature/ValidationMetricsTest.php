<?php

namespace Tests\Feature;

use App\Models\Cohort;
use App\Models\CohortMember;
use App\Models\CohortTestCase;
use App\Models\DailyTestSession;
use App\Models\DeveloperTask;
use App\Models\IssueReport;
use App\Models\Retest;
use App\Models\ValidationModule;
use App\Models\ValidationProduct;
use App\Models\ValidationTestCase;
use App\Models\ValidationWorkflow;
use App\Support\ValidationMetrics;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ValidationMetricsTest extends TestCase
{
    use RefreshDatabase;

    private function metrics(): ValidationMetrics
    {
        return app(ValidationMetrics::class);
    }

    /** Cohort with 1 active member, 1 assigned+covered test case, 1 session. */
    private function seedCohort(): array
    {
        $cohort  = Cohort::factory()->create();
        $member  = CohortMember::factory()->create(['cohort_id' => $cohort->id, 'status' => 'active']);
        $product = ValidationProduct::factory()->create();
        $module  = ValidationModule::factory()->create(['validation_product_id' => $product->id]);
        $workflow = ValidationWorkflow::factory()->create(['validation_module_id' => $module->id]);
        $testCase = ValidationTestCase::factory()->create(['validation_workflow_id' => $workflow->id]);
        CohortTestCase::create(['cohort_id' => $cohort->id, 'validation_test_case_id' => $testCase->id]);
        DailyTestSession::factory()->create([
            'cohort_member_id'       => $member->id,
            'validation_product_id'  => $product->id,
            'validation_module_id'   => $module->id,
            'validation_workflow_id' => $workflow->id,
            'date'                   => now()->toDateString(),
        ]);
        return compact('cohort', 'member', 'product', 'module', 'workflow', 'testCase');
    }

    public function test_cohort_progress_counts_and_coverage(): void
    {
        ['cohort' => $cohort] = $this->seedCohort();
        $rows = $this->metrics()->cohortProgress($cohort);
        $this->assertCount(1, $rows);
        $this->assertEquals(1, $rows[0]['active_members']);
        $this->assertEquals(1, $rows[0]['sessions']);
        $this->assertEquals(1, $rows[0]['assigned_test_cases']);
        $this->assertEquals(1, $rows[0]['covered_test_cases']);
        $this->assertEquals(100, $rows[0]['coverage_pct']);
    }

    public function test_issue_analytics_breakdowns_and_retest_rate(): void
    {
        ['member' => $member] = $this->seedCohort();
        IssueReport::factory()->create(['cohort_member_id' => $member->id, 'status' => 'submitted', 'severity' => 'high', 'issue_type' => 'bug']);
        IssueReport::factory()->create(['cohort_member_id' => $member->id, 'status' => 'closed', 'severity' => 'low', 'issue_type' => 'recommendation']);
        Retest::factory()->create(['cohort_member_id' => $member->id, 'result' => 'passed']);
        Retest::factory()->create(['cohort_member_id' => $member->id, 'result' => 'failed']);

        $a = $this->metrics()->issueAnalytics();
        $this->assertEquals(2, $a['total']);
        $this->assertEquals(1, $a['by_status']['submitted']);
        $this->assertEquals(1, $a['by_status']['closed']);
        $this->assertEquals(1, $a['by_severity']['high']);
        $this->assertEquals(1, $a['by_type']['bug']);
        $this->assertEquals(50, $a['retest_pass_rate']);
    }

    public function test_developer_throughput(): void
    {
        $issue = IssueReport::factory()->create();
        DeveloperTask::factory()->create(['issue_report_id' => $issue->id, 'status' => 'fixed', 'fixed_at' => now()]);
        DeveloperTask::factory()->create(['status' => 'reopened']);

        $t = $this->metrics()->developerThroughput();
        $this->assertEquals(2, $t['total']);
        $this->assertEquals(1, $t['by_status']['fixed']);
        $this->assertEquals(1, $t['by_status']['reopened']);
        $this->assertEquals(50, $t['reopened_rate']);
    }

    public function test_practitioner_contribution(): void
    {
        ['member' => $member] = $this->seedCohort();
        IssueReport::factory()->create(['cohort_member_id' => $member->id, 'status' => 'accepted']);
        Retest::factory()->create(['cohort_member_id' => $member->id, 'result' => 'passed']);

        $c = $this->metrics()->practitionerContribution($member);
        $this->assertEquals(1, $c['sessions']);
        $this->assertEquals(1, $c['issues_found']);
        $this->assertEquals(1, $c['issues_accepted']);
        $this->assertEquals(1, $c['retests']);
    }

    public function test_weekly_snapshot_respects_window(): void
    {
        ['cohort' => $cohort, 'member' => $member] = $this->seedCohort();
        // inside the current week
        IssueReport::factory()->create(['cohort_member_id' => $member->id, 'created_at' => now()]);
        // far outside
        IssueReport::factory()->create(['cohort_member_id' => $member->id, 'created_at' => now()->subMonths(2)]);

        $snap = $this->metrics()->weeklySnapshot($cohort, now()->startOfWeek());
        $this->assertEquals(1, $snap['issues_submitted']);
        $this->assertArrayHasKey('week_start', $snap);
    }

    public function test_member_contribution_snapshot_has_labels(): void
    {
        ['member' => $member] = $this->seedCohort();
        $snap = $this->metrics()->memberContributionSnapshot($member);
        $this->assertArrayHasKey('member_name', $snap);
        $this->assertArrayHasKey('cohort_name', $snap);
        $this->assertArrayHasKey('sessions', $snap);
    }

    public function test_avg_days_to_fix_and_close_from_known_timestamps(): void
    {
        DeveloperTask::factory()->create([
            'status' => 'fixed', 'created_at' => now()->subDays(3), 'fixed_at' => now(),
        ]);
        $this->assertEquals(3.0, $this->metrics()->developerThroughput()['avg_days_to_fix']);

        IssueReport::factory()->create([
            'status' => 'closed', 'created_at' => now()->subDays(2), 'updated_at' => now(),
        ]);
        $this->assertEquals(2.0, $this->metrics()->issueAnalytics()['avg_days_to_close']);
    }

    public function test_negative_timestamp_does_not_produce_negative_average(): void
    {
        // A malformed/backdated row (fixed before created) must not drag the mean negative.
        DeveloperTask::factory()->create([
            'status' => 'fixed', 'created_at' => now(), 'fixed_at' => now()->subDays(4),
        ]);
        $this->assertEquals(4.0, $this->metrics()->developerThroughput()['avg_days_to_fix']);
    }

    public function test_weekly_snapshot_nests_retest_results(): void
    {
        ['cohort' => $cohort, 'member' => $member] = $this->seedCohort();
        Retest::factory()->create(['cohort_member_id' => $member->id, 'result' => 'passed', 'retested_at' => now()]);

        $snap = $this->metrics()->weeklySnapshot($cohort, now()->startOfWeek());
        $this->assertEquals(1, $snap['retests']['passed']);
        $this->assertEquals(0, $snap['retests']['failed']);
    }
}

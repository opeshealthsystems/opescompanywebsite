<?php

namespace Tests\Feature;

use App\Models\ClinicalReview;
use App\Models\DailyTestSession;
use App\Models\IssueReport;
use App\Models\ProductReview;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IssueReportModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_issue_report_relationships_resolve(): void
    {
        $issue = IssueReport::factory()->create();
        $clinical = ClinicalReview::factory()->create(['issue_report_id' => $issue->id]);
        $product  = ProductReview::factory()->create(['issue_report_id' => $issue->id]);

        $this->assertEquals($clinical->id, $issue->fresh()->clinicalReview->id);
        $this->assertEquals($product->id, $issue->fresh()->productReview->id);
        $this->assertNotNull($issue->product);
        $this->assertNotNull($issue->workflow);
        $this->assertNotNull($issue->cohortMember);
    }

    public function test_daily_test_session_casts_and_relationships(): void
    {
        $session = DailyTestSession::factory()->create(['screenshots' => ['a.png', 'b.png']]);
        $this->assertIsArray($session->fresh()->screenshots);
        $this->assertCount(2, $session->fresh()->screenshots);
        $this->assertNotNull($session->cohortMember);
    }

    public function test_option_maps_have_expected_counts(): void
    {
        $this->assertCount(10, IssueReport::issueTypeOptions());
        $this->assertCount(4, IssueReport::severityOptions());
        $this->assertCount(10, IssueReport::statusOptions());
        $this->assertCount(3, ClinicalReview::decisionOptions());
        $this->assertCount(4, ProductReview::decisionOptions());
    }
}

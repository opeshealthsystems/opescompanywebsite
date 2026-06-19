<?php

namespace Tests\Feature;

use App\Filament\Resources\IssueReportResource;
use App\Filament\Resources\DailyTestSessionResource;
use App\Models\IssueReport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class IssueReportTriageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
    }

    private function admin(): User
    {
        $u = User::factory()->create();
        $u->assignRole('admin');
        return $u;
    }

    public function test_clinical_review_approved_moves_to_clinical_review_status(): void
    {
        $reviewer = $this->admin();
        $issue = IssueReport::factory()->create(['status' => 'submitted']);

        $issue->recordClinicalReview($reviewer->id, 'approved_for_product_review', 'Looks valid');

        $this->assertEquals('clinical_review', $issue->fresh()->status);
        $this->assertDatabaseHas('clinical_reviews', ['issue_report_id' => $issue->id, 'decision' => 'approved_for_product_review']);
        $this->assertTrue($issue->fresh()->clinicalApproved());
    }

    public function test_clinical_review_rejected_sets_rejected_status(): void
    {
        $issue = IssueReport::factory()->create(['status' => 'submitted']);
        $issue->recordClinicalReview($this->admin()->id, 'rejected', 'Not reproducible');
        $this->assertEquals('rejected', $issue->fresh()->status);
    }

    public function test_send_to_product_review_then_product_decision(): void
    {
        $issue = IssueReport::factory()->create(['status' => 'submitted']);
        $issue->recordClinicalReview($this->admin()->id, 'approved_for_product_review');
        $issue->sendToProductReview();
        $this->assertEquals('product_review', $issue->fresh()->status);

        $issue->recordProductReview($this->admin()->id, 'sent_to_development', 'Routing to dev');
        $this->assertEquals('sent_to_development', $issue->fresh()->status);
        $this->assertDatabaseHas('product_reviews', ['issue_report_id' => $issue->id, 'decision' => 'sent_to_development']);
    }

    public function test_close_sets_closed_status(): void
    {
        $issue = IssueReport::factory()->create(['status' => 'accepted']);
        $issue->closeIssue();
        $this->assertEquals('closed', $issue->fresh()->status);
    }

    public function test_admin_can_access_resources_practitioner_cannot(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin);
        $this->assertTrue(IssueReportResource::canAccess());
        $this->assertTrue(DailyTestSessionResource::canAccess());
        $this->assertFalse(IssueReportResource::canCreate());

        $prac = User::factory()->create();
        $prac->assignRole('practitioner');
        $this->actingAs($prac);
        $this->assertFalse(IssueReportResource::canAccess());
        $this->assertFalse(DailyTestSessionResource::canAccess());
    }
}

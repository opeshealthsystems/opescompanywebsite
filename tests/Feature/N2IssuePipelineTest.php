<?php

namespace Tests\Feature;

use App\Models\CohortMember;
use App\Models\IssueReport;
use App\Models\User;
use App\Notifications\IssueClinicalDecision;
use App\Notifications\IssueProductDecision;
use App\Notifications\IssueSubmitted;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class N2IssuePipelineTest extends TestCase
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
        $user->practitionerProfile()->create(['profession' => 'doctor', 'workplace_country' => 'CM']);

        return CohortMember::factory()->create(['user_id' => $user->id]);
    }

    public function test_submitting_an_issue_notifies_the_reporter(): void
    {
        Notification::fake();
        $member = $this->reporterMember();
        [$product, $module, $workflow] = $this->scopedCatalogFor($member);

        $this->actingAs($member->user)->post('/en/practitioner/validation/issues', [
            'validation_product_id'  => $product->id,
            'validation_module_id'   => $module->id,
            'validation_workflow_id' => $workflow->id,
            'title'                  => 'Vitals panel rounds wrong',
            'issue_type'             => 'bug',
            'severity'               => 'high',
            'description'            => str_repeat('a', 30),
            'steps_to_reproduce'     => str_repeat('b', 30),
            'expected_result'        => 'correct',
            'actual_result'          => 'wrong',
            'clinical_impact'        => 'moderate',
        ])->assertRedirect();

        Notification::assertSentTo($member->user, IssueSubmitted::class);
    }

    public function test_clinical_decision_notifies_the_reporter(): void
    {
        Notification::fake();
        $member = $this->reporterMember();
        $issue  = IssueReport::factory()->create(['cohort_member_id' => $member->id, 'status' => 'clinical_review']);

        $issue->recordClinicalReview(User::factory()->create()->id, 'approved_for_product_review', 'Looks valid.');

        Notification::assertSentTo($member->user, IssueClinicalDecision::class);
    }

    public function test_product_decision_notifies_the_reporter(): void
    {
        Notification::fake();
        $member = $this->reporterMember();
        $issue  = IssueReport::factory()->create(['cohort_member_id' => $member->id, 'status' => 'product_review']);

        $issue->recordProductReview(User::factory()->create()->id, 'sent_to_development', 'Routing to dev.');

        Notification::assertSentTo($member->user, IssueProductDecision::class);
    }

    /** Build a cohort-scoped product/module/workflow + attach a test case so the workflow is in scope. */
    private function scopedCatalogFor(CohortMember $member): array
    {
        $product  = \App\Models\ValidationProduct::factory()->create();
        $module   = \App\Models\ValidationModule::factory()->create(['validation_product_id' => $product->id]);
        $workflow = \App\Models\ValidationWorkflow::factory()->create(['validation_module_id' => $module->id]);
        $testCase = \App\Models\ValidationTestCase::factory()->create(['validation_workflow_id' => $workflow->id]);
        $member->cohort->testCases()->attach($testCase->id);

        return [$product, $module, $workflow];
    }
}

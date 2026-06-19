<?php

namespace Tests\Feature;

use App\Models\Cohort;
use App\Models\CohortMember;
use App\Models\CohortTestCase;
use App\Models\IssueReport;
use App\Models\User;
use App\Models\ValidationModule;
use App\Models\ValidationProduct;
use App\Models\ValidationTestCase;
use App\Models\ValidationWorkflow;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class IssueReportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
    }

    private function placedScope(): array
    {
        $user = User::factory()->create();
        $user->assignRole('practitioner');
        $cohort = Cohort::factory()->create();
        $member = CohortMember::factory()->create(['cohort_id' => $cohort->id, 'user_id' => $user->id, 'status' => 'active']);
        $product  = ValidationProduct::factory()->create();
        $module   = ValidationModule::factory()->create(['validation_product_id' => $product->id]);
        $workflow = ValidationWorkflow::factory()->create(['validation_module_id' => $module->id]);
        $testCase = ValidationTestCase::factory()->create(['validation_workflow_id' => $workflow->id]);
        CohortTestCase::create(['cohort_id' => $cohort->id, 'validation_test_case_id' => $testCase->id]);
        return compact('user', 'member', 'product', 'module', 'workflow', 'testCase');
    }

    private function validPayload(array $scope): array
    {
        return [
            'validation_product_id'  => $scope['product']->id,
            'validation_module_id'   => $scope['module']->id,
            'validation_workflow_id' => $scope['workflow']->id,
            'title'              => 'Patient save fails',
            'issue_type'         => 'bug',
            'severity'           => 'high',
            'description'        => 'Saving a patient throws an error.',
            'steps_to_reproduce' => 'Open form, fill, save.',
            'expected_result'    => 'Patient saved.',
            'actual_result'      => 'Error 500.',
            'clinical_impact'    => 'Cannot register patients.',
        ];
    }

    public function test_practitioner_can_submit_issue(): void
    {
        $scope = $this->placedScope();
        $this->actingAs($scope['user'])
            ->post('/en/practitioner/validation/issues', $this->validPayload($scope))
            ->assertRedirect();
        $this->assertDatabaseHas('issue_reports', [
            'cohort_member_id' => $scope['member']->id,
            'title' => 'Patient save fails',
            'status' => 'submitted',
        ]);
    }

    public function test_missing_severity_fails_validation(): void
    {
        $scope = $this->placedScope();
        $payload = $this->validPayload($scope);
        unset($payload['severity']);
        $this->actingAs($scope['user'])
            ->post('/en/practitioner/validation/issues', $payload)
            ->assertStatus(302); // redirect back with validation errors
        $this->assertDatabaseCount('issue_reports', 0);
    }

    public function test_invalid_issue_type_fails(): void
    {
        $scope = $this->placedScope();
        $payload = $this->validPayload($scope);
        $payload['issue_type'] = 'not_a_type';
        $this->actingAs($scope['user'])
            ->post('/en/practitioner/validation/issues', $payload)
            ->assertStatus(302);
        $this->assertDatabaseCount('issue_reports', 0);
    }

    public function test_owner_can_view_issue(): void
    {
        $scope = $this->placedScope();
        $issue = IssueReport::factory()->create(['cohort_member_id' => $scope['member']->id]);
        $this->actingAs($scope['user'])
            ->get('/en/practitioner/validation/issues/'.$issue->id)
            ->assertOk();
    }

    public function test_other_practitioner_cannot_view_issue(): void
    {
        $scope = $this->placedScope();
        $issue = IssueReport::factory()->create(['cohort_member_id' => $scope['member']->id]);

        $other = User::factory()->create();
        $other->assignRole('practitioner');
        $this->actingAs($other)
            ->get('/en/practitioner/validation/issues/'.$issue->id)
            ->assertForbidden();
    }

    public function test_product_and_module_are_derived_from_workflow(): void
    {
        $scope = $this->placedScope();
        $payload = $this->validPayload($scope);
        // Attempt to submit a mismatched (but individually valid) product/module.
        $foreignProduct = ValidationProduct::factory()->create();
        $foreignModule  = ValidationModule::factory()->create(['validation_product_id' => $foreignProduct->id]);
        $payload['validation_product_id'] = $foreignProduct->id;
        $payload['validation_module_id']  = $foreignModule->id;

        $this->actingAs($scope['user'])
            ->post('/en/practitioner/validation/issues', $payload)
            ->assertRedirect();

        // The persisted product/module must match the in-scope workflow's ancestry, not the spoofed values.
        $this->assertDatabaseHas('issue_reports', [
            'validation_workflow_id' => $scope['workflow']->id,
            'validation_module_id'   => $scope['module']->id,
            'validation_product_id'  => $scope['product']->id,
        ]);
    }

    public function test_cannot_link_another_members_session(): void
    {
        $scope = $this->placedScope();

        // A session belonging to a different cohort member.
        $otherMember  = CohortMember::factory()->create(['status' => 'active']);
        $otherSession = \App\Models\DailyTestSession::factory()->create(['cohort_member_id' => $otherMember->id]);

        $payload = $this->validPayload($scope);
        $payload['daily_test_session_id'] = $otherSession->id;

        $this->actingAs($scope['user'])
            ->post('/en/practitioner/validation/issues', $payload)
            ->assertStatus(422);

        $this->assertDatabaseCount('issue_reports', 0);
    }
}

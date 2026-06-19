<?php

namespace Tests\Feature;

use App\Models\Cohort;
use App\Models\CohortMember;
use App\Models\CohortTestCase;
use App\Models\User;
use App\Models\ValidationModule;
use App\Models\ValidationProduct;
use App\Models\ValidationTestCase;
use App\Models\ValidationWorkflow;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class DailyTestSessionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
    }

    /** Builds a placed practitioner with one scoped product/module/workflow/testcase. */
    private function placedPractitionerWithScope(): array
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

        return compact('user', 'cohort', 'member', 'product', 'module', 'workflow', 'testCase');
    }

    public function test_placed_practitioner_can_submit_session(): void
    {
        ['user' => $user, 'member' => $member, 'product' => $product, 'module' => $module, 'workflow' => $workflow] = $this->placedPractitionerWithScope();

        $this->actingAs($user)->post('/en/practitioner/validation/sessions', [
            'validation_product_id'  => $product->id,
            'validation_module_id'   => $module->id,
            'validation_workflow_id' => $workflow->id,
            'date'                   => now()->toDateString(),
            'tasks_completed'        => 5,
        ])->assertRedirect();

        $this->assertDatabaseHas('daily_test_sessions', [
            'cohort_member_id' => $member->id,
            'validation_workflow_id' => $workflow->id,
            'tasks_completed' => 5,
        ]);
    }

    public function test_workflow_outside_cohort_scope_rejected(): void
    {
        ['user' => $user, 'product' => $product, 'module' => $module] = $this->placedPractitionerWithScope();
        // A workflow NOT assigned to the cohort:
        $otherModule   = ValidationModule::factory()->create(['validation_product_id' => $product->id]);
        $otherWorkflow = ValidationWorkflow::factory()->create(['validation_module_id' => $otherModule->id]);

        $this->actingAs($user)->post('/en/practitioner/validation/sessions', [
            'validation_product_id'  => $product->id,
            'validation_module_id'   => $module->id,
            'validation_workflow_id' => $otherWorkflow->id,
            'date'                   => now()->toDateString(),
            'tasks_completed'        => 1,
        ])->assertStatus(422);
    }

    public function test_non_practitioner_forbidden_on_sessions(): void
    {
        $user = User::factory()->create();
        $user->assignRole('customer');
        $this->actingAs($user)->get('/en/practitioner/validation/sessions')->assertForbidden();
    }

    public function test_unplaced_practitioner_redirected_from_create(): void
    {
        $user = User::factory()->create();
        $user->assignRole('practitioner');
        $this->actingAs($user)->get('/en/practitioner/validation/sessions/create')->assertRedirect();
    }
}

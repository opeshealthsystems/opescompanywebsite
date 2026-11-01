<?php

namespace Tests\Feature;

use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter;
use App\Contexts\Shared\Infrastructure\WorkflowDispatcher;
use App\Models\ValidationModule;
use App\Models\ValidationProduct;
use App\Models\ValidationWorkflow;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminValidationCatalogWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_modules_have_product_scoped_create_and_history_safe_delete_operations(): void
    {
        Role::findOrCreate('admin', 'web');
        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole('admin');
        $this->actingAs($admin);
        $product = ValidationProduct::create(['name' => 'Fixture product', 'code' => 'FIXTURE-1', 'is_active' => true]);

        $module = AdminWorkflowAdapter::record('admin.validation.modules.create', [
            'name' => 'Fixture module', 'code' => 'FIX-MOD', 'description' => 'Test fixture', 'is_active' => true,
        ], ['product' => $product->id]);

        $this->assertSame($product->id, $module->validation_product_id);
        $this->assertDatabaseHas('business_outbox', ['type' => 'validation.module_created.v1', 'actor_id' => $admin->id]);
        AdminWorkflowAdapter::run('admin.validation.modules.delete', parameters: ['module' => $module->id]);
        $this->assertDatabaseMissing('validation_modules', ['id' => $module->id]);
        $this->assertDatabaseHas('business_outbox', ['type' => 'validation.module_deleted.v1', 'actor_id' => $admin->id]);
    }

    public function test_catalogue_reads_and_writes_share_commands_and_preserve_parentage(): void
    {
        Role::findOrCreate('admin', 'web');
        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole('admin');
        $this->actingAs($admin);

        $product = ValidationProduct::create(['name' => 'Fixture product', 'code' => 'FIXTURE-1', 'is_active' => true]);
        $otherProduct = ValidationProduct::create(['name' => 'Other fixture', 'code' => 'FIXTURE-2', 'is_active' => true]);
        $module = AdminWorkflowAdapter::record('admin.validation.modules.update', [
            'validation_product_id' => $product->id,
            'name' => 'Fixture module', 'code' => 'FIX-MOD', 'description' => 'Test fixture', 'is_active' => true,
        ], ['module' => ValidationModule::create([
            'validation_product_id' => $otherProduct->id, 'name' => 'Old module', 'code' => 'OLD-MOD', 'is_active' => true,
        ])]);
        $module->refresh();
        $workflow = AdminWorkflowAdapter::record('admin.validation.workflows.create', [
            'name' => 'Fixture workflow', 'code' => 'FIX-WF', 'description' => null, 'is_active' => true,
        ], ['module' => $module]);
        $testCase = AdminWorkflowAdapter::record('admin.validation.testCases.create', [
            'title' => 'Fixture test', 'description' => null, 'steps' => 'Run fixture', 'expected_result' => 'Pass', 'is_active' => true,
        ], ['workflow' => $workflow]);

        $query = app(WorkflowDispatcher::class)->execute(
            'admin.validation.testCases.index', new WorkflowInput(actor: $admin, parameters: ['workflow' => $workflow]),
        );
        $this->assertSame($testCase->id, $query->data['items'][0]['id']);
        $this->assertDatabaseHas('business_outbox', ['type' => 'validation.workflow_created.v1', 'actor_id' => $admin->id]);

        try {
            AdminWorkflowAdapter::run('admin.validation.workflows.update', ['name' => 'Wrong parent'], [
                'module' => ValidationModule::create(['validation_product_id' => $otherProduct->id, 'name' => 'Wrong parent', 'code' => 'WRONG', 'is_active' => true]),
                'workflow' => $workflow,
            ]);
            $this->fail('A workflow cannot be edited through a different module.');
        } catch (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
            $this->assertDatabaseHas('validation_workflows', ['id' => $workflow->id, 'name' => 'Fixture workflow']);
        }

        $this->expectException(ValidationException::class);
        AdminWorkflowAdapter::run('admin.validation.workflows.delete', parameters: ['module' => $module, 'workflow' => $workflow]);
    }
}

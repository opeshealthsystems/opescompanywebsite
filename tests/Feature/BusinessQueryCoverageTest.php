<?php

namespace Tests\Feature;

use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Infrastructure\{WorkflowDispatcher, ApiWorkflowSerializer};
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\PermissionRegistrar;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class BusinessQueryCoverageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_every_query_without_record_parameters_has_a_valid_api_representation(): void
    {
        Notification::fake(); $failures = []; $checked = 0;
        foreach (config('business_workflows.definitions') as $operation => $definition) {
            if ($definition['command'] || str_contains($definition['api_path'], '{')) continue;
            $actor = null;
            if ($definition['authenticated']) {
                $role = $definition['roles'][0] ?? 'customer'; Role::findOrCreate($role, 'web');
                $actor = User::factory()->create(['is_active' => true]); $actor->assignRole($role);
            }
            try {
                $query = match ($operation) {
                    'admin.resourceSummaries.metric' => ['metric' => 'licenses.expiring'],
                    'admin.references.rolePermissions' => ['id' => Role::query()->value('id')],
                    default => [],
                };
                $input = new WorkflowInput(values: $query, actor: $actor, api: true, scopes: [$definition['scope']]);
                $result = app(WorkflowDispatcher::class)->execute($operation, $input);
                if ($result->kind !== 'binary') app(ApiWorkflowSerializer::class)->serialize($result->data, $input);
                $checked++;
            } catch (\Throwable $e) { $failures[] = $operation.': '.get_class($e).' '.$e->getMessage(); }
        }
        $this->assertGreaterThan(30, $checked);
        $this->assertSame([], $failures);
    }
}

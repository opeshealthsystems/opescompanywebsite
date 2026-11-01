<?php

namespace Tests\Feature;

use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter;
use App\Contexts\Shared\Infrastructure\WorkflowDispatcher;
use App\Models\LeaveRequest;
use App\Models\LeaveBalance;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminLeaveWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_leave_crud_reads_and_writes_share_guarded_workforce_commands(): void
    {
        Notification::fake();
        foreach (['admin', 'support'] as $role) Role::findOrCreate($role, 'web');
        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole('admin');
        $employee = User::factory()->create(['is_active' => true]);
        $employee->assignRole('support');
        $this->actingAs($admin);
        $data = ['user_id' => $employee->id, 'leave_type_id' => null, 'type' => 'annual', 'start_date' => '2026-11-10', 'end_date' => '2026-11-11',
            'total_days' => 2, 'reason' => 'Fictional fixture reason.', 'notes' => null];

        AdminWorkflowAdapter::run('admin.workforce.leaves.create', $data);
        $leave = LeaveRequest::query()->where('user_id', $employee->id)->firstOrFail();
        $this->assertSame('pending', $leave->status);
        $this->assertDatabaseHas('business_outbox', ['type' => 'workforce.leave_request_created.v1', 'actor_id' => $admin->id]);

        $result = app(WorkflowDispatcher::class)->execute('admin.workforce.leaves.index', new WorkflowInput(['status' => 'pending'], actor: $admin));
        $this->assertSame($leave->id, $result->data['items'][0]['id']);

        AdminWorkflowAdapter::run('admin.workforce.leaves.update', [...$data, 'total_days' => 1.5, 'reason' => 'Updated fictional reason.'], ['leave' => $leave->id]);
        $this->assertDatabaseHas('leave_requests', ['id' => $leave->id, 'total_days' => 1.5, 'reason' => 'Updated fictional reason.']);
        $this->assertDatabaseHas('business_outbox', ['type' => 'workforce.leave_request_updated.v1', 'actor_id' => $admin->id]);
    }

    public function test_leave_balance_operations_keep_usage_derived_and_retain_history(): void
    {
        Role::findOrCreate('admin', 'web');
        Role::findOrCreate('support', 'web');
        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole('admin');
        $employee = User::factory()->create(['is_active' => true]);
        $employee->assignRole('support');
        $this->actingAs($admin);

        $balance = AdminWorkflowAdapter::record('admin.workforce.leaveBalances.create', [
            'year' => 2026, 'type' => 'annual', 'entitled_days' => 20,
        ], ['employee' => $employee->id]);
        $this->assertSame($employee->id, $balance->user_id);
        $this->assertSame('0.0', $balance->used_days);

        AdminWorkflowAdapter::record('admin.workforce.leaveBalances.update', ['entitled_days' => 22], ['leaveBalance' => $balance->id]);
        $this->assertDatabaseHas('leave_balances', ['id' => $balance->id, 'entitled_days' => 22, 'used_days' => 0]);
        $this->assertDatabaseHas('business_outbox', ['type' => 'workforce.leave_balance_created.v1', 'actor_id' => $admin->id]);

        LeaveBalance::query()->whereKey($balance->id)->update(['used_days' => 2]);
        $this->expectException(ValidationException::class);
        AdminWorkflowAdapter::run('admin.workforce.leaveBalances.delete', parameters: ['leaveBalance' => $balance->id]);
    }
}

<?php

namespace Tests\Feature;

use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Infrastructure\AdminWorkflowAdapter;
use App\Contexts\Shared\Infrastructure\WorkflowDispatcher;
use App\Models\EmployeeProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\{Mail, Notification};
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminEmployeeWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_employee_admin_create_read_and_update_share_hr_commands(): void
    {
        Mail::fake();
        Notification::fake();
        foreach (['admin', 'support', 'super_admin', 'tester'] as $role) Role::findOrCreate($role, 'web');
        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole('admin');
        $this->actingAs($admin);

        AdminWorkflowAdapter::run('admin.workforce.employees.create', [
            'name' => 'Fictional employee fixture', 'email' => 'employee-fixture@example.test', 'password' => 'Fictional-Pass-123!',
            'phone' => '+237600000000', 'employee_id' => 'FIXTURE-EMP-01', 'department' => 'Support', 'position' => 'Support analyst',
            'department_id' => null, 'hire_date' => '2026-11-01', 'is_active' => true, 'salary_grade_id' => null,
            'base_salary' => 100000, 'salary_currency' => 'XAF', 'roles' => ['support'],
            'employeeProfile' => ['employment_type' => 'full_time', 'currency' => 'XAF', 'salary' => 100000,
                'bank_name' => 'Fictional bank', 'bank_account' => 'FIXTURE-ACCOUNT', 'tax_id' => 'FIXTURE-TAX',
                'emergency_contact_name' => 'Fictional contact'],
        ]);
        $employee = User::query()->where('email', 'employee-fixture@example.test')->firstOrFail();
        $this->assertTrue($employee->hasRole('support'));
        $this->assertDatabaseHas('employee_profiles', ['user_id' => $employee->id, 'bank_account' => 'FIXTURE-ACCOUNT']);
        $this->assertDatabaseHas('business_outbox', ['type' => 'workforce.employee_created.v1', 'actor_id' => $admin->id]);

        $result = app(WorkflowDispatcher::class)->execute('admin.workforce.employees.show', new WorkflowInput(parameters: ['employee' => $employee], actor: $admin));
        $this->assertSame('FIXTURE-TAX', $result->data['employee']['employee_profile']['tax_id']);

        AdminWorkflowAdapter::run('admin.workforce.employees.update', [
            'position' => 'Updated support analyst', 'is_active' => false, 'roles' => ['support'],
            'employeeProfile' => ['emergency_contact_name' => 'Updated fictional contact'],
        ], ['employee' => $employee]);
        $this->assertDatabaseHas('users', ['id' => $employee->id, 'position' => 'Updated support analyst', 'is_active' => false]);
        $this->assertDatabaseHas('employee_profiles', ['user_id' => $employee->id, 'emergency_contact_name' => 'Updated fictional contact']);
        $this->assertDatabaseHas('business_outbox', ['type' => 'workforce.employee_updated.v1', 'actor_id' => $admin->id]);

        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        AdminWorkflowAdapter::run('admin.workforce.employees.create', ['name' => 'Privileged fixture', 'email' => 'privileged-fixture@example.test',
            'password' => 'Fictional-Pass-456!', 'salary_currency' => 'XAF', 'roles' => ['super_admin']]);
    }
}

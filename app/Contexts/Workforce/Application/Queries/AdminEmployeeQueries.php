<?php

declare(strict_types=1);

namespace App\Contexts\Workforce\Application\Queries;

use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Models\EmployeeProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

/** Read projections for administrator and delegated Workforce employee views. */
final class AdminEmployeeQueries
{
    private function admin(WorkflowInput $input): User
    {
        $actor = $input->user();
        abort_unless($actor?->is_active && $actor->hasAnyRole(['super_admin', 'admin']), 403);

        return $actor;
    }

    public function employeeQuery(WorkflowInput $input): Builder
    {
        $this->admin($input);

        return User::query()->with(['roles', 'department', 'salaryGrade', 'employeeProfile'])
            ->whereHas('roles', fn ($roles) => $roles->whereIn('name', ['super_admin', 'admin', 'support', 'tester']))
            ->orderBy('name');
    }

    private function employeeView(User $employee): array
    {
        $employee->loadMissing(['roles', 'department', 'salaryGrade', 'employeeProfile']);

        return [
            'id' => $employee->id,
            'name' => $employee->name,
            'email' => $employee->email,
            'phone' => $employee->phone,
            'employee_id' => $employee->employee_id,
            'department' => $employee->department,
            'department_id' => $employee->department_id,
            'position' => $employee->position,
            'hire_date' => $employee->hire_date?->toDateString(),
            'is_active' => $employee->is_active,
            'roles' => $employee->getRoleNames()->values()->all(),
            'salary_grade_id' => $employee->salary_grade_id,
            'base_salary' => $employee->base_salary,
            'salary_currency' => $employee->salary_currency,
            'employee_profile' => $employee->employeeProfile?->only([
                'employment_type', 'contract_end_date', 'salary', 'currency', 'bank_name', 'bank_account',
                'tax_id', 'emergency_contact_name', 'emergency_contact_phone', 'emergency_contact_relation',
            ]),
            'created_at' => $employee->created_at?->toISOString(),
            'updated_at' => $employee->updated_at?->toISOString(),
        ];
    }

    public function index(WorkflowInput $input): WorkflowResult
    {
        $query = $this->employeeQuery($input);
        $filters = $input->validate(['search' => 'sometimes|nullable|string|max:150', 'active' => 'sometimes|boolean']);

        if (isset($filters['active'])) {
            $query->where('is_active', $filters['active']);
        }

        if (! empty($filters['search'])) {
            $term = trim($filters['search']);
            $query->where(fn ($q) => $q->where('name', 'like', '%'.$term.'%')
                ->orWhere('email', 'like', '%'.$term.'%')
                ->orWhere('employee_id', 'like', '%'.$term.'%'));
        }

        $page = $input->paginate($query);

        return WorkflowResult::payload([
            'items' => $page->getCollection()->map(fn (User $employee) => $this->employeeView($employee))->all(),
            'pagination' => [
                'current_page' => $page->currentPage(),
                'per_page' => $page->perPage(),
                'total' => $page->total(),
                'last_page' => $page->lastPage(),
            ],
        ]);
    }

    public function show(WorkflowInput $input, User $employee): WorkflowResult
    {
        $this->admin($input);
        abort_unless($employee->hasAnyRole(['super_admin', 'admin', 'support', 'tester']), 404);

        return WorkflowResult::payload(['employee' => $this->employeeView($employee)]);
    }

    /** Serialize a just-written employee with the same field projection as reads. */
    public function present(User $employee): array
    {
        return $this->employeeView($employee);
    }
}

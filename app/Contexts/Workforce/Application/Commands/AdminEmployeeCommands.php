<?php

namespace App\Contexts\Workforce\Application\Commands;

use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Contexts\Shared\Infrastructure\DomainOutbox;
use App\Models\EmployeeProfile;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

/** Shared staff and HR profile writes for Filament and delegated APIs. */
final class AdminEmployeeCommands extends WorkflowHandler
{
    private function admin(WorkflowInput $input): User
    {
        $actor = $input->user();
        abort_unless($actor?->is_active && $actor->hasAnyRole(['super_admin', 'admin']), 403);
        return $actor;
    }

    private function rules(?User $employee = null): array
    {
        $sometimes = $employee ? 'sometimes|' : '';
        return [
            'name' => $sometimes.'required|string|max:100',
            'email' => [$employee ? 'sometimes' : 'required', 'required', 'email:rfc', 'max:150', Rule::unique('users', 'email')->ignore($employee?->id)],
            'password' => $employee ? 'sometimes|nullable|string|min:8|max:64' : 'required|string|min:8|max:64',
            'phone' => $sometimes.'nullable|string|max:30',
            'employee_id' => [$employee ? 'sometimes' : 'nullable', 'nullable', 'string', 'max:50', Rule::unique('users', 'employee_id')->ignore($employee?->id)],
            'department' => $sometimes.'nullable|string|max:80', 'position' => $sometimes.'nullable|string|max:80',
            'department_id' => [$employee ? 'sometimes' : 'nullable', 'nullable', 'integer', Rule::exists('departments', 'id')->where('is_active', true)],
            'hire_date' => $sometimes.'nullable|date', 'is_active' => $sometimes.'boolean',
            'salary_grade_id' => [$employee ? 'sometimes' : 'nullable', 'nullable', 'integer', Rule::exists('salary_grades', 'id')->where('is_active', true)],
            'base_salary' => $sometimes.'nullable|numeric|min:0|max:9999999999.99',
            'salary_currency' => $sometimes.'required|in:XAF,USD,EUR',
            'roles' => [$employee ? 'sometimes' : 'required', 'required', 'array', 'min:1', 'max:4'],
            'roles.*' => 'required|string|distinct|in:super_admin,admin,support,tester',
            'employeeProfile' => 'sometimes|array',
            'employeeProfile.employment_type' => ($employee ? 'sometimes|' : 'sometimes|').'required|in:full_time,part_time,contract,intern',
            'employeeProfile.contract_end_date' => 'sometimes|nullable|date',
            'employeeProfile.salary' => 'sometimes|nullable|numeric|min:0|max:9999999999.99',
            'employeeProfile.currency' => 'sometimes|required|in:XAF,USD,EUR',
            'employeeProfile.bank_name' => 'sometimes|nullable|string|max:100',
            'employeeProfile.bank_account' => 'sometimes|nullable|string|max:60',
            'employeeProfile.tax_id' => 'sometimes|nullable|string|max:60',
            'employeeProfile.emergency_contact_name' => 'sometimes|nullable|string|max:100',
            'employeeProfile.emergency_contact_phone' => 'sometimes|nullable|string|max:30',
            'employeeProfile.emergency_contact_relation' => 'sometimes|nullable|string|max:60',
        ];
    }

    private function profile(User $employee, array $values): void
    {
        if ($values !== []) $employee->employeeProfile()->updateOrCreate([], $values);
    }

    private function employeeView(User $employee): array
    {
        $employee->loadMissing(['roles', 'department', 'salaryGrade', 'employeeProfile']);
        return [
            'id' => $employee->id, 'name' => $employee->name, 'email' => $employee->email, 'phone' => $employee->phone,
            'employee_id' => $employee->employee_id, 'department' => $employee->department, 'department_id' => $employee->department_id,
            'position' => $employee->position, 'hire_date' => $employee->hire_date?->toDateString(), 'is_active' => $employee->is_active,
            'roles' => $employee->getRoleNames()->values()->all(), 'salary_grade_id' => $employee->salary_grade_id,
            'base_salary' => $employee->base_salary, 'salary_currency' => $employee->salary_currency,
            'employee_profile' => $employee->employeeProfile?->only(['employment_type', 'contract_end_date', 'salary', 'currency', 'bank_name', 'bank_account', 'tax_id', 'emergency_contact_name', 'emergency_contact_phone', 'emergency_contact_relation']),
            'created_at' => $employee->created_at?->toISOString(), 'updated_at' => $employee->updated_at?->toISOString(),
        ];
    }

    public function employeeQuery(WorkflowInput $input): Builder
    {
        $this->admin($input);

        return User::query()->with(['roles', 'department', 'salaryGrade', 'employeeProfile'])
            ->whereHas('roles', fn ($roles) => $roles->whereIn('name', ['super_admin', 'admin', 'support', 'tester']))
            ->orderBy('name');
    }

    public function index(WorkflowInput $input): WorkflowResult
    {
        $this->admin($input);
        $filters = $input->validate(['search' => 'sometimes|nullable|string|max:150', 'active' => 'sometimes|boolean']);
        $query = $this->employeeQuery($input);
        if (isset($filters['active'])) $query->where('is_active', $filters['active']);
        if (!empty($filters['search'])) { $term = trim($filters['search']); $query->where(fn ($q) => $q->where('name', 'like', '%'.$term.'%')->orWhere('email', 'like', '%'.$term.'%')->orWhere('employee_id', 'like', '%'.$term.'%')); }
        $page = $input->paginate($query);
        return WorkflowResult::payload(['items' => $page->getCollection()->map(fn (User $employee) => $this->employeeView($employee))->all(),
            'pagination' => ['current_page' => $page->currentPage(), 'per_page' => $page->perPage(), 'total' => $page->total(), 'last_page' => $page->lastPage()]]);
    }

    public function show(WorkflowInput $input, User $employee): WorkflowResult
    {
        $this->admin($input);
        abort_unless($employee->hasAnyRole(['super_admin', 'admin', 'support', 'tester']), 404);
        return WorkflowResult::payload(['employee' => $this->employeeView($employee)]);
    }

    public function create(WorkflowInput $input): WorkflowResult
    {
        $actor = $this->admin($input);
        $data = $input->validate($this->rules());
        $roles = $data['roles'];
        if (in_array('super_admin', $roles, true)) abort_unless($actor->hasRole('super_admin'), 403);
        $profile = $data['employeeProfile'] ?? ['employment_type' => 'full_time', 'currency' => 'XAF'];
        unset($data['roles'], $data['employeeProfile']);
        $employee = DB::transaction(function () use ($data, $roles, $profile, $actor) {
            $employee = User::create($data + ['is_active' => true, 'salary_currency' => 'XAF']);
            $employee->syncRoles($roles);
            $this->profile($employee, $profile);
            \App\Contexts\Shared\Infrastructure\DomainMail::to($employee->email)->queue(new \App\Mail\WelcomeEmail($employee));
            $employee->notify(new \App\Notifications\FeedEntry('account.welcome', 'Welcome to OPES', 'Welcome to OPES Health Systems.', 'sparkles', null));
            DomainOutbox::append('event', 'workforce.employee_created.v1', ['references' => [['resource' => 'User', 'id' => $employee->id]]], $actor->id);
            return $employee;
        }, 3);
        return WorkflowResult::payload(['employee' => $this->employeeView($employee)], 201)->with('success', 'Employee created.');
    }

    public function update(WorkflowInput $input, User $employee): WorkflowResult
    {
        $actor = $this->admin($input);
        abort_unless($employee->hasAnyRole(['super_admin', 'admin', 'support', 'tester']), 404);
        $data = $input->validate($this->rules($employee));
        if ($data === []) throw ValidationException::withMessages(['employee' => 'Provide at least one employee field to update.']);
        $roles = $data['roles'] ?? null;
        $profile = $data['employeeProfile'] ?? [];
        unset($data['roles'], $data['employeeProfile']);
        if ($roles !== null && in_array('super_admin', $roles, true)) abort_unless($actor->hasRole('super_admin'), 403);
        if ($actor->id === $employee->id && array_key_exists('is_active', $data) && !$data['is_active']) throw ValidationException::withMessages(['is_active' => 'You cannot deactivate your own account.']);
        $record = DB::transaction(function () use ($employee, $data, $roles, $profile, $actor) {
            $record = User::query()->lockForUpdate()->findOrFail($employee->id);
            if ($record->hasRole('super_admin') && $roles !== null && !in_array('super_admin', $roles, true)
                && User::role('super_admin')->where('is_active', true)->where('id', '!=', $record->id)->exists() === false) {
                throw ValidationException::withMessages(['roles' => 'The last active super administrator cannot be removed.']);
            }
            $record->fill($data)->save();
            if ($roles !== null) $record->syncRoles($roles);
            $this->profile($record, $profile);
            DomainOutbox::append('event', 'workforce.employee_updated.v1', ['references' => [['resource' => 'User', 'id' => $record->id]],
                'changed_fields' => array_merge(array_keys($data), $roles !== null ? ['roles'] : [], $profile !== [] ? ['employee_profile'] : [])], $actor->id);
            return $record;
        }, 3);
        return WorkflowResult::payload(['employee' => $this->employeeView($record)])->with('success', 'Employee updated.');
    }
}

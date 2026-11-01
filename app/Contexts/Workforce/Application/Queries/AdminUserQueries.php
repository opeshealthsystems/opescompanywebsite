<?php

namespace App\Contexts\Workforce\Application\Queries;

use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Models\User;
use App\Models\EmployeeProfile;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Permission\Models\Role;

/** User and role reads shared by the admin UI and public business API. */
final class AdminUserQueries extends WorkflowHandler
{
    private function admin(WorkflowInput $input, bool $superAdminOnly = false): User
    {
        $actor = $input->user();
        abort_unless($actor instanceof User && $actor->is_active, 403);
        abort_unless($superAdminOnly ? $actor->hasRole('super_admin') : $actor->hasAnyRole(['super_admin', 'admin']), 403);

        return $actor;
    }

    public function usersQuery(WorkflowInput $input): Builder
    {
        $this->admin($input);

        return User::query()->with('roles')->orderByDesc('created_at');
    }

    public function usersIndex(WorkflowInput $input): WorkflowResult
    {
        $this->admin($input);
        $filters = $input->validate([
            'search' => 'sometimes|nullable|string|max:150',
            'role' => 'sometimes|in:super_admin,admin,support,accountant,hr,manager,staff,employee,tester,customer,practitioner,partner',
            'is_active' => 'sometimes|boolean',
        ]);

        $query = $this->usersQuery($input);
        if (isset($filters['role'])) $query->role($filters['role']);
        if (array_key_exists('is_active', $filters)) $query->where('is_active', $filters['is_active']);
        if (!empty($filters['search'])) {
            $search = trim($filters['search']);
            $query->where(fn (Builder $users) => $users
                ->where('name', 'like', '%'.$search.'%')
                ->orWhere('email', 'like', '%'.$search.'%')
                ->orWhere('employee_id', 'like', '%'.$search.'%'));
        }

        $page = $input->paginate($query);

        return WorkflowResult::payload([
            'items' => $page->getCollection()->map(fn (User $user) => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'avatar' => $user->avatar,
                'employee_id' => $user->employee_id,
                'department' => $user->department,
                'position' => $user->position,
                'hire_date' => $user->hire_date?->toDateString(),
                'is_active' => (bool) $user->is_active,
                'roles' => $user->roles->map(fn (Role $role) => ['id' => $role->id, 'name' => $role->name])->values()->all(),
                'created_at' => $user->created_at?->toISOString(),
            ])->all(),
            'pagination' => [
                'current_page' => $page->currentPage(),
                'per_page' => $page->perPage(),
                'total' => $page->total(),
                'last_page' => $page->lastPage(),
            ],
        ]);
    }

    public function employeeProfileQuery(WorkflowInput $input, User $user): Builder
    {
        $this->admin($input);
        abort_unless($user->hasAnyRole(['super_admin', 'admin', 'support', 'tester']), 404);

        return EmployeeProfile::query()->where('user_id', $user->id);
    }

    public function employeeProfile(WorkflowInput $input, User $user): WorkflowResult
    {
        $profile = $this->employeeProfileQuery($input, $user)->first();

        return WorkflowResult::payload(['employee_profile' => $profile?->only([
            'user_id', 'employment_type', 'contract_end_date', 'salary', 'currency', 'bank_name', 'bank_account', 'tax_id',
            'emergency_contact_name', 'emergency_contact_phone', 'emergency_contact_relation',
        ])]);
    }

    public function rolesQuery(WorkflowInput $input): Builder
    {
        $this->admin($input, superAdminOnly: true);

        return Role::query()->withCount(['permissions', 'users'])->orderBy('id');
    }

    public function rolesIndex(WorkflowInput $input): WorkflowResult
    {
        $page = $input->paginate($this->rolesQuery($input));

        return WorkflowResult::payload([
            'items' => $page->getCollection()->map(fn (Role $role) => [
                'id' => $role->id,
                'name' => $role->name,
                'guard_name' => $role->guard_name,
                'permissions_count' => (int) $role->permissions_count,
                'users_count' => (int) $role->users_count,
            ])->all(),
            'pagination' => [
                'current_page' => $page->currentPage(),
                'per_page' => $page->perPage(),
                'total' => $page->total(),
                'last_page' => $page->lastPage(),
            ],
        ]);
    }
}

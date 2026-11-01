<?php

namespace App\Contexts\Workforce\Application\Queries;

use App\Models\Department;
use App\Models\LeaveBalance;
use App\Models\User;
class HREmployeeQueries extends \App\Contexts\Shared\Application\WorkflowHandler
{
    public function index(\App\Contexts\Shared\Application\WorkflowInput $request): \App\Contexts\Shared\Application\WorkflowResult
    {
        $query = \App\Models\User::with(['employeeProfile', 'department'])->whereNotNull('employee_id')->when($request->search, fn($q) => $q->where(fn($w) => $w->where('name', 'like', '%' . $request->search . '%')->orWhere('email', 'like', '%' . $request->search . '%')))->when($request->department_id, fn($q) => $q->where('department_id', $request->department_id))->when($request->employment_type, fn($q) => $q->whereHas('employeeProfile', fn($q2) => $q2->where('employment_type', $request->employment_type)));
        $employees = $request->paginate($query->orderBy('name'), 25);
        $departments = $request->collect(\App\Models\Department::orderBy('name'));
        return \App\Contexts\Shared\Application\WorkflowResult::read('hr_employeecontroller_index_1', compact('employees', 'departments'));
    }
    public function show(\App\Contexts\Shared\Application\WorkflowInput $workflowInput, \App\Models\User $user): \App\Contexts\Shared\Application\WorkflowResult
    {
        abort_unless($user->employee_id !== null, 404);
        $user->load('employeeProfile', 'department');
        $leaveBalances = $workflowInput->collect(\App\Models\LeaveBalance::where('user_id', $user->id)->where('year', now()->year));
        return \App\Contexts\Shared\Application\WorkflowResult::read('hr_employeecontroller_show_1', compact('user', 'leaveBalances'));
    }
}

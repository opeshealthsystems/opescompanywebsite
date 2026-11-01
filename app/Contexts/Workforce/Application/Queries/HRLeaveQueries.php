<?php

namespace App\Contexts\Workforce\Application\Queries;

use App\Models\Department;
use App\Models\LeaveRequest;
class HRLeaveQueries extends \App\Contexts\Shared\Application\WorkflowHandler
{
    public function index(\App\Contexts\Shared\Application\WorkflowInput $request): \App\Contexts\Shared\Application\WorkflowResult
    {
        $query = \App\Models\LeaveRequest::with(['employee', 'employee.department'])->when($request->status, fn($q) => $q->where('status', $request->status))->when($request->department_id, fn($q) => $q->whereHas('employee', fn($q2) => $q2->where('department_id', $request->department_id)))->when($request->from, fn($q) => $q->where('start_date', '>=', $request->from))->when($request->to, fn($q) => $q->where('end_date', '<=', $request->to))->latest();
        $leaves = $request->paginate($query, 25);
        $departments = $request->collect(\App\Models\Department::orderBy('name'));
        return \App\Contexts\Shared\Application\WorkflowResult::read('hr_leavecontroller_index_1', compact('leaves', 'departments'));
    }
}

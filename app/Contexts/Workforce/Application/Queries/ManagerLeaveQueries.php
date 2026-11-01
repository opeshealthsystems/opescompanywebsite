<?php

namespace App\Contexts\Workforce\Application\Queries;

use App\Models\Department;
use App\Models\LeaveRequest;
class ManagerLeaveQueries extends \App\Contexts\Shared\Application\WorkflowHandler
{
    public function index(\App\Contexts\Shared\Application\WorkflowInput $request): \App\Contexts\Shared\Application\WorkflowResult
    {
        $user = \App\Contexts\Shared\Application\CurrentWorkflow::actor();
        $dept = \App\Models\Department::where('head_id', $user->id)->first();
        $query = \App\Models\LeaveRequest::with('employee')->when($dept, fn($q) => $q->whereHas('employee', fn($q2) => $q2->where('department_id', $dept->id)), fn($emptyScope) => $emptyScope->whereRaw('1 = 0'))->when($request->status, fn($q) => $q->where('status', $request->status))->latest();
        $leaves = $request->paginate($query, 20);
        return \App\Contexts\Shared\Application\WorkflowResult::read('manager_leavecontroller_index_1', compact('leaves', 'dept'));
    }
}

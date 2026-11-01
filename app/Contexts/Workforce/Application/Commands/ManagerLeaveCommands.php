<?php

namespace App\Contexts\Workforce\Application\Commands;

use App\Models\Department;
use App\Models\LeaveRequest;
class ManagerLeaveCommands extends \App\Contexts\Shared\Application\WorkflowHandler
{
    public function approve(\App\Contexts\Shared\Application\WorkflowInput $request, $id): \App\Contexts\Shared\Application\WorkflowResult
    {
        $request->validate(['note' => 'nullable|string|max:5000']);
        app(\App\Contexts\Workforce\Application\LeaveDecision::class)->decide($request->user(), $id, 'approved', $request->note);
        return \App\Contexts\Shared\Application\WorkflowResult::next('manager_leavecontroller_approve_1')->with('success', 'Leave request approved.');
    }
    public function reject(\App\Contexts\Shared\Application\WorkflowInput $request, $id): \App\Contexts\Shared\Application\WorkflowResult
    {
        $request->validate(['note' => 'nullable|string|max:5000']);
        app(\App\Contexts\Workforce\Application\LeaveDecision::class)->decide($request->user(), $id, 'rejected', $request->note);
        return \App\Contexts\Shared\Application\WorkflowResult::next('manager_leavecontroller_reject_1')->with('success', 'Leave request rejected.');
    }
}

<?php

namespace App\Contexts\Workforce\Application\Queries;

use App\Models\Department;
use App\Models\LeaveRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;
class ManagerReportQueries extends \App\Contexts\Shared\Application\WorkflowHandler
{
    public function index(\App\Contexts\Shared\Application\WorkflowInput $workflowInput): \App\Contexts\Shared\Application\WorkflowResult
    {
        $user = \App\Contexts\Shared\Application\CurrentWorkflow::actor();
        $dept = \App\Models\Department::where('head_id', $user->id)->first();
        $teamIds = $dept ? \App\Models\User::where('department_id', $dept->id)->pluck('id') : collect();
        $leaveByMonth = $workflowInput->collect(\App\Models\LeaveRequest::whereIn('user_id', $teamIds)->where('status', 'approved')->selectRaw(\App\Contexts\Shared\Infrastructure\ReportingSql::month('start_date')." as month, SUM(total_days) as total_days, COUNT(*) as count")->groupBy('month')->orderByDesc('month')->take(12));
        $leaveByType = $workflowInput->collect(\App\Models\LeaveRequest::whereIn('user_id', $teamIds)->where('status', 'approved')->selectRaw('type, SUM(total_days) as total_days, COUNT(*) as count')->groupBy('type'));
        $leaveByEmployee = $workflowInput->collect(\App\Models\LeaveRequest::whereIn('user_id', $teamIds)->where('status', 'approved')->with('employee')->selectRaw('user_id, SUM(total_days) as total_days, COUNT(*) as count')->groupBy('user_id')->orderByDesc('total_days'));
        return \App\Contexts\Shared\Application\WorkflowResult::read('manager_reportcontroller_index_1', compact('dept', 'leaveByMonth', 'leaveByType', 'leaveByEmployee'));
    }
}

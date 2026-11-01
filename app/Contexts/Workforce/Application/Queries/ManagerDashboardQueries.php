<?php

namespace App\Contexts\Workforce\Application\Queries;

use App\Models\Department;
use App\Models\LeaveRequest;
use App\Models\PerformanceReview;
use App\Models\User;
class ManagerDashboardQueries extends \App\Contexts\Shared\Application\WorkflowHandler
{
    public function index(\App\Contexts\Shared\Application\WorkflowInput $workflowInput): \App\Contexts\Shared\Application\WorkflowResult
    {
        $user = \App\Contexts\Shared\Application\CurrentWorkflow::actor();
        $dept = \App\Models\Department::where('head_id', $user->id)->first();
        $teamSize = $dept ? \App\Models\User::where('department_id', $dept->id)->count() : 0;
        $pendingLeave = \App\Models\LeaveRequest::where('status', 'pending')->when($dept, fn($q) => $q->whereHas('employee', fn($q2) => $q2->where('department_id', $dept->id)), fn($emptyScope) => $emptyScope->whereRaw('1 = 0'))->count();
        $reviewsDue = \App\Models\PerformanceReview::where('status', 'draft')->when($dept, fn($q) => $q->whereHas('employee', fn($q2) => $q2->where('department_id', $dept->id)), fn($emptyScope) => $emptyScope->whereRaw('1 = 0'))->count();
        $recentLeave = $workflowInput->collect(\App\Models\LeaveRequest::where('status', 'pending')->when($dept, fn($q) => $q->whereHas('employee', fn($q2) => $q2->where('department_id', $dept->id)), fn($emptyScope) => $emptyScope->whereRaw('1 = 0'))->with('employee')->latest()->take(5));
        $team = $dept ? $workflowInput->collect(\App\Models\User::where('department_id', $dept->id)->take(8)) : collect();
        $upcomingReviews = $workflowInput->collect(\App\Models\PerformanceReview::where('status', 'draft')->when($dept, fn($q) => $q->whereHas('employee', fn($q2) => $q2->where('department_id', $dept->id)), fn($emptyScope) => $emptyScope->whereRaw('1 = 0'))->with('employee')->latest('review_date')->take(4));
        return \App\Contexts\Shared\Application\WorkflowResult::read('manager_dashboardcontroller_index_1', compact('user', 'dept', 'teamSize', 'pendingLeave', 'reviewsDue', 'recentLeave', 'team', 'upcomingReviews'));
    }
}

<?php

namespace App\Contexts\Testing\Application\Queries;

use App\Models\Ticket;
use App\Models\TesterAssignment;
use Illuminate\Support\Facades\Auth;
class TesterDashboardQueries extends \App\Contexts\Shared\Application\WorkflowHandler
{
    public function index(\App\Contexts\Shared\Application\WorkflowInput $workflowInput): \App\Contexts\Shared\Application\WorkflowResult
    {
        $user = \App\Contexts\Shared\Application\CurrentWorkflow::actor();
        $allAssignments = $workflowInput->collect(\App\Models\TesterAssignment::where('assigned_to', $user->id));
        $active = $allAssignments->whereIn('status', ['pending', 'in_progress'])->sortBy('due_date');
        $completed = $allAssignments->whereIn('status', ['completed', 'cancelled'])->sortByDesc('updated_at')->take(5);
        $overdue = $allAssignments->filter(fn($a) => $a->isOverdue());
        $totalAssigned = $allAssignments->count();
        $activeCount = $active->count();
        $completedCount = $allAssignments->where('status', 'completed')->count();
        $overdueCount = $overdue->count();
        $bugReportsCount = \App\Models\Ticket::where('user_id', $user->id)->where('type', 'bug_report')->count();
        return \App\Contexts\Shared\Application\WorkflowResult::read('tester_dashboardcontroller_index_1', compact('user', 'active', 'completed', 'totalAssigned', 'activeCount', 'completedCount', 'overdueCount', 'bugReportsCount'));
    }
}

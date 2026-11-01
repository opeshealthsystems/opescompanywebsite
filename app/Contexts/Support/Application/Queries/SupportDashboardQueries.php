<?php

namespace App\Contexts\Support\Application\Queries;

use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;
class SupportDashboardQueries extends \App\Contexts\Shared\Application\WorkflowHandler
{
    public function index(\App\Contexts\Shared\Application\WorkflowInput $workflowInput): \App\Contexts\Shared\Application\WorkflowResult
    {
        $me = \App\Contexts\Shared\Application\CurrentWorkflow::id();
        $myOpenCount = \App\Models\Ticket::where('assigned_to', $me)->whereIn('status', ['open', 'in_progress', 'pending_customer'])->count();
        $myResolvedToday = \App\Models\Ticket::where('assigned_to', $me)->where('status', 'resolved')->whereDate('resolved_at', today())->count();
        $unassignedCount = \App\Models\Ticket::whereNull('assigned_to')->whereIn('status', ['open', 'in_progress'])->count();
        $slaBreachedCount = \App\Models\Ticket::where('assigned_to', $me)->where('sla_resolution_due_at', '<', now())->whereNotIn('status', ['resolved', 'closed'])->count();
        $priorityOrder = "CASE priority WHEN 'urgent' THEN 1 WHEN 'high' THEN 2 WHEN 'medium' THEN 3 ELSE 4 END";
        $myQueue = $workflowInput->collect(\App\Models\Ticket::where('assigned_to', $me)->whereIn('status', ['open', 'in_progress', 'pending_customer'])->with('customer')->orderByRaw($priorityOrder)->take(8));
        $unassigned = $workflowInput->collect(\App\Models\Ticket::whereNull('assigned_to')->whereIn('status', ['open'])->with('customer')->orderByRaw($priorityOrder)->take(5));
        return \App\Contexts\Shared\Application\WorkflowResult::read('support_dashboardcontroller_index_1', compact('myOpenCount', 'myResolvedToday', 'unassignedCount', 'slaBreachedCount', 'myQueue', 'unassigned'));
    }
}

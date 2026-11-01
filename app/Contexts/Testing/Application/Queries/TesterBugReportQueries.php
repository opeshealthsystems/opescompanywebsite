<?php

namespace App\Contexts\Testing\Application\Queries;

use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;
class TesterBugReportQueries extends \App\Contexts\Shared\Application\WorkflowHandler
{
    public function index(\App\Contexts\Shared\Application\WorkflowInput $workflowInput): \App\Contexts\Shared\Application\WorkflowResult
    {
        $reports = $workflowInput->paginate(\App\Models\Ticket::where('user_id', \App\Contexts\Shared\Application\CurrentWorkflow::id())->where('type', 'bug_report')->with('testerAssignment')->orderByDesc('created_at'), 20);
        return \App\Contexts\Shared\Application\WorkflowResult::read('tester_bugreportcontroller_index_1', compact('reports'));
    }
}

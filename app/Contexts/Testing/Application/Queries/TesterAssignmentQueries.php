<?php

namespace App\Contexts\Testing\Application\Queries;

use App\Models\Ticket;
use App\Models\TesterAssignment;
use Illuminate\Support\Facades\Auth;
class TesterAssignmentQueries extends \App\Contexts\Shared\Application\WorkflowHandler
{
    public function index(\App\Contexts\Shared\Application\WorkflowInput $workflowInput): \App\Contexts\Shared\Application\WorkflowResult
    {
        $user = \App\Contexts\Shared\Application\CurrentWorkflow::actor();
        $assignments = $workflowInput->paginate(\App\Models\TesterAssignment::where('assigned_to', $user->id)->orderByDesc('created_at'), 15);
        return \App\Contexts\Shared\Application\WorkflowResult::read('tester_assignmentcontroller_index_1', compact('assignments'));
    }
    public function show(\App\Contexts\Shared\Application\WorkflowInput $request): \App\Contexts\Shared\Application\WorkflowResult
    {
        $id = (int) $request->route('id');
        $assignment = \App\Models\TesterAssignment::with('bugReports')->findOrFail($id);
        $this->authorize('view', $assignment);
        return \App\Contexts\Shared\Application\WorkflowResult::read('tester_assignmentcontroller_show_1', compact('assignment'));
    }
}

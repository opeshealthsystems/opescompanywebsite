<?php

namespace App\Contexts\Testing\Application\Commands;

use App\Models\Ticket;
use App\Models\TesterAssignment;
use Illuminate\Support\Facades\Auth;
class TesterAssignmentCommands extends \App\Contexts\Shared\Application\WorkflowHandler
{
    public function storeBugReport(\App\Contexts\Shared\Application\WorkflowInput $request): \App\Contexts\Shared\Application\WorkflowResult
    {
        $user = \App\Contexts\Shared\Application\CurrentWorkflow::actor();
        $id = (int) $request->route('id');
        $assignment = \App\Models\TesterAssignment::findOrFail($id);
        $this->authorize('update', $assignment);
        abort_unless($assignment->isActive(), 403, 'Cannot file a bug report on a completed or cancelled assignment.');
        $validated = $request->validate(['subject' => 'required|string|max:255', 'description' => 'required|string|max:10000', 'priority' => 'required|in:low,medium,high,urgent']);
        \App\Models\Ticket::create(array_merge($validated, ['user_id' => $user->id, 'type' => 'bug_report', 'status' => 'open', 'tester_assignment_id' => $assignment->id]));
        return \App\Contexts\Shared\Application\WorkflowResult::next('tester_assignmentcontroller_storeBugReport_1', ['locale' => $request->locale(), 'id' => $assignment->id])->with('success', 'Bug report filed and sent to support.');
    }
    public function updateStatus(\App\Contexts\Shared\Application\WorkflowInput $request): \App\Contexts\Shared\Application\WorkflowResult
    {
        $id = (int) $request->route('id');
        $assignment = \App\Models\TesterAssignment::findOrFail($id);
        $this->authorize('update', $assignment);
        $validated = $request->validate(['status' => 'required|in:pending,in_progress,completed,cancelled']);
        $assignment->update(['status' => $validated['status']]);
        return \App\Contexts\Shared\Application\WorkflowResult::next('tester_assignmentcontroller_updateStatus_1', ['locale' => $request->locale(), 'id' => $assignment->id])->with('success', 'Assignment status updated.');
    }
}

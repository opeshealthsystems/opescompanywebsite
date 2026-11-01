<?php

namespace App\Contexts\Validation\Application\Commands;

use App\Models\IssueReport;
class PractitionerValidationRetestCommands extends \App\Contexts\Shared\Application\WorkflowHandler
{
    public function store(\App\Contexts\Shared\Application\WorkflowInput $request, $locale, \App\Models\IssueReport $issue): \App\Contexts\Shared\Application\WorkflowResult
    {
        abort_unless($issue->cohortMember?->user_id === \App\Contexts\Shared\Application\CurrentWorkflow::id(), 403);
        abort_unless($issue->status === 'ready_for_retest', 422, 'Issue is not awaiting retest.');
        $validated = $request->validate(['result' => 'required|in:passed,failed', 'notes' => 'required|string|max:3000', 'attachments.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10240']);
        $paths = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $paths[] = $file->store('validation/retests', 'public');
            }
        }
        $issue->recordRetest($issue->cohort_member_id, $validated['result'], $validated['notes'], $paths ?: null);
        return \App\Contexts\Shared\Application\WorkflowResult::next('practitioner_validation_retestcontroller_store_1', ['locale' => $request->locale(), 'issue' => $issue->id])->with('success', 'Retest submitted.');
    }
}

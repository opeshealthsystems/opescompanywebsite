<?php

namespace App\Http\Controllers\Practitioner\Validation;

use App\Http\Controllers\Controller;
use App\Models\IssueReport;
use Illuminate\Http\Request;

class RetestController extends Controller
{
    public function store(Request $request, $locale, IssueReport $issue)
    {
        abort_unless($issue->cohortMember?->user_id === auth()->id(), 403);
        abort_unless($issue->status === 'ready_for_retest', 422, 'Issue is not awaiting retest.');

        $validated = $request->validate([
            'result'        => 'required|in:passed,failed',
            'notes'         => 'required|string|max:3000',
            'attachments.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10240',
        ]);

        $paths = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $paths[] = $file->store('validation/retests', 'public');
            }
        }

        $issue->recordRetest($issue->cohort_member_id, $validated['result'], $validated['notes'], $paths ?: null);

        return redirect()
            ->route('practitioner.validation.issues.show', ['locale' => app()->getLocale(), 'issue' => $issue->id])
            ->with('success', 'Retest submitted.');
    }
}

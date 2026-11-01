<?php

namespace App\Contexts\Programmes\Application\Queries;

use App\Models\PractitionerApplication;
class PractitionerApplicationQueries extends \App\Contexts\Shared\Application\WorkflowHandler
{
    public function index(\App\Contexts\Shared\Application\WorkflowInput $workflowInput): \App\Contexts\Shared\Application\WorkflowResult
    {
        $applications = $workflowInput->collect(\App\Models\PractitionerApplication::where('practitioner_id', \App\Contexts\Shared\Application\CurrentWorkflow::id())->with('program')->latest());
        return \App\Contexts\Shared\Application\WorkflowResult::read('practitioner_applicationcontroller_index_1', compact('applications'));
    }
    public function show(\App\Contexts\Shared\Application\WorkflowInput $workflowInput, $locale, \App\Models\PractitionerApplication $application): \App\Contexts\Shared\Application\WorkflowResult
    {
        $this->authorize('view', $application);
        $application->load('program', 'findings');
        return \App\Contexts\Shared\Application\WorkflowResult::read('practitioner_applicationcontroller_show_1', compact('application'));
    }
}

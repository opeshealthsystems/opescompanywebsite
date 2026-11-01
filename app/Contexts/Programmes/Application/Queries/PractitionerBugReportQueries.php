<?php

namespace App\Contexts\Programmes\Application\Queries;

use App\Filament\Resources\PractitionerBugReportResource;
use App\Models\PractitionerBugReport;
use App\Support\AdminNotifier;
class PractitionerBugReportQueries extends \App\Contexts\Shared\Application\WorkflowHandler
{
    public function index(\App\Contexts\Shared\Application\WorkflowInput $workflowInput): \App\Contexts\Shared\Application\WorkflowResult
    {
        $bugReports = $workflowInput->collect(\App\Contexts\Shared\Application\CurrentWorkflow::actor()->practitionerBugReports()->latest());
        return \App\Contexts\Shared\Application\WorkflowResult::read('practitioner_bugreportcontroller_index_1', compact('bugReports'));
    }
    public function create(\App\Contexts\Shared\Application\WorkflowInput $workflowInput): \App\Contexts\Shared\Application\WorkflowResult
    {
        $severityOptions = \App\Models\PractitionerBugReport::severityOptions();
        return \App\Contexts\Shared\Application\WorkflowResult::read('practitioner_bugreportcontroller_create_1', compact('severityOptions'));
    }
    public function show(\App\Contexts\Shared\Application\WorkflowInput $workflowInput, $locale, \App\Models\PractitionerBugReport $bugReport): \App\Contexts\Shared\Application\WorkflowResult
    {
        abort_unless($bugReport->practitioner_id === \App\Contexts\Shared\Application\CurrentWorkflow::id(), 403);
        return \App\Contexts\Shared\Application\WorkflowResult::read('practitioner_bugreportcontroller_show_1', compact('bugReport'));
    }
}

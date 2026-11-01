<?php

namespace App\Contexts\Programmes\Application\Commands;

use App\Filament\Resources\PractitionerBugReportResource;
use App\Models\PractitionerBugReport;
use App\Support\AdminNotifier;
class PractitionerBugReportCommands extends \App\Contexts\Shared\Application\WorkflowHandler
{
    public function store(\App\Contexts\Shared\Application\WorkflowInput $request): \App\Contexts\Shared\Application\WorkflowResult
    {
        $data = $request->validate(['title' => 'required|string|max:200', 'severity' => 'required|string|in:' . implode(',', array_keys(\App\Models\PractitionerBugReport::severityOptions())), 'description' => 'required|string|min:10', 'steps_to_reproduce' => 'nullable|string', 'screenshot_url' => 'nullable|url', 'screenshot' => 'nullable|image|max:4096', 'product_slug' => 'nullable|string|max:100']);
        unset($data['screenshot']);
        if ($request->hasFile('screenshot')) {
            $data['screenshot_path'] = $request->file('screenshot')->store('bug-report-screenshots', 'public');
        }
        $bugReport = \App\Contexts\Shared\Application\CurrentWorkflow::actor()->practitionerBugReports()->create($data);
        \App\Support\AdminNotifier::notify('New bug report', '[' . $bugReport->severity . '] ' . $bugReport->title, \App\Filament\Resources\PractitionerBugReportResource::getUrl('view', ['record' => $bugReport]), ['super_admin', 'admin', 'support']);
        return \App\Contexts\Shared\Application\WorkflowResult::next('practitioner_bugreportcontroller_store_1', ['locale' => $request->locale()])->with('success', 'Your bug report has been submitted. Thank you!');
    }
}

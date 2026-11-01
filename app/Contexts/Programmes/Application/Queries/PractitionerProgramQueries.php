<?php

namespace App\Contexts\Programmes\Application\Queries;

use App\Filament\Resources\PractitionerApplicationResource;
use App\Mail\PractitionerApplicationReceived;
use App\Models\PractitionerApplication;
use App\Models\PractitionerProgram;
use App\Support\AdminNotifier;
use Illuminate\Support\Facades\Mail;
class PractitionerProgramQueries extends \App\Contexts\Shared\Application\WorkflowHandler
{
    public function index(\App\Contexts\Shared\Application\WorkflowInput $workflowInput): \App\Contexts\Shared\Application\WorkflowResult
    {
        $programs = $workflowInput->collect(\App\Models\PractitionerProgram::where('status', 'open')->withCount('applications')->orderByDesc('created_at'));
        $myApplicationProgramIds = \App\Models\PractitionerApplication::where('practitioner_id', \App\Contexts\Shared\Application\CurrentWorkflow::id())->pluck('program_id')->toArray();
        return \App\Contexts\Shared\Application\WorkflowResult::read('practitioner_programcontroller_index_1', compact('programs', 'myApplicationProgramIds'));
    }
    public function show(\App\Contexts\Shared\Application\WorkflowInput $workflowInput, $locale, \App\Models\PractitionerProgram $program): \App\Contexts\Shared\Application\WorkflowResult
    {
        $program->loadCount('applications');
        $myApplication = \App\Models\PractitionerApplication::where('practitioner_id', \App\Contexts\Shared\Application\CurrentWorkflow::id())->where('program_id', $program->id)->first();
        return \App\Contexts\Shared\Application\WorkflowResult::read('practitioner_programcontroller_show_1', compact('program', 'myApplication'));
    }
}

<?php

namespace App\Contexts\Programmes\Application\Commands;

use App\Filament\Resources\PractitionerApplicationResource;
use App\Mail\PractitionerApplicationReceived;
use App\Models\PractitionerApplication;
use App\Models\PractitionerProgram;
use App\Support\AdminNotifier;
use Illuminate\Support\Facades\Mail;
class PractitionerProgramCommands extends \App\Contexts\Shared\Application\WorkflowHandler
{
    public function apply(\App\Contexts\Shared\Application\WorkflowInput $request, $locale, \App\Models\PractitionerProgram $program): \App\Contexts\Shared\Application\WorkflowResult
    {
        abort_unless($program->isOpen(), 403, 'This programme is not accepting applications.');
        abort_if($program->isFull(), 403, 'This programme has reached its maximum participants.');
        if ($program->type === 'paid') {
            abort_unless(\App\Contexts\Shared\Application\CurrentWorkflow::actor()->practitionerTier()->canApplyToPaid(), 403, 'Paid programmes are open to verified practitioners. Complete your profile and request verification to apply.');
        }
        $exists = \App\Models\PractitionerApplication::where('practitioner_id', \App\Contexts\Shared\Application\CurrentWorkflow::id())->where('program_id', $program->id)->exists();
        abort_if($exists, 422, 'You have already applied to this programme.');
        $validated = $request->validate(['motivation' => 'nullable|string|max:2000']);
        $application = \App\Models\PractitionerApplication::create(['practitioner_id' => \App\Contexts\Shared\Application\CurrentWorkflow::id(), 'program_id' => $program->id, 'motivation' => $validated['motivation'] ?? null, 'status' => 'pending']);
        \App\Contexts\Shared\Infrastructure\DomainMail::to(\App\Contexts\Shared\Application\CurrentWorkflow::actor()->email)->queue(new \App\Mail\PractitionerApplicationReceived($application));
        \App\Contexts\Shared\Application\CurrentWorkflow::actor()->notify(new \App\Notifications\FeedEntry('practitioner.application_received', 'Application received', 'We received your application to ' . $program->title . '.', 'clipboard-document', route('practitioner.applications', ['locale' => 'en'])));
        \App\Support\AdminNotifier::notify('New programme application', \App\Contexts\Shared\Application\CurrentWorkflow::actor()->name . ' applied to: ' . $program->title, \App\Filament\Resources\PractitionerApplicationResource::getUrl('view', ['record' => $application]));
        return \App\Contexts\Shared\Application\WorkflowResult::next('practitioner_programcontroller_apply_1', ['locale' => $request->locale()])->with('success', 'Your application has been submitted successfully.');
    }
}

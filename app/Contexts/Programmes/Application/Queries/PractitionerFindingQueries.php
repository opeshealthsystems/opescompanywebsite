<?php

namespace App\Contexts\Programmes\Application\Queries;

use App\Filament\Resources\PractitionerFindingResource;
use App\Models\PractitionerApplication;
use App\Models\PractitionerFinding;
use App\Support\AdminNotifier;
class PractitionerFindingQueries extends \App\Contexts\Shared\Application\WorkflowHandler
{
    public function create(\App\Contexts\Shared\Application\WorkflowInput $workflowInput, $locale, \App\Models\PractitionerApplication $application): \App\Contexts\Shared\Application\WorkflowResult
    {
        $this->authorize('view', $application);
        if (!\App\Contexts\Shared\Application\CurrentWorkflow::actor()->isVerifiedPractitioner()) {
            abort(403, 'Your practitioner profile must be verified before submitting findings.');
        }
        abort_unless($application->status === 'approved', 403, 'Only approved applications can submit findings.');
        return \App\Contexts\Shared\Application\WorkflowResult::read('practitioner_findingcontroller_create_1', compact('application'));
    }
}

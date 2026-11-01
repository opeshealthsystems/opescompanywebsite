<?php

namespace App\Contexts\Validation\Application\Queries;

use App\Models\DailyTestSession;
class PractitionerValidationSessionQueries extends \App\Contexts\Shared\Application\WorkflowHandler
{
    use \App\Contexts\Validation\Domain\ResolvesCohortScope;
    public function index(\App\Contexts\Shared\Application\WorkflowInput $workflowInput): \App\Contexts\Shared\Application\WorkflowResult
    {
        $member = $this->activeMember();
        if (!$member) {
            return \App\Contexts\Shared\Application\WorkflowResult::next('practitioner_validation_sessioncontroller_index_1', ['locale' => $workflowInput->locale()])->with('notice', 'You have not been placed in a validation cohort yet.');
        }
        $sessions = $workflowInput->paginate($member->dailyTestSessions()->latest('date'), 15);
        return \App\Contexts\Shared\Application\WorkflowResult::read('practitioner_validation_sessioncontroller_index_2', compact('sessions', 'member'));
    }
    public function create(\App\Contexts\Shared\Application\WorkflowInput $workflowInput): \App\Contexts\Shared\Application\WorkflowResult
    {
        $member = $this->activeMember();
        if (!$member) {
            return \App\Contexts\Shared\Application\WorkflowResult::next('practitioner_validation_sessioncontroller_create_1', ['locale' => $workflowInput->locale()])->with('notice', 'You have not been placed in a validation cohort yet.');
        }
        [$products, $modules, $workflows] = $this->scopedCatalog($member);
        return \App\Contexts\Shared\Application\WorkflowResult::read('practitioner_validation_sessioncontroller_create_2', compact('products', 'modules', 'workflows', 'member'));
    }
}

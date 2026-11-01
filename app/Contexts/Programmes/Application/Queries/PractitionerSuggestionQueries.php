<?php

namespace App\Contexts\Programmes\Application\Queries;

use App\Filament\Resources\SuggestionResource;
use App\Models\Suggestion;
use App\Support\AdminNotifier;
class PractitionerSuggestionQueries extends \App\Contexts\Shared\Application\WorkflowHandler
{
    public function index(\App\Contexts\Shared\Application\WorkflowInput $workflowInput): \App\Contexts\Shared\Application\WorkflowResult
    {
        $suggestions = $workflowInput->collect(\App\Contexts\Shared\Application\CurrentWorkflow::actor()->suggestions()->latest());
        return \App\Contexts\Shared\Application\WorkflowResult::read('practitioner_suggestioncontroller_index_1', compact('suggestions'));
    }
    public function create(\App\Contexts\Shared\Application\WorkflowInput $workflowInput): \App\Contexts\Shared\Application\WorkflowResult
    {
        $categoryOptions = \App\Models\Suggestion::categoryOptions();
        return \App\Contexts\Shared\Application\WorkflowResult::read('practitioner_suggestioncontroller_create_1', compact('categoryOptions'));
    }
}

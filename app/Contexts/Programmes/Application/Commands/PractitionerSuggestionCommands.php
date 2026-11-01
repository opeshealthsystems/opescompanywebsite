<?php

namespace App\Contexts\Programmes\Application\Commands;

use App\Filament\Resources\SuggestionResource;
use App\Models\Suggestion;
use App\Support\AdminNotifier;
class PractitionerSuggestionCommands extends \App\Contexts\Shared\Application\WorkflowHandler
{
    public function store(\App\Contexts\Shared\Application\WorkflowInput $request): \App\Contexts\Shared\Application\WorkflowResult
    {
        $data = $request->validate(['title' => 'required|string|max:200', 'category' => 'required|string|in:' . implode(',', array_keys(\App\Models\Suggestion::categoryOptions())), 'body' => 'required|string|min:20']);
        $suggestion = \App\Contexts\Shared\Application\CurrentWorkflow::actor()->suggestions()->create($data);
        \App\Support\AdminNotifier::notify('New suggestion submitted', \App\Contexts\Shared\Application\CurrentWorkflow::actor()->name . ' submitted: ' . $suggestion->title, \App\Filament\Resources\SuggestionResource::getUrl('view', ['record' => $suggestion]));
        return \App\Contexts\Shared\Application\WorkflowResult::next('practitioner_suggestioncontroller_store_1', ['locale' => $request->locale()])->with('success', 'Your suggestion has been submitted. Thank you!');
    }
}

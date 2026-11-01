<?php

namespace App\Contexts\Documents\Application\Queries;

use App\Models\Document;
use Illuminate\Support\Facades\Auth;
class CustomerDocumentQueries extends \App\Contexts\Shared\Application\WorkflowHandler
{
    public function index(\App\Contexts\Shared\Application\WorkflowInput $workflowInput): \App\Contexts\Shared\Application\WorkflowResult
    {
        $user = \App\Contexts\Shared\Application\CurrentWorkflow::actor();
        $documents = $workflowInput->paginate(\App\Models\Document::where('addressee_user_id', $user->id)->whereNotIn('status', ['draft'])->orderByDesc('created_at'), 15);
        return \App\Contexts\Shared\Application\WorkflowResult::read('customer_documentcontroller_index_1', compact('documents'));
    }
    public function show(\App\Contexts\Shared\Application\WorkflowInput $request): \App\Contexts\Shared\Application\WorkflowResult
    {
        $id = (int) $request->route('id');
        $user = \App\Contexts\Shared\Application\CurrentWorkflow::actor();
        $document = \App\Models\Document::findOrFail($id);
        abort_if($document->addressee_user_id !== $user->id, 403);
        abort_if(in_array($document->status, ['draft']), 403);
        return \App\Contexts\Shared\Application\WorkflowResult::read('customer_documentcontroller_show_1', compact('document'));
    }
}

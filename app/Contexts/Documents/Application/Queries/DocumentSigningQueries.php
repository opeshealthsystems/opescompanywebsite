<?php

namespace App\Contexts\Documents\Application\Queries;

use App\Models\Document;
class DocumentSigningQueries extends \App\Contexts\Shared\Application\WorkflowHandler
{
    public function show(\App\Contexts\Shared\Application\WorkflowInput $workflowInput, string $token): \App\Contexts\Shared\Application\WorkflowResult
    {
        $document = \App\Models\Document::where('signature_token', $token)->firstOrFail();
        if (!$document->isSigningTokenValid()) {
            return \App\Contexts\Shared\Application\WorkflowResult::read('documentsigningcontroller_show_1', ['document' => $document, 'expired' => true]);
        }
        return \App\Contexts\Shared\Application\WorkflowResult::read('documentsigningcontroller_show_2', ['document' => $document, 'expired' => false]);
    }
}

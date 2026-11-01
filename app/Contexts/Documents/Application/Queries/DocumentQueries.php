<?php

namespace App\Contexts\Documents\Application\Queries;

use App\Models\Document;
use Barryvdh\DomPDF\Facade\Pdf;
class DocumentQueries extends \App\Contexts\Shared\Application\WorkflowHandler
{
    public function pdf(\App\Contexts\Shared\Application\WorkflowInput $request, \App\Models\Document $document): \App\Contexts\Shared\Application\WorkflowResult
    {
        $user = $request->user();
        $canAccess = $user->hasAnyRole(['super_admin', 'admin']) || $document->addressee_user_id && (int) $document->addressee_user_id === $user->id;
        abort_unless($canAccess, 403);
        $pdf = \App\Contexts\Shared\Infrastructure\PdfRenderer::document('documents.pdf', compact('document'))->setPaper('a4', 'portrait');
        return \App\Contexts\Shared\Application\WorkflowResult::download($pdf->output(), $document->reference_number . '.pdf');
    }
    public function preview(\App\Contexts\Shared\Application\WorkflowInput $request, \App\Models\Document $document): \App\Contexts\Shared\Application\WorkflowResult
    {
        $user = $request->user();
        $canAccess = $user->hasAnyRole(['super_admin', 'admin']) || $document->addressee_user_id && (int) $document->addressee_user_id === $user->id;
        abort_unless($canAccess, 403);
        return \App\Contexts\Shared\Application\WorkflowResult::read('documentcontroller_preview_1', compact('document'));
    }
}

<?php

namespace App\Contexts\Publishing\Application\Queries;

use App\Models\Contract;
use Barryvdh\DomPDF\Facade\Pdf;
class ContractQueries extends \App\Contexts\Shared\Application\WorkflowHandler
{
    public function pdf(\App\Contexts\Shared\Application\WorkflowInput $request, \App\Models\Contract $contract): \App\Contexts\Shared\Application\WorkflowResult
    {
        abort_unless($request->user()?->hasAnyRole(['super_admin', 'admin']), 403);
        $pdf = \App\Contexts\Shared\Infrastructure\PdfRenderer::document('contracts.pdf', compact('contract'))->setPaper('a4', 'portrait');
        $filename = strtolower($contract->reference) . '.pdf';
        return \App\Contexts\Shared\Application\WorkflowResult::download($pdf->output(), $filename);
    }
}

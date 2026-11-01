<?php

namespace App\Contexts\Finance\Application\Queries;

use App\Models\Quote;
use Barryvdh\DomPDF\Facade\Pdf;
class QuoteQueries extends \App\Contexts\Shared\Application\WorkflowHandler
{
    public function pdf(\App\Contexts\Shared\Application\WorkflowInput $request, \App\Models\Quote $quote): \App\Contexts\Shared\Application\WorkflowResult
    {
        abort_unless($request->user()?->hasAnyRole(['super_admin', 'admin']), 403);
        $quote->load('items', 'lead');
        $pdf = \App\Contexts\Shared\Infrastructure\PdfRenderer::document('quotes.pdf', compact('quote'))->setPaper('a4', 'portrait');
        $filename = strtolower($quote->reference) . '.pdf';
        return \App\Contexts\Shared\Application\WorkflowResult::download($pdf->output(), $filename);
    }
}

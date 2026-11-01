<?php

namespace App\Contexts\Finance\Application\Queries;

use App\Models\PurchaseOrder;
use Barryvdh\DomPDF\Facade\Pdf;
class PurchaseOrderQueries extends \App\Contexts\Shared\Application\WorkflowHandler
{
    public function pdf(\App\Contexts\Shared\Application\WorkflowInput $request, \App\Models\PurchaseOrder $purchaseOrder): \App\Contexts\Shared\Application\WorkflowResult
    {
        abort_unless($request->user()?->hasAnyRole(['super_admin', 'admin']), 403);
        $purchaseOrder->load('items', 'vendor', 'requester', 'approver');
        $pdf = \App\Contexts\Shared\Infrastructure\PdfRenderer::document('purchase-orders.pdf', compact('purchaseOrder'))->setPaper('a4', 'portrait');
        $filename = strtolower(str_replace('/', '-', $purchaseOrder->reference)) . '.pdf';
        return \App\Contexts\Shared\Application\WorkflowResult::download($pdf->output(), $filename);
    }
}

<?php

namespace App\Contexts\Finance\Application\Queries;

use App\Models\SupplierBill;
use Barryvdh\DomPDF\Facade\Pdf;
class SupplierBillQueries extends \App\Contexts\Shared\Application\WorkflowHandler
{
    public function pdf(\App\Contexts\Shared\Application\WorkflowInput $request, \App\Models\SupplierBill $supplierBill): \App\Contexts\Shared\Application\WorkflowResult
    {
        abort_unless($request->user()?->hasAnyRole(['super_admin', 'admin']), 403);
        $supplierBill->load('items', 'vendor', 'purchaseOrder');
        $pdf = \App\Contexts\Shared\Infrastructure\PdfRenderer::document('supplier-bills.pdf', ['bill' => $supplierBill])->setPaper('a4', 'portrait');
        $filename = strtolower(str_replace('/', '-', $supplierBill->reference)) . '.pdf';
        return \App\Contexts\Shared\Application\WorkflowResult::download($pdf->output(), $filename);
    }
}

<?php

namespace App\Contexts\Finance\Application\Queries;

use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
class InvoiceQueries extends \App\Contexts\Shared\Application\WorkflowHandler
{
    public function pdf(\App\Contexts\Shared\Application\WorkflowInput $request): \App\Contexts\Shared\Application\WorkflowResult
    {
        $user = \App\Contexts\Shared\Application\CurrentWorkflow::actor();
        $id = (int) $request->route('invoice');
        $invoice = \App\Models\Invoice::with('items', 'customer', 'issuer')->findOrFail($id);
        if ($user->hasAnyRole(['super_admin', 'admin', 'support', 'accountant'])) {
            // Admin/support can download any invoice
        } elseif ($user->hasRole('customer')) {
            abort_if((int) $invoice->customer_id !== $user->id, 403);
        } else {
            abort(403);
        }
        $pdf = \App\Contexts\Shared\Infrastructure\PdfRenderer::document('invoices.pdf', compact('invoice'));
        return \App\Contexts\Shared\Application\WorkflowResult::download($pdf->output(), 'invoice-' . $invoice->invoice_number . '.pdf');
    }
}

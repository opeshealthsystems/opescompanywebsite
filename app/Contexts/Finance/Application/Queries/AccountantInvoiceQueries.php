<?php

namespace App\Contexts\Finance\Application\Queries;

use App\Models\Invoice;
class AccountantInvoiceQueries extends \App\Contexts\Shared\Application\WorkflowHandler
{
    public function index(\App\Contexts\Shared\Application\WorkflowInput $request): \App\Contexts\Shared\Application\WorkflowResult
    {
        $invoices = $request->paginate(\App\Models\Invoice::with('customer')->when($request->status, fn($q) => $q->where('status', $request->status))->when($request->from, fn($q) => $q->where('created_at', '>=', $request->from))->when($request->to, fn($q) => $q->where('created_at', '<=', $request->to . ' 23:59:59'))->orderByDesc('created_at'), 25);
        return \App\Contexts\Shared\Application\WorkflowResult::read('accountant_invoicecontroller_index_1', compact('invoices'));
    }
    public function show(\App\Contexts\Shared\Application\WorkflowInput $workflowInput, \App\Models\Invoice $invoice): \App\Contexts\Shared\Application\WorkflowResult
    {
        $invoice->load(['customer', 'items', 'issuer']);
        return \App\Contexts\Shared\Application\WorkflowResult::read('accountant_invoicecontroller_show_1', compact('invoice'));
    }
}

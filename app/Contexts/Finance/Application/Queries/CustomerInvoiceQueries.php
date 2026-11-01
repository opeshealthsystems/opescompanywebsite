<?php

namespace App\Contexts\Finance\Application\Queries;

use App\Models\Invoice;
use Illuminate\Support\Facades\Auth;
class CustomerInvoiceQueries extends \App\Contexts\Shared\Application\WorkflowHandler
{
    public function index(\App\Contexts\Shared\Application\WorkflowInput $workflowInput): \App\Contexts\Shared\Application\WorkflowResult
    {
        $user = \App\Contexts\Shared\Application\CurrentWorkflow::actor();
        $invoices = $workflowInput->paginate(\App\Models\Invoice::where('customer_id', $user->id)->whereIn('status', ['sent', 'paid', 'overdue'])->orderByDesc('created_at'), 15);
        return \App\Contexts\Shared\Application\WorkflowResult::read('customer_invoicecontroller_index_1', compact('invoices'));
    }
    public function show(\App\Contexts\Shared\Application\WorkflowInput $request): \App\Contexts\Shared\Application\WorkflowResult
    {
        $user = \App\Contexts\Shared\Application\CurrentWorkflow::actor();
        $id = (int) $request->route('id');
        $invoice = \App\Models\Invoice::with('items')->findOrFail($id);
        abort_if((int) $invoice->customer_id !== $user->id, 403);
        return \App\Contexts\Shared\Application\WorkflowResult::read('customer_invoicecontroller_show_1', compact('invoice'));
    }
}

<?php

namespace App\Contexts\Finance\Application\Commands;

use App\Models\Invoice;
class AccountantInvoiceCommands extends \App\Contexts\Shared\Application\WorkflowHandler
{
    public function markPaid(\App\Contexts\Shared\Application\WorkflowInput $workflowInput, \App\Models\Invoice $invoice): \App\Contexts\Shared\Application\WorkflowResult
    {
        app(\App\Contexts\Finance\Application\RecordInvoicePayment::class)->record($workflowInput->user(), $invoice->id,
            ['payment_method' => 'other', 'payment_date' => today()->toDateString(), 'notes' => 'Outstanding amount confirmed received by an authorized accountant.'], true);
        return \App\Contexts\Shared\Application\WorkflowResult::next('accountant_invoicecontroller_markPaid_1')->with('success', 'Invoice marked as paid.');
    }
}

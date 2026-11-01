<?php

namespace App\Contexts\Finance\Application\Queries;

use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Models\CreditNote;
use App\Models\Invoice;
use App\Models\PurchaseOrder;
use App\Models\Quote;
use App\Models\SupplierBill;
use App\Models\User;

/** Detail queries shared by finance edit forms and API clients. */
final class AdminFinanceLineItemQueries extends WorkflowHandler
{
    private function admin(WorkflowInput $input): void
    {
        $user = $input->user();
        abort_unless($user instanceof User && $user->is_active && $user->hasAnyRole(['super_admin', 'admin']), 403);
    }

    private function items(WorkflowInput $input, mixed $items, array $fields): WorkflowResult
    {
        $this->admin($input);
        return WorkflowResult::payload(['items' => $input->collect($items)->map(fn ($item) => $item->only($fields))->values()->all()]);
    }

    public function invoiceItems(WorkflowInput $input, Invoice $invoice): WorkflowResult
    {
        return $this->items($input, $invoice->items()->orderBy('id'), ['description', 'quantity', 'unit_price']);
    }

    public function quoteItems(WorkflowInput $input, Quote $quote): WorkflowResult
    {
        return $this->items($input, $quote->items()->orderBy('id'), ['product_name', 'quantity', 'unit_price']);
    }

    public function supplierBillItems(WorkflowInput $input, SupplierBill $bill): WorkflowResult
    {
        return $this->items($input, $bill->items()->orderBy('id'), ['description', 'quantity', 'unit_price']);
    }

    public function purchaseOrderItems(WorkflowInput $input, PurchaseOrder $order): WorkflowResult
    {
        return $this->items($input, $order->items()->orderBy('id'), ['description', 'quantity', 'unit_price']);
    }

    public function creditNoteItems(WorkflowInput $input, CreditNote $creditNote): WorkflowResult
    {
        return $this->items($input, $creditNote->items()->orderBy('id'), ['description', 'quantity', 'unit_price']);
    }
}

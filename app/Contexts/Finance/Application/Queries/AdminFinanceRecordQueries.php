<?php

namespace App\Contexts\Finance\Application\Queries;

use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Models\Expense;
use App\Models\CreditNote;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\PayrollRun;
use App\Models\PayrollEntry;
use App\Models\PurchaseOrder;
use App\Models\Quote;
use App\Models\SupplierBill;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

final class AdminFinanceRecordQueries extends WorkflowHandler
{
    private function admin(WorkflowInput $input): User
    {
        $actor = $input->user();
        abort_unless($actor instanceof User && $actor->is_active && $actor->hasAnyRole(['super_admin', 'admin']), 403);
        return $actor;
    }

    private function respond(WorkflowInput $input, Builder $query): WorkflowResult
    {
        $page = $input->paginate($query);
        return WorkflowResult::payload(['items' => $page->getCollection(), 'pagination' => [
            'current_page' => $page->currentPage(), 'per_page' => $page->perPage(), 'total' => $page->total(), 'last_page' => $page->lastPage(),
        ]]);
    }

    public function purchaseOrderQuery(WorkflowInput $input): Builder
    {
        $this->admin($input);
        return PurchaseOrder::query()->with(['vendor', 'requester'])->orderByDesc('created_at');
    }

    public function purchaseOrders(WorkflowInput $input): WorkflowResult
    {
        $this->admin($input);
        $filters = $input->validate(['status' => 'sometimes|in:'.implode(',', array_keys(PurchaseOrder::statusOptions())), 'vendor_id' => 'sometimes|integer|exists:vendors,id']);
        $query = $this->purchaseOrderQuery($input);
        foreach (['status', 'vendor_id'] as $field) if (isset($filters[$field])) $query->where($field, $filters[$field]);
        return $this->respond($input, $query);
    }

    public function quoteQuery(WorkflowInput $input): Builder
    {
        $this->admin($input);
        return Quote::query()->with(['lead', 'creator', 'items'])->orderByDesc('created_at');
    }

    public function quotes(WorkflowInput $input): WorkflowResult
    {
        $this->admin($input);
        $filters = $input->validate(['status' => 'sometimes|in:'.implode(',', array_keys(Quote::statusOptions())), 'lead_id' => 'sometimes|integer|exists:leads,id']);
        $query = $this->quoteQuery($input);
        if (isset($filters['status'])) $query->where('status', $filters['status']);
        if (isset($filters['lead_id'])) $query->where('lead_id', $filters['lead_id']);
        return $this->respond($input, $query);
    }

    public function supplierBillQuery(WorkflowInput $input): Builder
    {
        $this->admin($input);
        return SupplierBill::query()->with('vendor')->orderByDesc('created_at');
    }

    public function supplierBills(WorkflowInput $input): WorkflowResult
    {
        $this->admin($input);
        $filters = $input->validate(['status' => 'sometimes|in:'.implode(',', array_keys(SupplierBill::statusOptions())), 'vendor_id' => 'sometimes|integer|exists:vendors,id']);
        $query = $this->supplierBillQuery($input);
        foreach (['status', 'vendor_id'] as $field) if (isset($filters[$field])) $query->where($field, $filters[$field]);
        return $this->respond($input, $query);
    }

    public function expenseQuery(WorkflowInput $input): Builder
    {
        $this->admin($input);
        return Expense::query()->with('submitter')->orderByDesc('created_at');
    }

    public function expenses(WorkflowInput $input): WorkflowResult
    {
        $this->admin($input);
        $filters = $input->validate(['status' => 'sometimes|in:'.implode(',', array_keys(Expense::statusOptions())), 'category' => 'sometimes|in:'.implode(',', array_keys(Expense::categoryOptions()))]);
        $query = $this->expenseQuery($input);
        foreach (['status', 'category'] as $field) if (isset($filters[$field])) $query->where($field, $filters[$field]);
        return $this->respond($input, $query);
    }

    public function payrollRunQuery(WorkflowInput $input): Builder
    {
        $this->admin($input);
        return PayrollRun::query()->with('processor')->withCount('entries')->orderByDesc('period_start');
    }

    public function payrollRuns(WorkflowInput $input): WorkflowResult
    {
        $this->admin($input);
        $filters = $input->validate(['status' => 'sometimes|in:'.implode(',', array_keys(PayrollRun::statusOptions())), 'year' => 'sometimes|integer|min:2000|max:2100']);
        $query = $this->payrollRunQuery($input);
        if (isset($filters['status'])) $query->where('status', $filters['status']);
        if (isset($filters['year'])) $query->whereYear('period_start', $filters['year']);
        return $this->respond($input, $query);
    }

    public function payrollRun(WorkflowInput $input, PayrollRun $run): WorkflowResult
    {
        $this->admin($input);
        $run = $this->payrollRunQuery($input)->whereKey($run->id)->firstOrFail();

        return WorkflowResult::payload([
            'run' => [
                'id' => $run->id,
                'reference' => $run->reference,
                'period_start' => $run->period_start?->toDateString(),
                'period_end' => $run->period_end?->toDateString(),
                'status' => $run->status,
                'total_gross' => (float) $run->total_gross,
                'total_deductions' => (float) PayrollEntry::query()->where('payroll_run_id', $run->id)->sum('total_deductions'),
                'total_net' => (float) $run->total_net,
                'currency' => $run->currency,
                'notes' => $run->notes,
                'processed_by' => $run->processed_by,
                'processor_name' => $run->processor?->name,
                'completed_at' => $run->completed_at?->toIso8601String(),
                'created_at' => $run->created_at?->toIso8601String(),
                'updated_at' => $run->updated_at?->toIso8601String(),
            ],
        ]);
    }

    public function invoiceRegisterQuery(WorkflowInput $input): Builder
    {
        $this->admin($input);
        return Invoice::query()->with('customer')->orderByDesc('created_at');
    }

    public function invoiceRegister(WorkflowInput $input): WorkflowResult
    {
        $this->admin($input);
        $filters = $input->validate(['status' => 'sometimes|in:draft,sent,paid,overdue,cancelled', 'customer_id' => 'sometimes|integer|exists:users,id']);
        $query = $this->invoiceRegisterQuery($input);
        foreach (['status', 'customer_id'] as $field) if (isset($filters[$field])) $query->where($field, $filters[$field]);
        return $this->respond($input, $query);
    }

    public function invoicePaymentsQuery(WorkflowInput $input, Invoice $invoice): Builder
    {
        $this->admin($input);

        return InvoicePayment::query()->where('invoice_id', $invoice->id)->with(['invoice:id,invoice_number,currency', 'recorder:id,name'])->orderByDesc('payment_date')->orderByDesc('id');
    }

    public function invoicePayments(WorkflowInput $input, Invoice $invoice): WorkflowResult
    {
        $query = $this->invoicePaymentsQuery($input, $invoice);
        $page = $input->paginate($query);

        return WorkflowResult::payload(['items' => $page->getCollection()->map(fn (InvoicePayment $payment) => [
            'id' => $payment->id,
            'invoice_id' => $payment->invoice_id,
            'amount' => $payment->amount,
            'currency' => $payment->invoice?->currency ?? 'XAF',
            'payment_method' => $payment->payment_method,
            'payment_date' => $payment->payment_date?->toDateString(),
            'reference_number' => $payment->reference_number,
            'recorded_by' => $payment->recorded_by,
            'recorder_name' => $payment->recorder?->name,
        ])->all(), 'pagination' => ['current_page' => $page->currentPage(), 'per_page' => $page->perPage(), 'total' => $page->total(), 'last_page' => $page->lastPage()]]);
    }

    public function payrollEntriesQuery(WorkflowInput $input, PayrollRun $run): Builder
    {
        $this->admin($input);

        return PayrollEntry::query()->where('payroll_run_id', $run->id)->with('employee:id,name')->orderBy('id');
    }

    public function payrollEntries(WorkflowInput $input, PayrollRun $run): WorkflowResult
    {
        $page = $input->paginate($this->payrollEntriesQuery($input, $run));

        return WorkflowResult::payload(['items' => $page->getCollection()->map(fn (PayrollEntry $entry) => [
            'id' => $entry->id,
            'payroll_run_id' => $entry->payroll_run_id,
            'user_id' => $entry->user_id,
            'employee_name' => $entry->employee?->name,
            'gross_salary' => $entry->gross_salary,
            'deductions' => $entry->deductions,
            'total_deductions' => $entry->total_deductions,
            'net_salary' => $entry->net_salary,
            'currency' => $entry->currency,
            'status' => $entry->status,
        ])->all(), 'pagination' => ['current_page' => $page->currentPage(), 'per_page' => $page->perPage(), 'total' => $page->total(), 'last_page' => $page->lastPage()]]);
    }

    public function creditNoteQuery(WorkflowInput $input): Builder
    {
        $this->admin($input);
        return CreditNote::query()->with(['invoice', 'creator', 'items'])->orderByDesc('created_at');
    }

    public function creditNotes(WorkflowInput $input): WorkflowResult
    {
        $this->admin($input);
        $filters = $input->validate([
            'status' => 'sometimes|in:draft,issued,applied,void',
            'invoice_id' => 'sometimes|integer|exists:invoices,id',
            'search' => 'sometimes|string|max:120',
        ]);
        $query = $this->creditNoteQuery($input);
        if (isset($filters['status'])) $query->where('status', $filters['status']);
        if (isset($filters['invoice_id'])) $query->where('invoice_id', $filters['invoice_id']);
        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(fn (Builder $builder) => $builder
                ->where('reference', 'like', '%'.$search.'%')
                ->orWhere('reason', 'like', '%'.$search.'%'));
        }
        $page = $input->paginate($query);
        $items = $page->getCollection()->map(fn (CreditNote $creditNote) => [
            'id' => $creditNote->id,
            'reference' => $creditNote->reference,
            'invoice_id' => $creditNote->invoice_id,
            'reason' => $creditNote->reason,
            'status' => $creditNote->status,
            'subtotal' => $creditNote->subtotal,
            'tax_amount' => $creditNote->tax_amount,
            'total' => $creditNote->total,
            'currency' => $creditNote->currency,
            'issued_at' => $creditNote->issued_at?->toDateString(),
            'notes' => $creditNote->notes,
            'created_by' => $creditNote->created_by,
            'created_at' => $creditNote->created_at?->toIso8601String(),
            'updated_at' => $creditNote->updated_at?->toIso8601String(),
            'invoice' => $creditNote->invoice ? [
                'id' => $creditNote->invoice->id,
                'invoice_number' => $creditNote->invoice->invoice_number,
                'currency' => $creditNote->invoice->currency,
                'grand_total' => $creditNote->invoice->grand_total,
            ] : null,
            'creator' => $creditNote->creator ? [
                'id' => $creditNote->creator->id,
                'name' => $creditNote->creator->name,
            ] : null,
            'items' => $creditNote->items->map(fn ($item) => [
                'id' => $item->id,
                'description' => $item->description,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'total' => $item->total,
            ])->values(),
        ])->values();

        return WorkflowResult::payload(['items' => $items, 'pagination' => [
            'current_page' => $page->currentPage(), 'per_page' => $page->perPage(), 'total' => $page->total(), 'last_page' => $page->lastPage(),
        ]]);
    }

    public function summary(WorkflowInput $input): WorkflowResult
    {
        $this->admin($input);
        return WorkflowResult::payload([
            'purchase_orders_needing_action' => PurchaseOrder::whereIn('status', ['draft', 'submitted'])->count(),
            'open_quotes' => Quote::whereIn('status', ['draft', 'sent'])->count(),
            'received_supplier_bills' => SupplierBill::where('status', 'received')->count(),
            'pending_expenses' => Expense::where('status', 'pending')->count(),
            'draft_payroll_runs' => PayrollRun::where('status', 'draft')->count(),
            'overdue_invoices' => Invoice::where('status', 'overdue')->count(),
        ]);
    }
}

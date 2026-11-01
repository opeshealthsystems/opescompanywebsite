<?php

namespace App\Contexts\Finance\Application\Commands;

use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Contexts\Shared\Infrastructure\DomainMail;
use App\Contexts\Shared\Infrastructure\DomainOutbox;
use App\Models\CreditNote;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\InvoiceTemplate;
use App\Models\PayrollDeductionType;
use App\Models\PayrollEntry;
use App\Models\PayrollRun;
use App\Models\PurchaseOrder;
use App\Models\Quote;
use App\Models\SupplierBill;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/** Shared, transactional finance commands used by the API and Filament adapters. */
final class AdminFinanceCommands extends WorkflowHandler
{
    private function adminActor(WorkflowInput $input): User
    {
        $actor = $input->user();
        abort_unless($actor?->is_active && $actor->hasAnyRole(['super_admin', 'admin']), 403);
        return $actor;
    }

    private function outcome(string $type, int|string $id, string $status, string $message, array $extra = []): WorkflowResult
    {
        return WorkflowResult::payload(['id' => $id, 'status' => $status] + $extra)->with('success', $message);
    }

    private function invalid(string $field, string $message): never
    {
        throw ValidationException::withMessages([$field => $message]);
    }

    public function approveExpense(WorkflowInput $input, Expense $expense): WorkflowResult
    {
        $actor = $this->adminActor($input);
        $record = DB::transaction(function () use ($expense, $actor) {
            $record = Expense::query()->lockForUpdate()->findOrFail($expense->id);
            if ($record->status === 'pending') {
                $record->update(['status' => 'approved', 'approved_by' => $actor->id, 'approved_at' => now()]);
                DomainOutbox::append('event', 'finance.expense_approved.v1', ['references' => [['resource' => 'Expense', 'id' => $record->id]]], $actor->id);
            } elseif ($record->status !== 'approved') $this->invalid('status', 'Only pending expenses can be approved.');
            return $record;
        }, 3);
        return $this->outcome('expense', $record->id, $record->status, 'Expense approved.');
    }

    public function markExpensePaid(WorkflowInput $input, Expense $expense): WorkflowResult
    {
        $actor = $this->adminActor($input);
        $record = DB::transaction(function () use ($expense, $actor) {
            $record = Expense::query()->lockForUpdate()->findOrFail($expense->id);
            if ($record->status === 'approved') {
                $record->update(['status' => 'paid']);
                DomainOutbox::append('event', 'finance.expense_paid.v1', ['references' => [['resource' => 'Expense', 'id' => $record->id]]], $actor->id);
            } elseif ($record->status !== 'paid') $this->invalid('status', 'Only approved expenses can be marked paid.');
            return $record;
        }, 3);
        return $this->outcome('expense', $record->id, $record->status, 'Expense marked paid.');
    }

    public function issueCreditNote(WorkflowInput $input, CreditNote $creditNote): WorkflowResult
    {
        $actor = $this->adminActor($input);
        $record = DB::transaction(function () use ($creditNote, $actor) {
            $record = CreditNote::query()->lockForUpdate()->findOrFail($creditNote->id);
            if ($record->status === 'draft') {
                $record->recalculateTotals();
                if ((float) $record->total <= 0) $this->invalid('total', 'A credit note must have a positive total before it can be issued.');
                if ($record->invoice_id && $record->invoice()->where('status', 'cancelled')->exists()) $this->invalid('invoice_id', 'A credit note cannot be issued against a cancelled invoice.');
                $record->update(['status' => 'issued', 'issued_at' => $record->issued_at ?? today()]);
                DomainOutbox::append('event', 'finance.credit_note_issued.v1', ['references' => [['resource' => 'CreditNote', 'id' => $record->id]]], $actor->id);
            } elseif ($record->status !== 'issued') $this->invalid('status', 'Only draft credit notes can be issued.');
            return $record;
        }, 3);
        return $this->outcome('credit_note', $record->id, $record->status, 'Credit note issued.');
    }

    public function voidCreditNote(WorkflowInput $input, CreditNote $creditNote): WorkflowResult
    {
        $actor = $this->adminActor($input);
        $record = DB::transaction(function () use ($creditNote, $actor) {
            $record = CreditNote::query()->lockForUpdate()->findOrFail($creditNote->id);
            if (in_array($record->status, ['draft', 'issued'], true)) {
                $record->update(['status' => 'void']);
                DomainOutbox::append('event', 'finance.credit_note_voided.v1', ['references' => [['resource' => 'CreditNote', 'id' => $record->id]]], $actor->id);
            } elseif ($record->status !== 'void') $this->invalid('status', 'Applied credit notes cannot be voided.');
            return $record;
        }, 3);
        return $this->outcome('credit_note', $record->id, $record->status, 'Credit note voided.');
    }

    public function approvePurchaseOrder(WorkflowInput $input, PurchaseOrder $purchaseOrder): WorkflowResult
    {
        $actor = $this->adminActor($input);
        $record = DB::transaction(function () use ($purchaseOrder, $actor) {
            $record = PurchaseOrder::query()->lockForUpdate()->findOrFail($purchaseOrder->id);
            if ($record->status === 'submitted') {
                $record->update(['status' => 'approved', 'approved_by' => $actor->id, 'approved_at' => now()]);
                DomainOutbox::append('event', 'finance.purchase_order_approved.v1', ['references' => [['resource' => 'PurchaseOrder', 'id' => $record->id]]], $actor->id);
            } elseif ($record->status !== 'approved') $this->invalid('status', 'Only submitted purchase orders can be approved.');
            return $record;
        }, 3);
        return $this->outcome('purchase_order', $record->id, $record->status, 'Purchase order approved.');
    }

    public function markPurchaseOrderReceived(WorkflowInput $input, PurchaseOrder $purchaseOrder): WorkflowResult
    {
        $actor = $this->adminActor($input);
        $record = DB::transaction(function () use ($purchaseOrder, $actor) {
            $record = PurchaseOrder::query()->lockForUpdate()->findOrFail($purchaseOrder->id);
            if ($record->status === 'approved') {
                $record->update(['status' => 'received', 'received_date' => today()]);
                DomainOutbox::append('event', 'finance.purchase_order_received.v1', ['references' => [['resource' => 'PurchaseOrder', 'id' => $record->id]]], $actor->id);
            } elseif ($record->status !== 'received') $this->invalid('status', 'Only approved purchase orders can be received.');
            return $record;
        }, 3);
        return $this->outcome('purchase_order', $record->id, $record->status, 'Purchase order marked received.');
    }

    public function approveSupplierBill(WorkflowInput $input, SupplierBill $supplierBill): WorkflowResult
    {
        $actor = $this->adminActor($input);
        $record = DB::transaction(function () use ($supplierBill, $actor) {
            $record = SupplierBill::query()->lockForUpdate()->findOrFail($supplierBill->id);
            if ($record->status === 'received') {
                $record->update(['status' => 'approved']);
                DomainOutbox::append('event', 'finance.supplier_bill_approved.v1', ['references' => [['resource' => 'SupplierBill', 'id' => $record->id]]], $actor->id);
            } elseif ($record->status !== 'approved') $this->invalid('status', 'Only received supplier bills can be approved.');
            return $record;
        }, 3);
        return $this->outcome('supplier_bill', $record->id, $record->status, 'Supplier bill approved.');
    }

    public function markSupplierBillPaid(WorkflowInput $input, SupplierBill $supplierBill): WorkflowResult
    {
        $actor = $this->adminActor($input);
        $record = DB::transaction(function () use ($supplierBill, $actor) {
            $record = SupplierBill::query()->lockForUpdate()->findOrFail($supplierBill->id);
            if ($record->status === 'approved') {
                $record->update(['status' => 'paid', 'paid_at' => today()]);
                DomainOutbox::append('event', 'finance.supplier_bill_paid.v1', ['references' => [['resource' => 'SupplierBill', 'id' => $record->id]]], $actor->id);
            } elseif ($record->status !== 'paid') $this->invalid('status', 'Only approved supplier bills can be marked paid.');
            return $record;
        }, 3);
        return $this->outcome('supplier_bill', $record->id, $record->status, 'Supplier bill marked paid.');
    }

    public function markQuoteSent(WorkflowInput $input, Quote $quote): WorkflowResult
    {
        $actor = $this->adminActor($input);
        $record = DB::transaction(function () use ($quote, $actor) {
            $record = Quote::query()->lockForUpdate()->findOrFail($quote->id);
            if ($record->status === 'draft') {
                if ($record->valid_until && $record->valid_until->isBefore(today())) $this->invalid('valid_until', 'Expired quotes cannot be sent.');
                $record->update(['status' => 'sent']);
                DomainOutbox::append('event', 'finance.quote_sent.v1', ['references' => [['resource' => 'Quote', 'id' => $record->id]]], $actor->id);
            } elseif ($record->status !== 'sent') $this->invalid('status', 'Only draft quotes can be sent.');
            return $record;
        }, 3);
        return $this->outcome('quote', $record->id, $record->status, 'Quote marked sent.');
    }

    public function acceptQuote(WorkflowInput $input, Quote $quote): WorkflowResult
    {
        $actor = $this->adminActor($input);
        $record = DB::transaction(function () use ($quote, $actor) {
            $record = Quote::query()->lockForUpdate()->findOrFail($quote->id);
            if ($record->status === 'sent') {
                if ($record->valid_until && $record->valid_until->isBefore(today())) $this->invalid('valid_until', 'Expired quotes cannot be accepted.');
                $record->update(['status' => 'accepted']);
                DomainOutbox::append('event', 'finance.quote_accepted.v1', ['references' => [['resource' => 'Quote', 'id' => $record->id]]], $actor->id);
            } elseif ($record->status !== 'accepted') $this->invalid('status', 'Only sent quotes can be accepted.');
            return $record;
        }, 3);
        return $this->outcome('quote', $record->id, $record->status, 'Quote accepted.');
    }

    public function markInvoiceSent(WorkflowInput $input, Invoice $invoice): WorkflowResult
    {
        $actor = $this->adminActor($input);
        $record = DB::transaction(function () use ($invoice, $actor) {
            $record = Invoice::query()->lockForUpdate()->findOrFail($invoice->id);
            if ($record->status === 'draft') {
                $record->update(['status' => 'sent']);
                if ($record->customer?->email) {
                    DomainMail::to($record->customer->email)->queue(new \App\Mail\InvoiceIssued($record->load('items')));
                    $record->customer->notify(new \App\Notifications\FeedEntry(
                        'accounting.invoice_issued', 'Invoice issued', 'Invoice '.$record->invoice_number.' is ready to view.',
                        'document-text', route('customer.invoices.show', ['locale' => 'en', 'id' => $record->id]),
                    ));
                }
                DomainOutbox::append('event', 'finance.invoice_sent.v1', ['references' => [['resource' => 'Invoice', 'id' => $record->id]]], $actor->id);
            } elseif ($record->status !== 'sent') $this->invalid('status', 'Only draft invoices can be marked sent.');
            return $record;
        }, 3);
        return $this->outcome('invoice', $record->id, $record->status, 'Invoice marked sent.');
    }

    public function markInvoicePaid(WorkflowInput $input, Invoice $invoice): WorkflowResult
    {
        $actor = $this->adminActor($input);
        $record = app(\App\Contexts\Finance\Application\RecordInvoicePayment::class)->record(
            $actor, $invoice->id,
            ['payment_method' => 'other', 'payment_date' => today()->toDateString(), 'notes' => 'Outstanding amount confirmed received by an authorized administrator.'],
            true,
        );
        return $this->outcome('invoice', $record->id, $record->status, 'Invoice marked as paid.');
    }

    public function markInvoiceOverdue(WorkflowInput $input, Invoice $invoice): WorkflowResult
    {
        $actor = $this->adminActor($input);
        $record = DB::transaction(function () use ($invoice, $actor) {
            $record = Invoice::query()->lockForUpdate()->findOrFail($invoice->id);
            if ($record->status === 'overdue') return $record;
            if ($record->status !== 'sent') $this->invalid('status', 'Only sent invoices can be marked overdue.');
            $record->update(['status' => 'overdue']);
            DomainOutbox::append('event', 'finance.invoice_marked_overdue.v1', ['references' => [['resource' => 'Invoice', 'id' => $record->id]]], $actor->id);
            return $record;
        }, 3);
        return $this->outcome('invoice', $record->id, $record->status, 'Invoice marked overdue.');
    }

    public function markInvoicesSent(WorkflowInput $input): WorkflowResult
    {
        $actor = $this->adminActor($input);
        $data = $input->validate(['ids' => 'required|array|min:1|max:100', 'ids.*' => 'required|integer|distinct']);
        $result = DB::transaction(function () use ($data, $actor) {
            $records = Invoice::query()->whereIn('id', $data['ids'])->orderBy('id')->lockForUpdate()->get();
            if ($records->count() !== count($data['ids'])) $this->invalid('ids', 'One or more invoices do not exist.');
            $updated = 0;
            foreach ($records as $record) {
                if ($record->status === 'draft') {
                    $record->update(['status' => 'sent']); $updated++;
                    DomainOutbox::append('event', 'finance.invoice_sent.v1', ['references' => [['resource' => 'Invoice', 'id' => $record->id]]], $actor->id);
                } elseif ($record->status !== 'sent') $this->invalid('ids', 'Only draft or already-sent invoices can be selected.');
            }
            return ['updated' => $updated, 'ids' => $records->pluck('id')->all()];
        }, 3);
        return WorkflowResult::payload($result)->with('success', $result['updated'].' invoice(s) marked sent.');
    }

    public function generateInvoiceFromTemplate(WorkflowInput $input, InvoiceTemplate $invoiceTemplate): WorkflowResult
    {
        $actor = $this->adminActor($input);
        $invoice = DB::transaction(function () use ($invoiceTemplate, $actor) {
            $template = InvoiceTemplate::query()->lockForUpdate()->findOrFail($invoiceTemplate->id);
            if (!$template->is_active || ($template->end_date && $template->end_date->isBefore(today())) || $template->next_due_date->isAfter(today())) {
                $this->invalid('template', 'This invoice template is inactive, not yet due, or past its end date.');
            }
            $invoice = $template->generateInvoice();
            if (!$invoice) $this->invalid('template', 'Invoice generation is unavailable for this template.');
            DomainOutbox::append('event', 'finance.invoice_generated.v1', ['references' => [
                ['resource' => 'InvoiceTemplate', 'id' => $template->id], ['resource' => 'Invoice', 'id' => $invoice->id],
            ]], $actor->id);
            return $invoice;
        }, 3);
        return WorkflowResult::payload(['id' => $invoice->id, 'invoice_number' => $invoice->invoice_number, 'status' => $invoice->status], 201)->with('success', 'Invoice generated.');
    }

    public function generatePayrollEntries(WorkflowInput $input, PayrollRun $run): WorkflowResult
    {
        $actor = $this->adminActor($input);
        $result = DB::transaction(function () use ($run, $actor) {
            $record = PayrollRun::query()->lockForUpdate()->findOrFail($run->id);
            if ($record->status !== 'draft') $this->invalid('status', 'Payroll entries can only be generated for a draft run.');
            $existingIds = $record->entries()->pluck('user_id')->all();
            $staff = User::query()->where('is_active', true)->whereHas('roles', fn ($q) => $q->whereIn('name', ['super_admin', 'admin', 'support', 'tester']))
                ->with('employeeProfile')->whereNotIn('id', $existingIds)->orderBy('id')->get();
            $types = PayrollDeductionType::defaultDeductions();
            $created = 0;
            foreach ($staff as $employee) {
                $profile = $employee->employeeProfile;
                $gross = round((float) ($profile?->salary ?? $employee->base_salary ?? 0), 2);
                $deductions = $types->map(fn ($type) => ['label' => $type->name, 'amount' => round($type->calculateAmount($gross), 2)])->all();
                $total = round(array_sum(array_column($deductions, 'amount')), 2);
                $record->entries()->create(['user_id' => $employee->id, 'gross_salary' => $gross, 'deductions' => $deductions,
                    'total_deductions' => $total, 'net_salary' => round(max(0, $gross - $total), 2),
                    'currency' => $profile?->currency ?? $record->currency, 'status' => 'pending']);
                $created++;
            }
            if ($created > 0) {
                $record->update(['status' => 'processing']); $record->recalculateTotals();
                DomainOutbox::append('event', 'finance.payroll_entries_generated.v1', ['references' => [['resource' => 'PayrollRun', 'id' => $record->id]], 'count' => $created], $actor->id);
            }
            return ['id' => $record->id, 'status' => $record->status, 'created' => $created];
        }, 3);
        return WorkflowResult::payload($result)->with('success', $result['created'].' payroll entries generated.');
    }

    public function completePayroll(WorkflowInput $input, PayrollRun $run): WorkflowResult
    {
        $actor = $this->adminActor($input);
        $result = DB::transaction(function () use ($run, $actor) {
            $record = PayrollRun::query()->lockForUpdate()->findOrFail($run->id);
            if ($record->status === 'completed') return ['id' => $record->id, 'status' => 'completed', 'notified' => 0];
            if ($record->status !== 'processing') $this->invalid('status', 'Only a processing payroll run can be completed.');
            $entries = $record->entries()->with('employee')->orderBy('id')->get();
            if ($entries->isEmpty()) $this->invalid('entries', 'A payroll run must contain entries before completion.');
            $record->update(['status' => 'completed', 'processed_by' => $actor->id, 'completed_at' => now()]);
            $notified = 0;
            foreach ($entries as $entry) {
                $employee = $entry->employee;
                if (!$employee?->email) continue;
                DomainMail::to($employee->email)->queue(new \App\Mail\PayrollProcessed($record, $entry));
                $employee->notify(new \App\Notifications\FeedEntry('hr.payroll_processed', 'Payslip available', 'Your latest payroll has been processed.', 'banknotes'));
                $notified++;
            }
            DomainOutbox::append('event', 'finance.payroll_completed.v1', ['references' => [['resource' => 'PayrollRun', 'id' => $record->id]]], $actor->id);
            return ['id' => $record->id, 'status' => 'completed', 'notified' => $notified];
        }, 3);
        return WorkflowResult::payload($result)->with('success', 'Payroll run completed.');
    }

    public function markPayrollEntryPaid(WorkflowInput $input, PayrollRun $run, PayrollEntry $entry): WorkflowResult
    {
        $actor = $this->adminActor($input);
        $record = DB::transaction(function () use ($run, $entry, $actor) {
            $payrollRun = PayrollRun::query()->lockForUpdate()->findOrFail($run->id);
            $payrollEntry = PayrollEntry::query()->where('payroll_run_id', $payrollRun->id)->lockForUpdate()->findOrFail($entry->id);
            if (!in_array($payrollRun->status, ['processing', 'completed'], true)) $this->invalid('status', 'Entries can only be paid after a payroll run is processing.');
            if ($payrollEntry->status === 'pending') {
                $payrollEntry->update(['status' => 'paid']);
                DomainOutbox::append('event', 'finance.payroll_entry_paid.v1', ['references' => [['resource' => 'PayrollRun', 'id' => $payrollRun->id], ['resource' => 'PayrollEntry', 'id' => $payrollEntry->id]]], $actor->id);
            }
            return $payrollEntry;
        }, 3);
        return $this->outcome('payroll_entry', $record->id, $record->status, 'Payroll entry marked paid.');
    }

    public function markPayrollEntriesPaid(WorkflowInput $input, PayrollRun $run): WorkflowResult
    {
        $actor = $this->adminActor($input);
        $data = $input->validate(['ids' => 'required|array|min:1|max:100', 'ids.*' => 'required|integer|distinct']);
        $result = DB::transaction(function () use ($run, $data, $actor) {
            $payrollRun = PayrollRun::query()->lockForUpdate()->findOrFail($run->id);
            if (!in_array($payrollRun->status, ['processing', 'completed'], true)) $this->invalid('status', 'Entries can only be paid after a payroll run is processing.');
            $entries = PayrollEntry::query()->where('payroll_run_id', $payrollRun->id)->whereIn('id', $data['ids'])->orderBy('id')->lockForUpdate()->get();
            if ($entries->count() !== count($data['ids'])) $this->invalid('ids', 'One or more entries do not belong to this payroll run.');
            $updated = 0;
            foreach ($entries as $entry) if ($entry->status === 'pending') {
                $entry->update(['status' => 'paid']); $updated++;
                DomainOutbox::append('event', 'finance.payroll_entry_paid.v1', ['references' => [['resource' => 'PayrollRun', 'id' => $payrollRun->id], ['resource' => 'PayrollEntry', 'id' => $entry->id]]], $actor->id);
            }
            return ['run_id' => $payrollRun->id, 'updated' => $updated, 'ids' => $entries->pluck('id')->all()];
        }, 3);
        return WorkflowResult::payload($result)->with('success', $result['updated'].' payroll entries marked paid.');
    }
}

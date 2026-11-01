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
use Illuminate\Support\Facades\Storage;
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

    public function createPayrollRun(WorkflowInput $input): WorkflowResult
    {
        $actor = $this->adminActor($input);
        $data = $input->validate([
            'period_start' => 'required|date', 'period_end' => 'required|date|after_or_equal:period_start',
            'currency' => 'required|in:XAF,USD,EUR', 'notes' => 'nullable|string|max:5000',
            'reference' => 'prohibited', 'status' => 'prohibited', 'processed_by' => 'prohibited',
            'total_gross' => 'prohibited', 'total_net' => 'prohibited', 'completed_at' => 'prohibited',
        ]);
        $record = DB::transaction(function () use ($data, $actor) {
            $year = now()->year;
            $name = 'payroll_run:'.$year;
            DB::table('business_sequences')->insertOrIgnore(['name' => $name, 'last_value' => 0]);
            $sequence = DB::table('business_sequences')->where('name', $name)->lockForUpdate()->first();
            $previous = (int) $sequence->last_value;
            if ($previous === 0) {
                $prefix = 'PAY-'.$year.'-';
                $previous = PayrollRun::query()->where('reference', 'like', $prefix.'%')->pluck('reference')
                    ->map(fn ($ref) => ctype_digit(substr($ref, strlen($prefix))) ? (int) substr($ref, strlen($prefix)) : 0)->max() ?? 0;
            }
            $next = $previous + 1;
            DB::table('business_sequences')->where('name', $name)->update(['last_value' => $next]);
            $record = PayrollRun::create($data + ['reference' => 'PAY-'.$year.'-'.str_pad((string) $next, 4, '0', STR_PAD_LEFT), 'status' => 'draft', 'processed_by' => $actor->id, 'total_gross' => 0, 'total_net' => 0]);
            DomainOutbox::append('event', 'finance.payroll_run_created.v1', ['references' => [['resource' => 'PayrollRun', 'id' => $record->id]]], $actor->id);
            return $record;
        }, 3);
        return WorkflowResult::payload(['record' => $record, 'id' => $record->id, 'reference' => $record->reference], 201);
    }

    public function updatePayrollRun(WorkflowInput $input, PayrollRun $run): WorkflowResult
    {
        $actor = $this->adminActor($input);
        $data = $input->validate([
            'period_start' => 'sometimes|required|date', 'period_end' => 'sometimes|required|date',
            'currency' => 'sometimes|required|in:XAF,USD,EUR', 'notes' => 'sometimes|nullable|string|max:5000',
            'reference' => 'prohibited', 'status' => 'prohibited', 'processed_by' => 'prohibited',
            'total_gross' => 'prohibited', 'total_net' => 'prohibited', 'completed_at' => 'prohibited',
        ]);
        if ($data === []) $this->invalid('payroll_run', 'Provide at least one payroll run field to update.');
        $record = DB::transaction(function () use ($run, $data, $actor) {
            $record = PayrollRun::query()->lockForUpdate()->findOrFail($run->id);
            if ($record->status !== 'draft' || $record->entries()->exists()) $this->invalid('status', 'Only draft payroll runs without entries can be edited.');
            $start = isset($data['period_start']) ? \Carbon\Carbon::parse($data['period_start']) : $record->period_start;
            $end = isset($data['period_end']) ? \Carbon\Carbon::parse($data['period_end']) : $record->period_end;
            if ($end && $start && $end->lt($start)) $this->invalid('period_end', 'The period end must not precede the period start.');
            $record->update($data);
            DomainOutbox::append('event', 'finance.payroll_run_updated.v1', ['references' => [['resource' => 'PayrollRun', 'id' => $record->id]], 'changed_fields' => array_keys($data)], $actor->id);
            return $record;
        }, 3);
        return WorkflowResult::payload(['record' => $record, 'id' => $record->id, 'reference' => $record->reference]);
    }

    public function deletePayrollRun(WorkflowInput $input, PayrollRun $run): WorkflowResult
    {
        $actor = $this->adminActor($input);
        $id = DB::transaction(function () use ($run, $actor) {
            $record = PayrollRun::query()->lockForUpdate()->findOrFail($run->id);
            if ($record->status !== 'draft' || $record->entries()->exists()) $this->invalid('status', 'Only draft payroll runs without entries can be deleted.');
            $id = $record->id;
            $record->delete();
            DomainOutbox::append('event', 'finance.payroll_run_deleted.v1', ['references' => [['resource' => 'PayrollRun', 'id' => $id]]], $actor->id);
            return $id;
        }, 3);
        return WorkflowResult::payload(['deleted_id' => $id]);
    }

    public function createExpense(WorkflowInput $input): WorkflowResult
    {
        $actor = $this->adminActor($input);
        $data = $input->validate([
            'title' => 'required|string|max:200',
            'description' => 'nullable|string|max:5000',
            'category' => 'required|in:'.implode(',', array_keys(Expense::categoryOptions())),
            'amount' => 'required|numeric|gt:0',
            'currency' => 'required|in:XAF,USD,EUR',
            'vendor' => 'nullable|string|max:150',
            'expense_date' => 'required|date',
            'receipt_path' => 'nullable|string|max:255',
            'submitted_by' => 'required|integer|exists:users,id',
            'notes' => 'nullable|string|max:5000',
            'reference' => 'prohibited',
            'status' => 'prohibited',
            'approved_by' => 'prohibited',
            'approved_at' => 'prohibited',
        ]);
        $this->validateExpenseReceipt($data['receipt_path'] ?? null);

        $record = DB::transaction(function () use ($data, $actor) {
            $year = now()->year;
            $sequenceName = 'expense:'.$year;
            DB::table('business_sequences')->insertOrIgnore(['name' => $sequenceName, 'last_value' => 0]);
            $sequence = DB::table('business_sequences')->where('name', $sequenceName)->lockForUpdate()->first();
            $previous = (int) $sequence->last_value;
            if ($previous === 0) {
                $prefix = 'EXP-'.$year.'-';
                $previous = Expense::query()->where('reference', 'like', $prefix.'%')->pluck('reference')
                    ->map(fn ($reference) => ctype_digit(substr($reference, strlen($prefix))) ? (int) substr($reference, strlen($prefix)) : 0)->max() ?? 0;
            }
            $next = $previous + 1;
            DB::table('business_sequences')->where('name', $sequenceName)->update(['last_value' => $next]);
            $record = Expense::create($data + [
                'reference' => 'EXP-'.$year.'-'.str_pad((string) $next, 5, '0', STR_PAD_LEFT),
                'status' => 'pending',
                'approved_by' => null,
                'approved_at' => null,
            ]);
            DomainOutbox::append('event', 'finance.expense_created.v1', [
                'references' => [['resource' => 'Expense', 'id' => $record->id]],
                'changed_fields' => array_keys($data),
            ], $actor->id);
            return $record;
        }, 3);

        return WorkflowResult::payload(['id' => $record->id, 'reference' => $record->reference, 'status' => $record->status], 201)->with('success', 'Expense recorded for approval.');
    }

    public function updateExpense(WorkflowInput $input, Expense $expense): WorkflowResult
    {
        $actor = $this->adminActor($input);
        $data = $input->validate([
            'title' => 'sometimes|required|string|max:200',
            'description' => 'sometimes|nullable|string|max:5000',
            'category' => 'sometimes|required|in:'.implode(',', array_keys(Expense::categoryOptions())),
            'amount' => 'sometimes|required|numeric|gt:0',
            'currency' => 'sometimes|required|in:XAF,USD,EUR',
            'vendor' => 'sometimes|nullable|string|max:150',
            'expense_date' => 'sometimes|required|date',
            'receipt_path' => 'sometimes|nullable|string|max:255',
            'submitted_by' => 'sometimes|required|integer|exists:users,id',
            'notes' => 'sometimes|nullable|string|max:5000',
            'reference' => 'prohibited',
            'status' => 'prohibited',
            'approved_by' => 'prohibited',
            'approved_at' => 'prohibited',
        ]);
        if ($data === []) $this->invalid('expense', 'Provide at least one expense field to update.');
        $this->validateExpenseReceipt($data['receipt_path'] ?? null);

        $record = DB::transaction(function () use ($expense, $data, $actor) {
            $record = Expense::query()->lockForUpdate()->findOrFail($expense->id);
            if ($record->status !== 'pending') $this->invalid('status', 'Only pending expenses can be edited.');
            $record->update($data);
            DomainOutbox::append('event', 'finance.expense_updated.v1', [
                'references' => [['resource' => 'Expense', 'id' => $record->id]],
                'changed_fields' => array_keys($data),
            ], $actor->id);
            return $record;
        }, 3);

        return $this->outcome('expense', $record->id, $record->status, 'Expense updated.', ['reference' => $record->reference]);
    }

    public function createCreditNote(WorkflowInput $input): WorkflowResult
    {
        $actor = $this->adminActor($input);
        $data = $input->validate([
            'invoice_id' => 'nullable|integer|exists:invoices,id',
            'reason' => 'required|string|max:300',
            'currency' => 'required|in:XAF,USD,EUR',
            'tax_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:5000',
            'items' => 'required|array|min:1|max:100',
            'items.*.description' => 'required|string|max:500',
            'items.*.quantity' => 'required|numeric|gt:0|max:1000000',
            'items.*.unit_price' => 'required|numeric|min:0|max:1000000000',
            'reference' => 'prohibited', 'status' => 'prohibited', 'issued_at' => 'prohibited',
            'created_by' => 'prohibited', 'subtotal' => 'prohibited', 'total' => 'prohibited',
        ]);
        $items = $data['items'];
        unset($data['items']);

        $record = DB::transaction(function () use ($data, $items, $actor) {
            $year = now()->year;
            $sequenceName = 'credit-note:'.$year;
            DB::table('business_sequences')->insertOrIgnore(['name' => $sequenceName, 'last_value' => 0]);
            $sequence = DB::table('business_sequences')->where('name', $sequenceName)->lockForUpdate()->first();
            $previous = (int) $sequence->last_value;
            if ($previous === 0) {
                $prefix = 'CN-'.$year.'-';
                $previous = CreditNote::query()->where('reference', 'like', $prefix.'%')->pluck('reference')
                    ->map(fn ($reference) => ctype_digit(substr($reference, strlen($prefix))) ? (int) substr($reference, strlen($prefix)) : 0)->max() ?? 0;
            }
            $next = $previous + 1;
            DB::table('business_sequences')->where('name', $sequenceName)->update(['last_value' => $next]);
            $record = CreditNote::create($data + [
                'reference' => 'CN-'.$year.'-'.str_pad((string) $next, 4, '0', STR_PAD_LEFT),
                'status' => 'draft', 'created_by' => $actor->id, 'tax_amount' => $data['tax_amount'] ?? 0,
            ]);
            $this->replaceCreditNoteItems($record, $items);
            $record->recalculateTotals();
            DomainOutbox::append('event', 'finance.credit_note_created.v1', [
                'references' => [['resource' => 'CreditNote', 'id' => $record->id]],
                'changed_fields' => array_keys($data),
            ], $actor->id);
            return $record;
        }, 3);

        return WorkflowResult::payload(['id' => $record->id, 'reference' => $record->reference, 'status' => $record->status, 'subtotal' => $record->subtotal, 'total' => $record->total], 201)->with('success', 'Credit note created.');
    }

    public function updateCreditNote(WorkflowInput $input, CreditNote $creditNote): WorkflowResult
    {
        $actor = $this->adminActor($input);
        $data = $input->validate([
            'invoice_id' => 'sometimes|nullable|integer|exists:invoices,id',
            'reason' => 'sometimes|required|string|max:300',
            'currency' => 'sometimes|required|in:XAF,USD,EUR',
            'tax_amount' => 'sometimes|nullable|numeric|min:0',
            'notes' => 'sometimes|nullable|string|max:5000',
            'items' => 'sometimes|required|array|min:1|max:100',
            'items.*.description' => 'required_with:items|string|max:500',
            'items.*.quantity' => 'required_with:items|numeric|gt:0|max:1000000',
            'items.*.unit_price' => 'required_with:items|numeric|min:0|max:1000000000',
            'reference' => 'prohibited', 'status' => 'prohibited', 'issued_at' => 'prohibited',
            'created_by' => 'prohibited', 'subtotal' => 'prohibited', 'total' => 'prohibited',
        ]);
        if ($data === []) $this->invalid('credit_note', 'Provide at least one credit note field to update.');
        $items = $data['items'] ?? null;
        unset($data['items']);

        $record = DB::transaction(function () use ($creditNote, $data, $items, $actor) {
            $record = CreditNote::query()->lockForUpdate()->findOrFail($creditNote->id);
            if ($record->status !== 'draft') $this->invalid('status', 'Only draft credit notes can be edited.');
            $record->fill($data)->save();
            if ($items !== null) $this->replaceCreditNoteItems($record, $items);
            $record->recalculateTotals();
            DomainOutbox::append('event', 'finance.credit_note_updated.v1', [
                'references' => [['resource' => 'CreditNote', 'id' => $record->id]],
                'changed_fields' => array_merge(array_keys($data), $items !== null ? ['items'] : []),
            ], $actor->id);
            return $record;
        }, 3);

        return $this->outcome('credit_note', $record->id, $record->status, 'Credit note updated.', ['reference' => $record->reference, 'subtotal' => $record->subtotal, 'total' => $record->total]);
    }

    public function deleteCreditNote(WorkflowInput $input, CreditNote $creditNote): WorkflowResult
    {
        $actor = $this->adminActor($input);
        DB::transaction(function () use ($creditNote, $actor) {
            $record = CreditNote::query()->lockForUpdate()->findOrFail($creditNote->id);
            if ($record->status !== 'draft') $this->invalid('status', 'Only draft credit notes can be deleted.');
            DomainOutbox::append('event', 'finance.credit_note_deleted.v1', ['references' => [['resource' => 'CreditNote', 'id' => $record->id]]], $actor->id);
            $record->delete();
        }, 3);

        return WorkflowResult::payload(['id' => $creditNote->id, 'status' => 'deleted'])->with('success', 'Draft credit note deleted.');
    }

    private function replaceCreditNoteItems(CreditNote $record, array $items): void
    {
        $record->items()->delete();
        foreach ($items as $item) {
            $quantity = (float) $item['quantity'];
            $unitPrice = (float) $item['unit_price'];
            $record->items()->create([
                'description' => $item['description'], 'quantity' => $quantity,
                'unit_price' => $unitPrice, 'total' => round($quantity * $unitPrice, 2),
            ]);
        }
    }

    public function createQuote(WorkflowInput $input): WorkflowResult
    {
        $actor = $this->adminActor($input);
        $data = $input->validate([
            'lead_id' => 'nullable|integer|exists:leads,id',
            'title' => 'required|string|max:250',
            'valid_until' => 'nullable|date',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'currency' => 'required|in:XAF,USD,EUR',
            'notes' => 'nullable|string|max:5000',
            'items' => 'required|array|min:1|max:100',
            'items.*.product_name' => 'required|string|max:250',
            'items.*.quantity' => 'required|numeric|gt:0|max:1000000',
            'items.*.unit_price' => 'required|numeric|min:0|max:1000000000',
            'reference' => 'prohibited', 'status' => 'prohibited', 'tax_amount' => 'prohibited',
            'subtotal' => 'prohibited', 'total' => 'prohibited', 'created_by' => 'prohibited',
        ]);
        $items = $data['items'];
        unset($data['items']);

        $record = DB::transaction(function () use ($data, $items, $actor) {
            $year = now()->year;
            $sequenceName = 'quote:'.$year;
            DB::table('business_sequences')->insertOrIgnore(['name' => $sequenceName, 'last_value' => 0]);
            $sequence = DB::table('business_sequences')->where('name', $sequenceName)->lockForUpdate()->first();
            $previous = (int) $sequence->last_value;
            if ($previous === 0) {
                $prefix = 'QTE-'.$year.'-';
                $previous = Quote::query()->where('reference', 'like', $prefix.'%')->pluck('reference')
                    ->map(fn ($reference) => ctype_digit(substr($reference, strlen($prefix))) ? (int) substr($reference, strlen($prefix)) : 0)->max() ?? 0;
            }
            $next = $previous + 1;
            DB::table('business_sequences')->where('name', $sequenceName)->update(['last_value' => $next]);
            $record = Quote::create($data + [
                'reference' => 'QTE-'.$year.'-'.str_pad((string) $next, 4, '0', STR_PAD_LEFT),
                'status' => 'draft', 'created_by' => $actor->id, 'tax_rate' => $data['tax_rate'] ?? 0,
            ]);
            $this->replaceQuoteItems($record, $items);
            $record->recalculateTotals();
            DomainOutbox::append('event', 'finance.quote_created.v1', [
                'references' => [['resource' => 'Quote', 'id' => $record->id]], 'changed_fields' => array_keys($data),
            ], $actor->id);
            return $record;
        }, 3);

        return WorkflowResult::payload(['id' => $record->id, 'reference' => $record->reference, 'status' => $record->status, 'subtotal' => $record->subtotal, 'total' => $record->total], 201)->with('success', 'Quote created.');
    }

    public function updateQuote(WorkflowInput $input, Quote $quote): WorkflowResult
    {
        $actor = $this->adminActor($input);
        $data = $input->validate([
            'lead_id' => 'sometimes|nullable|integer|exists:leads,id',
            'title' => 'sometimes|required|string|max:250',
            'valid_until' => 'sometimes|nullable|date',
            'tax_rate' => 'sometimes|nullable|numeric|min:0|max:100',
            'currency' => 'sometimes|required|in:XAF,USD,EUR',
            'notes' => 'sometimes|nullable|string|max:5000',
            'items' => 'sometimes|required|array|min:1|max:100',
            'items.*.product_name' => 'required_with:items|string|max:250',
            'items.*.quantity' => 'required_with:items|numeric|gt:0|max:1000000',
            'items.*.unit_price' => 'required_with:items|numeric|min:0|max:1000000000',
            'reference' => 'prohibited', 'status' => 'prohibited', 'tax_amount' => 'prohibited',
            'subtotal' => 'prohibited', 'total' => 'prohibited', 'created_by' => 'prohibited',
        ]);
        if ($data === []) $this->invalid('quote', 'Provide at least one quote field to update.');
        $items = $data['items'] ?? null;
        unset($data['items']);

        $record = DB::transaction(function () use ($quote, $data, $items, $actor) {
            $record = Quote::query()->lockForUpdate()->findOrFail($quote->id);
            if ($record->status !== 'draft') $this->invalid('status', 'Only draft quotes can be edited.');
            $record->fill($data)->save();
            if ($items !== null) $this->replaceQuoteItems($record, $items);
            $record->recalculateTotals();
            DomainOutbox::append('event', 'finance.quote_updated.v1', [
                'references' => [['resource' => 'Quote', 'id' => $record->id]],
                'changed_fields' => array_merge(array_keys($data), $items !== null ? ['items'] : []),
            ], $actor->id);
            return $record;
        }, 3);

        return $this->outcome('quote', $record->id, $record->status, 'Quote updated.', ['reference' => $record->reference, 'subtotal' => $record->subtotal, 'total' => $record->total]);
    }

    public function deleteQuote(WorkflowInput $input, Quote $quote): WorkflowResult
    {
        $actor = $this->adminActor($input);
        DB::transaction(function () use ($quote, $actor) {
            $record = Quote::query()->lockForUpdate()->findOrFail($quote->id);
            if ($record->status !== 'draft') $this->invalid('status', 'Only draft quotes can be deleted.');
            DomainOutbox::append('event', 'finance.quote_deleted.v1', ['references' => [['resource' => 'Quote', 'id' => $record->id]]], $actor->id);
            $record->delete();
        }, 3);

        return WorkflowResult::payload(['id' => $quote->id, 'status' => 'deleted'])->with('success', 'Draft quote deleted.');
    }

    private function replaceQuoteItems(Quote $record, array $items): void
    {
        $record->items()->delete();
        foreach ($items as $item) {
            $quantity = (float) $item['quantity'];
            $unitPrice = (float) $item['unit_price'];
            $record->items()->create([
                'product_name' => $item['product_name'], 'quantity' => $quantity,
                'unit_price' => $unitPrice, 'total' => round($quantity * $unitPrice, 2),
            ]);
        }
    }

    public function createInvoice(WorkflowInput $input): WorkflowResult
    {
        $actor = $this->adminActor($input);
        $data = $input->validate([
            'customer_id' => 'required|integer|exists:users,id',
            'license_id' => 'nullable|integer|exists:licenses,id',
            'currency' => 'required|in:XAF,USD,EUR',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'due_date' => 'nullable|date',
            'notes' => 'nullable|string|max:5000',
            'items' => 'required|array|min:1|max:100',
            'items.*.description' => 'required|string|max:500',
            'items.*.quantity' => 'required|integer|min:1|max:1000000',
            'items.*.unit_price' => 'required|integer|min:0|max:1000000000',
            'invoice_number' => 'prohibited', 'status' => 'prohibited', 'issued_by' => 'prohibited',
            'paid_at' => 'prohibited', 'total' => 'prohibited', 'grand_total' => 'prohibited',
            'reference' => 'prohibited',
        ]);
        if (!User::role('customer')->whereKey($data['customer_id'])->exists()) $this->invalid('customer_id', 'Choose an active customer account.');
        $items = $data['items'];
        unset($data['items']);

        $record = DB::transaction(function () use ($data, $items, $actor) {
            $year = now()->year;
            $sequenceName = 'invoice:'.$year;
            DB::table('business_sequences')->insertOrIgnore(['name' => $sequenceName, 'last_value' => 0]);
            $sequence = DB::table('business_sequences')->where('name', $sequenceName)->lockForUpdate()->first();
            $previous = (int) $sequence->last_value;
            if ($previous === 0) {
                $prefix = 'INV-'.$year.'-';
                $previous = Invoice::query()->where('invoice_number', 'like', $prefix.'%')->pluck('invoice_number')
                    ->map(fn ($reference) => ctype_digit(substr($reference, strlen($prefix))) ? (int) substr($reference, strlen($prefix)) : 0)->max() ?? 0;
            }
            $next = $previous + 1;
            DB::table('business_sequences')->where('name', $sequenceName)->update(['last_value' => $next]);
            $record = Invoice::create($data + [
                'invoice_number' => 'INV-'.$year.'-'.str_pad((string) $next, 5, '0', STR_PAD_LEFT),
                'issued_by' => $actor->id, 'status' => 'draft', 'tax_rate' => $data['tax_rate'] ?? 0,
            ]);
            $this->replaceInvoiceItems($record, $items);
            DomainOutbox::append('event', 'finance.invoice_created.v1', [
                'references' => [['resource' => 'Invoice', 'id' => $record->id]], 'changed_fields' => array_keys($data),
            ], $actor->id);
            return $record;
        }, 3);

        return WorkflowResult::payload(['id' => $record->id, 'invoice_number' => $record->invoice_number, 'status' => $record->status, 'subtotal' => $record->subtotal, 'tax_amount' => $record->tax_amount, 'grand_total' => $record->grand_total], 201)->with('success', 'Invoice created.');
    }

    public function updateInvoice(WorkflowInput $input, Invoice $invoice): WorkflowResult
    {
        $actor = $this->adminActor($input);
        $data = $input->validate([
            'customer_id' => 'sometimes|required|integer|exists:users,id',
            'license_id' => 'sometimes|nullable|integer|exists:licenses,id',
            'currency' => 'sometimes|required|in:XAF,USD,EUR',
            'tax_rate' => 'sometimes|nullable|numeric|min:0|max:100',
            'due_date' => 'sometimes|nullable|date',
            'notes' => 'sometimes|nullable|string|max:5000',
            'items' => 'sometimes|required|array|min:1|max:100',
            'items.*.description' => 'required_with:items|string|max:500',
            'items.*.quantity' => 'required_with:items|integer|min:1|max:1000000',
            'items.*.unit_price' => 'required_with:items|integer|min:0|max:1000000000',
            'invoice_number' => 'prohibited', 'status' => 'prohibited', 'issued_by' => 'prohibited',
            'paid_at' => 'prohibited', 'total' => 'prohibited', 'grand_total' => 'prohibited', 'reference' => 'prohibited',
        ]);
        if (isset($data['customer_id']) && !User::role('customer')->whereKey($data['customer_id'])->exists()) $this->invalid('customer_id', 'Choose an active customer account.');
        if ($data === []) $this->invalid('invoice', 'Provide at least one invoice field to update.');
        $items = $data['items'] ?? null;
        unset($data['items']);

        $record = DB::transaction(function () use ($invoice, $data, $items, $actor) {
            $record = Invoice::query()->lockForUpdate()->findOrFail($invoice->id);
            if ($record->status !== 'draft') $this->invalid('status', 'Only draft invoices can be edited.');
            $record->fill($data)->save();
            if ($items !== null) $this->replaceInvoiceItems($record, $items);
            DomainOutbox::append('event', 'finance.invoice_updated.v1', [
                'references' => [['resource' => 'Invoice', 'id' => $record->id]],
                'changed_fields' => array_merge(array_keys($data), $items !== null ? ['items'] : []),
            ], $actor->id);
            return $record->load('items');
        }, 3);

        return $this->outcome('invoice', $record->id, $record->status, 'Invoice updated.', ['invoice_number' => $record->invoice_number, 'subtotal' => $record->subtotal, 'tax_amount' => $record->tax_amount, 'grand_total' => $record->grand_total]);
    }

    public function deleteInvoice(WorkflowInput $input, Invoice $invoice): WorkflowResult
    {
        $actor = $this->adminActor($input);
        DB::transaction(function () use ($invoice, $actor) {
            $record = Invoice::query()->lockForUpdate()->findOrFail($invoice->id);
            if ($record->status !== 'draft') $this->invalid('status', 'Only draft invoices can be deleted.');
            if ($record->payments()->exists() || $record->creditNotes()->exists()) $this->invalid('invoice', 'An invoice with payment or credit note history cannot be deleted.');
            DomainOutbox::append('event', 'finance.invoice_deleted.v1', ['references' => [['resource' => 'Invoice', 'id' => $record->id]]], $actor->id);
            $record->delete();
        }, 3);

        return WorkflowResult::payload(['id' => $invoice->id, 'status' => 'deleted'])->with('success', 'Draft invoice deleted.');
    }

    private function replaceInvoiceItems(Invoice $record, array $items): void
    {
        $record->items()->delete();
        foreach ($items as $item) {
            $record->items()->create([
                'description' => $item['description'], 'quantity' => (int) $item['quantity'],
                'unit_price' => (int) $item['unit_price'],
            ]);
        }
    }

    public function createPurchaseOrder(WorkflowInput $input): WorkflowResult
    {
        $actor = $this->adminActor($input);
        $data = $input->validate([
            'vendor_id' => 'nullable|integer|exists:vendors,id', 'vendor_name' => 'nullable|string|max:150',
            'title' => 'required|string|max:250', 'description' => 'nullable|string|max:5000',
            'expected_date' => 'nullable|date', 'currency' => 'required|in:XAF,USD,EUR',
            'tax_amount' => 'nullable|numeric|min:0', 'notes' => 'nullable|string|max:5000',
            'items' => 'required|array|min:1|max:100', 'items.*.description' => 'required|string|max:300',
            'items.*.quantity' => 'required|numeric|gt:0|max:1000000', 'items.*.unit_price' => 'required|numeric|min:0|max:1000000000',
            'reference' => 'prohibited', 'status' => 'prohibited', 'requested_by' => 'prohibited',
            'approved_by' => 'prohibited', 'approved_at' => 'prohibited', 'received_date' => 'prohibited',
            'subtotal' => 'prohibited', 'total' => 'prohibited',
        ]);
        if (empty($data['vendor_id']) && empty($data['vendor_name'])) $this->invalid('vendor_name', 'Select a vendor or enter a vendor name.');
        if (!empty($data['vendor_id']) && !Vendor::query()->whereKey($data['vendor_id'])->where('is_active', true)->exists()) $this->invalid('vendor_id', 'Select an active vendor.');
        $items = $data['items']; unset($data['items']);
        $record = DB::transaction(function () use ($data, $items, $actor) {
            $year = now()->year; $sequenceName = 'purchase-order:'.$year;
            DB::table('business_sequences')->insertOrIgnore(['name' => $sequenceName, 'last_value' => 0]);
            $sequence = DB::table('business_sequences')->where('name', $sequenceName)->lockForUpdate()->first();
            $previous = (int) $sequence->last_value;
            if ($previous === 0) {
                $prefix = 'PO-'.$year.'-';
                $previous = PurchaseOrder::query()->where('reference', 'like', $prefix.'%')->pluck('reference')
                    ->map(fn ($reference) => ctype_digit(substr($reference, strlen($prefix))) ? (int) substr($reference, strlen($prefix)) : 0)->max() ?? 0;
            }
            $next = $previous + 1;
            DB::table('business_sequences')->where('name', $sequenceName)->update(['last_value' => $next]);
            $record = PurchaseOrder::create($data + [
                'reference' => 'PO-'.$year.'-'.str_pad((string) $next, 5, '0', STR_PAD_LEFT),
                'status' => 'draft', 'requested_by' => $actor->id, 'tax_amount' => $data['tax_amount'] ?? 0,
            ]);
            $this->replacePurchaseOrderItems($record, $items); $record->recalculateTotals();
            DomainOutbox::append('event', 'finance.purchase_order_created.v1', ['references' => [['resource' => 'PurchaseOrder', 'id' => $record->id]], 'changed_fields' => array_keys($data)], $actor->id);
            return $record;
        }, 3);
        return WorkflowResult::payload(['id' => $record->id, 'reference' => $record->reference, 'status' => $record->status, 'subtotal' => $record->subtotal, 'total' => $record->total], 201)->with('success', 'Purchase order created.');
    }

    public function updatePurchaseOrder(WorkflowInput $input, PurchaseOrder $purchaseOrder): WorkflowResult
    {
        $actor = $this->adminActor($input);
        $data = $input->validate([
            'vendor_id' => 'sometimes|nullable|integer|exists:vendors,id', 'vendor_name' => 'sometimes|nullable|string|max:150',
            'title' => 'sometimes|required|string|max:250', 'description' => 'sometimes|nullable|string|max:5000',
            'expected_date' => 'sometimes|nullable|date', 'currency' => 'sometimes|required|in:XAF,USD,EUR',
            'tax_amount' => 'sometimes|nullable|numeric|min:0', 'notes' => 'sometimes|nullable|string|max:5000',
            'items' => 'sometimes|required|array|min:1|max:100', 'items.*.description' => 'required_with:items|string|max:300',
            'items.*.quantity' => 'required_with:items|numeric|gt:0|max:1000000', 'items.*.unit_price' => 'required_with:items|numeric|min:0|max:1000000000',
            'reference' => 'prohibited', 'status' => 'prohibited', 'requested_by' => 'prohibited',
            'approved_by' => 'prohibited', 'approved_at' => 'prohibited', 'received_date' => 'prohibited',
            'subtotal' => 'prohibited', 'total' => 'prohibited',
        ]);
        if ($data === []) $this->invalid('purchase_order', 'Provide at least one purchase order field to update.');
        if (!empty($data['vendor_id']) && !Vendor::query()->whereKey($data['vendor_id'])->where('is_active', true)->exists()) $this->invalid('vendor_id', 'Select an active vendor.');
        $items = $data['items'] ?? null; unset($data['items']);
        $record = DB::transaction(function () use ($purchaseOrder, $data, $items, $actor) {
            $record = PurchaseOrder::query()->lockForUpdate()->findOrFail($purchaseOrder->id);
            if ($record->status !== 'draft') $this->invalid('status', 'Only draft purchase orders can be edited.');
            $record->fill($data)->save(); if ($items !== null) $this->replacePurchaseOrderItems($record, $items); $record->recalculateTotals();
            DomainOutbox::append('event', 'finance.purchase_order_updated.v1', ['references' => [['resource' => 'PurchaseOrder', 'id' => $record->id]], 'changed_fields' => array_merge(array_keys($data), $items !== null ? ['items'] : [])], $actor->id);
            return $record;
        }, 3);
        return $this->outcome('purchase_order', $record->id, $record->status, 'Purchase order updated.', ['reference' => $record->reference, 'subtotal' => $record->subtotal, 'total' => $record->total]);
    }

    public function deletePurchaseOrder(WorkflowInput $input, PurchaseOrder $purchaseOrder): WorkflowResult
    {
        $actor = $this->adminActor($input);
        DB::transaction(function () use ($purchaseOrder, $actor) {
            $record = PurchaseOrder::query()->lockForUpdate()->findOrFail($purchaseOrder->id);
            if ($record->status !== 'draft') $this->invalid('status', 'Only draft purchase orders can be deleted.');
            if (SupplierBill::query()->where('purchase_order_id', $record->id)->exists()) $this->invalid('purchase_order', 'A purchase order with supplier bill history cannot be deleted.');
            DomainOutbox::append('event', 'finance.purchase_order_deleted.v1', ['references' => [['resource' => 'PurchaseOrder', 'id' => $record->id]]], $actor->id);
            $record->delete();
        }, 3);
        return WorkflowResult::payload(['id' => $purchaseOrder->id, 'status' => 'deleted'])->with('success', 'Draft purchase order deleted.');
    }

    private function replacePurchaseOrderItems(PurchaseOrder $record, array $items): void
    {
        $record->items()->delete();
        foreach ($items as $item) {
            $quantity = (float) $item['quantity']; $unitPrice = (float) $item['unit_price'];
            $record->items()->create(['description' => $item['description'], 'quantity' => $quantity, 'unit_price' => $unitPrice, 'total' => round($quantity * $unitPrice, 2)]);
        }
    }

    public function createSupplierBill(WorkflowInput $input): WorkflowResult
    {
        $actor = $this->adminActor($input);
        $data = $input->validate([
            'vendor_id' => 'nullable|integer|exists:vendors,id', 'vendor_name' => 'nullable|string|max:200',
            'purchase_order_id' => 'nullable|integer|exists:purchase_orders,id', 'bill_number' => 'nullable|string|max:100',
            'issue_date' => 'required|date', 'due_date' => 'nullable|date', 'currency' => 'required|in:XAF,USD,EUR',
            'tax_amount' => 'nullable|numeric|min:0', 'notes' => 'nullable|string|max:5000',
            'items' => 'required|array|min:1|max:100', 'items.*.description' => 'required|string|max:300',
            'items.*.quantity' => 'required|numeric|gt:0|max:1000000', 'items.*.unit_price' => 'required|numeric|min:0|max:1000000000',
            'reference' => 'prohibited', 'status' => 'prohibited', 'created_by' => 'prohibited', 'paid_at' => 'prohibited',
            'subtotal' => 'prohibited', 'total' => 'prohibited',
        ]);
        if (empty($data['vendor_id']) && empty($data['vendor_name'])) $this->invalid('vendor_name', 'Select a vendor or enter a vendor name.');
        if (!empty($data['vendor_id']) && !Vendor::query()->whereKey($data['vendor_id'])->where('is_active', true)->exists()) $this->invalid('vendor_id', 'Select an active vendor.');
        if (!empty($data['purchase_order_id']) && !PurchaseOrder::query()->whereKey($data['purchase_order_id'])->whereIn('status', ['approved', 'received'])->exists()) $this->invalid('purchase_order_id', 'Choose an approved or received purchase order.');
        if (!empty($data['purchase_order_id']) && !empty($data['vendor_id']) && PurchaseOrder::query()->whereKey($data['purchase_order_id'])->whereNotNull('vendor_id')->where('vendor_id', '!=', $data['vendor_id'])->exists()) $this->invalid('vendor_id', 'The linked purchase order belongs to a different vendor.');
        $items = $data['items']; unset($data['items']);
        $record = DB::transaction(function () use ($data, $items, $actor) {
            $year = now()->year; $sequenceName = 'supplier-bill:'.$year;
            DB::table('business_sequences')->insertOrIgnore(['name' => $sequenceName, 'last_value' => 0]);
            $sequence = DB::table('business_sequences')->where('name', $sequenceName)->lockForUpdate()->first();
            $previous = (int) $sequence->last_value;
            if ($previous === 0) {
                $prefix = 'BILL-'.$year.'-';
                $previous = SupplierBill::query()->where('reference', 'like', $prefix.'%')->pluck('reference')
                    ->map(fn ($reference) => ctype_digit(substr($reference, strlen($prefix))) ? (int) substr($reference, strlen($prefix)) : 0)->max() ?? 0;
            }
            $next = $previous + 1;
            DB::table('business_sequences')->where('name', $sequenceName)->update(['last_value' => $next]);
            $record = SupplierBill::create($data + [
                'reference' => 'BILL-'.$year.'-'.str_pad((string) $next, 4, '0', STR_PAD_LEFT),
                'status' => 'received', 'created_by' => $actor->id, 'tax_amount' => $data['tax_amount'] ?? 0,
            ]);
            $this->replaceSupplierBillItems($record, $items); $record->recalculateTotals();
            DomainOutbox::append('event', 'finance.supplier_bill_created.v1', ['references' => [['resource' => 'SupplierBill', 'id' => $record->id]], 'changed_fields' => array_keys($data)], $actor->id);
            return $record;
        }, 3);
        return WorkflowResult::payload(['id' => $record->id, 'reference' => $record->reference, 'status' => $record->status, 'subtotal' => $record->subtotal, 'total' => $record->total], 201)->with('success', 'Supplier bill recorded.');
    }

    public function updateSupplierBill(WorkflowInput $input, SupplierBill $supplierBill): WorkflowResult
    {
        $actor = $this->adminActor($input);
        $data = $input->validate([
            'vendor_id' => 'sometimes|nullable|integer|exists:vendors,id', 'vendor_name' => 'sometimes|nullable|string|max:200',
            'purchase_order_id' => 'sometimes|nullable|integer|exists:purchase_orders,id', 'bill_number' => 'sometimes|nullable|string|max:100',
            'issue_date' => 'sometimes|required|date', 'due_date' => 'sometimes|nullable|date', 'currency' => 'sometimes|required|in:XAF,USD,EUR',
            'tax_amount' => 'sometimes|nullable|numeric|min:0', 'notes' => 'sometimes|nullable|string|max:5000',
            'items' => 'sometimes|required|array|min:1|max:100', 'items.*.description' => 'required_with:items|string|max:300',
            'items.*.quantity' => 'required_with:items|numeric|gt:0|max:1000000', 'items.*.unit_price' => 'required_with:items|numeric|min:0|max:1000000000',
            'reference' => 'prohibited', 'status' => 'prohibited', 'created_by' => 'prohibited', 'paid_at' => 'prohibited',
            'subtotal' => 'prohibited', 'total' => 'prohibited',
        ]);
        if ($data === []) $this->invalid('supplier_bill', 'Provide at least one supplier bill field to update.');
        if (!empty($data['vendor_id']) && !Vendor::query()->whereKey($data['vendor_id'])->where('is_active', true)->exists()) $this->invalid('vendor_id', 'Select an active vendor.');
        if (!empty($data['purchase_order_id']) && !PurchaseOrder::query()->whereKey($data['purchase_order_id'])->whereIn('status', ['approved', 'received'])->exists()) $this->invalid('purchase_order_id', 'Choose an approved or received purchase order.');
        if (!empty($data['purchase_order_id']) && !empty($data['vendor_id']) && PurchaseOrder::query()->whereKey($data['purchase_order_id'])->whereNotNull('vendor_id')->where('vendor_id', '!=', $data['vendor_id'])->exists()) $this->invalid('vendor_id', 'The linked purchase order belongs to a different vendor.');
        $items = $data['items'] ?? null; unset($data['items']);
        $record = DB::transaction(function () use ($supplierBill, $data, $items, $actor) {
            $record = SupplierBill::query()->lockForUpdate()->findOrFail($supplierBill->id);
            if (!in_array($record->status, ['draft', 'received'], true)) $this->invalid('status', 'Only draft or received supplier bills can be edited.');
            $record->fill($data)->save(); if ($items !== null) $this->replaceSupplierBillItems($record, $items); $record->recalculateTotals();
            DomainOutbox::append('event', 'finance.supplier_bill_updated.v1', ['references' => [['resource' => 'SupplierBill', 'id' => $record->id]], 'changed_fields' => array_merge(array_keys($data), $items !== null ? ['items'] : [])], $actor->id);
            return $record;
        }, 3);
        return $this->outcome('supplier_bill', $record->id, $record->status, 'Supplier bill updated.', ['reference' => $record->reference, 'subtotal' => $record->subtotal, 'total' => $record->total]);
    }

    public function deleteSupplierBill(WorkflowInput $input, SupplierBill $supplierBill): WorkflowResult
    {
        $actor = $this->adminActor($input);
        DB::transaction(function () use ($supplierBill, $actor) {
            $record = SupplierBill::query()->lockForUpdate()->findOrFail($supplierBill->id);
            if (!in_array($record->status, ['draft', 'received'], true)) $this->invalid('status', 'Only draft or received supplier bills can be deleted.');
            DomainOutbox::append('event', 'finance.supplier_bill_deleted.v1', ['references' => [['resource' => 'SupplierBill', 'id' => $record->id]]], $actor->id);
            $record->delete();
        }, 3);
        return WorkflowResult::payload(['id' => $supplierBill->id, 'status' => 'deleted'])->with('success', 'Supplier bill deleted.');
    }

    private function replaceSupplierBillItems(SupplierBill $record, array $items): void
    {
        $record->items()->delete();
        foreach ($items as $item) {
            $quantity = (float) $item['quantity']; $unitPrice = (float) $item['unit_price'];
            $record->items()->create(['description' => $item['description'], 'quantity' => $quantity, 'unit_price' => $unitPrice, 'total' => round($quantity * $unitPrice, 2)]);
        }
    }

    private function validateExpenseReceipt(?string $path): void
    {
        if ($path !== null && $path !== ''
            && (!str_starts_with($path, 'expense-receipts/') || !Storage::disk('public')->exists($path))) {
            $this->invalid('receipt_path', 'Choose an existing receipt uploaded to expense-receipts.');
        }
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

<?php

namespace App\Contexts\Finance\Application;

use App\Models\{Invoice, InvoicePayment, User};
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

final class RecordInvoicePayment
{
    public function record(User $actor, int|string $id, array $values, bool $settleOutstanding = false): Invoice
    {
        abort_unless($actor->is_active && $actor->hasAnyRole(['admin', 'super_admin', 'accountant']), 403);
        $data = Validator::make($values, ['amount' => ($settleOutstanding ? 'nullable' : 'required').'|numeric|gt:0|decimal:0,2',
            'payment_method' => 'required|in:'.implode(',', array_keys(InvoicePayment::methodOptions())), 'payment_date' => 'required|date|before_or_equal:today',
            'reference_number' => 'nullable|string|max:255', 'notes' => 'nullable|string|max:5000'])->validate();
        return DB::transaction(function () use ($actor, $id, $data, $settleOutstanding) {
            $invoice = Invoice::query()->lockForUpdate()->findOrFail($id);
            if ($invoice->status === 'cancelled') throw ValidationException::withMessages(['invoice' => 'Cancelled invoices cannot receive payments.']);
            if ($settleOutstanding && $invoice->status === 'paid') return $invoice;
            $outstanding = (float) $invoice->amount_outstanding;
            $amount = $settleOutstanding ? $outstanding : (float) $data['amount'];
            if ($amount <= 0 || $amount > $outstanding) throw ValidationException::withMessages(['amount' => 'Payment must be positive and cannot exceed the outstanding amount.']);
            $payment = $invoice->payments()->create([...$data, 'amount' => $amount, 'recorded_by' => $actor->id]);
            $invoice->reconcilePaymentStatus();
            \App\Contexts\Shared\Infrastructure\DomainOutbox::append('event', 'finance.invoice_payment_recorded.v1', ['references' => [
                ['resource' => 'Invoice', 'id' => $invoice->id], ['resource' => 'InvoicePayment', 'id' => $payment->id],
            ]], $actor->id);
            return $invoice;
        }, 3);
    }
}

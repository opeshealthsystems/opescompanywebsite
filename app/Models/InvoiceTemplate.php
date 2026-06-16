<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceTemplate extends Model
{
    protected $fillable = [
        'name', 'customer_id', 'client_name', 'client_email', 'frequency',
        'next_due_date', 'end_date', 'payment_terms_days', 'currency', 'tax_rate',
        'line_items', 'notes', 'is_active', 'issued_by',
    ];

    protected $casts = [
        'next_due_date'      => 'date',
        'end_date'           => 'date',
        'line_items'         => 'array',
        'is_active'          => 'boolean',
        'tax_rate'           => 'decimal:2',
        'payment_terms_days' => 'integer',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function issuer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    public static function frequencyOptions(): array
    {
        return [
            'weekly'      => 'Weekly',
            'monthly'     => 'Monthly',
            'quarterly'   => 'Quarterly',
            'semi_annual' => 'Semi-Annual',
            'annual'      => 'Annual',
        ];
    }

    public function getNextDueDateAfter(): Carbon
    {
        $date = Carbon::parse($this->next_due_date);

        return match ($this->frequency) {
            'weekly'      => $date->addWeek(),
            'monthly'     => $date->addMonth(),
            'quarterly'   => $date->addMonths(3),
            'semi_annual' => $date->addMonths(6),
            'annual'      => $date->addYear(),
            default       => $date->addMonth(),
        };
    }

    public function generateInvoice(): ?Invoice
    {
        if (! $this->is_active) {
            return null;
        }

        if ($this->end_date && now()->isAfter($this->end_date)) {
            return null;
        }

        // invoice_number is auto-generated in Invoice::booted() — no need to set it.
        // invoices table has: customer_id (FK users), issued_by, license_id, status,
        //                     currency, tax_rate, notes, due_date, paid_at
        // subtotal / tax_amount / grand_total are computed attributes from items — not stored.
        $invoice = Invoice::create([
            'customer_id' => $this->customer_id,
            'issued_by'   => $this->issued_by,
            'status'      => 'draft',
            'currency'    => $this->currency,
            'tax_rate'    => $this->tax_rate,
            'due_date'    => now()->addDays($this->payment_terms_days)->toDateString(),
            'notes'       => $this->notes,
        ]);

        // Create line items — InvoiceItem::booted() auto-computes total = quantity * unit_price
        foreach (($this->line_items ?? []) as $item) {
            $invoice->items()->create([
                'description' => $item['description'] ?? '',
                'quantity'    => (int) ($item['quantity'] ?? 1),
                'unit_price'  => (int) ($item['unit_price'] ?? 0),
            ]);
        }

        // Advance next_due_date by one frequency period
        $this->update(['next_due_date' => $this->getNextDueDateAfter()]);

        return $invoice;
    }
}

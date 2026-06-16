<?php

namespace App\Models;

use App\Traits\LogsAuditActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Invoice extends Model
{
    use LogsAuditActivity;
    protected $fillable = [
        'customer_id', 'issued_by', 'license_id',
        'status', 'currency', 'tax_rate', 'notes', 'due_date', 'paid_at',
    ];

    protected $casts = [
        'tax_rate' => 'decimal:2',
        'due_date' => 'date',
        'paid_at'  => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (Invoice $invoice) {
            if (empty($invoice->invoice_number)) {
                $invoice->invoice_number = static::generateInvoiceNumber();
            }
        });
    }

    public static function generateInvoiceNumber(): string
    {
        return DB::transaction(function () {
            $year = now()->year;
            $last = static::whereYear('created_at', $year)
                ->lockForUpdate()
                ->orderByDesc('id')
                ->value('invoice_number');
            $seq = 1;
            if ($last && preg_match('/(\d+)$/', $last, $m)) {
                $seq = (int) $m[1] + 1;
            }
            return 'INV-' . $year . '-' . str_pad($seq, 5, '0', STR_PAD_LEFT);
        });
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function issuer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    public function license(): BelongsTo
    {
        return $this->belongsTo(License::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class)->orderBy('id');
    }

    public function creditNotes(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Models\CreditNote::class);
    }

    public function payments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Models\InvoicePayment::class);
    }

    public function getAmountPaidAttribute(): float
    {
        return (float) $this->payments()->sum('amount');
    }

    public function getAmountOutstandingAttribute(): float
    {
        return max(0, (float) $this->grand_total - $this->amount_paid);
    }

    public function reconcilePaymentStatus(): void
    {
        $paid  = $this->amount_paid;
        $total = (float) $this->grand_total;

        if ($total > 0 && $paid >= $total) {
            $this->update(['status' => 'paid', 'paid_at' => $this->paid_at ?? now()]);
        }
    }

    public function getSubtotalAttribute(): int
    {
        return $this->items->sum('total');
    }

    public function getTaxAmountAttribute(): int
    {
        return (int) round($this->subtotal * ((float) $this->tax_rate / 100));
    }

    public function getGrandTotalAttribute(): int
    {
        return $this->subtotal + $this->taxAmount;
    }

    public function isOverdue(): bool
    {
        return $this->due_date !== null
            && $this->due_date->isPast()
            && in_array($this->status, ['draft', 'sent']);
    }

    public static function statusOptions(): array
    {
        return [
            'draft'     => 'Draft',
            'sent'      => 'Sent',
            'paid'      => 'Paid',
            'overdue'   => 'Overdue',
            'cancelled' => 'Cancelled',
        ];
    }

    public function formatAmount(int $amount): string
    {
        return number_format($amount) . ' ' . $this->currency;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SupplierBill extends Model
{
    protected $fillable = [
        'reference', 'vendor_id', 'vendor_name', 'purchase_order_id',
        'bill_number', 'status', 'issue_date', 'due_date',
        'subtotal', 'tax_amount', 'total', 'currency',
        'paid_at', 'notes', 'created_by',
    ];

    protected $casts = [
        'issue_date'  => 'date',
        'due_date'    => 'date',
        'paid_at'     => 'date',
        'subtotal'    => 'decimal:2',
        'tax_amount'  => 'decimal:2',
        'total'       => 'decimal:2',
    ];

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(SupplierBillItem::class);
    }

    public static function generateReference(): string
    {
        $year = now()->year;
        $last = static::where('reference', 'like', "BILL-{$year}-%")
            ->orderByDesc('reference')
            ->value('reference');
        $next = $last ? (int) preg_replace('/.*-/', '', $last) + 1 : 1;
        return "BILL-{$year}-" . str_pad($next, 4, '0', STR_PAD_LEFT);
    }

    public static function statusOptions(): array
    {
        return [
            'draft'    => 'Draft',
            'received' => 'Received',
            'approved' => 'Approved',
            'paid'     => 'Paid',
            'overdue'  => 'Overdue',
            'disputed' => 'Disputed',
        ];
    }

    public function recalculateTotals(): void
    {
        $this->subtotal = $this->items()->sum('total');
        $this->total    = $this->subtotal + $this->tax_amount;
        $this->save();
    }

    public function formatTotal(): string
    {
        return $this->currency . ' ' . number_format((float) $this->total, 0);
    }

    public function isOverdue(): bool
    {
        return $this->due_date
            && $this->due_date->isPast()
            && ! in_array($this->status, ['paid', 'disputed']);
    }
}

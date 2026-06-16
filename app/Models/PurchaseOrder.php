<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseOrder extends Model
{
    protected $fillable = [
        'reference', 'vendor_id', 'vendor_name', 'title', 'description', 'status',
        'requested_by', 'approved_by', 'approved_at',
        'expected_date', 'received_date',
        'subtotal', 'tax_amount', 'total', 'currency', 'notes',
    ];

    protected $casts = [
        'approved_at'   => 'datetime',
        'expected_date' => 'date',
        'received_date' => 'date',
        'subtotal'      => 'decimal:2',
        'tax_amount'    => 'decimal:2',
        'total'         => 'decimal:2',
    ];

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public static function generateReference(): string
    {
        $year = now()->year;
        $last = static::whereYear('created_at', $year)->max('reference');
        $seq  = 1;
        if ($last && preg_match('/(\d+)$/', $last, $m)) {
            $seq = ((int) $m[1]) + 1;
        }
        return 'PO-' . $year . '-' . str_pad($seq, 5, '0', STR_PAD_LEFT);
    }

    public static function statusOptions(): array
    {
        return [
            'draft'     => 'Draft',
            'submitted' => 'Submitted',
            'approved'  => 'Approved',
            'received'  => 'Received',
            'cancelled' => 'Cancelled',
        ];
    }

    public function recalculateTotals(): void
    {
        $this->subtotal  = $this->items()->sum('total');
        $this->total     = $this->subtotal + $this->tax_amount;
        $this->save();
    }

    public function formatTotal(): string
    {
        return $this->currency . ' ' . number_format((float) $this->total, 0);
    }
}

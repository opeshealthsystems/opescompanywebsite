<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CreditNote extends Model
{
    protected $fillable = [
        'reference', 'invoice_id', 'reason', 'status',
        'subtotal', 'tax_amount', 'total', 'currency',
        'issued_at', 'notes', 'created_by',
    ];

    protected $casts = [
        'issued_at'  => 'date',
        'subtotal'   => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total'      => 'decimal:2',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(CreditNoteItem::class);
    }

    public static function generateReference(): string
    {
        $year = now()->year;
        $last = static::where('reference', 'like', "CN-{$year}-%")
            ->orderByDesc('reference')
            ->value('reference');
        $next = $last ? (int) preg_replace('/.*-/', '', $last) + 1 : 1;
        return "CN-{$year}-" . str_pad($next, 4, '0', STR_PAD_LEFT);
    }

    public static function statusOptions(): array
    {
        return [
            'draft'   => 'Draft',
            'issued'  => 'Issued',
            'applied' => 'Applied',
            'void'    => 'Void',
        ];
    }

    public function recalculateTotals(): void
    {
        $this->subtotal = $this->items()->sum('total');
        $this->total = $this->subtotal + $this->tax_amount;
        $this->save();
    }

    public function formatTotal(): string
    {
        return $this->currency . ' ' . number_format((float) $this->total, 0);
    }
}

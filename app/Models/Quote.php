<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quote extends Model
{
    protected $fillable = [
        'reference', 'lead_id', 'title', 'status', 'valid_until',
        'subtotal', 'tax_rate', 'tax_amount', 'total', 'currency', 'notes', 'created_by',
    ];

    protected $casts = [
        'valid_until'  => 'date',
        'subtotal'     => 'decimal:2',
        'tax_rate'     => 'decimal:2',
        'tax_amount'   => 'decimal:2',
        'total'        => 'decimal:2',
    ];

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(QuoteItem::class);
    }

    public static function generateReference(): string
    {
        $year = now()->year;
        $last = static::where('reference', 'like', "QTE-{$year}-%")->orderByDesc('reference')->value('reference');
        $next = $last ? (int) preg_replace('/.*-/', '', $last) + 1 : 1;
        return "QTE-{$year}-" . str_pad($next, 4, '0', STR_PAD_LEFT);
    }

    public static function statusOptions(): array
    {
        return [
            'draft'    => 'Draft',
            'sent'     => 'Sent',
            'accepted' => 'Accepted',
            'rejected' => 'Rejected',
            'expired'  => 'Expired',
        ];
    }

    public function recalculateTotals(): void
    {
        $this->subtotal   = $this->items()->sum('total');
        $this->tax_amount = $this->subtotal * ($this->tax_rate / 100);
        $this->total      = $this->subtotal + $this->tax_amount;
        $this->save();
    }

    public function formatTotal(): string
    {
        return $this->currency . ' ' . number_format((float) $this->total, 0);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceItem extends Model
{
    protected $fillable = [
        'invoice_id', 'description', 'quantity', 'unit_price',
    ];

    protected $casts = [
        'quantity'   => 'integer',
        'unit_price' => 'integer',
        'total'      => 'integer',
    ];

    protected static function booted(): void
    {
        static::saving(function (InvoiceItem $item) {
            $item->total = $item->quantity * $item->unit_price;
        });
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }
}

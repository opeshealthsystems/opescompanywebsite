<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ObservedBy(\App\Observers\InvoicePaymentObserver::class)]
class InvoicePayment extends Model
{
    protected $fillable = [
        'invoice_id',
        'amount',
        'payment_method',
        'payment_date',
        'reference_number',
        'notes',
        'recorded_by',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount'       => 'decimal:2',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public static function methodOptions(): array
    {
        return [
            'bank_transfer' => 'Bank Transfer',
            'mobile_money'  => 'Mobile Money',
            'cash'          => 'Cash',
            'cheque'        => 'Cheque',
            'credit_card'   => 'Credit Card',
            'other'         => 'Other',
        ];
    }
}

<?php

namespace App\Observers;

use App\Models\InvoicePayment;
use App\Notifications\PaymentReceived;

class InvoicePaymentObserver
{
    public function created(InvoicePayment $payment): void
    {
        $payment->invoice?->customer?->notify(new PaymentReceived($payment));
    }
}

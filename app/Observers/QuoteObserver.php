<?php

namespace App\Observers;

use App\Models\Quote;
use App\Notifications\QuoteSent;
use Illuminate\Support\Facades\Notification;

class QuoteObserver
{
    public function updated(Quote $quote): void
    {
        if (! $quote->wasChanged('status') || $quote->status !== 'sent') {
            return;
        }

        $lead = $quote->lead;
        if ($lead && $lead->email) {
            Notification::route('mail', $lead->email)
                ->notify(new QuoteSent($quote, $lead->name ?? 'there'));
        }
    }
}

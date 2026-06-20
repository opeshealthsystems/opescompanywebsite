<?php

namespace App\Observers;

use App\Models\CreditNote;
use App\Notifications\CreditNoteIssued;

class CreditNoteObserver
{
    public function created(CreditNote $creditNote): void
    {
        if ($creditNote->status === 'issued') {
            $this->notifyCustomer($creditNote);
        }
    }

    public function updated(CreditNote $creditNote): void
    {
        if ($creditNote->wasChanged('status') && $creditNote->status === 'issued') {
            $this->notifyCustomer($creditNote);
        }
    }

    private function notifyCustomer(CreditNote $creditNote): void
    {
        $creditNote->invoice?->customer?->notify(new CreditNoteIssued($creditNote));
    }
}

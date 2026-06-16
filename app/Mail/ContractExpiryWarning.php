<?php

namespace App\Mail;

use App\Models\Contract;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContractExpiryWarning extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Contract $contract, public int $daysLeft) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Contract Expiry Notice: ' . $this->contract->title . ' (' . $this->daysLeft . ' days)'
        );
    }

    public function content(): Content
    {
        return new Content(view: 'mail.contract-expiry-warning');
    }
}

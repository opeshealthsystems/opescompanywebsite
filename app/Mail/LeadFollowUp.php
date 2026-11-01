<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

final class LeadFollowUp extends Mailable
{
    use Queueable;

    public function __construct(
        public string $subjectLine,
        public string $messageBody,
        public ?string $recipientName,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: $this->subjectLine);
    }

    public function content(): Content
    {
        return new Content(view: 'mail.lead-follow-up', with: [
            'messageBody' => $this->messageBody,
            'recipientName' => $this->recipientName,
        ]);
    }
}

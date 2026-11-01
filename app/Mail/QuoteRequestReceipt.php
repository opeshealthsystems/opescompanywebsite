<?php

namespace App\Mail;

use App\Models\Lead;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Headers;

class QuoteRequestReceipt extends Mailable
{
    public function __construct(public Lead $lead, public string $eventId) {}

    public function envelope(): Envelope
    {
        $french = $this->lead->locale === 'fr';

        return new Envelope(
            subject: $french ? 'Copie de votre demande de devis OPES' : 'Copy of your OPES quote request',
            replyTo: [new Address(config('website_forms.recipient'))],
        );
    }

    public function headers(): Headers
    {
        return new Headers(
            messageId: 'quote-request-'.$this->eventId.'@opeshealthsystems.com',
            text: ['X-OHS-Event-ID' => $this->eventId],
        );
    }

    public function content(): Content
    {
        $parts = array_map('trim', explode('—', (string) $this->lead->products, 3));

        return new Content(view: 'mail.quote-request-receipt', with: [
            'lead' => $this->lead,
            'product' => $parts[0] ?? '',
            'option' => $parts[1] ?? '',
            'price' => $parts[2] ?? '',
            'french' => $this->lead->locale === 'fr',
            'reference' => 'QREQ-'.$this->lead->id,
        ]);
    }
}

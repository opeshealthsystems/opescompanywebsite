<?php

namespace App\Mail;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TicketStatusChanged extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Ticket $ticket, public string $newStatus) {}

    public function envelope(): Envelope
    {
        $label = match ($this->newStatus) {
            'resolved'         => 'Resolved',
            'closed'           => 'Closed',
            'pending_customer' => 'Action Required',
            default            => ucfirst(str_replace('_', ' ', $this->newStatus)),
        };
        return new Envelope(subject: 'Ticket #' . $this->ticket->id . ' ' . $label . ' — ' . $this->ticket->subject);
    }

    public function content(): Content
    {
        return new Content(view: 'mail.ticket-status-changed');
    }
}

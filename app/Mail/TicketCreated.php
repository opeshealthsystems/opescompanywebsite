<?php

namespace App\Mail;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TicketCreated extends Mailable
{
    use Queueable, SerializesModels;

    private bool $templateChecked = false;
    private ?array $rendered = null;

    public function __construct(public Ticket $ticket) {}

    public function envelope(): Envelope
    {
        $rendered = $this->resolveRendered();
        return new Envelope(subject: $rendered ? $rendered['subject'] : $this->fallbackSubject());
    }

    public function content(): Content
    {
        $rendered = $this->resolveRendered();

        if ($rendered !== null) {
            return new Content(
                htmlString: '<html><body style="font-family:sans-serif;max-width:600px;margin:auto;padding:20px;">'
                    . nl2br(e($rendered['body']))
                    . '</body></html>',
            );
        }

        return new Content(view: 'mail.ticket-created');
    }

    private function resolveRendered(): ?array
    {
        if (! $this->templateChecked) {
            $this->templateChecked = true;
            $template = \App\Models\EmailTemplate::forType($this->templateType());
            $this->rendered = $template ? $template->render($this->templateVariables()) : null;
        }

        return $this->rendered;
    }

    protected function templateType(): string
    {
        return 'ticket_created';
    }

    protected function templateVariables(): array
    {
        $customer = $this->ticket->customer;

        return [
            'name'            => $customer ? $customer->name : '',
            'ticket_id'       => (string) $this->ticket->id,
            'ticket_subject'  => $this->ticket->subject,
            'ticket_priority' => $this->ticket->priority,
            'ticket_status'   => $this->ticket->status,
        ];
    }

    protected function fallbackSubject(): string
    {
        return 'Support Ticket #' . $this->ticket->id . ' Received — OPES Health Systems';
    }
}

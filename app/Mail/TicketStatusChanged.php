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

    private bool $templateChecked = false;
    private ?array $rendered = null;

    public function __construct(public Ticket $ticket, public string $newStatus) {}

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

        return new Content(view: 'mail.ticket-status-changed');
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
        return 'general';
    }

    protected function templateVariables(): array
    {
        $customer    = $this->ticket->customer;
        $statusLabel = match ($this->newStatus) {
            'resolved'         => 'Resolved',
            'closed'           => 'Closed',
            'pending_customer' => 'Action Required',
            default            => ucfirst(str_replace('_', ' ', $this->newStatus)),
        };

        return [
            'name'           => $customer ? $customer->name : '',
            'ticket_id'      => (string) $this->ticket->id,
            'ticket_subject' => $this->ticket->subject,
            'new_status'     => $statusLabel,
        ];
    }

    protected function fallbackSubject(): string
    {
        $label = match ($this->newStatus) {
            'resolved'         => 'Resolved',
            'closed'           => 'Closed',
            'pending_customer' => 'Action Required',
            default            => ucfirst(str_replace('_', ' ', $this->newStatus)),
        };

        return 'Ticket #' . $this->ticket->id . ' ' . $label . ' — ' . $this->ticket->subject;
    }
}

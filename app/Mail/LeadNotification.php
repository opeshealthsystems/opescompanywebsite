<?php

namespace App\Mail;

use App\Models\Lead;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LeadNotification extends Mailable
{
    use Queueable, SerializesModels;

    private bool $templateChecked = false;
    private ?array $rendered = null;

    public function __construct(public Lead $lead) {}

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

        return new Content(view: 'mail.lead-notification');
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
        return [
            'name'          => $this->lead->name,
            'email'         => $this->lead->email,
            'phone'         => $this->lead->phone ?? '',
            'facility_type' => $this->lead->facility_type ?? '',
            'message'       => $this->lead->message ?? '',
        ];
    }

    protected function fallbackSubject(): string
    {
        return 'New Demo Request: ' . $this->lead->name;
    }
}

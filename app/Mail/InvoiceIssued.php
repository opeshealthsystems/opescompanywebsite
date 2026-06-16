<?php

namespace App\Mail;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InvoiceIssued extends Mailable
{
    use Queueable, SerializesModels;

    private bool $templateChecked = false;
    private ?array $rendered = null;

    public function __construct(public Invoice $invoice) {}

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

        return new Content(view: 'mail.invoice-issued');
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
        return 'invoice_sent';
    }

    protected function templateVariables(): array
    {
        $customer = $this->invoice->customer;

        return [
            'name'           => $customer ? $customer->name : '',
            'invoice_number' => $this->invoice->invoice_number,
            'due_date'       => $this->invoice->due_date ? $this->invoice->due_date->format('Y-m-d') : '',
            'currency'       => $this->invoice->currency,
        ];
    }

    protected function fallbackSubject(): string
    {
        return 'Invoice ' . $this->invoice->invoice_number . ' from OPES Health Systems';
    }
}

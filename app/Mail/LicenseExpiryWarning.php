<?php

namespace App\Mail;

use App\Models\License;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LicenseExpiryWarning extends Mailable
{
    use Queueable, SerializesModels;

    private bool $templateChecked = false;
    private ?array $rendered = null;

    public function __construct(public License $license, public int $daysLeft) {}

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

        return new Content(view: 'mail.license-expiry-warning');
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
        $customer = $this->license->customer;

        return [
            'name'         => $customer ? $customer->name : '',
            'product_name' => $this->license->product_name,
            'days_left'    => (string) $this->daysLeft,
            'end_date'     => $this->license->end_date ? $this->license->end_date->format('Y-m-d') : '',
            'license_key'  => $this->license->license_key,
        ];
    }

    protected function fallbackSubject(): string
    {
        return 'Your OPES License Expires in ' . $this->daysLeft . ' Days — ' . $this->license->product_name;
    }
}

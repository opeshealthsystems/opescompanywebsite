<?php

namespace App\Notifications;

use App\Models\ValidationCertificate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CertificateIssued extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public ValidationCertificate $certificate) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your Clinical Validation certificate is ready')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Congratulations! Your Clinical Validation certificate has been issued.')
            ->line('Tier: ' . ucfirst($this->certificate->tier) . ' · Score: ' . $this->certificate->score . '/100 · Number: ' . $this->certificate->certificate_number)
            ->action('Download certificate', route('practitioner.certificates', ['locale' => 'en']))
            ->line('Thank you for your contribution to OPES Health validation.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'  => 'validation.certificate_issued',
            'title' => 'Certificate issued',
            'body'  => ucfirst($this->certificate->tier) . ' certificate (' . $this->certificate->score . '/100) is ready to download.',
            'icon'  => 'academic-cap',
            'url'   => route('practitioner.certificates', ['locale' => 'en']),
        ];
    }
}

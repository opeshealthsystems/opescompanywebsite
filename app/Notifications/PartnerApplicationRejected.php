<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PartnerApplicationRejected extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public string $contactName, public string $organizationName, public ?string $reason = null) {}

    public function via(object $notifiable): array
    {
        return $notifiable instanceof AnonymousNotifiable ? ['mail'] : ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject('Update on your partnership application')
            ->greeting('Hello ' . $this->contactName . ',')
            ->line('Thank you for your interest in partnering with OPES Health Systems. After review, we are unable to move forward with the application from ' . $this->organizationName . ' at this time.');

        if ($this->reason) {
            $mail->line('Note: ' . $this->reason);
        }

        return $mail->line('We appreciate your interest and wish ' . $this->organizationName . ' continued success.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'  => 'partner.application_rejected',
            'title' => 'Partnership application update',
            'body'  => 'Your partnership application was not accepted.',
            'icon'  => 'users',
            'url'   => null,
        ];
    }
}

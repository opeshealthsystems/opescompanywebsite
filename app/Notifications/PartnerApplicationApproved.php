<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PartnerApplicationApproved extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public string $contactName, public string $organizationName) {}

    public function via(object $notifiable): array
    {
        return $notifiable instanceof AnonymousNotifiable ? ['mail'] : ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your partnership with OPES is approved')
            ->greeting('Hello ' . $this->contactName . ',')
            ->line('We are delighted to approve the partnership with ' . $this->organizationName . '.')
            ->line('Our partnerships team will reach out shortly with next steps.')
            ->action('Visit OPES', route('home', ['locale' => 'en']));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'  => 'partner.application_approved',
            'title' => 'Partnership approved',
            'body'  => 'Your partnership with OPES has been approved.',
            'icon'  => 'users',
            'url'   => route('home', ['locale' => 'en']),
        ];
    }
}

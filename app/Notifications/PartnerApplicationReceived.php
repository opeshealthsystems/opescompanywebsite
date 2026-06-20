<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PartnerApplicationReceived extends Notification implements ShouldQueue
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
            ->subject('We received your partnership application')
            ->greeting('Hello ' . $this->contactName . ',')
            ->line('Thank you for your interest in partnering with OPES Health Systems on behalf of ' . $this->organizationName . '.')
            ->line('Your application is under review and our team will be in touch.')
            ->action('Visit OPES', route('home', ['locale' => 'en']));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'  => 'partner.application_received',
            'title' => 'Partnership application received',
            'body'  => 'Your partnership application is under review.',
            'icon'  => 'users',
            'url'   => route('home', ['locale' => 'en']),
        ];
    }
}

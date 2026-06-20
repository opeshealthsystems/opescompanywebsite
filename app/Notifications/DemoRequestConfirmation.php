<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DemoRequestConfirmation extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public string $name) {}

    public function via(object $notifiable): array
    {
        return $notifiable instanceof AnonymousNotifiable ? ['mail'] : ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Thanks for requesting a demo')
            ->greeting('Hello ' . $this->name . ',')
            ->line('Thank you for requesting a demo of OPES Health Systems. Our team will be in touch shortly to schedule a time.')
            ->action('Visit OPES', route('home', ['locale' => 'en']))
            ->line('We look forward to showing you what OPES can do.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'  => 'crm.demo_request_confirmation',
            'title' => 'Demo request received',
            'body'  => 'We received your demo request and will be in touch.',
            'icon'  => 'calendar',
            'url'   => route('home', ['locale' => 'en']),
        ];
    }
}

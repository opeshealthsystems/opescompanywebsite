<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TesterApplicationReceived extends Notification implements ShouldQueue
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
            ->subject('We received your tester application')
            ->greeting('Hello ' . $this->name . ',')
            ->line('Thank you for applying to the OPES Health tester program. Your application is under review.')
            ->line('We will be in touch once a decision has been made.')
            ->action('Visit OPES', route('home', ['locale' => 'en']));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'  => 'tester.application_received',
            'title' => 'Tester application received',
            'body'  => 'Your tester application is under review.',
            'icon'  => 'beaker',
            'url'   => route('home', ['locale' => 'en']),
        ];
    }
}

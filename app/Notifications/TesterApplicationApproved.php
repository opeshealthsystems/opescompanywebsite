<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TesterApplicationApproved extends Notification implements ShouldQueue
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
            ->subject("You're approved as an OPES tester")
            ->greeting('Hello ' . $this->name . ',')
            ->line('Congratulations! Your tester application has been approved.')
            ->line('You can now access the OPES tester portal to receive and complete testing assignments.')
            ->action('Open tester portal', route('tester.dashboard', ['locale' => 'en']));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'  => 'tester.application_approved',
            'title' => 'Tester application approved',
            'body'  => 'Welcome to the OPES tester program.',
            'icon'  => 'beaker',
            'url'   => route('tester.dashboard', ['locale' => 'en']),
        ];
    }
}

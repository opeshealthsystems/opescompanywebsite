<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TesterApplicationRejected extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public string $name, public ?string $reason = null) {}

    public function via(object $notifiable): array
    {
        return $notifiable instanceof AnonymousNotifiable ? ['mail'] : ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject('Update on your tester application')
            ->greeting('Hello ' . $this->name . ',')
            ->line('Thank you for your interest in the OPES Health tester program. After review, we are unable to move forward with your application at this time.');

        if ($this->reason) {
            $mail->line('Note: ' . $this->reason);
        }

        return $mail->line('We appreciate the time you took to apply and wish you the best.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'  => 'tester.application_rejected',
            'title' => 'Tester application update',
            'body'  => 'Your tester application was not accepted.',
            'icon'  => 'beaker',
            'url'   => null,
        ];
    }
}

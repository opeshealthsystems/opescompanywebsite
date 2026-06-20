<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AccountDeactivated extends Notification implements ShouldQueue
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your OPES account has been deactivated')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Your OPES Health Systems account has been deactivated and you can no longer sign in.')
            ->line('If you believe this is a mistake, please contact support@opeshealthsystems.com.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'  => 'account.deactivated',
            'title' => 'Account deactivated',
            'body'  => 'Your account has been deactivated.',
            'icon'  => 'lock-closed',
            'url'   => null,
        ];
    }
}

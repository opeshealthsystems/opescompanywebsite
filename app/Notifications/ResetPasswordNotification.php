<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public string $token) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = route('password.reset.form', ['locale' => 'en', 'token' => $this->token])
            . '?email=' . urlencode($notifiable->getEmailForPasswordReset());

        return (new MailMessage)
            ->subject('Reset your OPES password')
            ->greeting('Hello,')
            ->line('You are receiving this email because we received a password reset request for your OPES Health Systems account.')
            ->action('Reset password', $url)
            ->line('This password reset link will expire shortly. If you did not request a password reset, no further action is required.');
    }
}

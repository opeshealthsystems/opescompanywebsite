<?php

namespace App\Notifications;

use App\Models\IssueReport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class IssueClosed extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public IssueReport $issue) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your issue has been closed')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Your reported issue "' . $this->issue->title . '" has been closed.')
            ->line('Thank you for your contribution to validating OPES Health software.')
            ->action('View issue', route('practitioner.validation.issues.show', ['locale' => 'en', 'issue' => $this->issue->id]));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'  => 'validation.issue_closed',
            'title' => 'Issue closed',
            'body'  => 'Your issue "' . $this->issue->title . '" has been closed.',
            'icon'  => 'check-circle',
            'url'   => route('practitioner.validation.issues.show', ['locale' => 'en', 'issue' => $this->issue->id]),
        ];
    }
}

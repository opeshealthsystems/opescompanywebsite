<?php

namespace App\Notifications;

use App\Models\IssueReport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class IssueSubmitted extends Notification implements ShouldQueue
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
            ->subject('We received your issue report')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Your report "' . $this->issue->title . '" was submitted and is awaiting clinical review.')
            ->action('View issue', route('practitioner.validation.issues.show', ['locale' => 'en', 'issue' => $this->issue->id]))
            ->line('Thank you for helping validate OPES Health software.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'  => 'validation.issue_submitted',
            'title' => 'Issue report received',
            'body'  => 'Your report "' . $this->issue->title . '" is awaiting clinical review.',
            'icon'  => 'clipboard-document-check',
            'url'   => route('practitioner.validation.issues.show', ['locale' => 'en', 'issue' => $this->issue->id]),
        ];
    }
}

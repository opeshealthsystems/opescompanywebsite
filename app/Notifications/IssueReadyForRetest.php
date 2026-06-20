<?php

namespace App\Notifications;

use App\Models\IssueReport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class IssueReadyForRetest extends Notification implements ShouldQueue
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
            ->subject('Your reported issue has been fixed — please retest')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('The development team has fixed your reported issue "' . $this->issue->title . '".')
            ->line('Please verify the fix and submit a retest result from the issue page.')
            ->action('Retest now', route('practitioner.validation.issues.show', ['locale' => 'en', 'issue' => $this->issue->id]));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'  => 'validation.issue_ready_for_retest',
            'title' => 'Fixed — please retest',
            'body'  => 'Your issue "' . $this->issue->title . '" is ready for retest.',
            'icon'  => 'wrench-screwdriver',
            'url'   => route('practitioner.validation.issues.show', ['locale' => 'en', 'issue' => $this->issue->id]),
        ];
    }
}

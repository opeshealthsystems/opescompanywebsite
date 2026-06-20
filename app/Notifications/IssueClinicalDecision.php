<?php

namespace App\Notifications;

use App\Models\IssueReport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class IssueClinicalDecision extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public IssueReport $issue, public string $decision, public ?string $notes = null) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    private function headline(): string
    {
        return match ($this->decision) {
            'approved_for_product_review' => 'Your issue passed clinical review',
            'needs_more_information'      => 'Your issue needs more information',
            'rejected'                    => 'Your issue was not accepted',
            default                        => 'Update on your issue',
        };
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject($this->headline())
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Clinical review of your report "' . $this->issue->title . '" is complete: ' . $this->headline() . '.');

        if ($this->notes) {
            $mail->line('Reviewer note: ' . $this->notes);
        }

        return $mail->action('View issue', route('practitioner.validation.issues.show', ['locale' => 'en', 'issue' => $this->issue->id]));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'  => 'validation.issue_clinical_decision',
            'title' => $this->headline(),
            'body'  => 'Clinical review complete for "' . $this->issue->title . '".',
            'icon'  => 'chat-bubble-left-right',
            'url'   => route('practitioner.validation.issues.show', ['locale' => 'en', 'issue' => $this->issue->id]),
        ];
    }
}

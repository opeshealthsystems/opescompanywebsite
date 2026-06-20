<?php

namespace App\Notifications;

use App\Models\IssueReport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class IssueProductDecision extends Notification implements ShouldQueue
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
            'accepted'           => 'Your issue was accepted',
            'sent_to_development' => 'Your issue was sent to development',
            'duplicate'          => 'Your issue was marked a duplicate',
            'rejected'           => 'Your issue was not accepted',
            default               => 'Update on your issue',
        };
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject($this->headline())
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Product review of your report "' . $this->issue->title . '" is complete: ' . $this->headline() . '.');

        if ($this->notes) {
            $mail->line('Reviewer note: ' . $this->notes);
        }

        return $mail->action('View issue', route('practitioner.validation.issues.show', ['locale' => 'en', 'issue' => $this->issue->id]));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'  => 'validation.issue_product_decision',
            'title' => $this->headline(),
            'body'  => 'Product review complete for "' . $this->issue->title . '".',
            'icon'  => 'wrench-screwdriver',
            'url'   => route('practitioner.validation.issues.show', ['locale' => 'en', 'issue' => $this->issue->id]),
        ];
    }
}

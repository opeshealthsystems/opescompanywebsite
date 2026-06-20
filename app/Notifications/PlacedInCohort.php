<?php

namespace App\Notifications;

use App\Models\Cohort;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PlacedInCohort extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Cohort $cohort) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("You've been placed in a validation cohort")
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('You have been placed in the ' . $this->cohort->name . ' cohort (' . $this->cohort->specialty . ').')
            ->line('You can now log daily test sessions and report issues from your Validation Hub.')
            ->action('Open the Validation Hub', route('practitioner.validation.dashboard', ['locale' => 'en']))
            ->line('Thank you for helping validate OPES Health software.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'  => 'validation.placed_in_cohort',
            'title' => 'Placed in a validation cohort',
            'body'  => 'You joined the ' . $this->cohort->name . ' cohort.',
            'icon'  => 'clipboard-check',
            'url'   => route('practitioner.validation.dashboard', ['locale' => 'en']),
        ];
    }
}

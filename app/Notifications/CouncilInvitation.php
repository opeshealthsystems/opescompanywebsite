<?php

namespace App\Notifications;

use App\Models\AdvisoryCouncilMember;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CouncilInvitation extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public AdvisoryCouncilMember $member) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $term = $this->member->term_start
            ? $this->member->term_start->format('M Y') . ($this->member->term_end ? ' – ' . $this->member->term_end->format('M Y') : ' onward')
            : '';

        return (new MailMessage)
            ->subject("You've been invited to the Clinical Validation Advisory Council")
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('In recognition of your validation work, you are invited to join the OPES Clinical Validation Advisory Council as ' . $this->member->title . '.')
            ->line($term ? 'Term: ' . $term . '.' : 'Welcome to the council.')
            ->action('View certificates', route('practitioner.certificates', ['locale' => 'en']))
            ->line('We look forward to your continued guidance.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'  => 'validation.council_invitation',
            'title' => 'Advisory Council invitation',
            'body'  => 'You are invited to the Clinical Validation Advisory Council as ' . $this->member->title . '.',
            'icon'  => 'user-plus',
            'url'   => route('practitioner.certificates', ['locale' => 'en']),
        ];
    }
}

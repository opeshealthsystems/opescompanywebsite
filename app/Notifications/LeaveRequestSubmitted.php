<?php

namespace App\Notifications;

use App\Models\LeaveRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LeaveRequestSubmitted extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public LeaveRequest $leave) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    private function reviewUrl(object $notifiable): string
    {
        return $notifiable->hasRole('hr')
            ? route('hr.leave.index', ['locale' => 'en'])
            : route('manager.leave.index', ['locale' => 'en']);
    }

    public function toMail(object $notifiable): MailMessage
    {
        $employee = $this->leave->employee?->name ?? 'An employee';

        return (new MailMessage)
            ->subject('New leave request from ' . $employee)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line($employee . ' submitted a ' . $this->leave->type . ' leave request.')
            ->line('Dates: ' . $this->leave->start_date->format('M j, Y') . ' – ' . $this->leave->end_date->format('M j, Y') . ' (' . $this->leave->total_days . ' day(s)).')
            ->action('Review leave', $this->reviewUrl($notifiable));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'  => 'hr.leave_submitted',
            'title' => 'New leave request',
            'body'  => ($this->leave->employee?->name ?? 'An employee') . ' requested ' . $this->leave->type . ' leave.',
            'icon'  => 'calendar-days',
            'url'   => $this->reviewUrl($notifiable),
        ];
    }
}

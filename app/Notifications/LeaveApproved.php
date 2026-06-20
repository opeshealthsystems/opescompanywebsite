<?php

namespace App\Notifications;

use App\Models\LeaveRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LeaveApproved extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public LeaveRequest $leave) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your leave request was approved')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Your ' . $this->leave->type . ' leave request has been approved.')
            ->line('Dates: ' . $this->leave->start_date->format('M j, Y') . ' – ' . $this->leave->end_date->format('M j, Y') . ' (' . $this->leave->total_days . ' day(s)).')
            ->line('Enjoy your time off.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'  => 'hr.leave_approved',
            'title' => 'Leave approved',
            'body'  => 'Your ' . $this->leave->type . ' leave was approved.',
            'icon'  => 'calendar-days',
            'url'   => null,
        ];
    }
}

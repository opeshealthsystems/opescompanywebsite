<?php

namespace App\Notifications;

use App\Models\LeaveRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LeaveRejected extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public LeaveRequest $leave) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject('Your leave request was declined')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Your ' . $this->leave->type . ' leave request for ' . $this->leave->start_date->format('M j, Y') . ' – ' . $this->leave->end_date->format('M j, Y') . ' was not approved.');

        if ($this->leave->notes) {
            $mail->line('Note: ' . $this->leave->notes);
        }

        return $mail->line('Please contact your manager or HR for details.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'  => 'hr.leave_rejected',
            'title' => 'Leave declined',
            'body'  => 'Your ' . $this->leave->type . ' leave was declined.',
            'icon'  => 'calendar-days',
            'url'   => null,
        ];
    }
}

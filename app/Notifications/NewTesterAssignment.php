<?php

namespace App\Notifications;

use App\Models\TesterAssignment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewTesterAssignment extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public TesterAssignment $assignment) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject('You have a new testing assignment')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('You have been assigned a new testing task: "' . $this->assignment->title . '" on ' . $this->assignment->product_name . '.');

        if ($this->assignment->due_date) {
            $mail->line('Due date: ' . $this->assignment->due_date->format('M j, Y') . '.');
        }

        return $mail->action('View assignment', route('tester.assignments.show', ['locale' => 'en', 'id' => $this->assignment->id]));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'  => 'tester.new_assignment',
            'title' => 'New testing assignment',
            'body'  => $this->assignment->title . ' · ' . $this->assignment->product_name,
            'icon'  => 'clipboard-document-list',
            'url'   => route('tester.assignments.show', ['locale' => 'en', 'id' => $this->assignment->id]),
        ];
    }
}

<?php

namespace App\Notifications;

use App\Models\Quote;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class QuoteSent extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Quote $quote, public string $recipientName) {}

    public function via(object $notifiable): array
    {
        return $notifiable instanceof AnonymousNotifiable ? ['mail'] : ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your quote from OPES Health Systems')
            ->greeting('Hello ' . $this->recipientName . ',')
            ->line('Please find your quote "' . $this->quote->title . '" (' . $this->quote->reference . ').')
            ->line('Total: ' . $this->quote->formatTotal() . ($this->quote->valid_until ? ', valid until ' . $this->quote->valid_until->format('M j, Y') : '') . '.')
            ->line('Our team will follow up with you shortly. Reply to this email with any questions.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'  => 'crm.quote_sent',
            'title' => 'Quote sent',
            'body'  => 'Quote ' . $this->quote->reference . ' for ' . $this->quote->formatTotal() . '.',
            'icon'  => 'document-text',
            'url'   => null,
        ];
    }
}

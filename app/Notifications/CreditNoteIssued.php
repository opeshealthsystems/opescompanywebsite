<?php

namespace App\Notifications;

use App\Models\CreditNote;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CreditNoteIssued extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public CreditNote $creditNote) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    private function invoiceUrl(): string
    {
        return route('customer.invoices.show', ['locale' => 'en', 'id' => $this->creditNote->invoice_id]);
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('A credit note has been issued')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('A credit note (' . $this->creditNote->reference . ') of ' . number_format((float) $this->creditNote->total) . ' ' . $this->creditNote->currency . ' has been issued to your account.')
            ->action('View invoice', $this->invoiceUrl())
            ->line('Thank you for your business.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'  => 'accounting.credit_note_issued',
            'title' => 'Credit note issued',
            'body'  => 'Credit note ' . $this->creditNote->reference . ' for ' . number_format((float) $this->creditNote->total) . ' ' . $this->creditNote->currency . '.',
            'icon'  => 'receipt-refund',
            'url'   => $this->invoiceUrl(),
        ];
    }
}

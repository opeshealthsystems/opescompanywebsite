<?php

namespace App\Notifications;

use App\Models\InvoicePayment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentReceived extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public InvoicePayment $payment) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    private function invoiceUrl(): string
    {
        return route('customer.invoices.show', ['locale' => 'en', 'id' => $this->payment->invoice_id]);
    }

    public function toMail(object $notifiable): MailMessage
    {
        $invoice = $this->payment->invoice;

        return (new MailMessage)
            ->subject('We received your payment')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('We have received your payment of ' . number_format((float) $this->payment->amount) . ' ' . ($invoice?->currency ?? '') . ' for invoice ' . ($invoice?->invoice_number ?? '') . '.')
            ->action('View invoice', $this->invoiceUrl())
            ->line('Thank you for your business.');
    }

    public function toArray(object $notifiable): array
    {
        $invoice = $this->payment->invoice;

        return [
            'type'  => 'accounting.payment_received',
            'title' => 'Payment received',
            'body'  => 'Payment of ' . number_format((float) $this->payment->amount) . ' ' . ($invoice?->currency ?? '') . ' received.',
            'icon'  => 'banknotes',
            'url'   => $this->invoiceUrl(),
        ];
    }
}

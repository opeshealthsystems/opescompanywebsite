<?php

namespace App\Mail;

use App\Models\TrainingRecord;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TrainingExpiryWarning extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public TrainingRecord $training, public int $daysLeft) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Training Certification Expiring Soon: ' . $this->training->title . ' (' . $this->daysLeft . ' days)'
        );
    }

    public function content(): Content
    {
        return new Content(view: 'mail.training-expiry-warning');
    }
}

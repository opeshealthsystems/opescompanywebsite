<?php

namespace App\Mail;

use App\Models\License;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LicenseExpiryWarning extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public License $license, public int $daysLeft) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Your OPES License Expires in ' . $this->daysLeft . ' Days — ' . $this->license->product_name);
    }

    public function content(): Content
    {
        return new Content(view: 'mail.license-expiry-warning');
    }
}

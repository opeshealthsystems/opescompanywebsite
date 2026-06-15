<?php

namespace App\Mail;

use App\Models\License;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LicenseIssued extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public License $license) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Your OPES License is Ready — ' . $this->license->product_name);
    }

    public function content(): Content
    {
        return new Content(view: 'mail.license-issued');
    }
}

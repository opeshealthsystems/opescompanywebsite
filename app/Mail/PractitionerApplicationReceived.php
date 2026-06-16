<?php

namespace App\Mail;

use App\Models\PractitionerApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PractitionerApplicationReceived extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public PractitionerApplication $application) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: "Application Received — {$this->application->program->title}");
    }

    public function content(): Content
    {
        return new Content(view: 'mail.practitioner-application-received');
    }
}

<?php
namespace App\Mail;

use App\Models\CourseCertificate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CourseCertificateIssued extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public CourseCertificate $certificate) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Your Certificate is Ready');
    }

    public function content(): Content
    {
        return new Content(view: 'mail.course-certificate-issued');
    }
}

<?php
namespace App\Mail;

use App\Models\CourseEnrollment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CourseEnrollmentConfirmed extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public CourseEnrollment $enrollment) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Enrollment Confirmed');
    }

    public function content(): Content
    {
        return new Content(view: 'mail.course-enrollment-confirmed');
    }
}

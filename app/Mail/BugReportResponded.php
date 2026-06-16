<?php
namespace App\Mail;

use App\Models\PractitionerBugReport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BugReportResponded extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public PractitionerBugReport $bugReport) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Update on Your Bug Report: ' . $this->bugReport->title);
    }

    public function content(): Content
    {
        return new Content(view: 'mail.bug-report-responded');
    }
}

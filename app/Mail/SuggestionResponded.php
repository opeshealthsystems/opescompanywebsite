<?php
namespace App\Mail;

use App\Models\Suggestion;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SuggestionResponded extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Suggestion $suggestion) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Update on Your Suggestion: ' . $this->suggestion->title);
    }

    public function content(): Content
    {
        return new Content(view: 'mail.suggestion-responded');
    }
}

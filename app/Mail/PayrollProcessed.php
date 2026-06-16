<?php

namespace App\Mail;

use App\Models\PayrollEntry;
use App\Models\PayrollRun;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PayrollProcessed extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public PayrollRun $run, public PayrollEntry $entry) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Payslip for ' . $this->run->period_start->format('F Y') . ' is Ready'
        );
    }

    public function content(): Content
    {
        return new Content(view: 'mail.payroll-processed');
    }
}

<?php
namespace App\Mail;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class WebsiteSubmissionReceived extends Mailable
{
    public function __construct(public Model $submission, public bool $recovered = false, public ?string $eventId = null) {}

    public function headers(): \Illuminate\Mail\Mailables\Headers
    {
        return new \Illuminate\Mail\Mailables\Headers(
            messageId: $this->eventId ? 'website-form-'.$this->eventId.'@opeshealthsystems.com' : null,
            text: $this->eventId ? ['X-OHS-Event-ID' => $this->eventId] : [],
        );
    }

    public function envelope(): Envelope
    {
        $email = $this->submission->getAttribute('email');
        return new Envelope(
            subject: ($this->recovered ? '[OPES website recovery] ' : '[OPES website] ').class_basename($this->submission).' #'.$this->submission->getKey(),
            replyTo: filter_var($email, FILTER_VALIDATE_EMAIL) ? [new Address($email)] : [],
        );
    }

    public function content(): Content
    {
        $fields = [];
        foreach (['name','contact_name','organization_name','email','phone','facility_type','products','product_slug','message','country','city','institution_type','institution_size','preferred_date','partner_type','organization_type','annual_revenue_range','target_market','description','website','profession','specialty','institution_name','years_experience','devices','platforms','motivation','tech_experience','locale','source','created_at'] as $field) {
            $value = $this->submission->getAttribute($field);
            if ($value === null || $value === '') continue;
            $fields[str_replace('_', ' ', $field)] = is_array($value) ? implode(', ', $value) : (string) $value;
        }
        return new Content(view: 'mail.website-submission', with: ['fields' => $fields]);
    }
}

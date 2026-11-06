<?php
namespace Tests\Feature;

use App\Mail\WebsiteSubmissionReceived;
use App\Models\Lead;
use App\Services\WebsiteFormMailer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class WebsiteFormDeliveryTest extends TestCase
{
    use RefreshDatabase;
    private \Illuminate\Mail\MailManager $mailManager;

    protected function setUp(): void
    {
        parent::setUp();
        config(['website_forms.recipient' => 'contact@opeshealthsystems.com']);
        Role::findOrCreate('admin', 'web');
        Role::findOrCreate('super_admin', 'web');
        $this->mailManager = app('mail.manager');
        Mail::fake();
        Notification::fake();
    }

    public function test_all_public_enquiry_forms_notify_contact_inbox_immediately(): void
    {
        $this->post('/en/contact', ['name' => 'Contact Sender', 'email' => 'contact-sender@example.test', 'message' => 'Original enquiry'])->assertRedirect();
        $this->post('/fr/book-demo', ['name' => 'Demo Sender', 'email' => 'demo-sender@example.test', 'organization_name' => 'Example clinic'])->assertRedirect();
        $this->post('/en/become-a-partner', ['organization_name' => 'Example partner', 'contact_name' => 'Partner Sender', 'email' => 'partner-sender@example.test', 'country' => 'Cameroon', 'partner_type' => 'technology', 'description' => 'A proposed partnership'])->assertRedirect();
        $this->post('/fr/join-testers', ['name' => 'Tester Sender', 'email' => 'tester-sender@example.test', 'country' => 'Cameroon', 'profession' => 'doctor', 'years_experience' => 3, 'motivation' => 'Interested in evaluating software'])->assertRedirect();
        Mail::assertSent(WebsiteSubmissionReceived::class, 4);
        Mail::assertNothingQueued();
        foreach (['contact-sender@example.test', 'demo-sender@example.test', 'partner-sender@example.test', 'tester-sender@example.test'] as $email) {
            Mail::assertSent(WebsiteSubmissionReceived::class, fn ($mail) => $mail->hasTo('contact@opeshealthsystems.com') && $mail->submission->email === $email && $mail->envelope()->replyTo[0]->address === $email);
        }
        $this->assertCount(4, array_filter(app(WebsiteFormMailer::class)->records(), fn ($r) => $r['status'] === 'sent'));
    }

    public function test_health_data_and_cdss_service_interests_reach_the_shared_enquiry_record_and_contact_email(): void
    {
        foreach ([
            ['Health Data Services', 'data-sender@example.test'],
            ['Clinical Decision Support Systems (CDSS)', 'cdss-sender@example.test'],
        ] as [$service, $email]) {
            $this->post('/en/contact', [
                'name' => 'Service Enquiry',
                'email' => $email,
                'products' => $service,
                'message' => 'Please contact me about this service.',
            ])->assertRedirect();
        }

        $this->assertDatabaseHas('leads', ['email' => 'data-sender@example.test', 'products' => 'Health Data Services']);
        $this->assertDatabaseHas('leads', ['email' => 'cdss-sender@example.test', 'products' => 'Clinical Decision Support Systems (CDSS)']);
        Mail::assertSent(WebsiteSubmissionReceived::class, 2);
        foreach (['data-sender@example.test', 'cdss-sender@example.test'] as $email) {
            Mail::assertSent(WebsiteSubmissionReceived::class, fn ($mail) => $mail->hasTo('contact@opeshealthsystems.com') && $mail->submission->email === $email);
        }
    }

    public function test_recovery_does_not_resend_successful_deliveries(): void
    {
        $lead = Lead::create(['name' => 'Original enquiry', 'email' => 'sender@example.test', 'source' => 'contact', 'message' => 'Preserve this message']);
        $mailer = app(WebsiteFormMailer::class);
        $this->assertSame('sent', $mailer->deliver($lead, true));
        $this->assertSame('already-sent', $mailer->deliver($lead, true));
        $this->artisan('website-forms:deliver --recover')->assertSuccessful();
        Mail::assertSent(WebsiteSubmissionReceived::class, 1);
        $this->assertStringContainsString('Preserve this message', (new WebsiteSubmissionReceived($lead, true))->render());
    }

    public function test_failed_transport_is_saved_and_retried_after_delay(): void
    {
        $lead = Lead::create(['name' => 'Retry enquiry', 'email' => 'retry@example.test', 'source' => 'contact']);
        Mail::shouldReceive('to')->once()->with('contact@opeshealthsystems.com')->andThrow(new \RuntimeException('Transport unavailable'));
        $mailer = app(WebsiteFormMailer::class);
        $this->assertSame('failed', $mailer->deliver($lead));
        $this->assertSame('deferred', $mailer->deliver($lead));
        $this->travel(2)->minutes();
        Mail::swap(new \Illuminate\Support\Testing\Fakes\MailFake($this->mailManager));
        $this->artisan('website-forms:deliver --retry-only')->assertSuccessful();
        Mail::assertSent(WebsiteSubmissionReceived::class, 1);
        $this->assertSame('sent', array_values($mailer->records())[0]['status']);
    }
}

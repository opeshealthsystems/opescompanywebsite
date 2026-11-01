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

class EnquiriesApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::findOrCreate('admin', 'web');
        Mail::fake();
        Notification::fake();
    }

    public function test_public_submission_delivers_without_exposing_private_fields(): void
    {
        $this->postJson('/api/v1/enquiries', ['name' => 'Example', 'email' => 'sender@example.test', 'message' => 'A question', 'locale' => 'fr'])
            ->assertCreated()->assertExactJson(['data' => ['status' => 'recorded', 'delivery_status' => 'sent']])
            ->assertHeader('X-Request-ID');
        $this->assertStringContainsString('no-store', $this->postJson('/api/v1/enquiries', [])->headers->get('Cache-Control'));
        $this->assertDatabaseHas('leads', ['name' => 'Example', 'locale' => 'fr', 'source' => 'contact']);
        Mail::assertSent(WebsiteSubmissionReceived::class, fn ($mail) => $mail->hasTo('contact@opeshealthsystems.com'));
        Mail::assertNothingQueued();
    }

    public function test_invalid_and_privileged_fields_are_rejected_before_writing(): void
    {
        $this->postJson('/api/v1/enquiries', ['name' => 'Example', 'email' => 'invalid'])
            ->assertStatus(422)->assertHeader('Content-Type', 'application/problem+json')->assertJsonPath('status', 422)->assertJsonStructure(['errors' => ['email'], 'request_id']);
        $this->postJson('/api/v1/enquiries', ['name' => 'Example', 'email' => 'sender@example.test', 'recipient' => 'attacker@example.test'])
            ->assertStatus(422)->assertJsonStructure(['errors' => ['body']]);
        $this->assertDatabaseCount('leads', 0);
        Mail::assertNothingSent();
    }

    public function test_enquiries_cannot_be_listed_or_retrieved(): void
    {
        $this->getJson('/api/v1/enquiries')->assertStatus(405)->assertHeader('Content-Type', 'application/problem+json');
        $this->getJson('/api/v1/enquiries/1')->assertNotFound()->assertJsonPath('status', 404);
        $this->post('/api/v1/enquiries', ['name' => 'Example'])->assertStatus(415);
    }

    public function test_submission_limits_apply_to_invalid_requests_too(): void
    {
        for ($i = 0; $i < 5; $i++) $this->postJson('/api/v1/enquiries', [])->assertStatus(422);
        $this->postJson('/api/v1/enquiries', [])->assertStatus(429)->assertHeader('Retry-After')
            ->assertHeader('Content-Type', 'application/problem+json');
        $this->assertDatabaseCount('leads', 0);
    }

    public function test_transport_failure_preserves_enquiry_and_reports_pending(): void
    {
        Mail::shouldReceive('to')->once()->andThrow(new \RuntimeException('Secret transport diagnostic'));
        $this->postJson('/api/v1/enquiries', ['name' => 'Example', 'email' => 'sender@example.test'])
            ->assertCreated()->assertJsonPath('data.delivery_status', 'pending')->assertDontSee('Secret transport diagnostic');
        $this->assertDatabaseCount('leads', 1);
        $this->assertSame('failed', array_values(app(WebsiteFormMailer::class)->records())[0]['status']);
    }

    public function test_unexpected_errors_do_not_leak_exception_details(): void
    {
        $this->mock(\App\Contexts\Enquiries\Application\SubmitEnquiry::class)->shouldReceive('handle')->andThrow(new \RuntimeException('secret diagnostic'));
        $this->postJson('/api/v1/enquiries', ['name' => 'Example', 'email' => 'sender@example.test'])
            ->assertStatus(500)->assertHeader('Content-Type', 'application/problem+json')->assertDontSee('secret diagnostic');
    }

    public function test_published_contract_is_the_versioned_source_document(): void
    {
        $response = $this->get('/api/v1/openapi.json')->assertOk()->assertHeader('Content-Type', 'application/json');
        $contract = json_decode($response->baseResponse->getFile()->getContent(), true, flags: JSON_THROW_ON_ERROR);
        $this->assertSame('3.1.1', $contract['openapi']);
        $this->assertSame('submitEnquiry', $contract['paths']['/enquiries']['post']['operationId']);
        $this->assertSame([], $contract['security']);
    }
}

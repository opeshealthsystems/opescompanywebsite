<?php

namespace Tests\Feature;

use App\Contexts\Enquiries\Application\SubmitEnquiry;
use App\Mail\WebsiteSubmissionReceived;
use App\Models\ApiIdempotencyRecord;
use App\Models\Lead;
use App\Models\User;
use App\Models\WebsiteFormDelivery;
use App\Services\WebsiteFormMailer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class EnquiryReliabilityTest extends TestCase
{
    use RefreshDatabase;


    private const INPUT = ['name' => 'Reliability test', 'email' => 'fictional@example.test', 'message' => 'A fictional enquiry'];
    private const KEY = 'fictional-enquiry-key-123456';

    protected function setUp(): void
    {
        parent::setUp();
        Role::findOrCreate('admin', 'web');
        Mail::fake();
        Notification::fake();
    }

    private function submit(array $input = self::INPUT, string $key = self::KEY)
    {
        return $this->postJson('/api/v1/enquiries', $input, ['Idempotency-Key' => $key]);
    }

    public function test_same_key_replays_response_without_repeating_side_effects(): void
    {
        $first = $this->submit()->assertCreated()->assertHeader('Idempotency-Replayed', 'false');
        $this->submit()->assertCreated()->assertHeader('Idempotency-Replayed', 'true')->assertExactJson($first->json());
        $this->assertDatabaseCount('leads', 1);
        $this->assertDatabaseCount('website_form_deliveries', 1);
        $this->assertDatabaseCount('api_idempotency_records', 1);
        Mail::assertSent(WebsiteSubmissionReceived::class, 1);
        $stored = ApiIdempotencyRecord::first();
        $this->assertNotSame(self::KEY, $stored->key_hash);
        $this->assertStringNotContainsString('fictional@example.test', $stored->toJson());
    }

    public function test_same_key_with_changed_payload_or_locale_is_rejected(): void
    {
        $this->submit()->assertCreated();
        $this->submit(array_replace(self::INPUT, ['message' => 'Different']))->assertStatus(409);
        $this->submit(self::INPUT + ['locale' => 'fr'])->assertStatus(409);
        $this->assertDatabaseCount('leads', 1);
        Mail::assertSent(WebsiteSubmissionReceived::class, 1);
    }

    public function test_default_locale_and_null_optional_fields_have_equivalent_fingerprints(): void
    {
        $this->submit()->assertCreated();
        $this->submit(self::INPUT + ['locale' => 'en', 'phone' => null])->assertCreated()->assertHeader('Idempotency-Replayed', 'true');
        $this->assertDatabaseCount('leads', 1);
    }

    public function test_invalid_keys_and_validation_failures_do_not_consume_receipts(): void
    {
        $this->submit(key: 'short')->assertStatus(422);
        $this->submit(key: 'invalid key with spaces')->assertStatus(422);
        $this->submit(['name' => 'Invalid', 'email' => 'invalid'])->assertStatus(422);
        $this->assertDatabaseCount('api_idempotency_records', 0);
        $this->submit()->assertCreated();
    }

    public function test_expired_key_can_be_reused_after_24_hours(): void
    {
        $this->submit()->assertCreated();
        $this->travel(25)->hours();
        $this->submit()->assertCreated()->assertHeader('Idempotency-Replayed', 'false');
        $this->assertDatabaseCount('leads', 2);
        $this->assertDatabaseCount('api_idempotency_records', 1);
    }

    public function test_source_and_delivery_intent_roll_back_together(): void
    {
        $this->mock(WebsiteFormMailer::class)->shouldReceive('ensure')->once()->andThrow(new \RuntimeException('Intent unavailable'));
        $this->submit()->assertStatus(500);
        $this->assertDatabaseCount('leads', 0);
        $this->assertDatabaseCount('website_form_deliveries', 0);
        $this->assertDatabaseCount('api_idempotency_records', 0);
        Mail::assertNothingSent();
    }

    public function test_recent_incomplete_receipt_returns_conflict_then_recovers_without_new_lead(): void
    {
        $this->submit()->assertCreated();
        $receipt = ApiIdempotencyRecord::first();
        $receipt->update(['response' => null]);
        $this->submit()->assertStatus(409)->assertHeader('Retry-After', '2');
        $this->travel(3)->minutes();
        $this->submit()->assertCreated()->assertHeader('Idempotency-Replayed', 'true')->assertJsonPath('data.delivery_status', 'sent');
        $this->assertDatabaseCount('leads', 1);
        Mail::assertSent(WebsiteSubmissionReceived::class, 1);
    }

    public function test_committed_intent_survives_request_interruption_and_is_delivered_by_recovery(): void
    {
        $lead = DB::transaction(function () {
            $lead = Lead::create(self::INPUT + ['source' => 'contact', 'locale' => 'en']);
            app(WebsiteFormMailer::class)->ensure($lead);
            return $lead;
        });
        Mail::assertNothingSent();
        $this->artisan('website-forms:deliver --retry-only')->assertSuccessful();
        $this->artisan('website-forms:deliver --retry-only')->assertSuccessful();
        Mail::assertSent(WebsiteSubmissionReceived::class, 1);
        $this->assertDatabaseHas('website_form_deliveries', ['submission_id' => $lead->id, 'status' => 'sent', 'attempts' => 1]);
    }

    public function test_active_claim_is_not_taken_over_and_abandoned_claim_requires_review(): void
    {
        $lead = Lead::create(self::INPUT);
        $row = app(WebsiteFormMailer::class)->ensure($lead);
        $row->update(['status' => 'processing', 'locked_at' => now(), 'claim_token' => (string) Str::uuid()]);
        $this->assertSame('deferred', app(WebsiteFormMailer::class)->deliver($lead));
        $this->travel(3)->minutes();
        $this->assertSame('uncertain', app(WebsiteFormMailer::class)->deliver($lead));
        Mail::assertNothingSent();
        $this->artisan('website-forms:deliver --retry-only')->assertFailed();
        Mail::assertNothingSent();
        $this->artisan('website-forms:deliver --retry-uncertain')->assertSuccessful();
        Mail::assertSent(WebsiteSubmissionReceived::class, 1);
    }

    public function test_exception_during_send_is_held_instead_of_automatically_resent(): void
    {
        $pending = \Mockery::mock(\Illuminate\Mail\PendingMail::class);
        $pending->shouldReceive('send')->once()->andThrow(new \RuntimeException('SMTP outcome unknown'));
        Mail::shouldReceive('to')->once()->andReturn($pending);
        $lead = Lead::create(self::INPUT);
        $this->assertSame('uncertain', app(WebsiteFormMailer::class)->deliver($lead));
        $this->travel(3)->minutes();
        $this->assertSame('uncertain', app(WebsiteFormMailer::class)->deliver($lead));
        $this->assertSame(1, WebsiteFormDelivery::first()->attempts);
    }

    public function test_legacy_successes_import_without_sending_or_modifying_legacy_file(): void
    {
        $lead = Lead::create(self::INPUT);
        $path = config('website_forms.ledger_path');
        if (!is_dir(dirname($path))) mkdir(dirname($path), 0700, true);
        $key = Lead::class.'|'.$lead->id.'|contact@opeshealthsystems.com';
        $json = json_encode([$key => ['type' => Lead::class, 'id' => $lead->id, 'recipient' => 'contact@opeshealthsystems.com', 'status' => 'sent', 'attempts' => 1, 'sent_at' => now()->toIso8601String()]]);
        file_put_contents($path, $json);
        try {
            $this->artisan('website-forms:import-ledger')->assertSuccessful();
            $this->assertDatabaseCount('website_form_deliveries', 0);
            $this->artisan('website-forms:import-ledger --apply')->assertSuccessful();
            $this->artisan('website-forms:import-ledger --apply')->assertSuccessful();
            $this->artisan('website-forms:deliver --recover')->assertSuccessful();
            Mail::assertNothingSent();
            $this->assertSame($json, file_get_contents($path));
            $this->assertDatabaseCount('website_form_deliveries', 1);
            $this->assertDatabaseHas('website_form_deliveries', ['status' => 'sent']);
        } finally { unlink($path); unlink($path.'.lock'); }
    }

    public function test_confirmed_ambiguous_delivery_is_resolved_without_resending(): void
    {
        $lead = Lead::create(self::INPUT);
        $row = app(WebsiteFormMailer::class)->ensure($lead);
        $row->update(['status' => 'uncertain']);
        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole('admin');
        $this->artisan('website-forms:resolve', ['event' => $row->event_id, '--owner' => $admin->email, '--sent' => true])->assertSuccessful();
        $this->assertSame('already-sent', app(WebsiteFormMailer::class)->deliver($lead));
        Mail::assertNothingSent();
    }

    public function test_rollback_export_refuses_ambiguous_deliveries(): void
    {
        $lead = Lead::create(self::INPUT);
        $row = app(WebsiteFormMailer::class)->ensure($lead);
        $row->update(['status' => 'uncertain']);
        $this->artisan('website-forms:export-ledger')->assertFailed();
    }

    public function test_import_does_not_downgrade_delivery_attempted_after_cutover(): void
    {
        $lead = Lead::create(self::INPUT);
        $row = app(WebsiteFormMailer::class)->ensure($lead);
        $row->update(['status' => 'uncertain', 'attempts' => 2]);
        $path = config('website_forms.ledger_path');
        if (!is_dir(dirname($path))) mkdir(dirname($path), 0700, true);
        $key = Lead::class.'|'.$lead->id.'|contact@opeshealthsystems.com';
        file_put_contents($path, json_encode([$key => ['type' => Lead::class, 'id' => $lead->id, 'recipient' => 'contact@opeshealthsystems.com', 'status' => 'failed', 'attempts' => 1]]));
        try {
            $this->artisan('website-forms:import-ledger --apply')->assertSuccessful();
            $this->assertSame('uncertain', $row->fresh()->status);
            $this->assertSame(2, $row->fresh()->attempts);
        } finally { unlink($path); unlink($path.'.lock'); }
    }

    public function test_future_retries_do_not_starve_pending_deliveries_in_small_batches(): void
    {
        $first = Lead::create(self::INPUT);
        app(WebsiteFormMailer::class)->ensure($first)->update(['status' => 'failed', 'next_attempt_at' => now()->addHour()]);
        $second = Lead::create(self::INPUT);
        app(WebsiteFormMailer::class)->ensure($second);
        $this->artisan('website-forms:deliver --retry-only --limit=1')->assertSuccessful();
        $this->assertDatabaseHas('website_form_deliveries', ['submission_id' => $second->id, 'status' => 'sent']);
        Mail::assertSent(WebsiteSubmissionReceived::class, 1);
    }

    public function test_different_keys_create_independent_enquiries(): void
    {
        $this->submit()->assertCreated();
        $this->submit(key: 'another-fictional-key-123456')->assertCreated();
        $this->assertDatabaseCount('leads', 2);
        Mail::assertSent(WebsiteSubmissionReceived::class, 2);
    }

    public function test_developer_access_request_is_typed_reviewable_and_idempotent(): void
    {
        $payload = [
            'name' => 'Integration applicant',
            'email' => 'developer@example.test',
            'integration_application' => [
                'application_name' => 'Clinic scheduling',
                'access_type' => 'delegated',
                'redirect_uri' => 'https://clinic.example/callback',
                'requested_scopes' => ['crm:read'],
                'use_case' => 'Allow clinic staff to review their licensed OHS services.',
            ],
        ];

        $first = $this->submit($payload, 'developer-access-request-key-1234')
            ->assertCreated()->assertJsonPath('data.status', 'recorded');
        $this->submit($payload, 'developer-access-request-key-1234')
            ->assertCreated()->assertHeader('Idempotency-Replayed', 'true')->assertExactJson($first->json());

        $lead = Lead::query()->where('source', 'integration-request')->firstOrFail();
        $this->assertSame('integration-request', $lead->source);
        $this->assertSame('API integration', $lead->facility_type);
        $this->assertSame('Clinic scheduling', $lead->integration_application['application_name']);
        $this->assertSame(['crm:read'], $lead->integration_application['requested_scopes']);
        $this->assertSame('Allow clinic staff to review their licensed OHS services.', $lead->message);
        $this->assertDatabaseCount('leads', 1);
        Mail::assertSent(WebsiteSubmissionReceived::class, 1);
    }

    public function test_developer_access_requests_reject_insecure_callbacks_and_machine_business_scopes(): void
    {
        $delegated = [
            'application_name' => 'Clinic scheduler', 'access_type' => 'delegated',
            'redirect_uri' => 'http://clinic.example/callback', 'requested_scopes' => ['crm:read'],
            'use_case' => 'Allow clinic staff to review their licensed OHS services.',
        ];
        $this->submit(['name' => 'Applicant', 'email' => 'developer@example.test', 'integration_application' => $delegated], 'insecure-callback-key-123456')
            ->assertStatus(422);

        $machine = [
            'application_name' => 'Server integration', 'access_type' => 'machine',
            'requested_scopes' => ['crm:read'],
            'use_case' => 'Read only public catalogue metadata for a server integration.',
        ];
        $this->submit(['name' => 'Applicant', 'email' => 'developer@example.test', 'integration_application' => $machine], 'machine-scope-rejected-key-1234')
            ->assertStatus(422);

        $this->assertDatabaseCount('leads', 0);
        $this->assertDatabaseCount('api_idempotency_records', 0);
        Mail::assertNothingSent();
    }

    public function test_other_public_forms_rollback_when_intent_cannot_be_saved(): void
    {
        $this->mock(WebsiteFormMailer::class)->shouldReceive('ensure')->times(3)->andThrow(new \RuntimeException('Intent unavailable'));
        $this->post('/en/book-demo', ['name' => 'Example', 'email' => 'fictional@example.test', 'organization_name' => 'Example clinic'])->assertStatus(500);
        $this->post('/en/become-a-partner', ['organization_name' => 'Example partner', 'contact_name' => 'Example', 'email' => 'fictional@example.test', 'country' => 'Cameroon', 'partner_type' => 'technology', 'description' => 'Fictional partnership'])->assertStatus(500);
        $this->post('/en/join-testers', ['name' => 'Example', 'email' => 'fictional@example.test', 'country' => 'Cameroon', 'profession' => 'doctor', 'years_experience' => 3, 'motivation' => 'Fictional evaluation'])->assertStatus(500);
        foreach (['demo_requests', 'partner_applications', 'tester_applications'] as $table) $this->assertDatabaseCount($table, 0);
        Mail::assertNothingSent();
    }

    public function test_successful_delivery_can_be_exported_for_source_rollback(): void
    {
        $this->submit()->assertCreated();
        $path = config('website_forms.ledger_path');
        try {
            $this->artisan('website-forms:export-ledger --apply')->assertSuccessful();
            $records = json_decode(file_get_contents($path), true, flags: JSON_THROW_ON_ERROR);
            $this->assertSame('sent', array_values($records)[0]['status']);
            if (PHP_OS_FAMILY !== 'Windows') $this->assertSame(0, fileperms($path) & 0077);
        } finally {
            if (is_file($path)) unlink($path);
            if (is_file($path.'.lock')) unlink($path.'.lock');
        }
    }

    public function test_pending_rollback_record_can_be_imported_without_sending(): void
    {
        $lead = Lead::create(self::INPUT);
        app(WebsiteFormMailer::class)->ensure($lead);
        $path = config('website_forms.ledger_path');
        try {
            $this->artisan('website-forms:export-ledger --apply')->assertSuccessful();
            WebsiteFormDelivery::query()->delete();
            $this->artisan('website-forms:import-ledger --apply')->assertSuccessful();
            $this->assertDatabaseHas('website_form_deliveries', ['status' => 'pending', 'attempts' => 0]);
            Mail::assertNothingSent();
        } finally {
            if (is_file($path)) unlink($path);
            if (is_file($path.'.lock')) unlink($path.'.lock');
        }
    }
}

<?php

namespace Tests\Feature;

use App\Models\Lead;
use App\Models\Quote;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class N4QuoteDemoNotificationsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
    }

    public function test_marking_a_quote_sent_emails_the_lead(): void
    {
        Notification::fake();
        $lead  = Lead::create(['name' => 'Jane Buyer', 'email' => 'jane@example.com', 'source' => 'web', 'status' => 'new']);
        $quote = Quote::create([
            'reference'  => 'QTE-2026-0001',
            'lead_id'    => $lead->id,
            'title'      => 'OPES Health OS licence',
            'status'     => 'draft',
            'currency'   => 'XAF',
            'subtotal'   => 100000,
            'tax_rate'   => 0,
            'tax_amount' => 0,
            'total'      => 100000,
            'created_by' => User::factory()->create()->id,
        ]);

        $quote->update(['status' => 'sent']);

        Notification::assertSentOnDemand(\App\Notifications\QuoteSent::class);
    }

    public function test_submitting_a_demo_request_emails_the_requester(): void
    {
        Notification::fake();

        $this->post('/en/book-demo', [
            'name'              => 'Sam Lead',
            'email'             => 'sam@example.com',
            'organization_name' => 'Sam Clinic',
        ])->assertRedirect();

        Notification::assertSentOnDemand(\App\Notifications\DemoRequestConfirmation::class);
    }
}

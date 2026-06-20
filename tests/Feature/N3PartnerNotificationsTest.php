<?php

namespace Tests\Feature;

use App\Models\PartnerApplication;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class N3PartnerNotificationsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
    }

    private function applicationData(array $overrides = []): array
    {
        return array_merge([
            'organization_name' => 'Acme Health',
            'contact_name'      => 'Bola Smith',
            'email'             => 'partner@example.com',
            'country'           => 'CM',
            'partner_type'      => 'hospital',
            'description'       => str_repeat('d', 40),
            'status'            => 'pending',
        ], $overrides);
    }

    public function test_submitting_a_partner_application_emails_the_applicant(): void
    {
        Notification::fake();

        $this->post('/en/become-a-partner', [
            'organization_name' => 'Acme Health',
            'contact_name'      => 'Bola Smith',
            'email'             => 'partner@example.com',
            'country'           => 'CM',
            'partner_type'      => 'hospital',
            'description'       => str_repeat('d', 40),
        ])->assertRedirect();

        Notification::assertSentOnDemand(\App\Notifications\PartnerApplicationReceived::class);
    }

    public function test_approving_a_partner_application_emails_the_applicant(): void
    {
        Notification::fake();
        $app = PartnerApplication::create($this->applicationData());

        $app->update(['status' => 'approved']);

        Notification::assertSentOnDemand(\App\Notifications\PartnerApplicationApproved::class);
    }

    public function test_rejecting_a_partner_application_emails_the_applicant(): void
    {
        Notification::fake();
        $app = PartnerApplication::create($this->applicationData());

        $app->update(['status' => 'rejected']);

        Notification::assertSentOnDemand(\App\Notifications\PartnerApplicationRejected::class);
    }
}

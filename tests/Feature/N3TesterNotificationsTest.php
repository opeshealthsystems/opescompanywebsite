<?php

namespace Tests\Feature;

use App\Models\TesterApplication;
use App\Models\User;
use App\Notifications\TesterApplicationApproved;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class N3TesterNotificationsTest extends TestCase
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
            'name'             => 'Dr Ada Eze',
            'email'            => 'ada@example.com',
            'profession'       => 'doctor',
            'country'          => 'CM',
            'years_experience' => 6,
            'motivation'       => str_repeat('m', 40),
            'status'           => 'pending',
        ], $overrides);
    }

    public function test_submitting_a_tester_application_emails_the_applicant(): void
    {
        Notification::fake();

        $this->post('/en/join-testers', [
            'name'             => 'Dr Ada Eze',
            'email'            => 'ada@example.com',
            'profession'       => 'doctor',
            'country'          => 'CM',
            'years_experience' => 6,
            'motivation'       => str_repeat('m', 40),
        ])->assertRedirect();

        Notification::assertSentOnDemand(\App\Notifications\TesterApplicationReceived::class);
    }

    public function test_accepting_an_application_notifies_an_existing_user_in_app(): void
    {
        Notification::fake();
        $user = User::factory()->create(['email' => 'ada@example.com']);
        $user->assignRole('tester');
        $app  = TesterApplication::create($this->applicationData());

        $app->update(['status' => 'accepted']);

        Notification::assertSentTo($user, TesterApplicationApproved::class);
    }

    public function test_rejecting_an_application_emails_the_applicant_on_demand(): void
    {
        Notification::fake();
        $app = TesterApplication::create($this->applicationData(['email' => 'noaccount@example.com']));

        $app->update(['status' => 'rejected']);

        Notification::assertSentOnDemand(\App\Notifications\TesterApplicationRejected::class);
    }
}

<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class N5CustomerBackfillTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
    }

    private function feedTypes(User $user): array
    {
        return $user->fresh()->notifications->pluck('data.type')->all();
    }

    public function test_creating_a_ticket_adds_a_feed_row(): void
    {
        Mail::fake();
        $customer = User::factory()->create();
        $customer->assignRole('customer');

        $this->actingAs($customer)->post('/en/customer/tickets', [
            'subject'     => 'Cannot log in',
            'description' => 'I am locked out of my account.',
            'type'        => 'support',
            'priority'    => 'high',
        ])->assertRedirect();

        $this->assertContains('support.ticket_created', $this->feedTypes($customer));
    }

    public function test_customer_registration_adds_a_welcome_feed_row(): void
    {
        Mail::fake();

        $this->post('/register', [
            'account_type'          => 'facility',
            'name'                  => 'Douala General',
            'email'                 => 'feed-welcome@example.cm',
            'password'              => 'Secret1234!',
            'password_confirmation' => 'Secret1234!',
            'facility_name'         => 'Douala General',
            'facility_type'         => 'hospital',
            'country'               => 'CM',
        ])->assertRedirect();

        $user = User::where('email', 'feed-welcome@example.cm')->firstOrFail();
        $this->assertContains('account.welcome', $this->feedTypes($user));
    }
}

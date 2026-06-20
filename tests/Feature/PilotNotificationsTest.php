<?php

namespace Tests\Feature;

use App\Models\Cohort;
use App\Models\User;
use App\Notifications\AccountDeactivated;
use App\Notifications\PlacedInCohort;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class PilotNotificationsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
    }

    public function test_deactivating_a_user_notifies_them(): void
    {
        Notification::fake();
        $user = User::factory()->create(['is_active' => true]);

        $user->update(['is_active' => false]);

        Notification::assertSentTo($user, AccountDeactivated::class);
    }

    public function test_creating_inactive_user_does_not_notify(): void
    {
        Notification::fake();
        User::factory()->create(['is_active' => false]); // creation, not deactivation
        Notification::assertNothingSent();
    }

    public function test_placed_in_cohort_notification_channels_and_payload(): void
    {
        $cohort = Cohort::factory()->create(['name' => 'July Pharmacy', 'specialty' => 'Pharmacy']);
        $user   = User::factory()->create();

        $notification = new PlacedInCohort($cohort);
        $this->assertEquals(['mail', 'database'], $notification->via($user));

        $array = $notification->toArray($user);
        $this->assertEquals('validation.placed_in_cohort', $array['type']);
        $this->assertArrayHasKey('url', $array);

        // Branded mail renders (proves the OPES theme is wired).
        $html = $notification->toMail($user)->render();
        $this->assertStringContainsString('OPES Health Systems', $html);
    }
}

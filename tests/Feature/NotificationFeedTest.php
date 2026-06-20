<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\AccountDeactivated;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class NotificationFeedTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
    }

    private function userWithNotification(): User
    {
        $user = User::factory()->create();
        $user->assignRole('practitioner');
        $user->notify(new AccountDeactivated()); // real send → row in notifications
        return $user;
    }

    public function test_feed_lists_notifications(): void
    {
        $user = $this->userWithNotification();
        $this->actingAs($user)->get('/en/notifications')
            ->assertOk()
            ->assertSee('Account deactivated');
    }

    public function test_mark_all_read(): void
    {
        $user = $this->userWithNotification();
        $this->assertEquals(1, $user->unreadNotifications()->count());

        $this->actingAs($user)->post('/en/notifications/read-all')->assertRedirect();

        $this->assertEquals(0, $user->fresh()->unreadNotifications()->count());
    }

    public function test_guest_cannot_access_feed(): void
    {
        $this->get('/en/notifications')->assertRedirect('/login');
    }
}

<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class N4PasswordResetNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_password_reset_sends_the_branded_notification(): void
    {
        Notification::fake();
        $user = User::factory()->create();

        $user->sendPasswordResetNotification('test-token');

        Notification::assertSentTo($user, ResetPasswordNotification::class);
    }

    public function test_branded_reset_email_renders_with_brand_and_link(): void
    {
        $user = User::factory()->create(['email' => 'reset@example.com']);
        $html = (new ResetPasswordNotification('tok123'))->toMail($user)->render();

        $this->assertStringContainsString('OPES Health Systems', $html);
        $this->assertStringContainsString('reset-password/tok123', $html);
    }
}

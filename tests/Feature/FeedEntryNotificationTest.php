<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\FeedEntry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FeedEntryNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_feed_entry_is_database_only_with_expected_payload(): void
    {
        $entry = new FeedEntry('accounting.invoice_issued', 'Invoice issued', 'Invoice INV-1 is ready.', 'document-text', '/en/customer/invoices/1');

        $this->assertEquals(['database'], $entry->via(new User()));
        $payload = $entry->toArray(new User());
        $this->assertSame('accounting.invoice_issued', $payload['type']);
        $this->assertSame('Invoice issued', $payload['title']);
        $this->assertSame('document-text', $payload['icon']);
        $this->assertSame('/en/customer/invoices/1', $payload['url']);
    }

    public function test_notifying_a_user_writes_a_feed_row(): void
    {
        $user = User::factory()->create();
        $user->notify(new FeedEntry('account.welcome', 'Welcome to OPES', 'Welcome.', 'sparkles', null));

        $this->assertEquals(1, $user->notifications()->count());
        $this->assertSame('Welcome to OPES', $user->notifications()->first()->data['title']);
    }
}

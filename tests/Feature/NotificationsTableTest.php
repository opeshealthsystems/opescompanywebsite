<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class NotificationsTableTest extends TestCase
{
    use RefreshDatabase;

    public function test_notifications_table_exists(): void
    {
        $this->assertTrue(Schema::hasTable('notifications'));
        foreach (['id', 'type', 'notifiable_type', 'notifiable_id', 'data', 'read_at'] as $c) {
            $this->assertTrue(Schema::hasColumn('notifications', $c), "notifications.$c");
        }
    }
}

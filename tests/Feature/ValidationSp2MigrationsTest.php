<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ValidationSp2MigrationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_developer_tasks_and_retests_tables_exist(): void
    {
        $this->assertTrue(Schema::hasTable('developer_tasks'));
        $this->assertTrue(Schema::hasTable('retests'));
        foreach (['issue_report_id', 'assigned_to', 'title', 'priority', 'status', 'resolution_notes', 'started_at', 'fixed_at'] as $c) {
            $this->assertTrue(Schema::hasColumn('developer_tasks', $c), "developer_tasks.$c");
        }
        foreach (['issue_report_id', 'developer_task_id', 'cohort_member_id', 'result', 'notes', 'attachments', 'retested_at'] as $c) {
            $this->assertTrue(Schema::hasColumn('retests', $c), "retests.$c");
        }
    }
}

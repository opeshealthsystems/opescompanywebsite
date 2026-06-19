<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ValidationSp3MigrationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_sp3_tables_exist(): void
    {
        $this->assertTrue(Schema::hasTable('weekly_reviews'));
        $this->assertTrue(Schema::hasTable('final_evaluations'));
        foreach (['cohort_id', 'week_start', 'week_end', 'metrics', 'summary', 'action_items', 'author_id', 'generated_at'] as $c) {
            $this->assertTrue(Schema::hasColumn('weekly_reviews', $c), "weekly_reviews.$c");
        }
        foreach (['cohort_member_id', 'metrics', 'assessment', 'rating', 'recommendation', 'evaluator_id', 'evaluated_at'] as $c) {
            $this->assertTrue(Schema::hasColumn('final_evaluations', $c), "final_evaluations.$c");
        }
    }
}

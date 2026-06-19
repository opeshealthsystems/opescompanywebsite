<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ValidationCertificationMigrationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_sp4_tables_exist(): void
    {
        $this->assertTrue(Schema::hasTable('validation_certificates'));
        $this->assertTrue(Schema::hasTable('advisory_council_members'));
        foreach (['cohort_member_id', 'final_evaluation_id', 'certificate_number', 'score', 'tier', 'issued_by', 'issued_at'] as $c) {
            $this->assertTrue(Schema::hasColumn('validation_certificates', $c), "validation_certificates.$c");
        }
        foreach (['user_id', 'validation_certificate_id', 'title', 'term_start', 'term_end', 'status', 'invited_by', 'invited_at'] as $c) {
            $this->assertTrue(Schema::hasColumn('advisory_council_members', $c), "advisory_council_members.$c");
        }
    }
}

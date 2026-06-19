<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ValidationMigrationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_program_type_column_added_to_practitioner_programs(): void
    {
        $this->assertTrue(Schema::hasColumn('practitioner_programs', 'program_type'));
    }

    public function test_all_validation_tables_exist(): void
    {
        foreach ([
            'cohorts', 'cohort_members', 'validation_products', 'validation_modules',
            'validation_workflows', 'validation_test_cases', 'cohort_test_cases',
            'daily_test_sessions', 'issue_reports', 'clinical_reviews', 'product_reviews',
        ] as $table) {
            $this->assertTrue(Schema::hasTable($table), "Missing table: {$table}");
        }
    }
}

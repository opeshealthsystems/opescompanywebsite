<?php

namespace Tests\Feature;

use Database\Seeders\ValidationDataSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ValidationDataSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_creates_full_catalog(): void
    {
        $this->seed(ValidationDataSeeder::class);

        $this->assertDatabaseCount('validation_products', 1);
        $this->assertDatabaseCount('validation_modules', 10);
        $this->assertDatabaseCount('validation_workflows', 56);
        $this->assertDatabaseCount('validation_test_cases', 56);
        $this->assertDatabaseHas('validation_products', ['code' => 'ohos', 'name' => 'OPES Health OS']);
        $this->assertDatabaseHas('validation_workflows', ['code' => 'create_new_patient', 'name' => 'Create New Patient']);
    }

    public function test_seeder_is_idempotent(): void
    {
        $this->seed(ValidationDataSeeder::class);
        $this->seed(ValidationDataSeeder::class);

        $this->assertDatabaseCount('validation_modules', 10);
        $this->assertDatabaseCount('validation_workflows', 56);
        $this->assertDatabaseCount('validation_test_cases', 56);
    }
}

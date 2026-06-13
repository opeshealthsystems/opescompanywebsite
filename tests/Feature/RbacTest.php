<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class RbacTest extends TestCase
{
    use RefreshDatabase;

    public function test_permission_tables_exist(): void
    {
        $this->assertTrue(Schema::hasTable('roles'));
        $this->assertTrue(Schema::hasTable('permissions'));
        $this->assertTrue(Schema::hasTable('model_has_roles'));
        $this->assertTrue(Schema::hasTable('model_has_permissions'));
        $this->assertTrue(Schema::hasTable('role_has_permissions'));
    }

    public function test_user_has_employee_fields(): void
    {
        $user = User::factory()->create([
            'employee_id' => 'EMP-2026-0001',
            'department'  => 'Engineering',
            'position'    => 'Software Developer',
            'phone'       => '+237612345678',
            'is_active'   => true,
        ]);

        $this->assertDatabaseHas('users', [
            'employee_id' => 'EMP-2026-0001',
            'department'  => 'Engineering',
            'position'    => 'Software Developer',
        ]);
    }
}

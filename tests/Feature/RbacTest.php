<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class RbacTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }

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

    public function test_five_roles_exist_after_seeding(): void
    {
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);

        foreach (['super_admin', 'admin', 'support', 'tester', 'customer'] as $role) {
            $this->assertDatabaseHas('roles', ['name' => $role]);
        }
    }

    public function test_super_admin_has_all_permissions(): void
    {
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);

        $superAdmin = \Spatie\Permission\Models\Role::findByName('super_admin');
        $this->assertCount(14, $superAdmin->permissions);
        $this->assertTrue($superAdmin->hasPermissionTo('manage_roles'));
        $this->assertTrue($superAdmin->hasPermissionTo('manage_accounting'));
    }

    public function test_customer_role_has_no_permissions(): void
    {
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);

        $customer = \Spatie\Permission\Models\Role::findByName('customer');
        $this->assertEquals(0, $customer->permissions->count());
    }

    public function test_support_role_cannot_manage_accounting(): void
    {
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);

        $support = \Spatie\Permission\Models\Role::findByName('support');
        $this->assertFalse($support->hasPermissionTo('manage_accounting'));
        $this->assertTrue($support->hasPermissionTo('manage_tickets'));
    }
}

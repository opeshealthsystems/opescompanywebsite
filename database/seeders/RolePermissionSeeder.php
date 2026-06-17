<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'view_admin_panel',
            'manage_users',
            'manage_roles',
            'manage_leads',
            'manage_blog',
            'manage_tickets',
            'assign_tickets',
            'view_reports',
            'manage_accounting',
            'manage_employees',
            'manage_licenses',
            'manage_documents',
            'assign_testers',
            'view_tester_dashboard',
            'manage_bug_reports',
            'view_practitioner_dashboard',
            'apply_paid_program',
            'submit_findings',
        ];

        foreach ($permissions as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        $roleMap = [
            'super_admin'  => $permissions,
            'admin'        => array_diff($permissions, ['manage_roles']),
            'support'      => ['view_admin_panel', 'manage_tickets', 'assign_tickets', 'manage_bug_reports'],
            'tester'       => ['view_tester_dashboard'],
            'customer'     => [],
            'practitioner' => ['view_practitioner_dashboard', 'apply_paid_program', 'submit_findings'],
        ];

        foreach ($roleMap as $roleName => $rolePerms) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
            $role->syncPermissions($rolePerms);
        }

        $admin = User::where('email', 'admin@opeshealthsystems.com')->first();
        if ($admin) {
            $admin->syncRoles(['super_admin']);
            if (!$admin->employee_id) {
                $admin->update([
                    'employee_id' => 'EMP-2026-0001',
                    'department'  => 'Administration',
                    'position'    => 'System Administrator',
                    'hire_date'   => '2026-01-01',
                ]);
            }
        }
    }
}

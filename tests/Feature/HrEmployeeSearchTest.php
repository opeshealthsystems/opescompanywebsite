<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class HrEmployeeSearchTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
    }

    public function test_employee_search_does_not_leak_non_employees(): void
    {
        $hr = User::factory()->create();
        $hr->assignRole('hr');

        // Employee: matches the term by NAME.
        User::factory()->create(['name' => 'Zentest Employee', 'employee_id' => 'EMP-2026-9001']);
        // Non-employee (customer): matches the term only by EMAIL, employee_id is null.
        User::factory()->create(['name' => 'Bob Outsider', 'email' => 'zentest@customer.cm', 'employee_id' => null]);

        $this->actingAs($hr)
            ->get('/en/hr/employees?search=zentest')
            ->assertOk()
            ->assertSee('Zentest Employee')
            ->assertDontSee('Bob Outsider');
    }
}

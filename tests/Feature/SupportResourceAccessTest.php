<?php

namespace Tests\Feature;

use App\Filament\Resources\DocumentResource;
use App\Filament\Resources\LeaveRequestResource;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class SupportResourceAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
    }

    public function test_support_cannot_access_documents_or_leave_requests(): void
    {
        $support = User::factory()->create();
        $support->assignRole('support');
        $this->actingAs($support);

        $this->assertFalse(DocumentResource::canAccess());
        $this->assertFalse(LeaveRequestResource::canAccess());
    }

    public function test_admin_can_access_documents_and_leave_requests(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $this->actingAs($admin);

        $this->assertTrue(DocumentResource::canAccess());
        $this->assertTrue(LeaveRequestResource::canAccess());
    }
}

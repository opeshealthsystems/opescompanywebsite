<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RbacHardeningTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    private function makeUser(string $role): User
    {
        $user = User::factory()->create();
        $user->assignRole($role);
        return $user;
    }

    public function test_support_cannot_access_demo_request_resource(): void
    {
        $support = $this->makeUser('support');
        $this->actingAs($support);
        $this->assertFalse(\App\Filament\Resources\DemoRequestResource::canAccess());
    }

    public function test_admin_can_access_demo_request_resource(): void
    {
        $admin = $this->makeUser('admin');
        $this->actingAs($admin);
        $this->assertTrue(\App\Filament\Resources\DemoRequestResource::canAccess());
    }

    public function test_support_cannot_access_tester_application_resource(): void
    {
        $support = $this->makeUser('support');
        $this->actingAs($support);
        $this->assertFalse(\App\Filament\Resources\TesterApplicationResource::canAccess());
    }

    public function test_support_cannot_access_partner_application_resource(): void
    {
        $support = $this->makeUser('support');
        $this->actingAs($support);
        $this->assertFalse(\App\Filament\Resources\PartnerApplicationResource::canAccess());
    }

    public function test_support_cannot_access_confidential_routes(): void
    {
        $support = $this->makeUser('support');
        $response = $this->actingAs($support)->get('/en/strategy');
        $response->assertStatus(403);
    }

    public function test_admin_can_access_confidential_routes(): void
    {
        $admin = $this->makeUser('admin');
        $response = $this->actingAs($admin)->get('/en/strategy');
        $response->assertStatus(200);
    }

    public function test_audit_log_resource_cannot_be_edited(): void
    {
        $model = new \App\Models\AuditLog();
        $admin = $this->makeUser('admin');
        $this->actingAs($admin);
        $this->assertFalse(\App\Filament\Resources\AuditLogResource::canEdit($model));
    }

    public function test_audit_log_resource_cannot_be_deleted(): void
    {
        $model = new \App\Models\AuditLog();
        $admin = $this->makeUser('admin');
        $this->actingAs($admin);
        $this->assertFalse(\App\Filament\Resources\AuditLogResource::canDelete($model));
    }
}

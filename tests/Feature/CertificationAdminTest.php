<?php

namespace Tests\Feature;

use App\Filament\Resources\AdvisoryCouncilMemberResource;
use App\Filament\Resources\ValidationCertificateResource;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class CertificationAdminTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
    }

    public function test_resources_admin_gated_and_not_creatable(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $this->actingAs($admin);
        $this->assertTrue(ValidationCertificateResource::canAccess());
        $this->assertTrue(AdvisoryCouncilMemberResource::canAccess());
        $this->assertFalse(ValidationCertificateResource::canCreate());
        $this->assertFalse(AdvisoryCouncilMemberResource::canCreate());

        foreach (['practitioner', 'support'] as $role) {
            $u = User::factory()->create();
            $u->assignRole($role);
            $this->actingAs($u);
            $this->assertFalse(ValidationCertificateResource::canAccess(), $role);
            $this->assertFalse(AdvisoryCouncilMemberResource::canAccess(), $role);
        }
    }
}

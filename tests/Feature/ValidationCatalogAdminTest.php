<?php

namespace Tests\Feature;

use App\Filament\Resources\ValidationProductResource;
use App\Filament\Resources\ValidationModuleResource;
use App\Filament\Resources\ValidationWorkflowResource;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class ValidationCatalogAdminTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
    }

    public function test_admin_can_access_product_resource(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $this->actingAs($admin);
        $this->assertTrue(ValidationProductResource::canAccess());
    }

    public function test_practitioner_cannot_access_catalog_resources(): void
    {
        $prac = User::factory()->create();
        $prac->assignRole('practitioner');
        $this->actingAs($prac);
        $this->assertFalse(ValidationProductResource::canAccess());
        $this->assertFalse(ValidationModuleResource::canAccess());
        $this->assertFalse(ValidationWorkflowResource::canAccess());
    }

    public function test_hidden_resources_not_in_navigation(): void
    {
        $this->assertFalse(ValidationModuleResource::shouldRegisterNavigation());
        $this->assertFalse(ValidationWorkflowResource::shouldRegisterNavigation());
    }

    public function test_resource_pages_registered(): void
    {
        $this->assertArrayHasKey('index', ValidationProductResource::getPages());
        $this->assertArrayHasKey('create', ValidationProductResource::getPages());
        $this->assertArrayHasKey('edit', ValidationProductResource::getPages());
    }
}

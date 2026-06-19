<?php

namespace Tests\Feature;

use App\Filament\Resources\DeveloperTaskResource;
use App\Filament\Resources\DeveloperTaskResource\Pages\ListDeveloperTasks;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class DeveloperTaskAdminTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
    }

    public function test_admin_can_access_resource_practitioner_cannot(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $this->actingAs($admin);
        $this->assertTrue(DeveloperTaskResource::canAccess());
        $this->assertFalse(DeveloperTaskResource::canCreate());

        $prac = User::factory()->create();
        $prac->assignRole('practitioner');
        $this->actingAs($prac);
        $this->assertFalse(DeveloperTaskResource::canAccess());
    }

    public function test_pages_and_tabs_registered(): void
    {
        $this->assertArrayHasKey('index', DeveloperTaskResource::getPages());
        $this->assertArrayHasKey('view', DeveloperTaskResource::getPages());

        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $this->actingAs($admin);
        $page = new ListDeveloperTasks();
        $this->assertCount(6, $page->getTabs());
    }
}

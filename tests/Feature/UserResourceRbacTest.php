<?php

namespace Tests\Feature;

use App\Filament\Resources\UserResource\Pages\EditUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class UserResourceRbacTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
    }

    public function test_admin_cannot_grant_super_admin_via_user_form(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $target = User::factory()->create();
        $target->assignRole('customer');

        $superAdminRoleId = Role::where('name', 'super_admin')->value('id');

        $this->actingAs($admin);

        // The roles field is hidden for non-super_admins; forcing the underlying
        // Livewire state must NOT result in a super_admin grant.
        Livewire::test(EditUser::class, ['record' => $target->getRouteKey()])
            ->set('data.roles', [$superAdminRoleId])
            ->call('save');

        $this->assertFalse($target->fresh()->hasRole('super_admin'));
    }

    public function test_super_admin_can_assign_roles(): void
    {
        $super = User::factory()->create();
        $super->assignRole('super_admin');

        $target = User::factory()->create();

        $supportRoleId = Role::where('name', 'support')->value('id');

        $this->actingAs($super);

        Livewire::test(EditUser::class, ['record' => $target->getRouteKey()])
            ->fillForm(['roles' => [$supportRoleId]])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertTrue($target->fresh()->hasRole('support'));
    }
}

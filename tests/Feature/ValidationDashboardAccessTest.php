<?php

namespace Tests\Feature;

use App\Filament\Pages\ValidationCohortDashboard;
use App\Filament\Pages\ValidationDeveloperDashboard;
use App\Filament\Pages\ValidationIssueDashboard;
use App\Filament\Pages\ValidationPractitionerDashboard;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class ValidationDashboardAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
    }

    public function test_admin_can_access_all_dashboards_practitioner_cannot(): void
    {
        $pages = [
            ValidationCohortDashboard::class,
            ValidationIssueDashboard::class,
            ValidationDeveloperDashboard::class,
            ValidationPractitionerDashboard::class,
        ];

        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $this->actingAs($admin);
        foreach ($pages as $p) {
            $this->assertTrue($p::canAccess(), $p.' admin');
        }

        $prac = User::factory()->create();
        $prac->assignRole('practitioner');
        $this->actingAs($prac);
        foreach ($pages as $p) {
            $this->assertFalse($p::canAccess(), $p.' practitioner');
        }
    }

    public function test_support_role_cannot_access_validation_reporting(): void
    {
        // 'support' can reach the /admin panel but must NOT see validation reporting.
        $support = User::factory()->create();
        $support->assignRole('support');
        $this->actingAs($support);

        foreach ([
            ValidationCohortDashboard::class,
            ValidationIssueDashboard::class,
            ValidationDeveloperDashboard::class,
            ValidationPractitionerDashboard::class,
            \App\Filament\Resources\WeeklyReviewResource::class,
            \App\Filament\Resources\FinalEvaluationResource::class,
        ] as $class) {
            $this->assertFalse($class::canAccess(), $class.' support');
        }
    }
}

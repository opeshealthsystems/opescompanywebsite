<?php

namespace Tests\Feature;

use App\Filament\Widgets\OpesDashboardStats;
use App\Filament\Widgets\RecentInvoicesWidget;
use App\Filament\Widgets\RecentLeadsWidget;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class DashboardWidgetAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
    }

    private function statLabels(): array
    {
        $method = new \ReflectionMethod(OpesDashboardStats::class, 'getStats');
        $method->setAccessible(true);
        return collect($method->invoke(new OpesDashboardStats()))
            ->map(fn ($stat) => $stat->getLabel())
            ->all();
    }

    public function test_support_cannot_view_finance_or_lead_widgets(): void
    {
        $support = User::factory()->create();
        $support->assignRole('support');
        $this->actingAs($support);

        $this->assertFalse(RecentInvoicesWidget::canView());
        $this->assertFalse(RecentLeadsWidget::canView());

        $labels = $this->statLabels();
        $this->assertContains('Customers', $labels);
        $this->assertNotContains('Outstanding Invoices', $labels);
        $this->assertNotContains('New Leads', $labels);
    }

    public function test_admin_sees_finance_and_lead_widgets(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $this->actingAs($admin);

        $this->assertTrue(RecentInvoicesWidget::canView());
        $this->assertTrue(RecentLeadsWidget::canView());

        $labels = $this->statLabels();
        $this->assertContains('Outstanding Invoices', $labels);
        $this->assertContains('New Leads', $labels);
    }
}

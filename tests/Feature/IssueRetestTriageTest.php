<?php

namespace Tests\Feature;

use App\Filament\Resources\IssueReportResource\Pages\ListIssueReports;
use App\Models\IssueReport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class IssueRetestTriageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
    }

    public function test_ready_for_retest_tab_present(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $this->actingAs($admin);

        $page = new ListIssueReports();
        $this->assertArrayHasKey('ready_for_retest', $page->getTabs());
    }

    public function test_status_options_include_retest_states(): void
    {
        $opts = IssueReport::statusOptions();
        $this->assertArrayHasKey('ready_for_retest', $opts);
        $this->assertArrayHasKey('retest_passed', $opts);
        $this->assertArrayHasKey('retest_failed', $opts);
    }
}

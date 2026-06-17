<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

/**
 * Asserts the responsive hamburger-drawer markup (portal-nav / portal-burger /
 * portal-menu) renders on each authenticated portal dashboard.
 */
class PortalNavResponsiveTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
    }

    private function assertPortalNav(string $html): void
    {
        $this->assertStringContainsString('portal-nav', $html);
        $this->assertStringContainsString('data-portal-burger', $html);
        $this->assertStringContainsString('portal-menu', $html);
        $this->assertStringContainsString('portal-actions', $html);
    }

    public function test_customer_dashboard_has_responsive_nav(): void
    {
        $user = User::factory()->create();
        $user->assignRole('customer');
        $user->customerProfile()->create(['country' => 'CM']);

        $res = $this->actingAs($user)->get('/en/customer/dashboard');
        $res->assertOk();
        $this->assertPortalNav($res->getContent());
    }

    public function test_tester_dashboard_has_responsive_nav(): void
    {
        $user = User::factory()->create();
        $user->assignRole('tester');

        $res = $this->actingAs($user)->get('/en/tester/dashboard');
        $res->assertOk();
        $this->assertPortalNav($res->getContent());
    }

    public function test_practitioner_dashboard_has_responsive_nav(): void
    {
        $user = User::factory()->create();
        $user->assignRole('practitioner');
        $user->practitionerProfile()->create(['profession' => 'doctor', 'workplace_country' => 'CM']);

        $res = $this->actingAs($user)->get('/en/practitioner/dashboard');
        $res->assertOk();
        $this->assertPortalNav($res->getContent());
    }
}

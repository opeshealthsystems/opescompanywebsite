<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

/**
 * Guards the public header navbar (resources/views/components/navbar.blade.php):
 * the account menu must point each role at its OWN portal (not hardcoded customer.*),
 * and the confidential Strategy/Risk links must only appear for admin/super_admin.
 */
class NavbarAccountMenuTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        $this->seed(RolePermissionSeeder::class);
    }

    /** Render a public page (which carries the navbar) and return only the <nav> region. */
    private function navFor(?string $role): string
    {
        if ($role !== null) {
            $user = User::factory()->create();
            $user->assignRole($role);
            $res = $this->actingAs($user)->get('/en');
        } else {
            $res = $this->get('/en');
        }
        $res->assertOk();
        $html = $res->getContent();
        preg_match('/<nav class="site-nav".*?<\/nav>/s', $html, $m);

        return $m[0] ?? '';
    }

    public function test_customer_account_menu_points_to_customer_portal(): void
    {
        $nav = $this->navFor('customer');
        $this->assertStringContainsString('/en/customer/dashboard', $nav);
        $this->assertStringContainsString('/en/customer/profile', $nav);
        $this->assertStringContainsString('/en/customer/licenses', $nav);
        $this->assertStringContainsString('/en/customer/tickets', $nav);
    }

    public function test_practitioner_account_menu_points_to_practitioner_portal_not_customer(): void
    {
        $nav = $this->navFor('practitioner');
        $this->assertStringContainsString('/en/practitioner/dashboard', $nav);
        $this->assertStringContainsString('/en/practitioner/profile', $nav);
        $this->assertStringNotContainsString('/en/customer/dashboard', $nav);
        $this->assertStringNotContainsString('/en/customer/licenses', $nav);
    }

    public function test_each_staff_role_links_to_its_own_dashboard_not_customer(): void
    {
        foreach (['tester', 'manager', 'hr', 'accountant', 'support'] as $role) {
            $nav = $this->navFor($role);
            $this->assertStringContainsString("/en/{$role}/dashboard", $nav, "missing {$role} dashboard");
            $this->assertStringContainsString("/en/{$role}/profile", $nav, "missing {$role} profile");
            $this->assertStringNotContainsString('/en/customer/dashboard', $nav, "{$role} wrongly links customer dashboard");
            // Licenses are customer-only; no other role should be offered them.
            $this->assertStringNotContainsString('/en/customer/licenses', $nav, "{$role} wrongly links customer licenses");
        }
    }

    public function test_admin_account_menu_links_to_filament_panel(): void
    {
        $nav = $this->navFor('admin');
        $this->assertStringContainsString('/admin"', $nav); // Filament panel (url('admin') is absolute)
        $this->assertStringNotContainsString('/en/customer/dashboard', $nav);
    }

    public function test_admin_sees_confidential_strategy_and_risk_links(): void
    {
        $nav = $this->navFor('super_admin');
        $this->assertStringContainsString('/en/strategy', $nav);
        $this->assertStringContainsString('/en/risk', $nav);
    }

    public function test_customer_does_not_see_confidential_links(): void
    {
        $nav = $this->navFor('customer');
        $this->assertStringNotContainsString('/en/strategy', $nav);
        $this->assertStringNotContainsString('/en/risk', $nav);
    }

    public function test_support_does_not_see_confidential_links(): void
    {
        $nav = $this->navFor('support');
        $this->assertStringNotContainsString('/en/strategy', $nav);
        $this->assertStringNotContainsString('/en/risk', $nav);
    }

    public function test_guest_sees_login_link_and_no_account_menu_or_confidential_links(): void
    {
        $nav = $this->navFor(null);
        $this->assertStringContainsString('nav-account-login', $nav);
        // The rendered account dropdown header only exists when authenticated.
        $this->assertStringNotContainsString('nav-account-drop-header', $nav);
        $this->assertStringNotContainsString('/en/strategy', $nav);
        $this->assertStringNotContainsString('/en/risk', $nav);
    }
}

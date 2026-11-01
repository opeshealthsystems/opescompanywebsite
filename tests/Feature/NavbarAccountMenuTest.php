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

    /** Render a public page (which carries the navbar + footer) and return the full HTML. */
    private function pageFor(?string $role): string
    {
        if ($role !== null) {
            $user = User::factory()->create();
            $user->assignRole($role);
            $res = $this->actingAs($user)->get('/en');
        } else {
            $res = $this->get('/en');
        }
        $res->assertOk();

        return $res->getContent();
    }

    /** Only the <nav> region (so footer links don't interfere with nav assertions). */
    private function navFor(?string $role): string
    {
        preg_match('/<nav class="site-nav".*?<\/nav>/s', $this->pageFor($role), $m);

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

    public function test_admin_sees_confidential_strategy_and_risk_links_in_page(): void
    {
        $page = $this->pageFor('super_admin');
        $this->assertStringContainsString('/en/strategy', $page);
        $this->assertStringContainsString('/en/risk', $page);
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

    public function test_developer_and_learning_entry_points_are_reachable(): void
    {
        $page = $this->pageFor(null);
        $this->assertStringContainsString('/en/developers', $page);
        $this->assertStringContainsString('/en/academy', $page);
    }

    public function test_primary_navigation_links_to_six_public_sections(): void
    {
        $nav = $this->navFor(null);
        foreach (['products', 'solutions', 'about', 'blog', 'compliance', 'contact'] as $path) {
            $this->assertStringContainsString('/en/'.$path, $nav);
        }
        preg_match('/<div class="nav-links">(.*?)<\/div>/s', $nav, $links);
        $this->assertSame(6, substr_count($links[1] ?? '', '<a '));
    }

    public function test_footer_confidential_links_hidden_from_guests_and_non_admin_roles(): void
    {
        // Footer links /risk, /strategy, /financial-model only for admin|super_admin.
        foreach ([null, 'customer', 'support'] as $role) {
            $page = $this->pageFor($role);
            $label = $role ?? 'guest';
            $this->assertStringNotContainsString('/en/risk', $page, "{$label} should not see /risk");
            $this->assertStringNotContainsString('/en/strategy', $page, "{$label} should not see /strategy");
            $this->assertStringNotContainsString('/en/financial-model', $page, "{$label} should not see /financial-model");
        }
    }

    public function test_admin_sees_confidential_links_in_footer(): void
    {
        $page = $this->pageFor('super_admin');
        $this->assertStringContainsString('/en/risk', $page);
        $this->assertStringContainsString('/en/financial-model', $page);
    }

    public function test_resources_are_available_from_header_and_footer(): void
    {
        $page = $this->pageFor(null);
        $nav = $this->navFor(null);
        $this->assertStringContainsString('/en/blog', $nav, 'Resources must remain a primary destination');
        $this->assertStringContainsString('/en/blog', $page, 'Blog should still be reachable from the footer');
    }

    public function test_language_switcher_is_a_globe_dropdown(): void
    {
        $nav = $this->navFor(null);
        $this->assertStringContainsString('lang-trigger', $nav);
        $this->assertStringContainsString('lang-menu', $nav);
        $this->assertStringContainsString('aria-controls="language-options"', $nav);
        $this->assertStringContainsString('aria-expanded="false"', $nav);
        $this->assertStringContainsString('English', $nav);
        $this->assertStringContainsString('Français', $nav);
    }
}

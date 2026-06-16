<?php

namespace Tests\Feature;

use App\Models\PartnerInstitution;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class PartnerInstitutionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
    }

    public function test_partnerships_page_shows_featured_active_partner(): void
    {
        $partner = PartnerInstitution::factory()->featured()->create([
            'name'      => 'University of Buea Medical School',
            'is_active' => true,
        ]);

        $this->get('/en/partnerships')
            ->assertOk()
            ->assertSee($partner->name);
    }

    public function test_partnerships_page_hides_non_featured_and_inactive_partners(): void
    {
        $nonFeatured = PartnerInstitution::factory()->create([
            'name'        => 'Hidden Non Featured Institute',
            'is_featured' => false,
            'is_active'   => true,
        ]);

        $inactive = PartnerInstitution::factory()->featured()->create([
            'name'      => 'Hidden Inactive Institute',
            'is_active' => false,
        ]);

        $this->get('/en/partnerships')
            ->assertOk()
            ->assertDontSee($nonFeatured->name)
            ->assertDontSee($inactive->name);
    }

    public function test_customer_role_user_cannot_access_partner_institution_admin_resource(): void
    {
        $customer = User::factory()->create();
        $customer->assignRole('customer');

        $response = $this->actingAs($customer)
            ->get('/admin/partner-institutions');

        $this->assertContains($response->status(), [403, 404, 302]);
    }
}

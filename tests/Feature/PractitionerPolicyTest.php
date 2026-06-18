<?php

namespace Tests\Feature;

use App\Models\PractitionerApplication;
use App\Models\PractitionerFinding;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PractitionerPolicyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    private function makeUser(string $role): User
    {
        $user = User::factory()->create();
        $user->assignRole($role);
        return $user;
    }

    public function test_practitioner_can_view_own_application(): void
    {
        $practitioner = $this->makeUser('practitioner');
        $app = PractitionerApplication::factory()->create(['practitioner_id' => $practitioner->id]);
        $this->assertTrue($practitioner->can('view', $app));
    }

    public function test_practitioner_cannot_view_others_application(): void
    {
        $p1 = $this->makeUser('practitioner');
        $p2 = $this->makeUser('practitioner');
        $app = PractitionerApplication::factory()->create(['practitioner_id' => $p1->id]);
        $this->assertFalse($p2->can('view', $app));
    }

    public function test_admin_can_view_any_application(): void
    {
        $practitioner = $this->makeUser('practitioner');
        $admin = $this->makeUser('admin');
        $app = PractitionerApplication::factory()->create(['practitioner_id' => $practitioner->id]);
        $this->assertTrue($admin->can('view', $app));
    }

    public function test_practitioner_cannot_update_application(): void
    {
        $practitioner = $this->makeUser('practitioner');
        $app = PractitionerApplication::factory()->create(['practitioner_id' => $practitioner->id]);
        $this->assertFalse($practitioner->can('update', $app));
    }

    public function test_admin_can_update_application(): void
    {
        $practitioner = $this->makeUser('practitioner');
        $admin = $this->makeUser('admin');
        $app = PractitionerApplication::factory()->create(['practitioner_id' => $practitioner->id]);
        $this->assertTrue($admin->can('update', $app));
    }

    public function test_practitioner_can_update_unpublished_finding(): void
    {
        $practitioner = $this->makeUser('practitioner');
        $finding = PractitionerFinding::factory()->create([
            'practitioner_id' => $practitioner->id,
            'is_published'    => false,
        ]);
        $this->assertTrue($practitioner->can('update', $finding));
    }

    public function test_practitioner_cannot_update_published_finding(): void
    {
        $practitioner = $this->makeUser('practitioner');
        $finding = PractitionerFinding::factory()->create([
            'practitioner_id' => $practitioner->id,
            'is_published'    => true,
        ]);
        $this->assertFalse($practitioner->can('update', $finding));
    }

    public function test_other_practitioner_cannot_update_finding(): void
    {
        $p1 = $this->makeUser('practitioner');
        $p2 = $this->makeUser('practitioner');
        $finding = PractitionerFinding::factory()->create([
            'practitioner_id' => $p1->id,
            'is_published'    => false,
        ]);
        $this->assertFalse($p2->can('update', $finding));
    }

    public function test_application_show_uses_policy(): void
    {
        $p1 = $this->makeUser('practitioner');
        $p2 = $this->makeUser('practitioner');
        $app = PractitionerApplication::factory()->create(['practitioner_id' => $p1->id]);

        $response = $this->actingAs($p2)->get("/en/practitioner/applications/{$app->id}");
        $response->assertStatus(403);
    }
}

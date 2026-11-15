<?php

namespace Tests\Feature;

use App\Filament\Resources\HospitalOnboardingResource\Pages\CreateHospitalOnboarding;
use App\Models\HospitalOnboarding;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class EmrOnboardingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // notifyTeam() resolves these roles; create them so the scope does not throw.
        Role::findOrCreate('admin', 'web');
        Role::findOrCreate('super_admin', 'web');
    }

    private function payload(array $overrides = []): array
    {
        return array_replace([
            'facility_name' => 'Central Hospital',
            'facility_type' => 'private_hospital',
            'contact_name'  => 'Dr. Jane Doe',
            'contact_email' => 'jane@example.test',
            'staff'         => [
                ['full_name' => 'Alice Nurse', 'job_role' => 'nurse', 'access_level' => 'clinician', 'takes_tasks' => '1'],
                ['full_name' => 'Bob Cashier', 'job_role' => 'cashier'],
                ['full_name' => ''], // empty row -> ignored
            ],
        ], $overrides);
    }

    public function test_public_page_renders_in_both_locales(): void
    {
        $this->get('/en/emr-onboarding')->assertOk()->assertSee('EMR Onboarding');
        $this->get('/fr/emr-onboarding')->assertOk();
    }

    public function test_submit_creates_onboarding_with_staff_and_takes_tasks(): void
    {
        $response = $this->post('/en/emr-onboarding', $this->payload());

        $response->assertRedirect('/en/emr-onboarding');
        $response->assertSessionHas('success');

        $this->assertDatabaseCount('hospital_onboardings', 1);
        $this->assertDatabaseCount('onboarding_staff', 2); // empty row dropped

        $onboarding = HospitalOnboarding::first();
        $this->assertSame('new', $onboarding->status);
        $this->assertSame('en', $onboarding->locale);
        $this->assertSame(2, $onboarding->staff()->count());

        $this->assertDatabaseHas('onboarding_staff', ['full_name' => 'Alice Nurse', 'takes_tasks' => 1]);
        $this->assertDatabaseHas('onboarding_staff', ['full_name' => 'Bob Cashier', 'takes_tasks' => 0]);
    }

    public function test_submit_requires_facility_and_contact(): void
    {
        $response = $this->post('/en/emr-onboarding', [
            'facility_name' => '',
            'contact_name'  => '',
            'contact_email' => 'not-an-email',
        ]);

        $response->assertSessionHasErrors(['facility_name', 'contact_name', 'contact_email']);
        $this->assertDatabaseCount('hospital_onboardings', 0);
        $this->assertDatabaseCount('onboarding_staff', 0);
    }

    public function test_unknown_staff_options_are_discarded(): void
    {
        $this->post('/en/emr-onboarding', $this->payload([
            'staff' => [
                ['full_name' => 'Carol', 'job_role' => 'wizard', 'access_level' => 'root', 'takes_tasks' => '1'],
            ],
        ]))->assertRedirect('/en/emr-onboarding');

        $this->assertDatabaseHas('onboarding_staff', [
            'full_name'    => 'Carol',
            'job_role'     => null,
            'access_level' => null,
            'takes_tasks'  => 1,
        ]);
    }

    public function test_staff_are_removed_with_their_onboarding(): void
    {
        $this->post('/en/emr-onboarding', $this->payload())->assertRedirect();

        HospitalOnboarding::first()->delete();

        $this->assertDatabaseCount('hospital_onboardings', 0);
        $this->assertDatabaseCount('onboarding_staff', 0);
    }

    public function test_admin_can_create_onboarding_with_staff_via_panel(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);

        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole('admin');
        $this->actingAs($admin);

        Livewire::test(CreateHospitalOnboarding::class)
            ->fillForm([
                'facility_name' => 'Panel Hospital',
                'contact_name'  => 'Admin Contact',
                'contact_email' => 'admin-contact@example.test',
                'status'        => 'new',
                'staff'         => [
                    ['full_name' => 'Nurse One', 'job_role' => 'nurse', 'takes_tasks' => true, 'emr_account_created' => false],
                ],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('hospital_onboardings', ['facility_name' => 'Panel Hospital']);
        $this->assertDatabaseHas('onboarding_staff', ['full_name' => 'Nurse One', 'takes_tasks' => 1]);
    }
}

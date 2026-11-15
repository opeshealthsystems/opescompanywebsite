<?php

namespace Tests\Feature;

use App\Filament\Resources\HospitalOnboardingResource\Pages\CreateHospitalOnboarding;
use App\Models\HospitalOnboarding;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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
        Role::findOrCreate('admin', 'web');
        Role::findOrCreate('super_admin', 'web');
    }

    private function payload(array $overrides = []): array
    {
        return array_replace([
            'facility_name'        => 'Joko Clinic',
            'facility_type'        => 'clinic',
            'contact_name'         => 'Dr. Jane Doe',
            'contact_email'        => 'jane@example.test',
            'data_sharing_consent' => '1',
            'sites'                => [
                ['name' => 'Main branch', 'address' => 'Douala'],
                ['name' => ''], // ignored
            ],
            'staff' => [
                ['full_name' => 'Alice Nurse', 'job_role' => 'nurse', 'access_level' => 'approve', 'licence_number' => 'RN-123'],
                ['full_name' => 'Bob Cashier', 'job_role' => 'billing', 'access_level' => 'view_only'],
                ['full_name' => ''], // ignored
            ],
        ], $overrides);
    }

    public function test_public_page_renders_in_both_locales(): void
    {
        $this->get('/en/emr-onboarding')->assertOk()->assertSee('EMR Onboarding');
        $this->get('/fr/emr-onboarding')->assertOk();
    }

    public function test_submit_creates_full_onboarding_with_sites_and_staff(): void
    {
        $response = $this->post('/en/emr-onboarding', $this->payload());

        $response->assertRedirect('/en/emr-onboarding');
        $response->assertSessionHas('success');

        $this->assertDatabaseCount('hospital_onboardings', 1);
        $this->assertDatabaseCount('onboarding_sites', 1); // empty site dropped
        $this->assertDatabaseCount('onboarding_staff', 2); // empty staff dropped

        $onboarding = HospitalOnboarding::first();
        $this->assertSame('new', $onboarding->status);
        $this->assertTrue($onboarding->data_sharing_consent);
        $this->assertDatabaseHas('onboarding_sites', ['name' => 'Main branch', 'address' => 'Douala']);

        // takes_tasks is derived from the access level
        $this->assertDatabaseHas('onboarding_staff', ['full_name' => 'Alice Nurse', 'licence_number' => 'RN-123', 'access_level' => 'approve', 'takes_tasks' => 1]);
        $this->assertDatabaseHas('onboarding_staff', ['full_name' => 'Bob Cashier', 'access_level' => 'view_only', 'takes_tasks' => 0]);
    }

    public function test_consent_is_required(): void
    {
        $this->post('/en/emr-onboarding', $this->payload(['data_sharing_consent' => null]))
            ->assertSessionHasErrors('data_sharing_consent');
        $this->assertDatabaseCount('hospital_onboardings', 0);
    }

    public function test_submit_requires_facility_and_contact(): void
    {
        $this->post('/en/emr-onboarding', [
            'facility_name' => '',
            'contact_name'  => '',
            'contact_email' => 'not-an-email',
        ])->assertSessionHasErrors(['facility_name', 'contact_name', 'contact_email']);

        $this->assertDatabaseCount('hospital_onboardings', 0);
    }

    public function test_uploaded_documents_and_signatures_are_stored_privately(): void
    {
        Storage::fake('local');

        $this->post('/en/emr-onboarding', $this->payload([
            'logo'  => UploadedFile::fake()->image('logo.png'),
            'staff' => [
                ['full_name' => 'Dr. Sign', 'job_role' => 'doctor', 'access_level' => 'approve', 'signature' => UploadedFile::fake()->image('sig.png')],
            ],
        ]))->assertRedirect('/en/emr-onboarding');

        $onboarding = HospitalOnboarding::first();
        $this->assertNotNull($onboarding->logo_path);
        Storage::disk('local')->assertExists($onboarding->logo_path);

        $signaturePath = $onboarding->staff()->first()->signature_path;
        $this->assertNotNull($signaturePath);
        Storage::disk('local')->assertExists($signaturePath);
    }

    public function test_unknown_staff_options_are_discarded(): void
    {
        $this->post('/en/emr-onboarding', $this->payload([
            'staff' => [['full_name' => 'Carol', 'job_role' => 'wizard', 'access_level' => 'root']],
        ]))->assertRedirect('/en/emr-onboarding');

        $this->assertDatabaseHas('onboarding_staff', [
            'full_name'    => 'Carol',
            'job_role'     => null,
            'access_level' => null,
            'takes_tasks'  => 0,
        ]);
    }

    public function test_staff_and_sites_are_removed_with_their_onboarding(): void
    {
        $this->post('/en/emr-onboarding', $this->payload())->assertRedirect();

        HospitalOnboarding::first()->delete();

        $this->assertDatabaseCount('hospital_onboardings', 0);
        $this->assertDatabaseCount('onboarding_staff', 0);
        $this->assertDatabaseCount('onboarding_sites', 0);
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
                    ['full_name' => 'Nurse One', 'job_role' => 'nurse', 'access_level' => 'enter_data', 'takes_tasks' => true, 'emr_account_created' => false],
                ],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('hospital_onboardings', ['facility_name' => 'Panel Hospital']);
        $this->assertDatabaseHas('onboarding_staff', ['full_name' => 'Nurse One', 'takes_tasks' => 1]);
    }
}

<?php

namespace Tests\Feature;

use App\Models\CourseCertificate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class CertificatePdfTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
    }

    private function certificateFor(User $owner): CourseCertificate
    {
        return CourseCertificate::factory()->create(['user_id' => $owner->id]);
    }

    public function test_owner_can_download_certificate_pdf(): void
    {
        Storage::fake();

        $owner = User::factory()->create();
        $owner->assignRole('practitioner');
        $certificate = $this->certificateFor($owner);

        $response = $this->actingAs($owner)
            ->get(route('certificates.pdf', $certificate));

        $response->assertOk();
        $this->assertStringContainsString('application/pdf', $response->headers->get('Content-Type'));
    }

    public function test_non_owner_non_admin_is_forbidden(): void
    {
        Storage::fake();

        $owner = User::factory()->create();
        $owner->assignRole('practitioner');
        $certificate = $this->certificateFor($owner);

        $other = User::factory()->create();
        $other->assignRole('practitioner');

        $this->actingAs($other)
            ->get(route('certificates.pdf', $certificate))
            ->assertForbidden();
    }

    public function test_admin_can_download_any_certificate(): void
    {
        Storage::fake();

        $owner = User::factory()->create();
        $owner->assignRole('practitioner');
        $certificate = $this->certificateFor($owner);

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin)
            ->get(route('certificates.pdf', $certificate))
            ->assertOk();
    }

    public function test_pdf_path_is_persisted_after_first_download(): void
    {
        Storage::fake();

        $owner = User::factory()->create();
        $owner->assignRole('practitioner');
        $certificate = $this->certificateFor($owner);

        $this->assertNull($certificate->pdf_path);

        $this->actingAs($owner)
            ->get(route('certificates.pdf', $certificate))
            ->assertOk();

        $this->assertNotNull($certificate->fresh()->pdf_path);
    }
}

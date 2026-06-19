<?php

namespace Tests\Feature;

use App\Models\CohortMember;
use App\Models\User;
use App\Models\ValidationCertificate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class ValidationCertificateDownloadTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
    }

    private function certFor(User $user): ValidationCertificate
    {
        $member = CohortMember::factory()->create(['user_id' => $user->id]);
        return ValidationCertificate::factory()->create(['cohort_member_id' => $member->id, 'tier' => 'distinction', 'score' => 90]);
    }

    public function test_owner_can_download(): void
    {
        $user = User::factory()->create();
        $user->assignRole('practitioner');
        $cert = $this->certFor($user);

        $this->actingAs($user)
            ->get("/en/practitioner/certificates/validation/{$cert->id}/download")
            ->assertOk()
            ->assertDownload($cert->certificate_number.'.pdf');
    }

    public function test_other_practitioner_forbidden(): void
    {
        $owner = User::factory()->create();
        $owner->assignRole('practitioner');
        $cert = $this->certFor($owner);

        $other = User::factory()->create();
        $other->assignRole('practitioner');
        $this->actingAs($other)
            ->get("/en/practitioner/certificates/validation/{$cert->id}/download")
            ->assertForbidden();
    }
}

<?php

namespace Tests\Feature;

use App\Models\AdvisoryCouncilMember;
use App\Models\CohortMember;
use App\Models\User;
use App\Models\ValidationCertificate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ValidationCertificationModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_certificate_auto_numbers_and_relationships(): void
    {
        $cert = ValidationCertificate::factory()->create();
        $this->assertStringStartsWith('VCERT-'.date('Y').'-', $cert->certificate_number);
        $this->assertInstanceOf(CohortMember::class, $cert->cohortMember);
        $this->assertNotNull($cert->issuedBy);
    }

    public function test_user_relationships(): void
    {
        $user   = User::factory()->create();
        $member = CohortMember::factory()->create(['user_id' => $user->id]);
        $cert   = ValidationCertificate::factory()->create(['cohort_member_id' => $member->id]);
        AdvisoryCouncilMember::factory()->create(['user_id' => $user->id]);

        $this->assertTrue($user->validationCertificates->contains($cert));
        $this->assertNotNull($user->advisoryCouncilMembership);
    }

    public function test_option_maps(): void
    {
        $this->assertArrayHasKey('active', AdvisoryCouncilMember::statusOptions());
        $this->assertArrayHasKey('distinction', ValidationCertificate::tierBadgeColors());
        $this->assertArrayHasKey('pass', ValidationCertificate::tierBadgeColors());
    }
}

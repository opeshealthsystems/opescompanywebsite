<?php

namespace Tests\Feature;

use App\Models\AdvisoryCouncilMember;
use App\Models\CohortMember;
use App\Models\FinalEvaluation;
use App\Models\User;
use App\Models\ValidationCertificate;
use App\Notifications\CertificateIssued;
use App\Notifications\CouncilInvitation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class N2CertificationNotificationsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
    }

    private function certifiableEvaluation(): FinalEvaluation
    {
        $user = User::factory()->create();
        $user->assignRole('practitioner');
        $member = CohortMember::factory()->create(['user_id' => $user->id]);

        // rating + metrics drive CertificationScore: outstanding(50) + capped contribution(50) = 100 → distinction,
        // so issueFor() does not abort with "not eligible".
        return FinalEvaluation::factory()->create([
            'cohort_member_id' => $member->id,
            'rating'           => 'outstanding',
            'metrics'          => ['issues_accepted' => 8, 'sessions' => 10, 'retests' => 3],
        ]);
    }

    public function test_issuing_a_certificate_notifies_the_practitioner(): void
    {
        Notification::fake();
        $evaluation = $this->certifiableEvaluation();
        $admin      = User::factory()->create();

        ValidationCertificate::issueFor($evaluation, $admin->id);

        Notification::assertSentTo($evaluation->cohortMember->user, CertificateIssued::class);
    }

    public function test_council_invitation_notifies_the_practitioner(): void
    {
        Notification::fake();
        $user   = User::factory()->create();
        $member = CohortMember::factory()->create(['user_id' => $user->id]);
        $cert   = ValidationCertificate::factory()->create([
            'cohort_member_id' => $member->id,
            'tier'             => 'distinction',
        ]);

        $councilMember = AdvisoryCouncilMember::create([
            'user_id'                   => $user->id,
            'validation_certificate_id' => $cert->id,
            'title'                     => 'Clinical Validation Advisor',
            'term_start'                => now(),
            'status'                    => 'active',
            'invited_by'                => User::factory()->create()->id,
            'invited_at'                => now(),
        ]);
        $user->notify(new CouncilInvitation($councilMember));

        Notification::assertSentTo($user, CouncilInvitation::class);
    }
}

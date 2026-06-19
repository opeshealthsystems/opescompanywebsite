<?php

namespace Tests\Feature;

use App\Models\FinalEvaluation;
use App\Models\User;
use App\Models\ValidationCertificate;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class ValidationCertificateTest extends TestCase
{
    use RefreshDatabase;

    public function test_issue_for_freezes_score_and_tier(): void
    {
        $admin = User::factory()->create();
        $eval  = FinalEvaluation::factory()->create([
            'rating'  => 'outstanding',
            'metrics' => ['issues_accepted' => 8, 'sessions' => 10, 'retests' => 4],
        ]);

        $cert = ValidationCertificate::issueFor($eval, $admin->id);

        $this->assertEquals(100, $cert->score);
        $this->assertEquals('distinction', $cert->tier);
        $this->assertEquals($eval->cohort_member_id, $cert->cohort_member_id);
        $this->assertEquals($admin->id, $cert->issued_by);
        $this->assertStringStartsWith('VCERT-', $cert->certificate_number);
    }

    public function test_issue_for_rejects_not_certified(): void
    {
        $admin = User::factory()->create();
        $eval  = FinalEvaluation::factory()->create([
            'rating'  => 'needs_improvement',
            'metrics' => ['issues_accepted' => 0, 'sessions' => 0, 'retests' => 0],
        ]);

        $this->expectException(HttpException::class);
        ValidationCertificate::issueFor($eval, $admin->id);
        $this->assertDatabaseCount('validation_certificates', 0);
    }

    public function test_unique_per_member(): void
    {
        $admin = User::factory()->create();
        $eval  = FinalEvaluation::factory()->create(['rating' => 'strong', 'metrics' => ['issues_accepted' => 5, 'sessions' => 5, 'retests' => 2]]);

        ValidationCertificate::issueFor($eval, $admin->id);

        $this->expectException(QueryException::class);
        ValidationCertificate::factory()->create(['cohort_member_id' => $eval->cohort_member_id]);
    }
}

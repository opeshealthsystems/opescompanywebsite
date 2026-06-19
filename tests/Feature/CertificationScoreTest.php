<?php

namespace Tests\Feature;

use App\Models\FinalEvaluation;
use App\Support\CertificationScore;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CertificationScoreTest extends TestCase
{
    use RefreshDatabase;

    private function score(FinalEvaluation $e): array
    {
        return app(CertificationScore::class)->for($e);
    }

    public function test_outstanding_with_high_contribution_is_distinction(): void
    {
        $e = FinalEvaluation::factory()->create([
            'rating'  => 'outstanding',
            'metrics' => ['issues_accepted' => 8, 'sessions' => 10, 'retests' => 4],
        ]);
        $r = $this->score($e);
        $this->assertEquals(100, $r['score']); // 50 + min(50, 40+10+8)
        $this->assertEquals('distinction', $r['tier']);
    }

    public function test_strong_with_modest_contribution_is_pass_at_boundary(): void
    {
        $e = FinalEvaluation::factory()->create([
            'rating'  => 'strong', // 38
            'metrics' => ['issues_accepted' => 4, 'sessions' => 2, 'retests' => 0], // 20+2 = 22
        ]);
        $r = $this->score($e);
        $this->assertEquals(60, $r['score']);
        $this->assertEquals('pass', $r['tier']);
    }

    public function test_needs_improvement_is_not_certified(): void
    {
        $e = FinalEvaluation::factory()->create([
            'rating'  => 'needs_improvement', // 10
            'metrics' => ['issues_accepted' => 0, 'sessions' => 1, 'retests' => 0], // 1
        ]);
        $r = $this->score($e);
        $this->assertEquals(11, $r['score']);
        $this->assertEquals('not_certified', $r['tier']);
    }

    public function test_contribution_is_capped_at_fifty(): void
    {
        $e = FinalEvaluation::factory()->create([
            'rating'  => 'satisfactory', // 25
            'metrics' => ['issues_accepted' => 100, 'sessions' => 100, 'retests' => 100],
        ]);
        $r = $this->score($e);
        $this->assertEquals(75, $r['score']); // 25 + 50 (capped)
    }

    public function test_tier_options(): void
    {
        $this->assertCount(3, CertificationScore::tierOptions());
    }
}

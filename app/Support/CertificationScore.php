<?php

namespace App\Support;

use App\Models\FinalEvaluation;

class CertificationScore
{
    public const PASS_THRESHOLD = 60;
    public const DISTINCTION_THRESHOLD = 85;

    /** @return array{score:int, tier:string, breakdown:array} */
    public function for(FinalEvaluation $evaluation): array
    {
        $ratingPoints = match ($evaluation->rating) {
            'outstanding'       => 50,
            'strong'            => 38,
            'satisfactory'      => 25,
            'needs_improvement' => 10,
            default             => 0,
        };

        $m = $evaluation->metrics ?? [];
        $contribution = min(50,
            ((int) ($m['issues_accepted'] ?? 0)) * 5
            + ((int) ($m['sessions'] ?? 0)) * 1
            + ((int) ($m['retests'] ?? 0)) * 2
        );

        $score = $ratingPoints + $contribution;

        $tier = match (true) {
            $score >= self::DISTINCTION_THRESHOLD => 'distinction',
            $score >= self::PASS_THRESHOLD        => 'pass',
            default                               => 'not_certified',
        };

        return [
            'score'     => $score,
            'tier'      => $tier,
            'breakdown' => ['rating' => $ratingPoints, 'contribution' => $contribution],
        ];
    }

    public static function tierOptions(): array
    {
        return [
            'distinction'   => 'Distinction',
            'pass'          => 'Pass',
            'not_certified' => 'Not Certified',
        ];
    }
}

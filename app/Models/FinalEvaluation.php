<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinalEvaluation extends Model
{
    use HasFactory;

    protected $fillable = [
        'cohort_member_id', 'metrics', 'assessment',
        'rating', 'recommendation', 'evaluator_id', 'evaluated_at',
    ];

    protected $casts = [
        'metrics'      => 'array',
        'evaluated_at' => 'datetime',
    ];

    public function cohortMember(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(CohortMember::class);
    }

    public function evaluator(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'evaluator_id');
    }

    public static function ratingOptions(): array
    {
        return [
            'outstanding'       => 'Outstanding',
            'strong'            => 'Strong',
            'satisfactory'      => 'Satisfactory',
            'needs_improvement' => 'Needs Improvement',
        ];
    }
}

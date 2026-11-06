<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class CourseAssessmentAttempt extends Model
{
    protected $fillable = ['assessment_id', 'user_id', 'status', 'score', 'passed', 'answers', 'started_at', 'submitted_at'];

    protected $casts = ['score' => 'decimal:2', 'passed' => 'boolean', 'answers' => 'array', 'started_at' => 'datetime', 'submitted_at' => 'datetime'];

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(CourseAssessment::class, 'assessment_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

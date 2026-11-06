<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class CourseAssessment extends Model
{
    protected $fillable = ['course_id', 'title', 'instructions', 'pass_mark', 'attempt_limit', 'duration_minutes', 'questions', 'is_published'];

    protected $casts = ['questions' => 'array', 'is_published' => 'boolean', 'pass_mark' => 'integer', 'attempt_limit' => 'integer', 'duration_minutes' => 'integer'];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(CourseAssessmentAttempt::class, 'assessment_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CourseEnrollment extends Model
{
    protected $fillable = ['course_id','user_id','status','enrolled_at','completed_at'];

    protected $casts = [
        'enrolled_at'  => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function lessonProgress(): HasMany
    {
        return $this->hasMany(CourseLessonProgress::class, 'enrollment_id');
    }

    public function certificate(): HasOne
    {
        return $this->hasOne(CourseCertificate::class, 'enrollment_id');
    }

    public function progressPercent(): int
    {
        $total = $this->course->lessons()->count();
        if ($total === 0) return 0;
        $done = $this->lessonProgress()->whereNotNull('completed_at')->count();
        return (int) round($done / $total * 100);
    }

    public function isComplete(): bool
    {
        return $this->status === 'completed';
    }

    public static function statusOptions(): array
    {
        return [
            'enrolled'    => 'Enrolled',
            'in_progress' => 'In Progress',
            'completed'   => 'Completed',
            'dropped'     => 'Dropped',
        ];
    }
}

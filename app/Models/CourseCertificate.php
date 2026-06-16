<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseCertificate extends Model
{
    protected $fillable = ['enrollment_id','user_id','course_id','certificate_number','issued_at','pdf_path'];

    protected $casts = ['issued_at' => 'datetime'];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function ($model) {
            if (!$model->certificate_number) {
                $year = date('Y');
                $last = static::where('certificate_number', 'like', "CERT-{$year}-%")->max('certificate_number');
                $next = $last ? ((int) substr($last, -6)) + 1 : 1;
                $model->certificate_number = 'CERT-' . $year . '-' . str_pad($next, 6, '0', STR_PAD_LEFT);
            }
            if (!$model->issued_at) {
                $model->issued_at = now();
            }
        });
    }

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(CourseEnrollment::class, 'enrollment_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}

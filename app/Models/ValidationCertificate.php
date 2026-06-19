<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ValidationCertificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'cohort_member_id', 'final_evaluation_id', 'certificate_number',
        'score', 'tier', 'issued_by', 'issued_at',
    ];

    protected $casts = [
        'issued_at' => 'datetime',
        'score'     => 'integer',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function (ValidationCertificate $model) {
            if (! $model->certificate_number) {
                $year = date('Y');
                $last = static::where('certificate_number', 'like', "VCERT-{$year}-%")->max('certificate_number');
                $next = $last ? ((int) substr($last, -6)) + 1 : 1;
                $model->certificate_number = 'VCERT-' . $year . '-' . str_pad((string) $next, 6, '0', STR_PAD_LEFT);
            }
            if (! $model->issued_at) {
                $model->issued_at = now();
            }
        });
    }

    public function cohortMember(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(CohortMember::class);
    }

    public function finalEvaluation(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(FinalEvaluation::class);
    }

    public function issuedBy(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    public function advisoryCouncilMember(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(AdvisoryCouncilMember::class);
    }

    public static function tierBadgeColors(): array
    {
        return ['distinction' => 'success', 'pass' => 'info'];
    }
}

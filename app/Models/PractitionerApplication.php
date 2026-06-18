<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PractitionerApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'practitioner_id','program_id','motivation','status',
        'reviewed_by','reviewed_at','admin_notes',
        'payout_status','payout_amount','payout_currency','payout_reference','paid_at',
        'payout_provider','payout_initiated_at','payout_failure_reason',
    ];

    protected $casts = [
        'practitioner_id'     => 'integer',
        'reviewed_at'         => 'datetime',
        'paid_at'             => 'datetime',
        'payout_amount'       => 'decimal:2',
        'payout_initiated_at' => 'datetime',
    ];

    public function practitioner()
    {
        return $this->belongsTo(User::class, 'practitioner_id');
    }

    public function program()
    {
        return $this->belongsTo(PractitionerProgram::class, 'program_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function findings()
    {
        return $this->hasMany(PractitionerFinding::class, 'application_id');
    }

    /**
     * Order applicants by practitioner tier priority (Fellow → Distinguished
     * → Verified → Associate), then oldest application first (FIFO). Tier
     * ordering is monotonic in (is_verified, published-findings count), so we
     * sort by verification first, then published-findings count.
     */
    public function scopeByTierPriority($query)
    {
        return $query
            ->select('practitioner_applications.*')
            ->leftJoin('practitioner_profiles', 'practitioner_profiles.user_id', '=', 'practitioner_applications.practitioner_id')
            ->orderByDesc('practitioner_profiles.is_verified')
            ->orderByDesc(
                PractitionerFinding::selectRaw('count(*)')
                    ->whereColumn('practitioner_findings.practitioner_id', 'practitioner_applications.practitioner_id')
                    ->where('practitioner_findings.is_published', true)
            )
            ->orderBy('practitioner_applications.created_at');
    }

    public static function statusOptions(): array
    {
        return [
            'pending'   => 'Pending',
            'approved'  => 'Approved',
            'rejected'  => 'Rejected',
            'withdrawn' => 'Withdrawn',
        ];
    }

    public static function payoutStatusOptions(): array
    {
        return [
            'not_applicable' => 'N/A',
            'pending'        => 'Pending',
            'paid'           => 'Paid',
            'failed'         => 'Failed',
        ];
    }

    public function isPaidProgram(): bool
    {
        return $this->program?->type === 'paid';
    }

    /** Whether this application can currently be paid out (paid program, approved, not already paid). */
    public function isPayable(): bool
    {
        return $this->isPaidProgram()
            && $this->status === 'approved'
            && $this->payout_status !== 'paid';
    }

    /**
     * Approve this application. Paid programs become payout-pending;
     * volunteer programs stay not_applicable.
     */
    public function markApproved(?int $reviewerId): void
    {
        $this->update([
            'status'        => 'approved',
            'reviewed_by'   => $reviewerId,
            'reviewed_at'   => now(),
            'payout_status' => $this->isPaidProgram() ? 'pending' : 'not_applicable',
        ]);
    }
}

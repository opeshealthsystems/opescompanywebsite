<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CohortMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'cohort_id',
        'user_id',
        'status',
        'placed_at',
    ];

    protected $casts = [
        'placed_at' => 'datetime',
    ];

    public function cohort(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Cohort::class);
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function dailyTestSessions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Models\DailyTestSession::class);
    }

    public function issueReports(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Models\IssueReport::class);
    }

    public function finalEvaluation(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(\App\Models\FinalEvaluation::class);
    }

    public static function statusOptions(): array
    {
        return ['active' => 'Active', 'suspended' => 'Suspended', 'completed' => 'Completed', 'removed' => 'Removed'];
    }
}

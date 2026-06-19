<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClinicalReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'issue_report_id',
        'reviewer_id',
        'decision',
        'notes',
        'reviewed_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    public function issueReport(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(IssueReport::class);
    }

    public function reviewer(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public static function decisionOptions(): array
    {
        return ['approved_for_product_review' => 'Approved for Product Review', 'rejected' => 'Rejected', 'needs_more_information' => 'Needs More Information'];
    }
}

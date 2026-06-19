<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Retest extends Model
{
    use HasFactory;

    protected $fillable = [
        'issue_report_id', 'developer_task_id', 'cohort_member_id',
        'result', 'notes', 'attachments', 'retested_at',
    ];

    protected $casts = [
        'attachments' => 'array',
        'retested_at' => 'datetime',
    ];

    public function issueReport(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(IssueReport::class);
    }

    public function developerTask(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(DeveloperTask::class);
    }

    public function cohortMember(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(CohortMember::class);
    }

    public static function resultOptions(): array
    {
        return ['passed' => 'Passed', 'failed' => 'Failed'];
    }
}

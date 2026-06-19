<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyTestSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'cohort_member_id',
        'validation_product_id',
        'validation_module_id',
        'validation_workflow_id',
        'facility_context',
        'date',
        'start_time',
        'end_time',
        'tasks_completed',
        'screenshots',
        'comments',
    ];

    protected $casts = [
        'date'            => 'date',
        'screenshots'     => 'array',
        'tasks_completed' => 'integer',
    ];

    public function cohortMember(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(CohortMember::class);
    }

    public function product(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ValidationProduct::class, 'validation_product_id');
    }

    public function module(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ValidationModule::class, 'validation_module_id');
    }

    public function workflow(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ValidationWorkflow::class, 'validation_workflow_id');
    }

    public function issueReports(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(IssueReport::class);
    }
}

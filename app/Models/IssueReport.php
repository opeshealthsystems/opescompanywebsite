<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IssueReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'cohort_member_id',
        'daily_test_session_id',
        'validation_product_id',
        'validation_module_id',
        'validation_workflow_id',
        'validation_test_case_id',
        'title',
        'issue_type',
        'severity',
        'description',
        'steps_to_reproduce',
        'expected_result',
        'actual_result',
        'clinical_impact',
        'recommendation',
        'attachments',
        'status',
    ];

    protected $casts = [
        'attachments' => 'array',
    ];

    public function cohortMember(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(CohortMember::class);
    }

    public function dailyTestSession(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(DailyTestSession::class);
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

    public function testCase(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ValidationTestCase::class, 'validation_test_case_id');
    }

    public function clinicalReview(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(ClinicalReview::class);
    }

    public function productReview(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(ProductReview::class);
    }

    public static function issueTypeOptions(): array
    {
        return [
            'bug' => 'Bug',
            'missing_feature' => 'Missing Feature',
            'workflow_problem' => 'Workflow Problem',
            'clinical_risk' => 'Clinical Risk',
            'ui_ux_problem' => 'UI/UX Problem',
            'performance_issue' => 'Performance Issue',
            'security_concern' => 'Security Concern',
            'interoperability_issue' => 'Interoperability Issue',
            'data_quality_issue' => 'Data Quality Issue',
            'recommendation' => 'Recommendation',
        ];
    }

    public static function severityOptions(): array
    {
        return ['critical' => 'Critical', 'high' => 'High', 'medium' => 'Medium', 'low' => 'Low'];
    }

    public static function statusOptions(): array
    {
        return [
            'submitted' => 'Submitted',
            'clinical_review' => 'Clinical Review',
            'product_review' => 'Product Review',
            'accepted' => 'Accepted',
            'rejected' => 'Rejected',
            'duplicate' => 'Duplicate',
            'needs_more_information' => 'Needs More Information',
            'sent_to_development' => 'Sent to Development',
            'fixed' => 'Fixed',
            'closed' => 'Closed',
        ];
    }
}

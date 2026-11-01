<?php

namespace App\Contexts\Shared\Application\Queries;

use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Models\Announcement;
use App\Models\AdvisoryCouncilMember;
use App\Models\AuditLog;
use App\Models\AttendanceRecord;
use App\Models\Deal;
use App\Models\InvoiceTemplate;
use App\Models\JobOpening;
use App\Models\KnowledgeBaseArticle;
use App\Models\Lead;
use App\Models\LeaveRequest;
use App\Models\License;
use App\Models\PerformanceReview;
use App\Models\Product;
use App\Models\Project;
use App\Models\Risk;
use App\Models\Ticket;
use App\Models\TrainingRecord;
use App\Models\TesterAssignment;
use App\Models\Survey;
use App\Models\SurveyResponse;
use App\Models\ValidationCertificate;
use App\Models\User;
use Spatie\Permission\Models\Role;

/** Small, role-protected admin summaries shared by navigation and API consumers. */
final class AdminResourceSummaryQueries extends WorkflowHandler
{
    public function metric(WorkflowInput $input): WorkflowResult
    {
        $actor = $input->user();
        abort_unless($actor instanceof User && $actor->is_active && $actor->hasAnyRole(['super_admin', 'admin']), 403);

        $filters = $input->validate([
            'metric' => 'required|in:attendance.absent_today,announcements.active,audit_logs.total,deals.open,employees.staff,invoice_templates.due,jobs.open,knowledge.drafts,leads.new,leave.pending,licenses.expiring,performance.drafts,products.active,projects.active,risks.high_open,tickets.open,training.expiring,tester_assignments.pending,advisory_council.user_exists,validation_certificates.member_exists,tester_assignments.has_bug_reports,surveys.has_responses,leave_types.has_requests,document_templates.document_count,products.license_count,roles.permission_count,roles.user_count,practitioners.cohort_exists',
            'id' => 'sometimes|integer|min:1', 'program_id' => 'sometimes|integer|min:1',
        ]);
        $id = $filters['id'] ?? null;
        $requiresId = ['advisory_council.user_exists', 'validation_certificates.member_exists', 'tester_assignments.has_bug_reports', 'surveys.has_responses', 'leave_types.has_requests', 'document_templates.document_count', 'products.license_count', 'roles.permission_count', 'roles.user_count', 'practitioners.cohort_exists'];
        abort_if(in_array($filters['metric'], $requiresId, true) && $id === null, 422, 'This summary metric requires an id.');
        abort_if($filters['metric'] === 'practitioners.cohort_exists' && empty($filters['program_id']), 422, 'The cohort metric requires a program_id.');
        $count = match ($filters['metric']) {
            'attendance.absent_today' => AttendanceRecord::query()->whereDate('date', today())->where('status', 'absent')->count(),
            'announcements.active' => Announcement::query()->where('is_active', true)->count(),
            'audit_logs.total' => AuditLog::query()->count(),
            'deals.open' => Deal::query()->whereIn('stage', ['prospecting', 'qualification', 'proposal', 'negotiation'])->count(),
            'employees.staff' => User::query()->whereHas('roles', fn ($roles) => $roles->whereIn('name', ['super_admin', 'admin', 'support', 'tester']))->count(),
            'invoice_templates.due' => InvoiceTemplate::query()->where('is_active', true)->where('next_due_date', '<=', now())->count(),
            'jobs.open' => JobOpening::query()->where('status', 'open')->count(),
            'knowledge.drafts' => KnowledgeBaseArticle::query()->where('status', 'draft')->count(),
            'leads.new' => Lead::query()->where('status', 'new')->count(),
            'leave.pending' => LeaveRequest::query()->where('status', 'pending')->count(),
            'licenses.expiring' => License::query()->where('status', 'active')->whereBetween('end_date', [now(), now()->addDays(30)])->count(),
            'performance.drafts' => PerformanceReview::query()->where('status', 'draft')->count(),
            'products.active' => Product::query()->where('is_active', true)->count(),
            'projects.active' => Project::query()->whereIn('status', ['planning', 'active'])->count(),
            'risks.high_open' => Risk::query()->where('status', 'open')->where('risk_score', '>=', 12)->count(),
            'tickets.open' => Ticket::query()->whereIn('status', ['open', 'in_progress'])->count(),
            'training.expiring' => TrainingRecord::query()->where('status', 'completed')->whereNotNull('expires_at')->whereDate('expires_at', '<=', now()->addDays(30))->whereDate('expires_at', '>=', now())->count(),
            'tester_assignments.pending' => TesterAssignment::query()->where('status', 'pending')->count(),
            'advisory_council.user_exists' => (int) AdvisoryCouncilMember::query()->where('user_id', $id)->exists(),
            'validation_certificates.member_exists' => (int) ValidationCertificate::query()->where('cohort_member_id', $id)->exists(),
            'tester_assignments.has_bug_reports' => (int) TesterAssignment::query()->findOrFail($id)->bugReports()->exists(),
            'surveys.has_responses' => (int) SurveyResponse::query()->where('survey_id', $id)->exists(),
            'leave_types.has_requests' => (int) LeaveRequest::query()->where('leave_type_id', $id)->exists(),
            'document_templates.document_count' => DocumentTemplate::query()->findOrFail($id)->documents()->count(),
            'products.license_count' => Product::query()->findOrFail($id)->licenses()->count(),
            'roles.permission_count' => (int) Role::query()->findOrFail($id)->permissions()->count(),
            'roles.user_count' => (int) Role::query()->findOrFail($id)->users()->count(),
            'practitioners.cohort_exists' => (int) User::query()->findOrFail($id)->cohortMembers()
                ->whereHas('cohort', fn ($cohorts) => $cohorts->where('practitioner_program_id', $filters['program_id']))->exists(),
        };

        return WorkflowResult::payload(['metric' => $filters['metric'], 'count' => $count]);
    }
}

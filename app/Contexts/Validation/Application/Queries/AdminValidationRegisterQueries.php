<?php

namespace App\Contexts\Validation\Application\Queries;

use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Models\DailyTestSession;
use App\Models\DeveloperTask;
use App\Models\IssueReport;
use App\Models\Retest;
use App\Models\User;
use App\Models\WeeklyReview;
use Illuminate\Database\Eloquent\Builder;

final class AdminValidationRegisterQueries extends WorkflowHandler
{
    private function admin(WorkflowInput $input): User
    {
        $actor = $input->user();
        abort_unless($actor instanceof User && $actor->is_active && $actor->hasAnyRole(['super_admin', 'admin']), 403);
        return $actor;
    }

    private function paginated(WorkflowInput $input, Builder $query, callable $map): WorkflowResult
    {
        $page = $input->paginate($query);
        return WorkflowResult::payload([
            'items' => $page->getCollection()->map($map)->values()->all(),
            'pagination' => ['current_page' => $page->currentPage(), 'per_page' => $page->perPage(), 'total' => $page->total(), 'last_page' => $page->lastPage()],
        ]);
    }

    public function issueReportQuery(WorkflowInput $input): Builder
    {
        $this->admin($input);
        return IssueReport::query()->with(['product:id,name', 'cohortMember.user:id,name', 'cohortMember.cohort:id,name'])->orderByDesc('created_at');
    }

    public function issueReports(WorkflowInput $input): WorkflowResult
    {
        $this->admin($input);
        $filters = $input->validate([
            'status' => 'sometimes|in:submitted,clinical_review,product_review,accepted,rejected,duplicate,needs_more_information,sent_to_development,fixed,ready_for_retest,retest_passed,retest_failed,closed',
            'severity' => 'sometimes|in:critical,high,medium,low',
            'issue_type' => 'sometimes|in:bug,missing_feature,workflow_problem,clinical_risk,ui_ux_problem,performance_issue,security_concern,interoperability_issue,data_quality_issue,recommendation',
            'search' => 'sometimes|string|max:120',
        ]);
        $query = $this->issueReportQuery($input);
        foreach (['status', 'severity', 'issue_type'] as $field) if (isset($filters[$field])) $query->where($field, $filters[$field]);
        if (isset($filters['search'])) $query->where('title', 'like', '%'.$filters['search'].'%');

        return $this->paginated($input, $query, fn (IssueReport $record) => [
            'id' => $record->id,
            'title' => $record->title,
            'issue_type' => $record->issue_type,
            'severity' => $record->severity,
            'status' => $record->status,
            'validation_product_id' => $record->validation_product_id,
            'product_name' => $record->product?->name,
            'cohort_member_id' => $record->cohort_member_id,
            'reporter_name' => $record->cohortMember?->user?->name,
            'cohort_name' => $record->cohortMember?->cohort?->name,
            'created_at' => $record->created_at?->toIso8601String(),
        ]);
    }

    public function developerTaskQuery(WorkflowInput $input): Builder
    {
        $this->admin($input);
        return DeveloperTask::query()->with(['issueReport:id,title,severity,status', 'assignedTo:id,name'])->withCount('retests')->orderByDesc('created_at');
    }

    public function developerTasks(WorkflowInput $input): WorkflowResult
    {
        $this->admin($input);
        $filters = $input->validate([
            'status' => 'sometimes|in:open,in_progress,fixed,reopened,wont_fix',
            'priority' => 'sometimes|in:critical,high,medium,low',
            'assigned_to' => 'sometimes|integer|exists:users,id',
            'search' => 'sometimes|string|max:120',
        ]);
        $query = $this->developerTaskQuery($input);
        foreach (['status', 'priority', 'assigned_to'] as $field) if (isset($filters[$field])) $query->where($field, $filters[$field]);
        if (isset($filters['search'])) $query->where('title', 'like', '%'.$filters['search'].'%');

        return $this->paginated($input, $query, fn (DeveloperTask $record) => [
            'id' => $record->id,
            'issue_report_id' => $record->issue_report_id,
            'title' => $record->title,
            'priority' => $record->priority,
            'status' => $record->status,
            'assigned_to' => $record->assigned_to,
            'assignee_name' => $record->assignedTo?->name,
            'issue' => $record->issueReport ? ['id' => $record->issueReport->id, 'title' => $record->issueReport->title, 'severity' => $record->issueReport->severity, 'status' => $record->issueReport->status] : null,
            'retests_count' => $record->retests_count,
            'started_at' => $record->started_at?->toIso8601String(),
            'fixed_at' => $record->fixed_at?->toIso8601String(),
            'created_at' => $record->created_at?->toIso8601String(),
        ]);
    }

    public function retestQuery(WorkflowInput $input, DeveloperTask $task): Builder
    {
        $this->admin($input);

        return Retest::query()->where('developer_task_id', $task->id)
            ->with('cohortMember.user:id,name')->orderByDesc('retested_at')->orderByDesc('id');
    }

    public function retests(WorkflowInput $input, DeveloperTask $task): WorkflowResult
    {
        return $this->paginated($input, $this->retestQuery($input, $task), fn (Retest $retest) => [
            'id' => $retest->id,
            'developer_task_id' => $retest->developer_task_id,
            'result' => $retest->result,
            'notes' => $retest->notes,
            'retested_at' => $retest->retested_at?->toIso8601String(),
            'cohort_member_id' => $retest->cohort_member_id,
            'tester_name' => $retest->cohortMember?->user?->name,
        ]);
    }

    public function dailyTestSessionQuery(WorkflowInput $input): Builder
    {
        $this->admin($input);
        return DailyTestSession::query()->with([
            'cohortMember.user:id,name', 'cohortMember.cohort:id,name', 'product:id,name', 'module:id,name', 'workflow:id,name',
        ])->withCount('issueReports')->orderByDesc('date');
    }

    public function dailyTestSessions(WorkflowInput $input): WorkflowResult
    {
        $this->admin($input);
        $filters = $input->validate([
            'product_id' => 'sometimes|integer|exists:validation_products,id',
            'cohort_member_id' => 'sometimes|integer|exists:cohort_members,id',
            'date_from' => 'sometimes|date_format:Y-m-d',
            'date_to' => 'sometimes|date_format:Y-m-d|after_or_equal:date_from',
        ]);
        $query = $this->dailyTestSessionQuery($input);
        foreach (['validation_product_id' => 'product_id', 'cohort_member_id' => 'cohort_member_id'] as $column => $filter) if (isset($filters[$filter])) $query->where($column, $filters[$filter]);
        if (isset($filters['date_from'])) $query->whereDate('date', '>=', $filters['date_from']);
        if (isset($filters['date_to'])) $query->whereDate('date', '<=', $filters['date_to']);

        return $this->paginated($input, $query, fn (DailyTestSession $record) => [
            'id' => $record->id,
            'date' => $record->date?->toDateString(),
            'cohort_member_id' => $record->cohort_member_id,
            'practitioner_name' => $record->cohortMember?->user?->name,
            'cohort_name' => $record->cohortMember?->cohort?->name,
            'product_id' => $record->validation_product_id,
            'product_name' => $record->product?->name,
            'module_name' => $record->module?->name,
            'workflow_name' => $record->workflow?->name,
            'tasks_completed' => $record->tasks_completed,
            'issue_reports_count' => $record->issue_reports_count,
            'start_time' => $record->start_time,
            'end_time' => $record->end_time,
        ]);
    }

    public function weeklyReviewQuery(WorkflowInput $input): Builder
    {
        $this->admin($input);
        return WeeklyReview::query()->with(['cohort:id,name', 'author:id,name'])->orderByDesc('week_start');
    }

    public function weeklyReviews(WorkflowInput $input): WorkflowResult
    {
        $this->admin($input);
        $filters = $input->validate([
            'cohort_id' => 'sometimes|integer|exists:cohorts,id',
            'week_from' => 'sometimes|date_format:Y-m-d',
            'week_to' => 'sometimes|date_format:Y-m-d|after_or_equal:week_from',
        ]);
        $query = $this->weeklyReviewQuery($input);
        if (isset($filters['cohort_id'])) $query->where('cohort_id', $filters['cohort_id']);
        if (isset($filters['week_from'])) $query->whereDate('week_start', '>=', $filters['week_from']);
        if (isset($filters['week_to'])) $query->whereDate('week_start', '<=', $filters['week_to']);

        return $this->paginated($input, $query, fn (WeeklyReview $record) => [
            'id' => $record->id,
            'cohort_id' => $record->cohort_id,
            'cohort_name' => $record->cohort?->name,
            'week_start' => $record->week_start?->toDateString(),
            'week_end' => $record->week_end?->toDateString(),
            'author_name' => $record->author?->name,
            'generated_at' => $record->generated_at?->toIso8601String(),
            'summary' => $record->summary,
            'action_items' => $record->action_items,
            'metrics' => $record->metrics,
        ]);
    }
}

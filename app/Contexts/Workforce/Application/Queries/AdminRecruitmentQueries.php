<?php

namespace App\Contexts\Workforce\Application\Queries;

use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Models\JobApplication;
use App\Models\JobOpening;
use Illuminate\Database\Eloquent\Builder;

final class AdminRecruitmentQueries extends WorkflowHandler
{
    private function admin(WorkflowInput $input): void
    {
        $user = $input->user();
        abort_unless($user?->is_active && $user->hasAnyRole(['super_admin', 'admin']), 403);
    }

    private function openingItem(JobOpening $opening): array
    {
        return $opening->only(['id', 'title', 'department_id', 'type', 'location', 'status', 'description', 'requirements',
            'openings_count', 'posted_at', 'closes_at', 'salary_range', 'created_by', 'created_at', 'updated_at']);
    }

    private function applicationItem(JobApplication $application): array
    {
        return $application->only(['id', 'job_opening_id', 'applicant_name', 'email', 'phone', 'status', 'resume_path',
            'applied_at', 'interview_date', 'notes', 'created_at', 'updated_at']);
    }

    public function openingQuery(WorkflowInput $input): Builder
    {
        $this->admin($input);

        return JobOpening::query()->with('department')->withCount('applications')->orderByDesc('created_at');
    }

    public function applicationQuery(WorkflowInput $input): Builder
    {
        $this->admin($input);

        return JobApplication::query()->with('jobOpening')->orderByDesc('applied_at')->orderByDesc('created_at');
    }

    public function openings(WorkflowInput $input): WorkflowResult
    {
        $this->admin($input);
        $filters = $input->validate(['status' => 'sometimes|in:'.implode(',', array_keys(JobOpening::statusOptions())),
            'type' => 'sometimes|in:'.implode(',', array_keys(JobOpening::typeOptions())), 'search' => 'sometimes|nullable|string|max:150']);
        $query = $this->openingQuery($input);
        if (isset($filters['status'])) $query->where('status', $filters['status']);
        if (isset($filters['type'])) $query->where('type', $filters['type']);
        if (!empty($filters['search'])) { $search = trim($filters['search']); $query->where(fn ($q) => $q->where('title', 'like', '%'.$search.'%')->orWhere('location', 'like', '%'.$search.'%')); }
        $page = $input->paginate($query);
        return WorkflowResult::payload(['items' => $page->getCollection()->map(fn (JobOpening $opening) => $this->openingItem($opening))->all(),
            'pagination' => ['current_page' => $page->currentPage(), 'per_page' => $page->perPage(), 'total' => $page->total(), 'last_page' => $page->lastPage()]]);
    }

    public function opening(WorkflowInput $input, JobOpening $opening): WorkflowResult
    {
        $this->admin($input);
        return WorkflowResult::payload(['job_opening' => $this->openingItem($opening)]);
    }

    public function applications(WorkflowInput $input): WorkflowResult
    {
        $this->admin($input);
        $filters = $input->validate(['status' => 'sometimes|in:'.implode(',', array_keys(JobApplication::statusOptions())),
            'job_opening_id' => 'sometimes|integer|exists:job_openings,id', 'search' => 'sometimes|nullable|string|max:150']);
        $query = $this->applicationQuery($input);
        if (isset($filters['status'])) $query->where('status', $filters['status']);
        if (isset($filters['job_opening_id'])) $query->where('job_opening_id', $filters['job_opening_id']);
        if (!empty($filters['search'])) { $search = trim($filters['search']); $query->where(fn ($q) => $q->where('applicant_name', 'like', '%'.$search.'%')->orWhere('email', 'like', '%'.$search.'%')); }
        $page = $input->paginate($query);
        return WorkflowResult::payload(['items' => $page->getCollection()->map(fn (JobApplication $application) => $this->applicationItem($application))->all(),
            'pagination' => ['current_page' => $page->currentPage(), 'per_page' => $page->perPage(), 'total' => $page->total(), 'last_page' => $page->lastPage()]]);
    }

    public function application(WorkflowInput $input, JobApplication $application): WorkflowResult
    {
        $this->admin($input);
        return WorkflowResult::payload(['application' => $this->applicationItem($application)]);
    }
}

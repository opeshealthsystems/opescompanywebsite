<?php

namespace App\Contexts\Workforce\Application\Queries;

use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Models\PerformanceReview;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

final class AdminPerformanceQueries extends WorkflowHandler
{
    private function admin(WorkflowInput $input): void
    {
        $actor = $input->user();
        abort_unless($actor?->is_active && $actor->hasAnyRole(['super_admin', 'admin']), 403);
    }

    private function item(PerformanceReview $review): array
    {
        return $review->only(['id', 'user_id', 'reviewer_id', 'review_period', 'review_date', 'overall_rating', 'goals_rating', 'teamwork_rating', 'technical_rating', 'communication_rating', 'strengths', 'areas_for_improvement', 'goals_for_next_period', 'employee_comments', 'status', 'acknowledged_at', 'created_at', 'updated_at'])
            + ['employee' => $review->employee?->only(['id', 'name', 'email']), 'reviewer' => $review->reviewer?->only(['id', 'name'])];
    }

    public function query(WorkflowInput $input): Builder
    {
        $this->admin($input);

        return PerformanceReview::query()->with(['employee', 'reviewer'])->latest('review_date');
    }

    public function index(WorkflowInput $input): WorkflowResult
    {
        $this->admin($input);
        $filters = $input->validate(['status' => 'sometimes|in:draft,submitted,acknowledged', 'user_id' => 'sometimes|integer|exists:users,id', 'search' => 'sometimes|nullable|string|max:100']);
        $query = $this->query($input);
        if (isset($filters['status'])) $query->where('status', $filters['status']);
        if (isset($filters['user_id'])) $query->where('user_id', $filters['user_id']);
        if (!empty($filters['search'])) $query->where('review_period', 'like', '%'.trim($filters['search']).'%');
        $page = $input->paginate($query);
        return WorkflowResult::payload(['items' => $page->getCollection()->map(fn (PerformanceReview $review) => $this->item($review))->all(),
            'pagination' => ['current_page' => $page->currentPage(), 'per_page' => $page->perPage(), 'total' => $page->total(), 'last_page' => $page->lastPage()]]);
    }

    public function show(WorkflowInput $input, PerformanceReview $review): WorkflowResult
    {
        $this->admin($input);
        $review->load(['employee', 'reviewer']);
        return WorkflowResult::payload(['performance_review' => $this->item($review)]);
    }
}

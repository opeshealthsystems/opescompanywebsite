<?php

namespace App\Contexts\Workforce\Application\Queries;

use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Models\Risk;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

final class AdminRiskQueries extends WorkflowHandler
{
    private function admin(WorkflowInput $input): void
    {
        $actor = $input->user();
        abort_unless($actor instanceof User && $actor->is_active && $actor->hasAnyRole(['super_admin', 'admin']), 403);
    }

    private function item(Risk $risk): array
    {
        return $risk->only(['id', 'reference', 'title', 'description', 'category', 'owner_id', 'likelihood', 'impact', 'risk_score', 'status', 'review_date', 'mitigation_plan', 'created_at', 'updated_at'])
            + ['owner' => $risk->owner?->only(['id', 'name'])];
    }

    public function query(WorkflowInput $input): Builder
    {
        $this->admin($input);

        return Risk::query()->with('owner:id,name')->orderByDesc('risk_score')->orderByDesc('created_at');
    }

    public function index(WorkflowInput $input): WorkflowResult
    {
        $this->admin($input);
        $filters = $input->validate([
            'status' => 'sometimes|in:'.implode(',', array_keys(Risk::statusOptions())),
            'category' => 'sometimes|in:'.implode(',', array_keys(Risk::categoryOptions())),
            'search' => 'sometimes|nullable|string|max:150',
        ]);
        $query = $this->query($input);
        if (isset($filters['status'])) $query->where('status', $filters['status']);
        if (isset($filters['category'])) $query->where('category', $filters['category']);
        if (!empty($filters['search'])) {
            $search = trim($filters['search']);
            $query->where(fn ($builder) => $builder->where('title', 'like', '%'.$search.'%')->orWhere('reference', 'like', '%'.$search.'%'));
        }
        $page = $input->paginate($query);

        return WorkflowResult::payload([
            'items' => $page->getCollection()->map(fn (Risk $risk) => $this->item($risk))->all(),
            'pagination' => ['current_page' => $page->currentPage(), 'per_page' => $page->perPage(), 'total' => $page->total(), 'last_page' => $page->lastPage()],
        ]);
    }

    public function show(WorkflowInput $input, Risk $risk): WorkflowResult
    {
        $this->admin($input);
        return WorkflowResult::payload(['risk' => $this->item($risk->load('owner:id,name'))]);
    }
}

<?php

namespace App\Contexts\Operations\Application\Queries;

use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Models\Deal;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

final class AdminDealQueries extends WorkflowHandler
{
    private function admin(WorkflowInput $input): void
    {
        $actor = $input->user();
        abort_unless($actor instanceof User && $actor->is_active && $actor->hasAnyRole(['super_admin', 'admin']), 403);
    }

    private function item(Deal $deal): array
    {
        return $deal->only([
            'id', 'reference', 'title', 'lead_id', 'stage', 'value', 'currency', 'probability',
            'expected_close_date', 'actual_close_date', 'owner_id', 'notes', 'lost_reason', 'created_at', 'updated_at',
        ]) + [
            'owner' => $deal->owner?->only(['id', 'name']),
            'lead' => $deal->lead?->only(['id', 'name', 'email']),
        ];
    }

    public function dealQuery(WorkflowInput $input): Builder
    {
        $this->admin($input);

        return Deal::query()->with(['owner:id,name', 'lead:id,name,email'])->orderByDesc('created_at');
    }

    public function index(WorkflowInput $input): WorkflowResult
    {
        $this->admin($input);
        $filters = $input->validate([
            'stage' => 'sometimes|in:'.implode(',', array_keys(Deal::stageOptions())),
            'owner_id' => 'sometimes|integer|exists:users,id',
            'search' => 'sometimes|nullable|string|max:150',
        ]);
        $query = $this->dealQuery($input);
        if (isset($filters['stage'])) $query->where('stage', $filters['stage']);
        if (isset($filters['owner_id'])) $query->where('owner_id', $filters['owner_id']);
        if (!empty($filters['search'])) {
            $search = trim($filters['search']);
            $query->where(fn ($builder) => $builder->where('title', 'like', '%'.$search.'%')
                ->orWhere('reference', 'like', '%'.$search.'%'));
        }
        $page = $input->paginate($query);

        return WorkflowResult::payload([
            'items' => $page->getCollection()->map(fn (Deal $deal) => $this->item($deal))->all(),
            'pagination' => ['current_page' => $page->currentPage(), 'per_page' => $page->perPage(), 'total' => $page->total(), 'last_page' => $page->lastPage()],
        ]);
    }

    public function show(WorkflowInput $input, Deal $deal): WorkflowResult
    {
        $this->admin($input);
        return WorkflowResult::payload(['deal' => $this->item($deal->load(['owner:id,name', 'lead:id,name,email']))]);
    }

    public function pipeline(WorkflowInput $input): WorkflowResult
    {
        $this->admin($input);
        $deals = $this->dealQuery($input)->whereNotIn('stage', ['closed_won', 'closed_lost'])
            ->orderByDesc('value')->limit(500)->get();
        $stats = Deal::query()->selectRaw('stage, COUNT(*) as count, SUM(value) as total_value, AVG(probability) as avg_probability')
            ->whereNotIn('stage', ['closed_won', 'closed_lost'])->groupBy('stage')->get()->keyBy('stage')->toArray();

        return WorkflowResult::payload([
            'stages' => Deal::stageOptions(),
            'deals_by_stage' => $deals->groupBy('stage')->map(fn ($items) => $items->map(fn (Deal $deal) => $this->item($deal))->values()->all())->all(),
            'stage_stats' => $stats,
            'truncated' => Deal::query()->whereNotIn('stage', ['closed_won', 'closed_lost'])->count() > $deals->count(),
        ]);
    }
}

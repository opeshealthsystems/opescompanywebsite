<?php

namespace App\Contexts\Programmes\Application\Queries;

use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Models\Suggestion;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

final class AdminSuggestionQueries extends WorkflowHandler
{
    private function admin(WorkflowInput $input): void
    {
        $actor = $input->user();
        abort_unless($actor instanceof User && $actor->is_active && $actor->hasAnyRole(['super_admin', 'admin']), 403);
    }

    public function suggestionQuery(WorkflowInput $input): Builder
    {
        $this->admin($input);
        return Suggestion::query()->with('user:id,name')->orderByDesc('created_at');
    }

    public function index(WorkflowInput $input): WorkflowResult
    {
        $this->admin($input);
        $filters = $input->validate([
            'status' => 'sometimes|in:pending,under_review,accepted,declined,implemented',
            'category' => 'sometimes|in:feature_request,improvement,process,bug,other',
            'search' => 'sometimes|string|max:120',
        ]);
        $query = $this->suggestionQuery($input);
        foreach (['status', 'category'] as $field) if (isset($filters[$field])) $query->where($field, $filters[$field]);
        if (isset($filters['search'])) $query->where(fn (Builder $builder) => $builder->where('title', 'like', '%'.$filters['search'].'%')->orWhereHas('user', fn (Builder $user) => $user->where('name', 'like', '%'.$filters['search'].'%')));
        $page = $input->paginate($query);

        return WorkflowResult::payload([
            'items' => $page->getCollection()->map(fn (Suggestion $suggestion) => [
                'id' => $suggestion->id,
                'user_id' => $suggestion->user_id,
                'submitted_by' => $suggestion->user?->name,
                'title' => $suggestion->title,
                'category' => $suggestion->category,
                'status' => $suggestion->status,
                'created_at' => $suggestion->created_at?->toIso8601String(),
            ])->values()->all(),
            'pagination' => ['current_page' => $page->currentPage(), 'per_page' => $page->perPage(), 'total' => $page->total(), 'last_page' => $page->lastPage()],
        ]);
    }
}

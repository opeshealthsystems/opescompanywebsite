<?php

namespace App\Contexts\Testing\Application\Queries;

use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Models\TesterAssignment;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

final class AdminTesterAssignmentQueries extends WorkflowHandler
{
    private function authorized(WorkflowInput $input): void
    {
        $actor = $input->user();
        abort_unless($actor instanceof User && $actor->is_active && $actor->hasPermissionTo('assign_testers'), 403);
    }

    public function assignmentQuery(WorkflowInput $input): Builder
    {
        $this->authorized($input);
        return TesterAssignment::query()->with(['tester:id,name', 'assigner:id,name'])->orderByDesc('created_at');
    }

    public function index(WorkflowInput $input): WorkflowResult
    {
        $this->authorized($input);
        $filters = $input->validate([
            'status' => 'sometimes|in:pending,in_progress,completed,cancelled',
            'assigned_to' => 'sometimes|integer|exists:users,id',
            'product_slug' => 'sometimes|string|max:120',
            'search' => 'sometimes|string|max:120',
        ]);
        $query = $this->assignmentQuery($input);
        foreach (['status', 'assigned_to', 'product_slug'] as $field) if (isset($filters[$field])) $query->where($field, $filters[$field]);
        if (isset($filters['search'])) $query->where(fn (Builder $builder) => $builder->where('title', 'like', '%'.$filters['search'].'%')->orWhere('product_name', 'like', '%'.$filters['search'].'%'));
        $page = $input->paginate($query);

        return WorkflowResult::payload([
            'items' => $page->getCollection()->map(fn (TesterAssignment $assignment) => [
                'id' => $assignment->id,
                'assigned_to' => $assignment->assigned_to,
                'tester_name' => $assignment->tester?->name,
                'product_slug' => $assignment->product_slug,
                'product_name' => $assignment->product_name,
                'title' => $assignment->title,
                'status' => $assignment->status,
                'due_date' => $assignment->due_date?->toDateString(),
                'assigned_by' => $assignment->assigned_by,
                'assigned_by_name' => $assignment->assigner?->name,
                'created_at' => $assignment->created_at?->toIso8601String(),
            ])->values()->all(),
            'pagination' => ['current_page' => $page->currentPage(), 'per_page' => $page->perPage(), 'total' => $page->total(), 'last_page' => $page->lastPage()],
        ]);
    }
}

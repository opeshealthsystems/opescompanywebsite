<?php

namespace App\Contexts\Operations\Application\Queries;

use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Models\Asset;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

final class AdminAssetQueries extends WorkflowHandler
{
    private function admin(WorkflowInput $input): User
    {
        $actor = $input->user();
        abort_unless($actor instanceof User && $actor->is_active && $actor->hasAnyRole(['super_admin', 'admin']), 403);
        return $actor;
    }

    public function query(WorkflowInput $input): Builder
    {
        $this->admin($input);
        return Asset::query()->with('assignee')->orderByDesc('created_at');
    }

    public function index(WorkflowInput $input): WorkflowResult
    {
        $this->admin($input);
        $filters = $input->validate([
            'category' => 'sometimes|in:'.implode(',', array_keys(Asset::categoryOptions())),
            'status' => 'sometimes|in:'.implode(',', array_keys(Asset::statusOptions())),
            'assigned_to' => 'sometimes|integer|exists:users,id',
        ]);
        $query = $this->query($input);
        foreach (['category', 'status', 'assigned_to'] as $field) if (isset($filters[$field])) $query->where($field, $filters[$field]);
        $page = $input->paginate($query);
        return WorkflowResult::payload(['items' => $page->getCollection(), 'pagination' => [
            'current_page' => $page->currentPage(), 'per_page' => $page->perPage(), 'total' => $page->total(), 'last_page' => $page->lastPage(),
        ]]);
    }
}

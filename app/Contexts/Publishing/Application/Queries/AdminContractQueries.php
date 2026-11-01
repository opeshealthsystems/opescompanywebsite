<?php

namespace App\Contexts\Publishing\Application\Queries;

use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Models\Contract;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

final class AdminContractQueries extends WorkflowHandler
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
        return Contract::query()->with('lead')->orderByDesc('created_at');
    }

    public function index(WorkflowInput $input): WorkflowResult
    {
        $this->admin($input);
        $filters = $input->validate([
            'status' => 'sometimes|in:'.implode(',', array_keys(Contract::statusOptions())),
            'type' => 'sometimes|in:'.implode(',', array_keys(Contract::typeOptions())),
            'auto_renew' => 'sometimes|boolean',
        ]);
        $query = $this->query($input);
        foreach (['status', 'type', 'auto_renew'] as $field) if (isset($filters[$field])) $query->where($field, $filters[$field]);
        $page = $input->paginate($query);
        return WorkflowResult::payload(['items' => $page->getCollection(), 'pagination' => [
            'current_page' => $page->currentPage(), 'per_page' => $page->perPage(), 'total' => $page->total(), 'last_page' => $page->lastPage(),
        ]]);
    }

    public function expiringSoon(WorkflowInput $input): WorkflowResult
    {
        $this->admin($input);
        return WorkflowResult::payload(['count' => Contract::query()->where('status', 'active')->whereNotNull('end_date')
            ->whereDate('end_date', '<=', now()->addDays(30))->count()]);
    }
}

<?php

namespace App\Contexts\Enquiries\Application\Queries;

use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

final class AdminLeadQueries extends WorkflowHandler
{
    private function admin(WorkflowInput $input): void
    {
        $actor = $input->user();
        abort_unless($actor instanceof User && $actor->is_active && $actor->hasAnyRole(['super_admin', 'admin']), 403);
    }

    private function item(Lead $lead): array
    {
        return $lead->only(['id', 'name', 'email', 'phone', 'facility_type', 'products', 'message', 'source', 'product_slug', 'locale', 'status', 'created_at', 'updated_at']);
    }

    public function leadQuery(WorkflowInput $input): Builder
    {
        $this->admin($input);

        return Lead::query()->withCount(['deals', 'quotes'])->orderByDesc('created_at');
    }

    public function leads(WorkflowInput $input): WorkflowResult
    {
        $this->admin($input);
        $filters = $input->validate([
            'status' => 'sometimes|in:new,contacted,qualified,closed',
            'facility_type' => 'sometimes|in:clinic,hospital,laboratory,pharmacy,government,ngo,other',
            'search' => 'sometimes|nullable|string|max:150',
        ]);
        $query = $this->leadQuery($input);
        if (isset($filters['status'])) $query->where('status', $filters['status']);
        if (isset($filters['facility_type'])) $query->where('facility_type', $filters['facility_type']);
        if (!empty($filters['search'])) {
            $search = trim($filters['search']);
            $query->where(fn ($builder) => $builder->where('name', 'like', '%'.$search.'%')->orWhere('email', 'like', '%'.$search.'%'));
        }
        $page = $input->paginate($query);
        return WorkflowResult::payload(['items' => $page->getCollection()->map(fn (Lead $lead) => $this->item($lead) + ['deal_count' => $lead->deals_count, 'quote_count' => $lead->quotes_count])->all(),
            'pagination' => ['current_page' => $page->currentPage(), 'per_page' => $page->perPage(), 'total' => $page->total(), 'last_page' => $page->lastPage()]]);
    }

    public function lead(WorkflowInput $input, Lead $lead): WorkflowResult
    {
        $this->admin($input);
        return WorkflowResult::payload(['lead' => $this->item($lead) + ['deal_count' => $lead->deals()->count(), 'quote_count' => $lead->quotes()->count()]]);
    }
}

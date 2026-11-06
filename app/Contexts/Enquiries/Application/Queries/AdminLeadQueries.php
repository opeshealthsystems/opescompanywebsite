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

    private function integrationRequestItem(Lead $lead): array
    {
        $application = is_array($lead->integration_application) ? $lead->integration_application : [];
        return [
            'id' => $lead->id,
            'status' => $lead->status,
            'applicant' => ['name' => $lead->name, 'email' => $lead->email, 'phone' => $lead->phone],
            'application' => [
                'name' => $application['application_name'] ?? null,
                'access_type' => $application['access_type'] ?? null,
                'redirect_uri' => $application['redirect_uri'] ?? null,
                'requested_scopes' => $application['requested_scopes'] ?? [],
                'use_case' => $application['use_case'] ?? null,
            ],
            'received_at' => $lead->created_at,
        ];
    }

    public function integrationRequestQuery(WorkflowInput $input): Builder
    {
        $this->admin($input);
        return Lead::query()->where('source', 'integration-request')->orderByDesc('created_at');
    }

    public function integrationRequests(WorkflowInput $input): WorkflowResult
    {
        $this->admin($input);
        $filters = $input->validate([
            'status' => 'sometimes|in:new,contacted,qualified,closed',
            'search' => 'sometimes|nullable|string|max:150',
        ]);
        $query = $this->integrationRequestQuery($input);
        if (isset($filters['status'])) $query->where('status', $filters['status']);
        if (!empty($filters['search'])) {
            $search = trim($filters['search']);
            $query->where(fn (Builder $builder) => $builder
                ->where('name', 'like', '%'.$search.'%')
                ->orWhere('email', 'like', '%'.$search.'%')
                ->orWhere('integration_application->application_name', 'like', '%'.$search.'%'));
        }
        $page = $input->paginate($query);
        return WorkflowResult::payload([
            'items' => $page->getCollection()->map(fn (Lead $lead) => $this->integrationRequestItem($lead))->all(),
            'pagination' => ['current_page' => $page->currentPage(), 'per_page' => $page->perPage(), 'total' => $page->total(), 'last_page' => $page->lastPage()],
        ]);
    }

    public function integrationRequest(WorkflowInput $input, Lead $lead): WorkflowResult
    {
        $this->admin($input);
        abort_unless($lead->source === 'integration-request' && is_array($lead->integration_application), 404);
        return WorkflowResult::payload(['integration_request' => $this->integrationRequestItem($lead)]);
    }
}

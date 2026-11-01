<?php

namespace App\Contexts\Enquiries\Application\Queries;

use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Models\PartnerApplication;
use App\Models\TesterApplication;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

final class AdminExternalApplicationQueries extends WorkflowHandler
{
    private function admin(WorkflowInput $input): void
    {
        $actor = $input->user();
        abort_unless($actor?->is_active && $actor->hasAnyRole(['super_admin', 'admin']), 403);
    }

    public function partnerQuery(WorkflowInput $input): Builder
    {
        $this->admin($input);

        return PartnerApplication::query()->orderByDesc('id');
    }

    public function testerQuery(WorkflowInput $input): Builder
    {
        $this->admin($input);

        return TesterApplication::query()->orderByDesc('id');
    }

    public function partners(WorkflowInput $input): WorkflowResult
    {
        $this->admin($input);
        $filters = $input->validate(['status' => 'sometimes|in:pending,reviewing,approved,rejected', 'partner_type' => 'sometimes|string|max:30', 'search' => 'sometimes|nullable|string|max:150']);
        $query = $this->partnerQuery($input);
        if (isset($filters['status'])) $query->where('status', $filters['status']);
        if (isset($filters['partner_type'])) $query->where('partner_type', $filters['partner_type']);
        if (!empty($filters['search'])) $query->where(fn ($q) => $q->where('organization_name', 'like', '%'.trim($filters['search']).'%')->orWhere('contact_name', 'like', '%'.trim($filters['search']).'%')->orWhere('email', 'like', '%'.trim($filters['search']).'%'));
        return $this->paginated($input, $query);
    }

    public function partner(WorkflowInput $input, PartnerApplication $partnerApplication): WorkflowResult
    {
        $this->admin($input);
        return WorkflowResult::payload(['application' => $partnerApplication->only(['id', 'organization_name', 'contact_name', 'email', 'phone', 'country', 'city', 'partner_type', 'organization_type', 'annual_revenue_range', 'target_market', 'description', 'website', 'locale', 'status', 'admin_notes', 'created_at', 'updated_at'])]);
    }

    public function testers(WorkflowInput $input): WorkflowResult
    {
        $this->admin($input);
        $filters = $input->validate(['status' => 'sometimes|in:pending,accepted,rejected,active', 'profession' => 'sometimes|string|max:30', 'search' => 'sometimes|nullable|string|max:150']);
        $query = $this->testerQuery($input);
        if (isset($filters['status'])) $query->where('status', $filters['status']);
        if (isset($filters['profession'])) $query->where('profession', $filters['profession']);
        if (!empty($filters['search'])) $query->where(fn ($q) => $q->where('name', 'like', '%'.trim($filters['search']).'%')->orWhere('email', 'like', '%'.trim($filters['search']).'%'));
        return $this->paginated($input, $query);
    }

    public function tester(WorkflowInput $input, TesterApplication $testerApplication): WorkflowResult
    {
        $this->admin($input);
        return WorkflowResult::payload(['application' => $testerApplication->only(['id', 'name', 'email', 'phone', 'profession', 'specialty', 'institution_name', 'country', 'city', 'years_experience', 'devices', 'platforms', 'motivation', 'tech_experience', 'locale', 'status', 'admin_notes', 'created_at', 'updated_at'])]);
    }

    private function paginated(WorkflowInput $input, mixed $query): WorkflowResult
    {
        $page = $input->paginate($query);
        return WorkflowResult::payload(['items' => $page->items(), 'pagination' => ['current_page' => $page->currentPage(), 'per_page' => $page->perPage(), 'total' => $page->total(), 'last_page' => $page->lastPage()]]);
    }
}

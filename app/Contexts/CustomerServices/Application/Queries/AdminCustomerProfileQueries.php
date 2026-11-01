<?php

namespace App\Contexts\CustomerServices\Application\Queries;

use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Models\CustomerProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

final class AdminCustomerProfileQueries extends WorkflowHandler
{
    private function admin(WorkflowInput $input): void
    {
        $actor = $input->user();
        abort_unless($actor?->is_active && $actor->hasAnyRole(['super_admin', 'admin']), 403);
    }

    private function item(CustomerProfile $profile): array
    {
        return $profile->only(['id', 'user_id', 'facility_name', 'facility_type', 'country', 'city', 'address', 'created_at', 'updated_at']);
    }

    public function query(WorkflowInput $input): Builder
    {
        $this->admin($input);

        return CustomerProfile::query()->with('user:id,name,email')->orderBy('facility_name');
    }

    public function index(WorkflowInput $input): WorkflowResult
    {
        $this->admin($input);
        $filters = $input->validate(['search' => 'sometimes|nullable|string|max:150', 'country' => 'sometimes|nullable|string|max:80']);
        $query = $this->query($input);
        if (!empty($filters['country'])) $query->where('country', $filters['country']);
        if (!empty($filters['search'])) {
            $term = trim($filters['search']);
            $query->where(fn ($q) => $q->where('facility_name', 'like', '%'.$term.'%')->orWhereHas('user', fn ($user) => $user->where('name', 'like', '%'.$term.'%')->orWhere('email', 'like', '%'.$term.'%')));
        }
        $page = $input->paginate($query);
        return WorkflowResult::payload(['items' => $page->getCollection()->map(fn (CustomerProfile $profile) => $this->item($profile) + ['customer' => $profile->user?->only(['id', 'name', 'email'])])->all(),
            'pagination' => ['current_page' => $page->currentPage(), 'per_page' => $page->perPage(), 'total' => $page->total(), 'last_page' => $page->lastPage()]]);
    }

    public function show(WorkflowInput $input, CustomerProfile $customerProfile): WorkflowResult
    {
        $this->admin($input);
        $customerProfile->load('user:id,name,email,phone');
        return WorkflowResult::payload(['customer_profile' => $this->item($customerProfile) + ['customer' => $customerProfile->user?->only(['id', 'name', 'email', 'phone'])]]);
    }
}

<?php

namespace App\Contexts\Enquiries\Application\Queries;

use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Models\PartnerApplication;
use App\Models\PartnerApplicationMembership;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

final class PartnerAccountQueries extends WorkflowHandler
{
    public function accounts(WorkflowInput $input): WorkflowResult
    {
        $user = $input->user();
        abort_unless($user?->is_active && $user->hasRole('partner'), 403);

        $accounts = PartnerApplicationMembership::query()
            ->join('partner_applications as applications', 'applications.id', '=', 'partner_application_memberships.partner_application_id')
            ->where('partner_application_memberships.user_id', $user->id)
            ->where('applications.status', 'approved')
            ->orderBy('applications.organization_name')
            ->get([
                'applications.id', 'applications.organization_name', 'applications.partner_type',
                'applications.organization_type', 'applications.country', 'applications.city',
                'applications.website', 'partner_application_memberships.created_at as member_since',
                'partner_application_memberships.membership_role',
            ])
            ->map(fn ($account) => [
                'application_id' => (int) $account->id,
                'organization_name' => $account->organization_name,
                'partner_type' => $account->partner_type,
                'organization_type' => $account->organization_type,
                'country' => $account->country,
                'city' => $account->city,
                'website' => $account->website,
                'membership_role' => $account->membership_role,
                'member_since' => $account->member_since ? \Carbon\CarbonImmutable::parse($account->member_since)->toISOString() : null,
            ])->values()->all();

        return WorkflowResult::payload(['accounts' => $accounts]);
    }

    public function members(WorkflowInput $input, PartnerApplication $partnerApplication): WorkflowResult
    {
        $query = $this->memberQuery($input, $partnerApplication);
        $page = $input->paginate($query);

        return WorkflowResult::payload([
            'items' => $page->getCollection()->map(fn ($membership) => [
                'id' => (int) $membership->id,
                'user_id' => (int) $membership->user_id,
                'name' => $membership->user->name,
                'email' => $membership->user->email,
                'member_since' => $membership->created_at?->toISOString(),
                'membership_role' => $membership->membership_role,
            ])->values()->all(),
            'pagination' => ['current_page' => $page->currentPage(), 'per_page' => $page->perPage(), 'total' => $page->total(), 'last_page' => $page->lastPage()],
        ]);
    }

    public function memberQuery(WorkflowInput $input, PartnerApplication $partnerApplication): Builder
    {
        $actor = $input->user();
        abort_unless($actor?->is_active, 403);
        if (! $actor->hasAnyRole(['super_admin', 'admin'])) {
            abort_unless($actor->hasRole('partner') && $partnerApplication->status === 'approved', 403);
            abort_unless(PartnerApplicationMembership::query()
                ->where('partner_application_id', $partnerApplication->id)
                ->where('user_id', $actor->id)
                ->where('membership_role', 'owner')
                ->exists(), 403);
        }
        $query = PartnerApplicationMembership::query()->where('partner_application_id', $partnerApplication->id)
            ->whereHas('user', fn ($users) => $users->where('is_active', true)->whereNotNull('email_verified_at')->role('partner'))
            ->with('user:id,name,email')->orderBy('id');
        return $partnerApplication->status === 'approved' ? $query : $query->whereRaw('1 = 0');
    }

    public function eligibleMemberQuery(WorkflowInput $input): Builder
    {
        $actor = $input->user();
        abort_unless($actor?->is_active && $actor->hasAnyRole(['super_admin', 'admin']), 403);
        return User::query()->where('is_active', true)->whereNotNull('email_verified_at')->role('partner')->orderBy('name');
    }

    public function eligibleMembers(WorkflowInput $input): WorkflowResult
    {
        $filters = $input->validate(['search' => 'sometimes|nullable|string|max:100']);
        $query = $this->eligibleMemberQuery($input);
        if (!empty($filters['search'])) {
            $search = trim($filters['search']);
            $query->where(fn ($users) => $users->where('name', 'like', '%'.$search.'%')->orWhere('email', 'like', '%'.$search.'%'));
        }
        $page = $input->paginate($query);
        return WorkflowResult::payload(['items' => $page->getCollection()->map(fn (User $user) => [
            'id' => (int) $user->id, 'name' => $user->name, 'email' => $user->email,
        ])->all(), 'pagination' => ['current_page' => $page->currentPage(), 'per_page' => $page->perPage(), 'total' => $page->total(), 'last_page' => $page->lastPage()]]);
    }

    private function admin(WorkflowInput $input): User
    {
        $actor = $input->user();
        abort_unless($actor?->is_active && $actor->hasAnyRole(['super_admin', 'admin']), 403);
        return $actor;
    }
}

<?php

namespace App\Contexts\Enquiries\Application\Commands;

use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Contexts\Shared\Infrastructure\DomainOutbox;
use App\Models\PartnerApplication;
use App\Models\PartnerApplicationMembership;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/** Partner-owned profile updates for explicitly linked, approved organizations. */
final class PartnerAccountCommands extends WorkflowHandler
{
    public function update(WorkflowInput $input, PartnerApplication $partnerApplication): WorkflowResult
    {
        $actor = $input->user();
        abort_unless($actor?->is_active && $actor->hasRole('partner'), 403);
        $values = $input->validate([
            'phone' => 'sometimes|nullable|string|max:40',
            'country' => 'sometimes|required|string|max:100',
            'city' => 'sometimes|nullable|string|max:100',
            'organization_type' => 'sometimes|nullable|string|max:120',
            'target_market' => 'sometimes|nullable|string|max:1000',
            'description' => 'sometimes|required|string|max:5000',
            'website' => 'sometimes|nullable|url|max:255',
        ]);
        if (!$values) {
            throw ValidationException::withMessages(['account' => ['Provide at least one editable organization field.']]);
        }

        $account = DB::transaction(function () use ($partnerApplication, $actor, $values) {
            $membership = PartnerApplicationMembership::query()->where('partner_application_id', $partnerApplication->id)
                ->where('user_id', $actor->id)->lockForUpdate()->firstOrFail();
            abort_unless($membership->membership_role === 'owner', 403, 'Only an organization owner can update its profile.');

            $account = PartnerApplication::query()->lockForUpdate()->findOrFail($partnerApplication->id);
            abort_unless($account->status === 'approved', 404);
            $account->fill($values);
            if (!$account->isDirty()) {
                return $account;
            }
            $changed = array_keys($account->getDirty());
            $account->save();
            DomainOutbox::append('event', 'enquiries.partner_account_profile_updated.v1', [
                'references' => [['resource' => 'PartnerApplication', 'id' => $account->id]],
                'changed_fields' => $changed,
            ], $actor->id);
            return $account;
        }, 3);

        return WorkflowResult::payload(['account' => [
            'application_id' => (int) $account->id,
            'organization_name' => $account->organization_name,
            'partner_type' => $account->partner_type,
            'organization_type' => $account->organization_type,
            'country' => $account->country,
            'city' => $account->city,
            'website' => $account->website,
            'target_market' => $account->target_market,
            'description' => $account->description,
            'phone' => $account->phone,
        ]]);
    }
}

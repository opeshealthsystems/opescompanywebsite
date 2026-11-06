<?php

namespace App\Contexts\Enquiries\Application\Commands;

use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Contexts\Shared\Infrastructure\DomainOutbox;
use App\Models\PartnerApplication;
use App\Models\PartnerApplicationMembership;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class PartnerAccountMembershipCommands extends WorkflowHandler
{
    public function addMember(WorkflowInput $input, PartnerApplication $partnerApplication): WorkflowResult
    {
        $actor = $this->adminOrOwner($input, $partnerApplication);
        $data = $input->validate([
            'user_id' => 'required_without:email|nullable|integer|min:1|exists:users,id',
            'email' => 'required_without:user_id|nullable|email:rfc|max:150',
            'membership_role' => 'required|in:owner,member',
        ]);

        $membership = DB::transaction(function () use ($partnerApplication, $data, $actor) {
            $application = PartnerApplication::query()->lockForUpdate()->findOrFail($partnerApplication->id);
            $this->assertOwnerWithLockedAccount($actor, $application);
            if ($application->status !== 'approved') {
                throw ValidationException::withMessages(['partner_application' => ['Only approved partner applications can have account members.']]);
            }

            $memberQuery = User::query()->lockForUpdate();
            $member = isset($data['email'])
                ? $memberQuery->where('email', $data['email'])->first()
                : $memberQuery->find($data['user_id']);
            if (! $member) {
                throw ValidationException::withMessages(['email' => ['No matching active, verified partner account was found.']]);
            }
            if (!$member->is_active || !$member->email_verified_at || !$member->hasRole('partner')) {
                throw ValidationException::withMessages([isset($data['email']) ? 'email' : 'user_id' => ['Only an existing active, verified OPES partner account can be added.']]);
            }

            $membership = PartnerApplicationMembership::query()->where([
                'partner_application_id' => $application->id,
                'user_id' => $member->id,
            ])->lockForUpdate()->first();
            if ($membership && $membership->membership_role === 'owner' && $data['membership_role'] !== 'owner') {
                $otherOwners = PartnerApplicationMembership::query()->where('partner_application_id', $application->id)
                    ->where('membership_role', 'owner')->where('id', '!=', $membership->id)->count();
                if ($otherOwners === 0) {
                    throw ValidationException::withMessages(['membership_role' => ['Assign another owner before changing the last owner to a member.']]);
                }
            }

            if (!$membership) {
                $membership = PartnerApplicationMembership::query()->create([
                    'partner_application_id' => $application->id, 'user_id' => $member->id,
                    'membership_role' => $data['membership_role'], 'added_by' => $actor->id,
                ]);
            } elseif ($membership->membership_role !== $data['membership_role']) {
                $membership->update(['membership_role' => $data['membership_role'], 'added_by' => $actor->id]);
            }

            if ($membership->wasRecentlyCreated) {
                DomainOutbox::append('event', 'enquiries.partner_account_member_added.v1', [
                    'references' => [
                        ['resource' => 'PartnerApplication', 'id' => $application->id],
                        ['resource' => 'User', 'id' => $member->id],
                    ],
                ], $actor->id);
            } elseif ($membership->wasChanged('membership_role')) {
                DomainOutbox::append('event', 'enquiries.partner_account_member_role_changed.v1', [
                    'references' => [
                        ['resource' => 'PartnerApplication', 'id' => $application->id],
                        ['resource' => 'User', 'id' => $member->id],
                    ], 'membership_role' => $membership->membership_role,
                ], $actor->id);
            }

            return $membership;
        }, 3);

        return WorkflowResult::payload(['membership_id' => (int) $membership->id, 'application_id' => (int) $membership->partner_application_id, 'user_id' => (int) $membership->user_id], 201);
    }

    public function removeMember(WorkflowInput $input, PartnerApplication $partnerApplication, User $member): WorkflowResult
    {
        $actor = $this->adminOrOwner($input, $partnerApplication);
        $removed = DB::transaction(function () use ($partnerApplication, $member, $actor) {
            $application = PartnerApplication::query()->lockForUpdate()->findOrFail($partnerApplication->id);
            $this->assertOwnerWithLockedAccount($actor, $application);
            $membership = PartnerApplicationMembership::query()->where('partner_application_id', $application->id)
                ->where('user_id', $member->id)->lockForUpdate()->first();
            if (!$membership) return false;

            if ($membership->membership_role === 'owner') {
                $otherOwners = PartnerApplicationMembership::query()->where('partner_application_id', $application->id)
                    ->where('membership_role', 'owner')->where('id', '!=', $membership->id)->count();
                if ($otherOwners === 0) {
                    throw ValidationException::withMessages(['member' => ['Assign another owner before removing the last organization owner.']]);
                }
            }

            $membership->delete();
            DomainOutbox::append('event', 'enquiries.partner_account_member_removed.v1', [
                'references' => [
                    ['resource' => 'PartnerApplication', 'id' => $application->id],
                    ['resource' => 'User', 'id' => $member->id],
                ],
            ], $actor->id);
            return true;
        }, 3);

        return $removed ? WorkflowResult::empty() : WorkflowResult::empty();
    }

    private function adminOrOwner(WorkflowInput $input, PartnerApplication $partnerApplication): User
    {
        $actor = $input->user();
        abort_unless($actor?->is_active, 403);
        if ($actor->hasAnyRole(['super_admin', 'admin'])) {
            return $actor;
        }

        abort_unless($actor->hasRole('partner') && $partnerApplication->status === 'approved', 403);
        $owner = PartnerApplicationMembership::query()
            ->where('partner_application_id', $partnerApplication->id)
            ->where('user_id', $actor->id)
            ->where('membership_role', 'owner')
            ->exists();
        abort_unless($owner, 403, 'Only a linked organization owner can manage its members.');

        return $actor;
    }

    private function assertOwnerWithLockedAccount(User $actor, PartnerApplication $application): void
    {
        if ($actor->hasAnyRole(['super_admin', 'admin'])) {
            return;
        }

        abort_unless($application->status === 'approved' && $actor->is_active && $actor->hasRole('partner'), 403);
        $owner = PartnerApplicationMembership::query()
            ->where('partner_application_id', $application->id)
            ->where('user_id', $actor->id)
            ->where('membership_role', 'owner')
            ->lockForUpdate()
            ->exists();
        abort_unless($owner, 403, 'Only a linked organization owner can manage its members.');
    }
}

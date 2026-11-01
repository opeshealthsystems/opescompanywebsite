<?php

namespace App\Contexts\Validation\Application\Commands;

use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Contexts\Shared\Infrastructure\DomainOutbox;
use App\Models\AdvisoryCouncilMember;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class AdminCouncilMemberCommands extends WorkflowHandler
{
    private function admin(WorkflowInput $input): User
    {
        $user = $input->user();
        abort_unless($user?->is_active && $user->hasAnyRole(['super_admin', 'admin']), 403);
        return $user;
    }

    public function update(WorkflowInput $input, AdvisoryCouncilMember $member): WorkflowResult
    {
        $actor = $this->admin($input);
        $data = $input->validate([
            'title' => 'sometimes|required|string|max:255',
            'term_start' => 'sometimes|required|date_format:Y-m-d',
            'term_end' => 'sometimes|nullable|date_format:Y-m-d',
            'status' => 'sometimes|required|in:active,inactive',
            'user_id' => 'prohibited', 'validation_certificate_id' => 'prohibited', 'invited_by' => 'prohibited', 'invited_at' => 'prohibited',
        ]);
        if (!$data) throw ValidationException::withMessages(['member' => 'Provide at least one council-member field to update.']);
        $updated = DB::transaction(function () use ($member, $data, $actor) {
            $record = AdvisoryCouncilMember::query()->lockForUpdate()->findOrFail($member->id);
            $start = $data['term_start'] ?? $record->term_start->toDateString();
            $end = $data['term_end'] ?? ($record->term_end?->toDateString());
            if ($end !== null && $end < $start) throw ValidationException::withMessages(['term_end' => 'The term end must be on or after the term start.']);
            $record->update($data);
            DomainOutbox::append('event', 'validation.council_member_updated.v1', [
                'references' => [['resource' => 'AdvisoryCouncilMember', 'id' => $record->id]],
                'changed_fields' => array_keys($data),
            ], $actor->id);
            return $record;
        }, 3);
        return WorkflowResult::payload(['id' => $updated->id, 'title' => $updated->title, 'term_start' => $updated->term_start?->toDateString(), 'term_end' => $updated->term_end?->toDateString(), 'status' => $updated->status]);
    }
}

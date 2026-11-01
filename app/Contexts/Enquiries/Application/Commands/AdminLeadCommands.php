<?php

namespace App\Contexts\Enquiries\Application\Commands;

use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Contexts\Shared\Infrastructure\DomainMail;
use App\Contexts\Shared\Infrastructure\DomainOutbox;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/** Reviewed admin follow-up messages for website enquiries. */
final class AdminLeadCommands extends WorkflowHandler
{
    public function sendFollowUp(WorkflowInput $input, Lead $lead): WorkflowResult
    {
        $actor = $input->user();
        abort_unless($actor instanceof User && $actor->is_active && $actor->hasAnyRole(['super_admin', 'admin']), 403);
        $data = $input->validate(['subject' => 'required|string|max:200', 'body' => 'required|string|max:10000']);

        $record = DB::transaction(function () use ($lead, $data, $actor) {
            $record = Lead::query()->lockForUpdate()->findOrFail($lead->id);
            if (!filter_var($record->email, FILTER_VALIDATE_EMAIL)) {
                throw ValidationException::withMessages(['lead' => 'This enquiry does not have a deliverable email address.']);
            }
            DomainMail::to($record->email)->queue(new \App\Mail\LeadFollowUp($data['subject'], $data['body'], $record->name));
            DomainOutbox::append('event', 'enquiries.lead_follow_up_queued.v1', [
                'references' => [['resource' => 'Lead', 'id' => $record->id]],
            ], $actor->id);
            return $record;
        }, 3);

        return WorkflowResult::payload(['id' => $record->id, 'status' => 'queued'])->with('success', 'Follow-up email queued.');
    }
}

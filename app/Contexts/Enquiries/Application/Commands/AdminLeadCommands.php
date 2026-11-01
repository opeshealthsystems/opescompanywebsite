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
    private function admin(WorkflowInput $input): User
    {
        $actor = $input->user();
        abort_unless($actor instanceof User && $actor->is_active && $actor->hasAnyRole(['super_admin', 'admin']), 403);
        return $actor;
    }

    public function create(WorkflowInput $input): WorkflowResult
    {
        $actor = $this->admin($input);
        $data = $input->validate([
            'name' => 'required|string|max:100', 'email' => 'required|email|max:150', 'phone' => 'nullable|string|max:30',
            'facility_type' => 'nullable|in:clinic,hospital,laboratory,pharmacy,government,ngo,other',
            'products' => 'nullable|string|max:255', 'message' => 'nullable|string|max:10000',
            'source' => 'nullable|in:contact,product-page,demo', 'product_slug' => 'nullable|string|max:255',
            'locale' => 'nullable|string|max:10', 'status' => 'sometimes|in:new,contacted,qualified,closed',
        ]);
        $record = DB::transaction(function () use ($data, $actor) {
            $lead = Lead::create($data + ['status' => 'new']);
            DomainOutbox::append('event', 'enquiries.lead_created.v1', ['references' => [['resource' => 'Lead', 'id' => $lead->id]]], $actor->id);
            return $lead;
        }, 3);
        return WorkflowResult::payload(['id' => $record->id, 'status' => $record->status], 201)->with('success', 'Enquiry created.');
    }

    public function update(WorkflowInput $input, Lead $lead): WorkflowResult
    {
        $actor = $this->admin($input);
        $data = $input->validate([
            'name' => 'required|string|max:100', 'email' => 'required|email|max:150', 'phone' => 'nullable|string|max:30',
            'facility_type' => 'nullable|in:clinic,hospital,laboratory,pharmacy,government,ngo,other',
            'products' => 'nullable|string|max:255', 'message' => 'nullable|string|max:10000',
            'source' => 'nullable|in:contact,product-page,demo', 'product_slug' => 'nullable|string|max:255',
            'locale' => 'nullable|string|max:10', 'status' => 'required|in:new,contacted,qualified,closed',
        ]);
        $record = DB::transaction(function () use ($lead, $data, $actor) {
            $record = Lead::query()->lockForUpdate()->findOrFail($lead->id);
            $record->update($data);
            DomainOutbox::append('event', 'enquiries.lead_updated.v1', ['references' => [['resource' => 'Lead', 'id' => $record->id]]], $actor->id);
            return $record;
        }, 3);
        return WorkflowResult::payload(['id' => $record->id, 'status' => $record->status])->with('success', 'Enquiry updated.');
    }

    public function delete(WorkflowInput $input, Lead $lead): WorkflowResult
    {
        $actor = $this->admin($input);
        DB::transaction(function () use ($lead, $actor) {
            $record = Lead::query()->lockForUpdate()->findOrFail($lead->id);
            if ($record->deals()->exists() || $record->quotes()->exists()) {
                throw ValidationException::withMessages(['lead' => 'Enquiries linked to a deal or quote must be retained.']);
            }
            $id = $record->id;
            $record->delete();
            DomainOutbox::append('event', 'enquiries.lead_deleted.v1', ['references' => [['resource' => 'Lead', 'id' => $id]]], $actor->id);
        }, 3);
        return WorkflowResult::empty();
    }

    public function sendFollowUp(WorkflowInput $input, Lead $lead): WorkflowResult
    {
        $actor = $this->admin($input);
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

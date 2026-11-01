<?php

namespace App\Contexts\Support\Application\Commands;

use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Contexts\Shared\Infrastructure\DomainMail;
use App\Contexts\Shared\Infrastructure\DomainOutbox;
use App\Models\PractitionerBugReport;
use App\Models\ServiceRequest;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/** Shared support lifecycle commands for Filament and the versioned API. */
final class AdminSupportCommands extends WorkflowHandler
{
    private function admin(WorkflowInput $input): User
    {
        $actor = $input->user();
        abort_unless($actor?->is_active && $actor->hasAnyRole(['super_admin', 'admin', 'support']), 403);
        return $actor;
    }

    private function invalid(string $field, string $message): never
    {
        throw ValidationException::withMessages([$field => $message]);
    }

    public function respondToBugReport(WorkflowInput $input, PractitionerBugReport $bugReport): WorkflowResult
    {
        $actor = $this->admin($input);
        $data = $input->validate(['status' => 'required|in:triaged,in_progress,resolved,closed,wont_fix', 'admin_response' => 'required|string|max:10000']);
        $record = DB::transaction(function () use ($bugReport, $actor, $data) {
            $record = PractitionerBugReport::query()->with('practitioner')->lockForUpdate()->findOrFail($bugReport->id);
            if (in_array($record->status, ['resolved', 'closed', 'wont_fix'], true)) $this->invalid('status', 'This bug report is already in a terminal state.');
            $record->update(['status' => $data['status'], 'admin_response' => $data['admin_response'], 'responded_by' => $actor->id, 'responded_at' => now()]);
            if ($record->practitioner?->email && class_exists(\App\Mail\BugReportResponded::class)) DomainMail::to($record->practitioner->email)->queue(new \App\Mail\BugReportResponded($record));
            $record->practitioner?->notify(new \App\Notifications\FeedEntry('practitioner.bug_report_responded', 'Response to your bug report', 'Your bug report received a response.', 'bug-ant', route('practitioner.bug-reports.show', ['locale' => 'en', 'bugReport' => $record->id])));
            DomainOutbox::append('event', 'support.practitioner_bug_report_responded.v1', ['references' => [['resource' => 'PractitionerBugReport', 'id' => $record->id]]], $actor->id);
            return $record;
        }, 3);
        return WorkflowResult::payload(['id' => $record->id, 'status' => $record->status, 'responded_at' => $record->responded_at?->toISOString()])->with('success', 'Bug report response saved.');
    }

    public function confirmServiceRequest(WorkflowInput $input, ServiceRequest $serviceRequest): WorkflowResult
    {
        $actor = $this->admin($input);
        $data = $input->validate(['assigned_technician_id' => 'required|integer|exists:users,id', 'confirmed_date' => 'required|date', 'confirmed_time' => 'nullable|string|max:10', 'admin_notes' => 'nullable|string|max:5000']);
        $record = DB::transaction(function () use ($serviceRequest, $actor, $data) {
            $record = ServiceRequest::query()->with('customer')->lockForUpdate()->findOrFail($serviceRequest->id);
            if (!in_array($record->status, ['pending', 'confirmed'], true)) $this->invalid('status', 'Only pending or confirmed service requests can be confirmed or reassigned.');
            $technician = User::query()->where('is_active', true)->whereHas('roles', fn ($query) => $query->whereIn('name', ['admin', 'support']))->find($data['assigned_technician_id']);
            if (!$technician) $this->invalid('assigned_technician_id', 'Select an active support technician.');
            $record->update(['status' => 'confirmed', 'assigned_technician_id' => $technician->id, 'confirmed_date' => $data['confirmed_date'], 'confirmed_time' => $data['confirmed_time'] ?? null, 'admin_notes' => $data['admin_notes'] ?? null]);
            if ($record->customer?->email && class_exists(\App\Mail\ServiceRequestConfirmed::class)) DomainMail::to($record->customer->email)->queue(new \App\Mail\ServiceRequestConfirmed($record));
            $record->customer?->notify(new \App\Notifications\FeedEntry('support.service_request', 'Service request confirmed', 'Your service request has been confirmed.', 'wrench', route('customer.service-requests.show', ['locale' => 'en', 'serviceRequest' => $record->id])));
            DomainOutbox::append('event', 'support.service_request_confirmed.v1', ['references' => [['resource' => 'ServiceRequest', 'id' => $record->id]]], $actor->id);
            return $record;
        }, 3);
        return WorkflowResult::payload(['id' => $record->id, 'status' => $record->status, 'assigned_technician_id' => $record->assigned_technician_id])->with('success', 'Service request confirmed.');
    }

    public function completeServiceRequest(WorkflowInput $input, ServiceRequest $serviceRequest): WorkflowResult
    {
        $actor = $this->admin($input);
        $record = DB::transaction(function () use ($serviceRequest, $actor) {
            $record = ServiceRequest::query()->lockForUpdate()->findOrFail($serviceRequest->id);
            if ($record->status === 'completed') return $record;
            if ($record->status !== 'assigned') $this->invalid('status', 'Only assigned service requests can be completed.');
            $record->update(['status' => 'completed']);
            DomainOutbox::append('event', 'support.service_request_completed.v1', ['references' => [['resource' => 'ServiceRequest', 'id' => $record->id]]], $actor->id);
            return $record;
        }, 3);
        return WorkflowResult::payload(['id' => $record->id, 'status' => $record->status])->with('success', 'Service request completed.');
    }

    public function replyToTicket(WorkflowInput $input, Ticket $ticket): WorkflowResult
    {
        $actor = $this->admin($input);
        $data = $input->validate(['body' => 'required|string|max:10000', 'is_internal' => 'sometimes|boolean']);
        $record = Ticket::query()->with('user')->lockForUpdate()->findOrFail($ticket->id);
        $reply = \App\Models\TicketReply::create([
            'ticket_id' => $record->id, 'user_id' => $actor->id, 'body' => $data['body'], 'is_internal' => $data['is_internal'] ?? false,
        ]);
        if ($record->status === 'open') $record->update(['status' => 'in_progress']);
        if (!$reply->is_internal && $record->user?->email) {
            DomainMail::to($record->user->email)->queue(new \App\Mail\TicketReplied($record, $reply));
            $record->user->notify(new \App\Notifications\FeedEntry(
                'support.ticket_replied', 'New reply on your ticket', 'There is a new reply on "'.$record->subject.'".',
                'chat-bubble-left-right', route('customer.tickets.show', ['locale' => 'en', 'id' => $record->id]),
            ));
        }
        DomainOutbox::append('event', 'support.ticket_replied.v1', [
            'references' => [['resource' => 'Ticket', 'id' => $record->id], ['resource' => 'TicketReply', 'id' => $reply->id]],
        ], $actor->id);
        return WorkflowResult::payload(['id' => $record->id, 'reply_id' => $reply->id, 'status' => $record->fresh()->status])->with('success', 'Reply added.');
    }

    public function changeTicketStatus(WorkflowInput $input, Ticket $ticket): WorkflowResult
    {
        $actor = $this->admin($input);
        $data = $input->validate([
            'status' => 'required|in:open,in_progress,pending_customer,resolved,closed',
            'assigned_to' => 'nullable|integer|exists:users,id',
            'resolution' => 'nullable|string|max:10000',
        ]);
        $record = Ticket::query()->with('user')->lockForUpdate()->findOrFail($ticket->id);
        $previous = $record->status;
        if (($data['assigned_to'] ?? null) !== null) {
            $assignee = User::query()->where('is_active', true)->whereHas('roles', fn ($query) => $query->whereIn('name', ['super_admin', 'admin', 'support']))->find($data['assigned_to']);
            if (!$assignee) $this->invalid('assigned_to', 'Select an active support team member.');
        }
        $updates = ['status' => $data['status'], 'assigned_to' => $data['assigned_to'] ?? null];
        if (!empty($data['resolution'])) $updates['resolution'] = $data['resolution'];
        if (in_array($data['status'], ['resolved', 'closed'], true) && !$record->resolved_at) $updates['resolved_at'] = now();
        if ($data['status'] === 'closed' && !$record->closed_at) $updates['closed_at'] = now();
        $record->update($updates);
        if ($data['status'] !== $previous && in_array($data['status'], ['resolved', 'closed', 'pending_customer'], true) && $record->user?->email) {
            DomainMail::to($record->user->email)->queue(new \App\Mail\TicketStatusChanged($record, $data['status']));
            $record->user->notify(new \App\Notifications\FeedEntry(
                'support.ticket_status', 'Ticket status updated', '"'.$record->subject.'" is now '.str_replace('_', ' ', $data['status']).'.',
                'arrow-path', route('customer.tickets.show', ['locale' => 'en', 'id' => $record->id]),
            ));
        }
        DomainOutbox::append('event', 'support.ticket_status_changed.v1', [
            'references' => [['resource' => 'Ticket', 'id' => $record->id]],
        ], $actor->id);
        return WorkflowResult::payload(['id' => $record->id, 'status' => $record->status, 'assigned_to' => $record->assigned_to])->with('success', 'Ticket updated.');
    }
}

<?php

namespace App\Contexts\Support\Application\Commands;

use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Contexts\Shared\Infrastructure\DomainOutbox;
use App\Models\SlaRule;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class AdminSlaRuleCommands extends WorkflowHandler
{
    private function admin(WorkflowInput $input): User { $u = $input->user(); abort_unless($u?->is_active && $u->hasAnyRole(['super_admin', 'admin']), 403); return $u; }
    private function values(WorkflowInput $input, bool $update = false): array
    {
        $p = $update ? 'sometimes|' : '';
        return $input->validate(['name' => $p.'required|string|max:150', 'ticket_priority' => $p.'required|in:low,medium,high,urgent',
            'ticket_type' => 'sometimes|nullable|in:support,billing,technical,bug_report,other', 'response_time_hours' => $p.'required|integer|min:1|max:8760',
            'resolution_time_hours' => $p.'required|integer|min:1|max:8760', 'is_active' => $p.'required|boolean', 'id' => 'prohibited']);
    }
    private function uniqueCombination(array $data, ?int $ignore = null): void
    {
        $priority = $data['ticket_priority']; $type = $data['ticket_type'] ?? null;
        $query = SlaRule::query()->where('ticket_priority', $priority)->when($type === null, fn ($q) => $q->whereNull('ticket_type'), fn ($q) => $q->where('ticket_type', $type));
        if ($ignore) $query->where('id', '!=', $ignore);
        if ($query->exists()) throw ValidationException::withMessages(['ticket_priority' => 'An SLA rule already exists for this priority and ticket type.']);
    }
    public function create(WorkflowInput $input): WorkflowResult
    {
        $actor = $this->admin($input); $data = $this->values($input);
        $r = DB::transaction(function () use ($data, $actor) { $this->uniqueCombination($data); $r = SlaRule::create($data); DomainOutbox::append('event', 'support.sla_rule_created.v1', ['references' => [['resource' => 'SlaRule', 'id' => $r->id]]], $actor->id); return $r; }, 3);
        return WorkflowResult::payload(['record' => $r, 'id' => $r->id], 201);
    }
    public function update(WorkflowInput $input, SlaRule $slaRule): WorkflowResult
    {
        $actor = $this->admin($input); $data = $this->values($input, true); if (!$data) throw ValidationException::withMessages(['sla_rule' => 'Provide at least one field to update.']);
        $r = DB::transaction(function () use ($slaRule, $data, $actor) { $r = SlaRule::query()->lockForUpdate()->findOrFail($slaRule->id); $this->uniqueCombination($data + ['ticket_priority' => $r->ticket_priority, 'ticket_type' => $r->ticket_type], $r->id); $r->update($data); DomainOutbox::append('event', 'support.sla_rule_updated.v1', ['references' => [['resource' => 'SlaRule', 'id' => $r->id]], 'changed_fields' => array_keys($data)], $actor->id); return $r; }, 3);
        return WorkflowResult::payload(['record' => $r, 'id' => $r->id]);
    }
    public function delete(WorkflowInput $input, SlaRule $slaRule): WorkflowResult
    {
        $actor = $this->admin($input); $id = DB::transaction(function () use ($slaRule, $actor) { $r = SlaRule::query()->lockForUpdate()->findOrFail($slaRule->id); if ($r->tickets()->exists()) throw ValidationException::withMessages(['sla_rule' => 'SLA rules with ticket history cannot be deleted.']); $id = $r->id; $r->delete(); DomainOutbox::append('event', 'support.sla_rule_deleted.v1', ['references' => [['resource' => 'SlaRule', 'id' => $id]]], $actor->id); return $id; }, 3);
        return WorkflowResult::payload(['deleted_id' => $id]);
    }
}

<?php

namespace App\Contexts\Support\Application\Queries;

use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Models\SlaRule;
use App\Models\User;

final class AdminSlaRuleQueries extends WorkflowHandler
{
    private function admin(WorkflowInput $input): void { $u = $input->user(); abort_unless($u?->is_active && $u->hasAnyRole(['super_admin', 'admin']), 403); }
    private function item(SlaRule $r): array { return ['id' => $r->id, 'name' => $r->name, 'ticket_type' => $r->ticket_type, 'ticket_priority' => $r->ticket_priority, 'response_time_hours' => $r->response_time_hours, 'resolution_time_hours' => $r->resolution_time_hours, 'is_active' => (bool) $r->is_active]; }
    public function index(WorkflowInput $input): WorkflowResult { $this->admin($input); $p = $input->paginate(SlaRule::query()->orderBy('ticket_priority')->orderBy('ticket_type')); return WorkflowResult::payload(['items' => $p->getCollection()->map(fn ($r) => $this->item($r))->all(), 'pagination' => ['current_page' => $p->currentPage(), 'per_page' => $p->perPage(), 'total' => $p->total(), 'last_page' => $p->lastPage()]]); }
    public function show(WorkflowInput $input, SlaRule $slaRule): WorkflowResult { $this->admin($input); return WorkflowResult::payload(['sla_rule' => $this->item($slaRule)]); }
}

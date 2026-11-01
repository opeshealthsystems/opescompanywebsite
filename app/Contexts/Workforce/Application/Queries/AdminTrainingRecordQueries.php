<?php

namespace App\Contexts\Workforce\Application\Queries;

use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Models\TrainingRecord;

final class AdminTrainingRecordQueries extends WorkflowHandler
{
    private function admin(WorkflowInput $input): void { $u = $input->user(); abort_unless($u?->is_active && $u->hasAnyRole(['super_admin', 'admin']), 403); }
    private function item(TrainingRecord $r): array { return ['id' => $r->id, 'user_id' => $r->user_id, 'employee_name' => $r->employee?->name, 'title' => $r->title, 'provider' => $r->provider, 'category' => $r->category, 'status' => $r->status, 'start_date' => $r->start_date?->toDateString(), 'completed_at' => $r->completed_at?->toDateString(), 'expires_at' => $r->expires_at?->toDateString(), 'certificate_path' => $r->certificate_path, 'notes' => $r->notes]; }
    public function index(WorkflowInput $input): WorkflowResult { $this->admin($input); $p = $input->paginate(TrainingRecord::query()->with('employee')->orderByDesc('created_at')); return WorkflowResult::payload(['items' => $p->getCollection()->map(fn ($r) => $this->item($r))->all(), 'pagination' => ['current_page' => $p->currentPage(), 'per_page' => $p->perPage(), 'total' => $p->total(), 'last_page' => $p->lastPage()]]); }
    public function show(WorkflowInput $input, TrainingRecord $trainingRecord): WorkflowResult { $this->admin($input); $trainingRecord->loadMissing('employee'); return WorkflowResult::payload(['training_record' => $this->item($trainingRecord)]); }
}

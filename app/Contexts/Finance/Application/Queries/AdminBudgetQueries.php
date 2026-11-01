<?php

namespace App\Contexts\Finance\Application\Queries;

use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Models\Budget;
use Illuminate\Database\Eloquent\Builder;

final class AdminBudgetQueries extends WorkflowHandler
{
    private function admin(WorkflowInput $input): void { $u = $input->user(); abort_unless($u?->is_active && $u->hasAnyRole(['super_admin', 'admin']), 403); }
    private function item(Budget $b): array { return ['id' => $b->id, 'year' => $b->year, 'category' => $b->category, 'department' => $b->department, 'allocated_amount' => $b->allocated_amount, 'currency' => $b->currency, 'notes' => $b->notes]; }
    public function query(WorkflowInput $input): Builder { $this->admin($input); return Budget::query()->orderByDesc('year')->orderBy('category')->orderBy('department'); }
    public function index(WorkflowInput $input): WorkflowResult { $p = $input->paginate($this->query($input)); return WorkflowResult::payload(['items' => $p->getCollection()->map(fn ($b) => $this->item($b))->all(), 'pagination' => ['current_page' => $p->currentPage(), 'per_page' => $p->perPage(), 'total' => $p->total(), 'last_page' => $p->lastPage()]]); }
    public function show(WorkflowInput $input, Budget $budget): WorkflowResult { $this->admin($input); return WorkflowResult::payload(['budget' => $this->item($budget)]); }
}

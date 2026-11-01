<?php

namespace App\Contexts\Finance\Application\Queries;

use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Models\PayrollDeductionType;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

final class AdminPayrollConfigurationQueries extends WorkflowHandler
{
    private function admin(WorkflowInput $input): void { $actor = $input->user(); abort_unless($actor?->is_active && $actor->hasAnyRole(['super_admin', 'admin']), 403); }
    private function item(PayrollDeductionType $d): array { return ['id' => $d->id, 'name' => $d->name, 'code' => $d->code, 'calculation_type' => $d->calculation_type, 'rate' => $d->rate, 'description' => $d->description, 'apply_by_default' => (bool) $d->apply_by_default, 'is_active' => (bool) $d->is_active, 'sort_order' => $d->sort_order]; }
    public function query(WorkflowInput $input): Builder { $this->admin($input); return PayrollDeductionType::query()->orderBy('sort_order')->orderBy('name'); }
    public function index(WorkflowInput $input): WorkflowResult
    {
        $page = $input->paginate($this->query($input));
        return WorkflowResult::payload(['items' => $page->getCollection()->map(fn (PayrollDeductionType $d) => $this->item($d))->all(), 'pagination' => ['current_page' => $page->currentPage(), 'per_page' => $page->perPage(), 'total' => $page->total(), 'last_page' => $page->lastPage()]]);
    }
    public function show(WorkflowInput $input, PayrollDeductionType $deductionType): WorkflowResult { $this->admin($input); return WorkflowResult::payload(['deduction_type' => $this->item($deductionType)]); }
}

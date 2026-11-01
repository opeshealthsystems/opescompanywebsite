<?php

namespace App\Contexts\Workforce\Application\Queries;

use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Models\SalaryGrade;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

final class AdminSalaryGradeQueries extends WorkflowHandler
{
    private function admin(WorkflowInput $input): void { $actor = $input->user(); abort_unless($actor?->is_active && $actor->hasAnyRole(['super_admin', 'admin']), 403); }
    private function item(SalaryGrade $g): array { return ['id' => $g->id, 'name' => $g->name, 'code' => $g->code, 'description' => $g->description, 'min_salary' => $g->min_salary, 'max_salary' => $g->max_salary, 'currency' => $g->currency, 'is_active' => (bool) $g->is_active]; }
    public function query(WorkflowInput $input): Builder { $this->admin($input); return SalaryGrade::query()->orderBy('code'); }
    public function index(WorkflowInput $input): WorkflowResult
    {
        $page = $input->paginate($this->query($input));
        return WorkflowResult::payload(['items' => $page->getCollection()->map(fn (SalaryGrade $g) => $this->item($g))->all(), 'pagination' => ['current_page' => $page->currentPage(), 'per_page' => $page->perPage(), 'total' => $page->total(), 'last_page' => $page->lastPage()]]);
    }
    public function show(WorkflowInput $input, SalaryGrade $salaryGrade): WorkflowResult { $this->admin($input); return WorkflowResult::payload(['salary_grade' => $this->item($salaryGrade)]); }
}

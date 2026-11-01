<?php

namespace App\Contexts\Validation\Application\Queries;

use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Models\User;
use App\Models\ValidationProduct;
use Illuminate\Database\Eloquent\Builder;

final class AdminValidationProductQueries extends WorkflowHandler
{
    private function admin(WorkflowInput $input): void { $u = $input->user(); abort_unless($u?->is_active && $u->hasAnyRole(['super_admin', 'admin']), 403); }
    private function item(ValidationProduct $p): array { return ['id' => $p->id, 'name' => $p->name, 'code' => $p->code, 'description' => $p->description, 'is_active' => (bool) $p->is_active]; }
    public function query(WorkflowInput $input): Builder { $this->admin($input); return ValidationProduct::query()->orderBy('name'); }
    public function index(WorkflowInput $input): WorkflowResult { $p = $input->paginate($this->query($input)); return WorkflowResult::payload(['items' => $p->getCollection()->map(fn ($r) => $this->item($r))->all(), 'pagination' => ['current_page' => $p->currentPage(), 'per_page' => $p->perPage(), 'total' => $p->total(), 'last_page' => $p->lastPage()]]); }
    public function show(WorkflowInput $input, ValidationProduct $validationProduct): WorkflowResult { $this->admin($input); return WorkflowResult::payload(['product' => $this->item($validationProduct)]); }
}

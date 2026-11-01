<?php

namespace App\Contexts\Finance\Application\Queries;

use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Models\User;
use App\Models\Vendor;

final class AdminVendorQueries extends WorkflowHandler
{
    private function admin(WorkflowInput $input): void { $u = $input->user(); abort_unless($u?->is_active && $u->hasAnyRole(['super_admin', 'admin']), 403); }
    private function item(Vendor $v): array { return ['id' => $v->id, 'name' => $v->name, 'contact_name' => $v->contact_name, 'email' => $v->email, 'phone' => $v->phone, 'tax_id' => $v->tax_id, 'address' => $v->address, 'notes' => $v->notes, 'is_active' => (bool) $v->is_active]; }
    public function index(WorkflowInput $input): WorkflowResult { $this->admin($input); $p = $input->paginate(Vendor::query()->orderBy('name')); return WorkflowResult::payload(['items' => $p->getCollection()->map(fn ($v) => $this->item($v))->all(), 'pagination' => ['current_page' => $p->currentPage(), 'per_page' => $p->perPage(), 'total' => $p->total(), 'last_page' => $p->lastPage()]]); }
    public function show(WorkflowInput $input, Vendor $vendor): WorkflowResult { $this->admin($input); return WorkflowResult::payload(['vendor' => $this->item($vendor)]); }
}

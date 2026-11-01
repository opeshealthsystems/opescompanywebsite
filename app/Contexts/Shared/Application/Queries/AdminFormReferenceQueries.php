<?php

namespace App\Contexts\Shared\Application\Queries;

use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Models\Cohort;
use App\Models\CohortMember;
use App\Models\AuditLog;
use App\Models\CustomerProfile;
use App\Models\Department;
use App\Models\DocumentTemplate;
use App\Models\Invoice;
use App\Models\JobOpening;
use App\Models\KnowledgeBaseCategory;
use App\Models\Lead;
use App\Models\License;
use App\Models\PractitionerProgram;
use App\Models\Project;
use App\Models\PurchaseOrder;
use Spatie\Permission\Models\Role;
use App\Models\SalaryGrade;
use App\Models\User;
use App\Models\ValidationTestCase;
use App\Models\Vendor;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/** Role-protected reference data shared by admin forms and the public API. */
final class AdminFormReferenceQueries extends WorkflowHandler
{
    private function admin(WorkflowInput $input): User
    {
        $actor = $input->user();
        abort_unless($actor instanceof User && $actor->is_active && $actor->hasAnyRole(['super_admin', 'admin']), 403);
        return $actor;
    }

    private function payload(WorkflowInput $input, Builder $query, callable $map): WorkflowResult
    {
        /** @var Collection $records */
        $records = $input->collect($query);
        return WorkflowResult::payload(['items' => $records->map($map)->values()->all()]);
    }

    public function users(WorkflowInput $input): WorkflowResult
    {
        $this->admin($input);
        $filters = $input->validate([
            'role' => 'sometimes|in:super_admin,admin,support,accountant,hr,manager,staff,employee,tester,customer,practitioner,partner,administrators,support_staff,workforce_staff,managers,testers,customers,leave_staff,admin_support,payroll_staff',
            'id' => 'sometimes|integer|exists:users,id', 'include_email' => 'sometimes|boolean',
        ]);
        $query = User::query()->orderBy('name');
        if (isset($filters['role'])) {
            $groups = [
                'administrators' => ['super_admin', 'admin'],
                'support_staff' => ['super_admin', 'admin', 'support'],
                'workforce_staff' => ['super_admin', 'admin', 'support', 'tester', 'staff', 'employee'],
                'managers' => ['super_admin', 'admin', 'manager'],
                'testers' => ['tester'],
                'customers' => ['customer'],
                'leave_staff' => ['super_admin', 'admin', 'support', 'staff', 'employee'],
                'admin_support' => ['admin', 'support'],
                'payroll_staff' => ['super_admin', 'admin', 'support', 'tester'],
            ];
            if (isset($groups[$filters['role']])) $query->whereHas('roles', fn ($roles) => $roles->whereIn('name', $groups[$filters['role']]));
            else $query->role($filters['role']);
        }
        if (isset($filters['id'])) $query->whereKey($filters['id']);
        $includeEmail = (bool) ($filters['include_email'] ?? false);
        return $this->payload($input, $query, fn (User $user) => ['id' => $user->id, 'name' => $user->name]
            + ($includeEmail ? ['email' => $user->email] : []));
    }

    public function leads(WorkflowInput $input): WorkflowResult
    {
        $this->admin($input);
        $query = Lead::query()->orderBy('name');
        return $this->payload($input, $query, fn (Lead $lead) => ['id' => $lead->id, 'name' => $lead->name]);
    }

    public function departments(WorkflowInput $input): WorkflowResult
    {
        $this->admin($input);
        return $this->payload($input, Department::query()->where('is_active', true)->orderBy('name'),
            fn (Department $department) => ['id' => $department->id, 'name' => $department->name]);
    }

    public function salaryGrades(WorkflowInput $input): WorkflowResult
    {
        $this->admin($input);
        return $this->payload($input, SalaryGrade::query()->where('is_active', true)->orderBy('name'),
            fn (SalaryGrade $grade) => ['id' => $grade->id, 'name' => $grade->name,
                'label' => $grade->code.' — '.$grade->name.' ('.$grade->currency.' '.number_format((float) $grade->min_salary, 0).' – '.number_format((float) $grade->max_salary, 0).')']);
    }

    public function customerCountries(WorkflowInput $input): WorkflowResult
    {
        $this->admin($input);
        return WorkflowResult::payload(['items' => CustomerProfile::query()->whereNotNull('country')->distinct()->orderBy('country')->pluck('country')
            ->map(fn (string $country) => ['id' => $country, 'name' => $country])->values()->all()]);
    }

    public function userRoles(WorkflowInput $input): WorkflowResult
    {
        $this->admin($input);
        return $this->payload($input, Role::query()->orderBy('name'), fn (Role $role) => ['id' => $role->name, 'name' => $role->name]);
    }

    public function rolePermissions(WorkflowInput $input): WorkflowResult
    {
        $this->admin($input);
        $filters = $input->validate(['id' => 'required|integer|exists:roles,id']);
        $role = Role::query()->with('permissions:id,name')->findOrFail($filters['id']);
        return WorkflowResult::payload(['items' => $role->permissions->map(fn ($permission) => ['id' => $permission->name, 'name' => $permission->name])->values()->all()]);
    }

    public function auditLogFilters(WorkflowInput $input): WorkflowResult
    {
        $this->admin($input);
        return WorkflowResult::payload([
            'actions' => AuditLog::query()->distinct()->orderBy('action')->pluck('action')->values()->all(),
            'model_types' => AuditLog::query()->whereNotNull('model_type')->distinct()->orderBy('model_type')->pluck('model_type')->values()->all(),
        ]);
    }

    public function nextEmployeeId(WorkflowInput $input): WorkflowResult
    {
        $this->admin($input);
        $year = now()->format('Y');
        $last = User::query()->whereNotNull('employee_id')->where('employee_id', 'like', "EMP-{$year}-%")->max('employee_id');
        $next = $last ? ((int) substr((string) $last, -4)) + 1 : 1;
        return WorkflowResult::payload(['employee_id' => 'EMP-'.$year.'-'.str_pad((string) $next, 4, '0', STR_PAD_LEFT)]);
    }

    public function projects(WorkflowInput $input): WorkflowResult
    {
        $this->admin($input);
        return $this->payload($input, Project::query()->whereIn('status', ['active', 'planning'])->orderBy('title'),
            fn (Project $project) => ['id' => $project->id, 'title' => $project->title]);
    }

    public function jobOpenings(WorkflowInput $input): WorkflowResult
    {
        $this->admin($input);
        $filters = $input->validate(['status' => 'sometimes|in:open,all']);
        $query = JobOpening::query()->orderBy('title');
        if (($filters['status'] ?? 'open') !== 'all') $query->where('status', 'open');
        return $this->payload($input, $query,
            fn (JobOpening $opening) => ['id' => $opening->id, 'title' => $opening->title]);
    }

    public function knowledgeCategories(WorkflowInput $input): WorkflowResult
    {
        $this->admin($input);
        return $this->payload($input, KnowledgeBaseCategory::query()->where('is_active', true)->orderBy('name'),
            fn (KnowledgeBaseCategory $category) => ['id' => $category->id, 'name' => $category->name]);
    }

    public function validationPrograms(WorkflowInput $input): WorkflowResult
    {
        $this->admin($input);
        return $this->payload($input, PractitionerProgram::validation()->orderBy('title'),
            fn (PractitionerProgram $program) => ['id' => $program->id, 'title' => $program->title]);
    }

    public function testCases(WorkflowInput $input): WorkflowResult
    {
        $this->admin($input);
        return $this->payload($input, ValidationTestCase::query()->orderBy('title'),
            fn (ValidationTestCase $testCase) => ['id' => $testCase->id, 'title' => $testCase->title]);
    }

    public function vendors(WorkflowInput $input): WorkflowResult
    {
        $this->admin($input);
        return $this->payload($input, Vendor::query()->where('is_active', true)->orderBy('name'),
            fn (Vendor $vendor) => ['id' => $vendor->id, 'name' => $vendor->name]);
    }

    public function invoices(WorkflowInput $input): WorkflowResult
    {
        $this->admin($input);
        $query = Invoice::query()->orderByDesc('created_at');
        $filters = $input->validate(['id' => 'sometimes|integer|exists:invoices,id']);
        if (isset($filters['id'])) $query->whereKey($filters['id']);
        return $this->payload($input, $query, fn (Invoice $invoice) => ['id' => $invoice->id, 'invoice_number' => $invoice->invoice_number,
            'grand_total' => (float) $invoice->grand_total, 'currency' => $invoice->currency]);
    }

    public function licenses(WorkflowInput $input): WorkflowResult
    {
        $this->admin($input);
        return $this->payload($input, License::query()->with('customer')->orderByDesc('created_at'),
            fn (License $license) => ['id' => $license->id, 'license_key' => $license->license_key,
                'product_name' => $license->product_name, 'customer' => $license->customer?->name]);
    }

    public function purchaseOrders(WorkflowInput $input): WorkflowResult
    {
        $this->admin($input);
        return $this->payload($input, PurchaseOrder::query()->whereIn('status', ['approved', 'received'])->orderByDesc('created_at'),
            fn (PurchaseOrder $order) => ['id' => $order->id, 'reference' => $order->reference, 'title' => $order->title]);
    }

    public function documentTemplates(WorkflowInput $input): WorkflowResult
    {
        $this->admin($input);
        return $this->payload($input, DocumentTemplate::query()->where('is_active', true)->orderBy('name'),
            fn (DocumentTemplate $template) => ['id' => $template->id, 'name' => $template->name, 'type' => $template->type]);
    }

    public function cohortMembers(WorkflowInput $input): WorkflowResult
    {
        $this->admin($input);
        return $this->payload($input, CohortMember::query()->with(['user:id,name', 'cohort:id,name'])->orderByDesc('id'),
            fn (CohortMember $member) => ['id' => $member->id, 'name' => $member->user?->name,
                'cohort' => $member->cohort?->name, 'status' => $member->status]);
    }

    public function cohorts(WorkflowInput $input): WorkflowResult
    {
        $this->admin($input);
        $filters = $input->validate(['program_id' => 'sometimes|integer|exists:practitioner_programs,id', 'status' => 'sometimes|in:active,draft,completed']);
        $query = Cohort::query()->orderBy('name');
        if (isset($filters['program_id'])) $query->where('practitioner_program_id', $filters['program_id']);
        if (isset($filters['status'])) $query->where('status', $filters['status']);
        return $this->payload($input, $query, fn (Cohort $cohort) => ['id' => $cohort->id, 'name' => $cohort->name]);
    }
}

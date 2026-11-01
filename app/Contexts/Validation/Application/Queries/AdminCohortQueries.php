<?php

namespace App\Contexts\Validation\Application\Queries;

use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Models\Cohort;
use App\Models\CohortMember;
use Illuminate\Database\Eloquent\Builder;

final class AdminCohortQueries extends WorkflowHandler
{
    private function admin(WorkflowInput $input): void
    {
        $user = $input->user();
        abort_unless($user?->is_active && $user->hasAnyRole(['super_admin', 'admin']), 403);
    }

    private function item(Cohort $cohort): array
    {
        return [
            'id' => $cohort->id, 'practitioner_program_id' => $cohort->practitioner_program_id, 'program_title' => $cohort->practitionerProgram?->title,
            'name' => $cohort->name, 'specialty' => $cohort->specialty, 'description' => $cohort->description,
            'start_date' => $cohort->start_date?->toDateString(), 'end_date' => $cohort->end_date?->toDateString(),
            'max_members' => $cohort->max_members, 'status' => $cohort->status,
            'members_count' => $cohort->members_count ?? $cohort->members()->count(),
            'test_cases_count' => $cohort->test_cases_count ?? $cohort->testCases()->count(),
            'created_at' => $cohort->created_at, 'updated_at' => $cohort->updated_at,
        ];
    }

    public function query(WorkflowInput $input): Builder
    {
        $this->admin($input);

        return Cohort::query()->with('practitionerProgram:id,title')->withCount(['members', 'testCases'])->orderByDesc('start_date');
    }

    public function index(WorkflowInput $input): WorkflowResult
    {
        $this->admin($input);
        $filters = $input->validate(['status' => 'sometimes|in:draft,active,completed', 'practitioner_program_id' => 'sometimes|integer|exists:practitioner_programs,id']);
        $query = $this->query($input);
        if (isset($filters['status'])) $query->where('status', $filters['status']);
        if (isset($filters['practitioner_program_id'])) $query->where('practitioner_program_id', $filters['practitioner_program_id']);
        $page = $input->paginate($query);
        return WorkflowResult::payload(['items' => $page->getCollection()->map(fn (Cohort $cohort) => $this->item($cohort))->all(),
            'pagination' => ['current_page' => $page->currentPage(), 'per_page' => $page->perPage(), 'total' => $page->total(), 'last_page' => $page->lastPage()]]);
    }

    public function show(WorkflowInput $input, Cohort $cohort): WorkflowResult
    {
        $this->admin($input);
        $cohort->load(['practitionerProgram:id,title', 'members.user:id,name', 'testCases.workflow:id,name'])->loadCount(['members', 'testCases']);
        return WorkflowResult::payload(['cohort' => $this->item($cohort),
            'members' => $cohort->members->map(fn (CohortMember $member) => [
                'id' => $member->id, 'user_id' => $member->user_id, 'name' => $member->user?->name,
                'status' => $member->status, 'placed_at' => $member->placed_at,
            ])->all(),
            'test_cases' => $cohort->testCases->map(fn ($testCase) => [
                'id' => $testCase->id, 'title' => $testCase->title, 'workflow_id' => $testCase->validation_workflow_id,
                'workflow_name' => $testCase->workflow?->name, 'due_date' => $testCase->pivot?->due_date,
            ])->all(),
        ]);
    }

    public function memberQuery(WorkflowInput $input, Cohort $cohort): Builder
    {
        $this->admin($input);

        return CohortMember::query()->where('cohort_id', $cohort->id)->with('user:id,name,email')->orderByDesc('placed_at')->orderBy('id');
    }

    public function members(WorkflowInput $input, Cohort $cohort): WorkflowResult
    {
        $page = $input->paginate($this->memberQuery($input, $cohort));

        return WorkflowResult::payload(['items' => $page->getCollection()->map(fn (CohortMember $member) => [
            'id' => $member->id,
            'cohort_id' => $member->cohort_id,
            'user_id' => $member->user_id,
            'name' => $member->user?->name,
            'email' => $member->user?->email,
            'status' => $member->status,
            'placed_at' => $member->placed_at?->toIso8601String(),
        ])->all(), 'pagination' => ['current_page' => $page->currentPage(), 'per_page' => $page->perPage(), 'total' => $page->total(), 'last_page' => $page->lastPage()]]);
    }

    public function testCaseQuery(WorkflowInput $input, Cohort $cohort): Builder
    {
        $this->admin($input);

        $query = $cohort->testCases()->getQuery();
        $query->with(['workflow.module.product'])->orderBy('validation_test_cases.title');

        return $query;
    }

    public function testCases(WorkflowInput $input, Cohort $cohort): WorkflowResult
    {
        $page = $input->paginate($this->testCaseQuery($input, $cohort));

        return WorkflowResult::payload(['items' => $page->getCollection()->map(fn ($testCase) => [
            'id' => $testCase->id,
            'title' => $testCase->title,
            'workflow_id' => $testCase->validation_workflow_id,
            'workflow_name' => $testCase->workflow?->name,
            'module_name' => $testCase->workflow?->module?->name,
            'product_name' => $testCase->workflow?->module?->product?->name,
            'due_date' => $testCase->pivot?->due_date,
        ])->all(), 'pagination' => ['current_page' => $page->currentPage(), 'per_page' => $page->perPage(), 'total' => $page->total(), 'last_page' => $page->lastPage()]]);
    }
}

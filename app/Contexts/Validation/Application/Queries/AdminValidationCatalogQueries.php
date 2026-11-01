<?php

namespace App\Contexts\Validation\Application\Queries;

use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Models\ValidationModule;
use App\Models\ValidationTestCase;
use App\Models\ValidationWorkflow;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

final class AdminValidationCatalogQueries extends WorkflowHandler
{
    private function admin(WorkflowInput $input): void
    {
        $actor = $input->user();
        abort_unless($actor?->is_active && $actor->hasAnyRole(['super_admin', 'admin']), 403);
    }

    public function moduleQuery(WorkflowInput $input): Builder
    {
        $this->admin($input);

        return ValidationModule::query()->with('product')->withCount('workflows')->orderBy('name');
    }

    public function workflowQuery(WorkflowInput $input, ?ValidationModule $module = null): Builder
    {
        $this->admin($input);

        $query = $module ? $module->workflows()->getQuery() : ValidationWorkflow::query();

        return $query->withCount('testCases')->orderBy('name');
    }

    public function modules(WorkflowInput $input): WorkflowResult
    {
        $this->admin($input);
        $filters = $input->validate(['validation_product_id' => 'sometimes|integer|exists:validation_products,id', 'active' => 'sometimes|boolean', 'search' => 'sometimes|nullable|string|max:150']);
        $query = $this->moduleQuery($input);
        if (isset($filters['validation_product_id'])) $query->where('validation_product_id', $filters['validation_product_id']);
        if (isset($filters['active'])) $query->where('is_active', $filters['active']);
        if (!empty($filters['search'])) $query->where(fn ($q) => $q->where('name', 'like', '%'.trim($filters['search']).'%')->orWhere('code', 'like', '%'.trim($filters['search']).'%'));
        $page = $input->paginate($query);
        return WorkflowResult::payload(['items' => $page->getCollection()->map(fn (ValidationModule $module) => $module->only(['id', 'validation_product_id', 'name', 'code', 'description', 'is_active', 'workflows_count']) + ['product' => $module->product?->only(['id', 'name'])])->all(),
            'pagination' => ['current_page' => $page->currentPage(), 'per_page' => $page->perPage(), 'total' => $page->total(), 'last_page' => $page->lastPage()]]);
    }

    public function module(WorkflowInput $input, ValidationModule $module): WorkflowResult
    {
        $this->admin($input); $module->load('product')->loadCount('workflows');
        return WorkflowResult::payload(['module' => $module->only(['id', 'validation_product_id', 'name', 'code', 'description', 'is_active', 'workflows_count']) + ['product' => $module->product?->only(['id', 'name'])]]);
    }

    public function workflows(WorkflowInput $input, ValidationModule $module): WorkflowResult
    {
        $this->admin($input);
        $filters = $input->validate(['active' => 'sometimes|boolean', 'search' => 'sometimes|nullable|string|max:150']);
        $query = $this->workflowQuery($input, $module);
        if (isset($filters['active'])) $query->where('is_active', $filters['active']);
        if (!empty($filters['search'])) $query->where(fn ($q) => $q->where('name', 'like', '%'.trim($filters['search']).'%')->orWhere('code', 'like', '%'.trim($filters['search']).'%'));
        $page = $input->paginate($query);
        return WorkflowResult::payload(['items' => $page->getCollection()->map(fn (ValidationWorkflow $workflow) => $workflow->only(['id', 'validation_module_id', 'name', 'code', 'description', 'is_active', 'test_cases_count']))->all(),
            'pagination' => ['current_page' => $page->currentPage(), 'per_page' => $page->perPage(), 'total' => $page->total(), 'last_page' => $page->lastPage()]]);
    }

    public function workflow(WorkflowInput $input, ValidationModule $module, ValidationWorkflow $workflow): WorkflowResult
    {
        $this->admin($input); abort_unless((int) $workflow->validation_module_id === (int) $module->id, 404); $workflow->loadCount('testCases');
        return WorkflowResult::payload(['workflow' => $workflow->only(['id', 'validation_module_id', 'name', 'code', 'description', 'is_active', 'test_cases_count'])]);
    }

    public function testCases(WorkflowInput $input, ValidationWorkflow $workflow): WorkflowResult
    {
        $this->admin($input);
        $filters = $input->validate(['active' => 'sometimes|boolean', 'search' => 'sometimes|nullable|string|max:150']);
        $query = $this->testCaseQuery($input, $workflow);
        if (isset($filters['active'])) $query->where('is_active', $filters['active']);
        if (!empty($filters['search'])) $query->where('title', 'like', '%'.trim($filters['search']).'%');
        $page = $input->paginate($query);
        return WorkflowResult::payload(['items' => $page->getCollection()->map(fn (ValidationTestCase $case) => $case->only(['id', 'validation_workflow_id', 'title', 'description', 'steps', 'expected_result', 'is_active']))->all(),
            'pagination' => ['current_page' => $page->currentPage(), 'per_page' => $page->perPage(), 'total' => $page->total(), 'last_page' => $page->lastPage()]]);
    }

    public function testCaseQuery(WorkflowInput $input, ValidationWorkflow $workflow): Builder
    {
        $this->admin($input);

        return $workflow->testCases()->orderBy('id');
    }

    public function testCase(WorkflowInput $input, ValidationWorkflow $workflow, ValidationTestCase $testCase): WorkflowResult
    {
        $this->admin($input); abort_unless((int) $testCase->validation_workflow_id === (int) $workflow->id, 404);
        return WorkflowResult::payload(['test_case' => $testCase->only(['id', 'validation_workflow_id', 'title', 'description', 'steps', 'expected_result', 'is_active'])]);
    }
}

<?php

namespace App\Contexts\Validation\Application\Commands;

use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Contexts\Shared\Infrastructure\DomainOutbox;
use App\Models\CohortTestCase;
use App\Models\IssueReport;
use App\Models\ValidationModule;
use App\Models\ValidationProduct;
use App\Models\ValidationTestCase;
use App\Models\ValidationWorkflow;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

final class AdminValidationCatalogCommands extends WorkflowHandler
{
    private function admin(WorkflowInput $input): User
    {
        $actor = $input->user();
        abort_unless($actor?->is_active && $actor->hasAnyRole(['super_admin', 'admin']), 403);
        return $actor;
    }

    private function moduleValues(WorkflowInput $input, ?ValidationModule $module = null, ?ValidationProduct $product = null): array
    {
        $p = $module ? 'sometimes|' : '';
        $productId = $product?->id ?? $input->input('validation_product_id', $module?->validation_product_id);
        return $input->validate(['validation_product_id' => $product ? 'prohibited' : $p.'required|integer|exists:validation_products,id', 'name' => $p.'required|string|max:255',
            'code' => [$module ? 'sometimes' : 'required', 'string', 'max:255', Rule::unique('validation_modules', 'code')->where(fn ($query) => $query->where('validation_product_id', $productId))->ignore($module?->id)],
            'description' => $p.'nullable|string|max:10000', 'is_active' => $p.'boolean']);
    }

    private function workflowValues(WorkflowInput $input, ValidationModule $module, ?ValidationWorkflow $workflow = null): array
    {
        $p = $workflow ? 'sometimes|' : '';
        return $input->validate(['name' => $p.'required|string|max:255', 'code' => [$workflow ? 'sometimes' : 'required', 'string', 'max:255', Rule::unique('validation_workflows', 'code')->where(fn ($query) => $query->where('validation_module_id', $module->id))->ignore($workflow?->id)],
            'description' => $p.'nullable|string|max:10000', 'is_active' => $p.'boolean', 'validation_module_id' => 'prohibited', 'id' => 'prohibited']);
    }

    private function testCaseValues(WorkflowInput $input, bool $update = false): array
    {
        $p = $update ? 'sometimes|' : '';
        return $input->validate(['title' => $p.'required|string|max:255', 'description' => $p.'nullable|string|max:10000',
            'steps' => $p.'nullable|string|max:20000', 'expected_result' => $p.'nullable|string|max:10000', 'is_active' => $p.'boolean',
            'validation_workflow_id' => 'prohibited', 'id' => 'prohibited']);
    }

    public function createModule(WorkflowInput $input, ValidationProduct $product): WorkflowResult
    {
        $actor = $this->admin($input);
        $data = $this->moduleValues($input, product: $product);
        $data['validation_product_id'] = $product->id;
        $record = DB::transaction(function () use ($product, $data, $actor) {
            $parent = ValidationProduct::query()->lockForUpdate()->findOrFail($product->id);
            $module = $parent->modules()->create($data);
            DomainOutbox::append('event', 'validation.module_created.v1', ['references' => [['resource' => 'ValidationProduct', 'id' => $parent->id], ['resource' => 'ValidationModule', 'id' => $module->id]]], $actor->id);
            return $module;
        }, 3);
        return WorkflowResult::payload(['record' => $record, 'id' => $record->id, 'validation_product_id' => $record->validation_product_id], 201);
    }

    public function updateModule(WorkflowInput $input, ValidationModule $module): WorkflowResult
    {
        $actor = $this->admin($input); $data = $this->moduleValues($input, $module);
        if ($data === []) throw ValidationException::withMessages(['module' => 'Provide at least one module field to update.']);
        $record = DB::transaction(function () use ($module, $data, $actor) {
            $record = ValidationModule::query()->lockForUpdate()->findOrFail($module->id);
            if (isset($data['validation_product_id']) && (int) $data['validation_product_id'] !== (int) $record->validation_product_id && $record->workflows()->exists()) {
                throw ValidationException::withMessages(['validation_product_id' => 'A module with workflows cannot be moved to another product.']);
            }
            $record->update($data);
            DomainOutbox::append('event', 'validation.module_updated.v1', ['references' => [['resource' => 'ValidationModule', 'id' => $record->id]], 'changed_fields' => array_keys($data)], $actor->id);
            return $record;
        }, 3);
        return WorkflowResult::payload(['record' => $record, 'id' => $record->id, 'is_active' => $record->is_active]);
    }

    public function deleteModule(WorkflowInput $input, ValidationModule $module): WorkflowResult
    {
        $actor = $this->admin($input);
        $id = DB::transaction(function () use ($module, $actor) {
            $record = ValidationModule::query()->lockForUpdate()->findOrFail($module->id);
            if ($record->workflows()->exists()) {
                throw ValidationException::withMessages(['module' => 'A module with workflows must be retained.']);
            }
            $id = $record->id;
            $record->delete();
            DomainOutbox::append('event', 'validation.module_deleted.v1', ['references' => [['resource' => 'ValidationModule', 'id' => $id]]], $actor->id);
            return $id;
        }, 3);
        return WorkflowResult::payload(['id' => $id, 'status' => 'deleted']);
    }

    public function createWorkflow(WorkflowInput $input, ValidationModule $module): WorkflowResult
    {
        $actor = $this->admin($input); $data = $this->workflowValues($input, $module);
        $record = DB::transaction(function () use ($module, $data, $actor) {
            $parent = ValidationModule::query()->lockForUpdate()->findOrFail($module->id);
            $workflow = $parent->workflows()->create($data);
            DomainOutbox::append('event', 'validation.workflow_created.v1', ['references' => [['resource' => 'ValidationModule', 'id' => $parent->id], ['resource' => 'ValidationWorkflow', 'id' => $workflow->id]]], $actor->id);
            return $workflow;
        }, 3);
        return WorkflowResult::payload(['record' => $record, 'id' => $record->id, 'validation_module_id' => $record->validation_module_id], 201);
    }

    public function updateWorkflow(WorkflowInput $input, ValidationModule $module, ValidationWorkflow $workflow): WorkflowResult
    {
        $actor = $this->admin($input); $data = $this->workflowValues($input, $module, $workflow);
        if ($data === []) throw ValidationException::withMessages(['workflow' => 'Provide at least one workflow field to update.']);
        $record = DB::transaction(function () use ($module, $workflow, $data, $actor) {
            $parent = ValidationModule::query()->lockForUpdate()->findOrFail($module->id);
            $record = ValidationWorkflow::query()->lockForUpdate()->findOrFail($workflow->id);
            if ((int) $record->validation_module_id !== (int) $parent->id) abort(404);
            $record->update($data);
            DomainOutbox::append('event', 'validation.workflow_updated.v1', ['references' => [['resource' => 'ValidationWorkflow', 'id' => $record->id]], 'changed_fields' => array_keys($data)], $actor->id);
            return $record;
        }, 3);
        return WorkflowResult::payload(['record' => $record, 'id' => $record->id, 'is_active' => $record->is_active]);
    }

    public function deleteWorkflow(WorkflowInput $input, ValidationModule $module, ValidationWorkflow $workflow): WorkflowResult
    {
        $actor = $this->admin($input);
        $id = DB::transaction(function () use ($module, $workflow, $actor) {
            $parent = ValidationModule::query()->lockForUpdate()->findOrFail($module->id);
            $record = ValidationWorkflow::query()->lockForUpdate()->findOrFail($workflow->id);
            if ((int) $record->validation_module_id !== (int) $parent->id) abort(404);
            if ($record->testCases()->exists() || \App\Models\DailyTestSession::query()->where('validation_workflow_id', $record->id)->exists()) {
                throw ValidationException::withMessages(['workflow' => 'A workflow with test cases or recorded sessions must be retained.']);
            }
            $id = $record->id; $record->delete();
            DomainOutbox::append('event', 'validation.workflow_deleted.v1', ['references' => [['resource' => 'ValidationWorkflow', 'id' => $id]]], $actor->id);
            return $id;
        }, 3);
        return WorkflowResult::payload(['id' => $id, 'status' => 'deleted']);
    }

    public function createTestCase(WorkflowInput $input, ValidationWorkflow $workflow): WorkflowResult
    {
        $actor = $this->admin($input); $data = $this->testCaseValues($input);
        $record = DB::transaction(function () use ($workflow, $data, $actor) {
            $parent = ValidationWorkflow::query()->lockForUpdate()->findOrFail($workflow->id);
            $case = $parent->testCases()->create($data);
            DomainOutbox::append('event', 'validation.test_case_created.v1', ['references' => [['resource' => 'ValidationWorkflow', 'id' => $parent->id], ['resource' => 'ValidationTestCase', 'id' => $case->id]]], $actor->id);
            return $case;
        }, 3);
        return WorkflowResult::payload(['record' => $record, 'id' => $record->id, 'validation_workflow_id' => $record->validation_workflow_id], 201);
    }

    public function updateTestCase(WorkflowInput $input, ValidationWorkflow $workflow, ValidationTestCase $testCase): WorkflowResult
    {
        $actor = $this->admin($input); $data = $this->testCaseValues($input, true);
        if ($data === []) throw ValidationException::withMessages(['test_case' => 'Provide at least one test-case field to update.']);
        $record = DB::transaction(function () use ($workflow, $testCase, $data, $actor) {
            $parent = ValidationWorkflow::query()->lockForUpdate()->findOrFail($workflow->id);
            $record = ValidationTestCase::query()->lockForUpdate()->findOrFail($testCase->id);
            if ((int) $record->validation_workflow_id !== (int) $parent->id) abort(404);
            $record->update($data);
            DomainOutbox::append('event', 'validation.test_case_updated.v1', ['references' => [['resource' => 'ValidationTestCase', 'id' => $record->id]], 'changed_fields' => array_keys($data)], $actor->id);
            return $record;
        }, 3);
        return WorkflowResult::payload(['record' => $record, 'id' => $record->id, 'is_active' => $record->is_active]);
    }

    public function deleteTestCase(WorkflowInput $input, ValidationWorkflow $workflow, ValidationTestCase $testCase): WorkflowResult
    {
        $actor = $this->admin($input);
        $id = DB::transaction(function () use ($workflow, $testCase, $actor) {
            $parent = ValidationWorkflow::query()->lockForUpdate()->findOrFail($workflow->id);
            $record = ValidationTestCase::query()->lockForUpdate()->findOrFail($testCase->id);
            if ((int) $record->validation_workflow_id !== (int) $parent->id) abort(404);
            if (CohortTestCase::query()->where('validation_test_case_id', $record->id)->exists() || IssueReport::query()->where('validation_test_case_id', $record->id)->exists()) {
                throw ValidationException::withMessages(['test_case' => 'A test case referenced by a cohort or issue report must be retained.']);
            }
            $id = $record->id; $record->delete();
            DomainOutbox::append('event', 'validation.test_case_deleted.v1', ['references' => [['resource' => 'ValidationTestCase', 'id' => $id]]], $actor->id);
            return $id;
        }, 3);
        return WorkflowResult::payload(['id' => $id, 'status' => 'deleted']);
    }
}

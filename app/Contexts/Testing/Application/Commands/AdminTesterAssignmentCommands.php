<?php

namespace App\Contexts\Testing\Application\Commands;

use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Contexts\Shared\Infrastructure\DomainOutbox;
use App\Models\TesterAssignment;
use App\Models\User;
use App\Support\ProductCatalog;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/** Administrator commands for tester assignment management. */
final class AdminTesterAssignmentCommands extends WorkflowHandler
{
    private function admin(WorkflowInput $input): User
    {
        $actor = $input->user();
        abort_unless($actor?->is_active && $actor->hasAnyRole(['super_admin', 'admin']), 403);
        return $actor;
    }

    private function validateTester(int $id): void
    {
        abort_unless(User::query()->whereKey($id)->where('is_active', true)->whereHas('roles', fn ($query) => $query->where('name', 'tester'))->exists(), 422, 'Choose an active tester account.');
    }

    public function create(WorkflowInput $input): WorkflowResult
    {
        $actor = $this->admin($input);
        $products = ProductCatalog::options();
        $data = $input->validate([
            'assigned_to' => 'required|integer|exists:users,id', 'product_slug' => 'required|string|in:'.implode(',', array_keys($products)),
            'title' => 'required|string|max:255', 'description' => 'required|string|max:10000',
            'due_date' => 'nullable|date', 'notes' => 'nullable|string|max:5000',
            'product_name' => 'prohibited', 'assigned_by' => 'prohibited', 'status' => 'prohibited', 'id' => 'prohibited',
        ]);
        $this->validateTester((int) $data['assigned_to']);
        $record = DB::transaction(function () use ($data, $products, $actor) {
            $record = TesterAssignment::create($data + ['product_name' => $products[$data['product_slug']], 'assigned_by' => $actor->id, 'status' => 'pending']);
            DomainOutbox::append('event', 'testing.tester_assignment_created.v1', ['references' => [['resource' => 'TesterAssignment', 'id' => $record->id]]], $actor->id);
            return $record;
        }, 3);
        return WorkflowResult::payload(['id' => $record->id, 'status' => $record->status, 'product_slug' => $record->product_slug, 'product_name' => $record->product_name], 201)->with('success', 'Tester assigned.');
    }

    public function update(WorkflowInput $input, TesterAssignment $testerAssignment): WorkflowResult
    {
        $actor = $this->admin($input);
        $products = ProductCatalog::options();
        $data = $input->validate([
            'assigned_to' => 'sometimes|required|integer|exists:users,id', 'product_slug' => 'sometimes|required|string|in:'.implode(',', array_keys($products)),
            'title' => 'sometimes|required|string|max:255', 'description' => 'sometimes|required|string|max:10000',
            'status' => 'sometimes|required|in:'.implode(',', array_keys(TesterAssignment::statusOptions())),
            'due_date' => 'sometimes|nullable|date', 'notes' => 'sometimes|nullable|string|max:5000',
            'product_name' => 'prohibited', 'assigned_by' => 'prohibited', 'id' => 'prohibited',
        ]);
        if ($data === []) throw ValidationException::withMessages(['assignment' => 'Provide at least one assignment field to update.']);
        if (isset($data['assigned_to'])) $this->validateTester((int) $data['assigned_to']);
        $record = DB::transaction(function () use ($testerAssignment, $data, $products, $actor) {
            $record = TesterAssignment::query()->lockForUpdate()->findOrFail($testerAssignment->id);
            if (!$record->isActive()) throw ValidationException::withMessages(['status' => 'Completed or cancelled assignments cannot be edited.']);
            if (isset($data['status'])) {
                $allowed = $record->status === 'pending' ? ['in_progress', 'cancelled'] : ['completed', 'cancelled'];
                if (!in_array($data['status'], $allowed, true)) throw ValidationException::withMessages(['status' => 'Invalid assignment status transition.']);
            }
            if (isset($data['product_slug'])) $data['product_name'] = $products[$data['product_slug']];
            $record->fill($data)->save();
            DomainOutbox::append('event', 'testing.tester_assignment_updated.v1', ['references' => [['resource' => 'TesterAssignment', 'id' => $record->id]], 'changed_fields' => array_keys($data)], $actor->id);
            return $record;
        }, 3);
        return WorkflowResult::payload(['id' => $record->id, 'status' => $record->status, 'product_slug' => $record->product_slug, 'product_name' => $record->product_name])->with('success', 'Tester assignment updated.');
    }

    public function delete(WorkflowInput $input, TesterAssignment $testerAssignment): WorkflowResult
    {
        $actor = $this->admin($input);
        DB::transaction(function () use ($testerAssignment, $actor) {
            $record = TesterAssignment::query()->lockForUpdate()->findOrFail($testerAssignment->id);
            if (!in_array($record->status, ['pending', 'cancelled'], true) || $record->bugReports()->exists()) {
                throw ValidationException::withMessages(['assignment' => 'Only pending or cancelled assignments without bug reports can be deleted.']);
            }
            DomainOutbox::append('event', 'testing.tester_assignment_deleted.v1', ['references' => [['resource' => 'TesterAssignment', 'id' => $record->id]]], $actor->id);
            $record->delete();
        }, 3);
        return WorkflowResult::payload(['id' => $testerAssignment->id, 'status' => 'deleted'])->with('success', 'Tester assignment deleted.');
    }
}

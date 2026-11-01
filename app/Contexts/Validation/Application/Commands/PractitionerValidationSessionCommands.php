<?php

namespace App\Contexts\Validation\Application\Commands;

use App\Models\DailyTestSession;
class PractitionerValidationSessionCommands extends \App\Contexts\Shared\Application\WorkflowHandler
{
    use \App\Contexts\Validation\Domain\ResolvesCohortScope;
    public function store(\App\Contexts\Shared\Application\WorkflowInput $request): \App\Contexts\Shared\Application\WorkflowResult
    {
        $member = $this->activeMember();
        if (!$member) {
            return \App\Contexts\Shared\Application\WorkflowResult::next('practitioner_validation_sessioncontroller_store_1', ['locale' => $request->locale()])->with('notice', 'You have not been placed in a validation cohort yet.');
        }
        $validated = $request->validate(['validation_product_id' => 'required|exists:validation_products,id', 'validation_module_id' => 'required|exists:validation_modules,id', 'validation_workflow_id' => 'required|exists:validation_workflows,id', 'facility_context' => 'nullable|string|max:200', 'date' => 'required|date|before_or_equal:' . now('Africa/Douala')->toDateString(), 'start_time' => 'nullable|date_format:H:i', 'end_time' => 'nullable|date_format:H:i|after:start_time', 'tasks_completed' => 'required|integer|min:0|max:999', 'comments' => 'nullable|string|max:3000', 'screenshots.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120']);
        [, , , $allowedWorkflowIds] = $this->scopedCatalog($member);
        abort_unless($allowedWorkflowIds->map(fn($id) => (int) $id)->contains((int) $validated['validation_workflow_id']), 422, 'Workflow not in cohort scope.');
        // Derive product/module authoritatively from the in-scope workflow so a
        // mismatched (but individually valid) product/module cannot be persisted.
        $workflow = \App\Models\ValidationWorkflow::with('module')->findOrFail($validated['validation_workflow_id']);
        $paths = [];
        if ($request->hasFile('screenshots')) {
            foreach ($request->file('screenshots') as $file) {
                $paths[] = $file->store('validation/sessions', 'public');
            }
        }
        \App\Models\DailyTestSession::create(['cohort_member_id' => $member->id, 'validation_product_id' => $workflow->module->validation_product_id, 'validation_module_id' => $workflow->validation_module_id, 'validation_workflow_id' => $workflow->id, 'facility_context' => $validated['facility_context'] ?? null, 'date' => $validated['date'], 'start_time' => $validated['start_time'] ?? null, 'end_time' => $validated['end_time'] ?? null, 'tasks_completed' => $validated['tasks_completed'], 'screenshots' => $paths ?: null, 'comments' => $validated['comments'] ?? null]);
        return \App\Contexts\Shared\Application\WorkflowResult::next('practitioner_validation_sessioncontroller_store_2', ['locale' => $request->locale()])->with('success', 'Daily test session recorded.');
    }
}

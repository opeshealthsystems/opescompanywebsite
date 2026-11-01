<?php

namespace App\Contexts\Validation\Application\Commands;

use App\Models\IssueReport;
use App\Models\ValidationTestCase;
class PractitionerValidationIssueReportCommands extends \App\Contexts\Shared\Application\WorkflowHandler
{
    use \App\Contexts\Validation\Domain\ResolvesCohortScope;
    public function store(\App\Contexts\Shared\Application\WorkflowInput $request): \App\Contexts\Shared\Application\WorkflowResult
    {
        $member = $this->activeMember();
        if (!$member) {
            return \App\Contexts\Shared\Application\WorkflowResult::next('practitioner_validation_issuereportcontroller_store_1', ['locale' => $request->locale()])->with('notice', 'You have not been placed in a validation cohort yet.');
        }
        $validated = $request->validate(['validation_product_id' => 'required|exists:validation_products,id', 'validation_module_id' => 'required|exists:validation_modules,id', 'validation_workflow_id' => 'required|exists:validation_workflows,id', 'validation_test_case_id' => 'nullable|exists:validation_test_cases,id', 'daily_test_session_id' => 'nullable|exists:daily_test_sessions,id', 'title' => 'required|string|max:200', 'issue_type' => 'required|in:bug,missing_feature,workflow_problem,clinical_risk,ui_ux_problem,performance_issue,security_concern,interoperability_issue,data_quality_issue,recommendation', 'severity' => 'required|in:critical,high,medium,low', 'description' => 'required|string|max:5000', 'steps_to_reproduce' => 'required|string|max:5000', 'expected_result' => 'required|string|max:2000', 'actual_result' => 'required|string|max:2000', 'clinical_impact' => 'required|string|max:2000', 'recommendation' => 'nullable|string|max:2000', 'attachments.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10240']);
        [, , , $allowedWorkflowIds] = $this->scopedCatalog($member);
        abort_unless($allowedWorkflowIds->map(fn($id) => (int) $id)->contains((int) $validated['validation_workflow_id']), 422, 'Workflow not in cohort scope.');
        // Derive product/module authoritatively from the in-scope workflow.
        $workflow = \App\Models\ValidationWorkflow::with('module')->findOrFail($validated['validation_workflow_id']);
        $validated['validation_workflow_id'] = $workflow->id;
        $validated['validation_module_id'] = $workflow->validation_module_id;
        $validated['validation_product_id'] = $workflow->module->validation_product_id;
        // An optional test case must belong to a workflow in the cohort scope.
        if (!empty($validated['validation_test_case_id'])) {
            abort_unless(\App\Models\ValidationTestCase::whereKey($validated['validation_test_case_id'])->whereIn('validation_workflow_id', $allowedWorkflowIds)->exists(), 422, 'Test case not in cohort scope.');
        }
        // An optional linked session must belong to this practitioner's own cohort membership.
        if (!empty($validated['daily_test_session_id'])) {
            abort_unless($member->dailyTestSessions()->whereKey($validated['daily_test_session_id'])->exists(), 422, 'Session does not belong to you.');
        }
        $paths = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $paths[] = $file->store('validation/issues', 'public');
            }
        }
        $issue = \App\Models\IssueReport::create(array_merge($validated, ['cohort_member_id' => $member->id, 'attachments' => $paths ?: null, 'status' => 'submitted']));
        $member->user?->notify(new \App\Notifications\IssueSubmitted($issue));
        return \App\Contexts\Shared\Application\WorkflowResult::next('practitioner_validation_issuereportcontroller_store_2', ['locale' => $request->locale()])->with('success', 'Issue report submitted.');
    }
}

<?php

namespace App\Contexts\Validation\Application\Queries;

use App\Models\IssueReport;
use App\Models\ValidationTestCase;
class PractitionerValidationIssueReportQueries extends \App\Contexts\Shared\Application\WorkflowHandler
{
    use \App\Contexts\Validation\Domain\ResolvesCohortScope;
    public function index(\App\Contexts\Shared\Application\WorkflowInput $workflowInput): \App\Contexts\Shared\Application\WorkflowResult
    {
        $member = $this->activeMember();
        if (!$member) {
            return \App\Contexts\Shared\Application\WorkflowResult::next('practitioner_validation_issuereportcontroller_index_1', ['locale' => $workflowInput->locale()])->with('notice', 'You have not been placed in a validation cohort yet.');
        }
        $issues = $workflowInput->paginate($member->issueReports()->latest(), 15);
        return \App\Contexts\Shared\Application\WorkflowResult::read('practitioner_validation_issuereportcontroller_index_2', compact('issues', 'member'));
    }
    public function create(\App\Contexts\Shared\Application\WorkflowInput $workflowInput): \App\Contexts\Shared\Application\WorkflowResult
    {
        $member = $this->activeMember();
        if (!$member) {
            return \App\Contexts\Shared\Application\WorkflowResult::next('practitioner_validation_issuereportcontroller_create_1', ['locale' => $workflowInput->locale()])->with('notice', 'You have not been placed in a validation cohort yet.');
        }
        [$products, $modules, $workflows, $allowedWorkflowIds] = $this->scopedCatalog($member);
        $testCases = $workflowInput->collect(\App\Models\ValidationTestCase::whereIn('validation_workflow_id', $allowedWorkflowIds));
        return \App\Contexts\Shared\Application\WorkflowResult::read('practitioner_validation_issuereportcontroller_create_2', compact('products', 'modules', 'workflows', 'testCases', 'member'));
    }
    public function show(\App\Contexts\Shared\Application\WorkflowInput $workflowInput, $locale, \App\Models\IssueReport $issue): \App\Contexts\Shared\Application\WorkflowResult
    {
        $issue->load('clinicalReview', 'productReview', 'cohortMember', 'product', 'module', 'workflow', 'testCase', 'retests', 'developerTask');
        abort_unless($issue->cohortMember->user_id === \App\Contexts\Shared\Application\CurrentWorkflow::id(), 403);
        $latestNote = $issue->productReview?->notes ?? $issue->clinicalReview?->notes;
        return \App\Contexts\Shared\Application\WorkflowResult::read('practitioner_validation_issuereportcontroller_show_1', compact('issue', 'latestNote'));
    }
}

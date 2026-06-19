<?php

namespace App\Http\Controllers\Practitioner\Validation;

use App\Http\Controllers\Controller;
use App\Models\IssueReport;
use App\Models\ValidationTestCase;
use Illuminate\Http\Request;

class IssueReportController extends Controller
{
    use ResolvesCohortScope;

    public function index()
    {
        $member = $this->activeMember();
        if (! $member) {
            return redirect()->route('practitioner.validation.dashboard', ['locale' => app()->getLocale()])
                ->with('notice', 'You have not been placed in a validation cohort yet.');
        }

        $issues = $member->issueReports()->latest()->paginate(15);

        return view('practitioner.validation.issues.index', compact('issues', 'member'));
    }

    public function create()
    {
        $member = $this->activeMember();
        if (! $member) {
            return redirect()->route('practitioner.validation.dashboard', ['locale' => app()->getLocale()])
                ->with('notice', 'You have not been placed in a validation cohort yet.');
        }

        [$products, $modules, $workflows, $allowedWorkflowIds] = $this->scopedCatalog($member);
        $testCases = ValidationTestCase::whereIn('validation_workflow_id', $allowedWorkflowIds)->get();

        return view('practitioner.validation.issues.create', compact('products', 'modules', 'workflows', 'testCases', 'member'));
    }

    public function store(Request $request)
    {
        $member = $this->activeMember();
        if (! $member) {
            return redirect()->route('practitioner.validation.dashboard', ['locale' => app()->getLocale()])
                ->with('notice', 'You have not been placed in a validation cohort yet.');
        }

        $validated = $request->validate([
            'validation_product_id'   => 'required|exists:validation_products,id',
            'validation_module_id'    => 'required|exists:validation_modules,id',
            'validation_workflow_id'  => 'required|exists:validation_workflows,id',
            'validation_test_case_id' => 'nullable|exists:validation_test_cases,id',
            'daily_test_session_id'   => 'nullable|exists:daily_test_sessions,id',
            'title'                   => 'required|string|max:200',
            'issue_type'              => 'required|in:bug,missing_feature,workflow_problem,clinical_risk,ui_ux_problem,performance_issue,security_concern,interoperability_issue,data_quality_issue,recommendation',
            'severity'                => 'required|in:critical,high,medium,low',
            'description'             => 'required|string|max:5000',
            'steps_to_reproduce'      => 'required|string|max:5000',
            'expected_result'         => 'required|string|max:2000',
            'actual_result'           => 'required|string|max:2000',
            'clinical_impact'         => 'required|string|max:2000',
            'recommendation'          => 'nullable|string|max:2000',
            'attachments.*'           => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10240',
        ]);

        [, , , $allowedWorkflowIds] = $this->scopedCatalog($member);
        abort_unless(
            $allowedWorkflowIds->map(fn ($id) => (int) $id)->contains((int) $validated['validation_workflow_id']),
            422,
            'Workflow not in cohort scope.'
        );

        // Derive product/module authoritatively from the in-scope workflow.
        $workflow = \App\Models\ValidationWorkflow::with('module')->findOrFail($validated['validation_workflow_id']);
        $validated['validation_workflow_id'] = $workflow->id;
        $validated['validation_module_id']   = $workflow->validation_module_id;
        $validated['validation_product_id']  = $workflow->module->validation_product_id;

        // An optional test case must belong to a workflow in the cohort scope.
        if (! empty($validated['validation_test_case_id'])) {
            abort_unless(
                ValidationTestCase::whereKey($validated['validation_test_case_id'])
                    ->whereIn('validation_workflow_id', $allowedWorkflowIds)
                    ->exists(),
                422,
                'Test case not in cohort scope.'
            );
        }

        // An optional linked session must belong to this practitioner's own cohort membership.
        if (! empty($validated['daily_test_session_id'])) {
            abort_unless(
                $member->dailyTestSessions()->whereKey($validated['daily_test_session_id'])->exists(),
                422,
                'Session does not belong to you.'
            );
        }

        $paths = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $paths[] = $file->store('validation/issues', 'public');
            }
        }

        IssueReport::create(array_merge($validated, [
            'cohort_member_id' => $member->id,
            'attachments'      => $paths ?: null,
            'status'           => 'submitted',
        ]));

        return redirect()->route('practitioner.validation.issues.index', ['locale' => app()->getLocale()])
            ->with('success', 'Issue report submitted.');
    }

    public function show($locale, IssueReport $issue)
    {
        $issue->load('clinicalReview', 'productReview', 'cohortMember', 'product', 'module', 'workflow', 'testCase');
        abort_unless($issue->cohortMember->user_id === auth()->id(), 403);

        $latestNote = $issue->productReview?->notes ?? $issue->clinicalReview?->notes;

        return view('practitioner.validation.issues.show', compact('issue', 'latestNote'));
    }
}

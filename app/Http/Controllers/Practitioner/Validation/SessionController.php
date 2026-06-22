<?php

namespace App\Http\Controllers\Practitioner\Validation;

use App\Http\Controllers\Controller;
use App\Models\DailyTestSession;
use Illuminate\Http\Request;

class SessionController extends Controller
{
    use ResolvesCohortScope;

    public function index()
    {
        $member = $this->activeMember();
        if (! $member) {
            return redirect()->route('practitioner.validation.dashboard', ['locale' => app()->getLocale()])
                ->with('notice', 'You have not been placed in a validation cohort yet.');
        }

        $sessions = $member->dailyTestSessions()->latest('date')->paginate(15);

        return view('practitioner.validation.sessions.index', compact('sessions', 'member'));
    }

    public function create()
    {
        $member = $this->activeMember();
        if (! $member) {
            return redirect()->route('practitioner.validation.dashboard', ['locale' => app()->getLocale()])
                ->with('notice', 'You have not been placed in a validation cohort yet.');
        }

        [$products, $modules, $workflows] = $this->scopedCatalog($member);

        return view('practitioner.validation.sessions.create', compact('products', 'modules', 'workflows', 'member'));
    }

    public function store(Request $request)
    {
        $member = $this->activeMember();
        if (! $member) {
            return redirect()->route('practitioner.validation.dashboard', ['locale' => app()->getLocale()])
                ->with('notice', 'You have not been placed in a validation cohort yet.');
        }

        $validated = $request->validate([
            'validation_product_id'  => 'required|exists:validation_products,id',
            'validation_module_id'   => 'required|exists:validation_modules,id',
            'validation_workflow_id' => 'required|exists:validation_workflows,id',
            'facility_context'       => 'nullable|string|max:200',
            'date'                   => 'required|date|before_or_equal:'.now('Africa/Douala')->toDateString(),
            'start_time'             => 'nullable|date_format:H:i',
            'end_time'               => 'nullable|date_format:H:i|after:start_time',
            'tasks_completed'        => 'required|integer|min:0|max:999',
            'comments'               => 'nullable|string|max:3000',
            'screenshots.*'          => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        [, , , $allowedWorkflowIds] = $this->scopedCatalog($member);
        abort_unless(
            $allowedWorkflowIds->map(fn ($id) => (int) $id)->contains((int) $validated['validation_workflow_id']),
            422,
            'Workflow not in cohort scope.'
        );

        // Derive product/module authoritatively from the in-scope workflow so a
        // mismatched (but individually valid) product/module cannot be persisted.
        $workflow = \App\Models\ValidationWorkflow::with('module')->findOrFail($validated['validation_workflow_id']);

        $paths = [];
        if ($request->hasFile('screenshots')) {
            foreach ($request->file('screenshots') as $file) {
                $paths[] = $file->store('validation/sessions', 'public');
            }
        }

        DailyTestSession::create([
            'cohort_member_id'       => $member->id,
            'validation_product_id'  => $workflow->module->validation_product_id,
            'validation_module_id'   => $workflow->validation_module_id,
            'validation_workflow_id' => $workflow->id,
            'facility_context'       => $validated['facility_context'] ?? null,
            'date'                   => $validated['date'],
            'start_time'             => $validated['start_time'] ?? null,
            'end_time'               => $validated['end_time'] ?? null,
            'tasks_completed'        => $validated['tasks_completed'],
            'screenshots'            => $paths ?: null,
            'comments'               => $validated['comments'] ?? null,
        ]);

        return redirect()->route('practitioner.validation.sessions.index', ['locale' => app()->getLocale()])
            ->with('success', 'Daily test session recorded.');
    }
}

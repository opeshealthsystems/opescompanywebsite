<?php
namespace App\Http\Controllers\Practitioner;

use App\Filament\Resources\PractitionerBugReportResource;
use App\Http\Controllers\Controller;
use App\Models\PractitionerBugReport;
use App\Support\AdminNotifier;
use Illuminate\Http\Request;

class BugReportController extends Controller
{
    public function index()
    {
        $bugReports = auth()->user()->practitionerBugReports()->latest()->get();
        return view('practitioner.bug-reports.index', compact('bugReports'));
    }

    public function create()
    {
        $severityOptions = PractitionerBugReport::severityOptions();
        return view('practitioner.bug-reports.create', compact('severityOptions'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'              => 'required|string|max:200',
            'severity'           => 'required|string|in:' . implode(',', array_keys(PractitionerBugReport::severityOptions())),
            'description'        => 'required|string|min:10',
            'steps_to_reproduce' => 'nullable|string',
            'screenshot_url'     => 'nullable|url',
            'screenshot'         => 'nullable|image|max:4096',
            'product_slug'       => 'nullable|string|max:100',
        ]);

        unset($data['screenshot']);

        if ($request->hasFile('screenshot')) {
            $data['screenshot_path'] = $request->file('screenshot')->store('bug-report-screenshots', 'public');
        }

        $bugReport = auth()->user()->practitionerBugReports()->create($data);

        AdminNotifier::notify(
            'New bug report',
            '[' . $bugReport->severity . '] ' . $bugReport->title,
            PractitionerBugReportResource::getUrl('view', ['record' => $bugReport]),
            ['super_admin', 'admin', 'support'],
        );

        return redirect()
            ->route('practitioner.bug-reports', ['locale' => app()->getLocale()])
            ->with('success', 'Your bug report has been submitted. Thank you!');
    }

    public function show($locale, PractitionerBugReport $bugReport)
    {
        abort_unless($bugReport->practitioner_id === auth()->id(), 403);
        return view('practitioner.bug-reports.show', compact('bugReport'));
    }
}

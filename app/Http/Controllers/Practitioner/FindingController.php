<?php
namespace App\Http\Controllers\Practitioner;

use App\Http\Controllers\Controller;
use App\Models\PractitionerApplication;
use App\Models\PractitionerFinding;
use Illuminate\Http\Request;

class FindingController extends Controller
{
    public function create($locale, PractitionerApplication $application)
    {
        abort_unless($application->practitioner_id === auth()->id(), 403);
        if (! auth()->user()->isVerifiedPractitioner()) {
            abort(403, 'Your practitioner profile must be verified before submitting findings.');
        }
        abort_unless($application->status === 'approved', 403, 'Only approved applications can submit findings.');
        return view('practitioner.findings.create', compact('application'));
    }

    public function store(Request $request, $locale, PractitionerApplication $application)
    {
        abort_unless($application->practitioner_id === auth()->id(), 403);
        if (! auth()->user()->isVerifiedPractitioner()) {
            abort(403, 'Your practitioner profile must be verified before submitting findings.');
        }
        abort_unless($application->status === 'approved', 403);

        $validated = $request->validate([
            'overall_rating'        => 'nullable|integer|min:1|max:5',
            'wait_time_rating'      => 'nullable|integer|min:1|max:5',
            'data_integrity_rating' => 'nullable|integer|min:1|max:5',
            'usability_rating'      => 'nullable|integer|min:1|max:5',
            'findings_text'         => 'nullable|string|max:5000',
            'video_url'             => 'nullable|url|max:500',
        ]);

        PractitionerFinding::create([
            'application_id'        => $application->id,
            'practitioner_id'       => auth()->id(),
            'overall_rating'        => $validated['overall_rating'] ?? null,
            'wait_time_rating'      => $validated['wait_time_rating'] ?? null,
            'data_integrity_rating' => $validated['data_integrity_rating'] ?? null,
            'usability_rating'      => $validated['usability_rating'] ?? null,
            'findings_text'         => $validated['findings_text'] ?? null,
            'video_url'             => $validated['video_url'] ?? null,
            'is_published'          => false,
        ]);

        return redirect()
            ->route('practitioner.applications.show', ['locale' => app()->getLocale(), 'application' => $application->id])
            ->with('success', 'Your findings have been submitted. Thank you!');
    }
}

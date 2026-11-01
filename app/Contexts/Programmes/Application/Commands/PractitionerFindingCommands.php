<?php

namespace App\Contexts\Programmes\Application\Commands;

use App\Filament\Resources\PractitionerFindingResource;
use App\Models\PractitionerApplication;
use App\Models\PractitionerFinding;
use App\Support\AdminNotifier;
class PractitionerFindingCommands extends \App\Contexts\Shared\Application\WorkflowHandler
{
    public function store(\App\Contexts\Shared\Application\WorkflowInput $request, $locale, \App\Models\PractitionerApplication $application): \App\Contexts\Shared\Application\WorkflowResult
    {
        $this->authorize('view', $application);
        if (!\App\Contexts\Shared\Application\CurrentWorkflow::actor()->isVerifiedPractitioner()) {
            abort(403, 'Your practitioner profile must be verified before submitting findings.');
        }
        abort_unless($application->status === 'approved', 403);
        $validated = $request->validate(['overall_rating' => 'nullable|integer|min:1|max:5', 'wait_time_rating' => 'nullable|integer|min:1|max:5', 'data_integrity_rating' => 'nullable|integer|min:1|max:5', 'usability_rating' => 'nullable|integer|min:1|max:5', 'findings_text' => 'nullable|string|max:5000', 'video_url' => ['nullable', 'url', 'max:500', 'regex:~^https?://(?:www\.|player\.)?(?:youtube\.com/|youtu\.be/|vimeo\.com/)~i'], 'screenshot' => 'nullable|image|max:4096'], ['video_url.regex' => 'The video URL must be a YouTube or Vimeo link.']);
        $screenshotPath = null;
        if ($request->hasFile('screenshot')) {
            $screenshotPath = $request->file('screenshot')->store('finding-screenshots', 'public');
        }
        $finding = \App\Models\PractitionerFinding::create(['application_id' => $application->id, 'practitioner_id' => \App\Contexts\Shared\Application\CurrentWorkflow::id(), 'overall_rating' => $validated['overall_rating'] ?? null, 'wait_time_rating' => $validated['wait_time_rating'] ?? null, 'data_integrity_rating' => $validated['data_integrity_rating'] ?? null, 'usability_rating' => $validated['usability_rating'] ?? null, 'findings_text' => $validated['findings_text'] ?? null, 'video_url' => $validated['video_url'] ?? null, 'screenshot_path' => $screenshotPath, 'is_published' => false]);
        \App\Support\AdminNotifier::notify('New practitioner findings', \App\Contexts\Shared\Application\CurrentWorkflow::actor()->name . ' submitted findings for: ' . $application->program->title, \App\Filament\Resources\PractitionerFindingResource::getUrl('view', ['record' => $finding]));
        return \App\Contexts\Shared\Application\WorkflowResult::next('practitioner_findingcontroller_store_1', ['locale' => $request->locale(), 'application' => $application->id])->with('success', 'Your findings have been submitted. Thank you!');
    }
}

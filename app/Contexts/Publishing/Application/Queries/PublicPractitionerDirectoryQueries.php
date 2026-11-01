<?php

namespace App\Contexts\Publishing\Application\Queries;

use App\Models\PractitionerApplication;
use App\Models\PractitionerFinding;
use App\Models\PractitionerProfile;
class PublicPractitionerDirectoryQueries extends \App\Contexts\Shared\Application\WorkflowHandler
{
    public function index(\App\Contexts\Shared\Application\WorkflowInput $request): \App\Contexts\Shared\Application\WorkflowResult
    {
        $approvedIds = \App\Models\PractitionerApplication::where('status', 'approved')->pluck('practitioner_id')->unique()->values();
        $query = \App\Models\PractitionerProfile::whereIn('user_id', $approvedIds)->whereHas('user', fn($q) => $q->where('email', 'not like', '%@demo.opes')->where('email', 'not like', '%.test'))->with(['user', 'user.practitionerProfile'])->orderByDesc('is_verified')->orderByDesc('years_of_experience');
        if ($request->filled('profession')) {
            $query->where('profession', $request->profession);
        }
        if ($request->filled('country')) {
            $query->where('workplace_country', $request->country);
        }
        $practitioners = $request->paginate($query, 12);
        $ids = $practitioners->pluck('user_id');
        $findingStats = $request->collect(\App\Models\PractitionerFinding::whereIn('practitioner_id', $ids)->where('is_published', true)->selectRaw('practitioner_id, COUNT(*) as findings_count, AVG(overall_rating) as avg_rating')->groupBy('practitioner_id'))->keyBy('practitioner_id');
        $programStats = $request->collect(\App\Models\PractitionerApplication::whereIn('practitioner_id', $ids)->where('status', 'approved')->selectRaw('practitioner_id, COUNT(*) as programs_count')->groupBy('practitioner_id'))->keyBy('practitioner_id');
        $stats = [];
        foreach ($ids as $uid) {
            $fs = $request->collect($findingStats, $uid);
            $ps = $request->collect($programStats, $uid);
            $avgRating = $fs?->avg_rating ? round((float) $fs->avg_rating, 1) : null;
            $stats[$uid] = ['programs' => $ps?->programs_count ?? 0, 'findings' => $fs?->findings_count ?? 0, 'avgRating' => $avgRating];
        }
        $professions = \App\Models\PractitionerProfile::professionOptions();
        $countries = \App\Models\PractitionerProfile::whereIn('user_id', $approvedIds)->whereNotNull('workplace_country')->distinct()->orderBy('workplace_country')->pluck('workplace_country');
        return \App\Contexts\Shared\Application\WorkflowResult::read('public_practitionerdirectorycontroller_index_1', compact('practitioners', 'stats', 'professions', 'countries'));
    }
    public function show(\App\Contexts\Shared\Application\WorkflowInput $workflowInput, string $locale, int $id): \App\Contexts\Shared\Application\WorkflowResult
    {
        $profile = \App\Models\PractitionerProfile::where('user_id', $id)->whereHas('user', fn($q) => $q->where('email', 'not like', '%@demo.opes')->where('email', 'not like', '%.test'))->whereHas('user.practitionerApplications', fn($q) => $q->where('status', 'approved'))->with('user')->firstOrFail();
        $approvedApplications = $workflowInput->collect(\App\Models\PractitionerApplication::where('practitioner_id', $id)->where('status', 'approved')->with('program')->orderByDesc('reviewed_at'));
        $publishedFindings = $workflowInput->collect(\App\Models\PractitionerFinding::where('practitioner_id', $id)->where('is_published', true)->with('application.program')->orderByDesc('created_at')->take(10));
        $ratingRow = \App\Models\PractitionerFinding::where('practitioner_id', $id)->where('is_published', true)->selectRaw('AVG(overall_rating) as overall, AVG(usability_rating) as usability, AVG(wait_time_rating) as wait_time, AVG(data_integrity_rating) as data_integrity')->first();
        $ratingBreakdown = ['overall' => $ratingRow?->overall ? round((float) $ratingRow->overall, 1) : null, 'usability' => $ratingRow?->usability ? round((float) $ratingRow->usability, 1) : null, 'wait_time' => $ratingRow?->wait_time ? round((float) $ratingRow->wait_time, 1) : null, 'data_integrity' => $ratingRow?->data_integrity ? round((float) $ratingRow->data_integrity, 1) : null];
        return \App\Contexts\Shared\Application\WorkflowResult::read('public_practitionerdirectorycontroller_show_1', compact('profile', 'approvedApplications', 'publishedFindings', 'ratingBreakdown'));
    }
}

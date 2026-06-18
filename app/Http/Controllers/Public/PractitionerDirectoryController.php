<?php
namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\PractitionerApplication;
use App\Models\PractitionerFinding;
use App\Models\PractitionerProfile;
use Illuminate\Http\Request;

class PractitionerDirectoryController extends Controller
{
    public function index(Request $request)
    {
        $approvedIds = PractitionerApplication::where('status', 'approved')
            ->pluck('practitioner_id')
            ->unique()
            ->values();

        $query = PractitionerProfile::whereIn('user_id', $approvedIds)
            ->with(['user', 'user.practitionerProfile'])
            ->orderByDesc('is_verified')
            ->orderByDesc('years_of_experience');

        if ($request->filled('profession')) {
            $query->where('profession', $request->profession);
        }
        if ($request->filled('country')) {
            $query->where('workplace_country', $request->country);
        }

        $practitioners = $query->paginate(12)->withQueryString();

        $ids = $practitioners->pluck('user_id');

        $findingStats = \App\Models\PractitionerFinding::whereIn('practitioner_id', $ids)
            ->where('is_published', true)
            ->selectRaw('practitioner_id, COUNT(*) as findings_count, AVG(overall_rating) as avg_rating')
            ->groupBy('practitioner_id')
            ->get()
            ->keyBy('practitioner_id');

        $programStats = \App\Models\PractitionerApplication::whereIn('practitioner_id', $ids)
            ->where('status', 'approved')
            ->selectRaw('practitioner_id, COUNT(*) as programs_count')
            ->groupBy('practitioner_id')
            ->get()
            ->keyBy('practitioner_id');

        $stats = [];
        foreach ($ids as $uid) {
            $fs = $findingStats->get($uid);
            $ps = $programStats->get($uid);
            $avgRating = $fs?->avg_rating ? round((float) $fs->avg_rating, 1) : null;
            $stats[$uid] = [
                'programs'  => $ps?->programs_count ?? 0,
                'findings'  => $fs?->findings_count ?? 0,
                'avgRating' => $avgRating,
            ];
        }

        $professions = PractitionerProfile::professionOptions();
        $countries   = PractitionerProfile::whereIn('user_id', $approvedIds)
            ->whereNotNull('workplace_country')
            ->distinct()
            ->orderBy('workplace_country')
            ->pluck('workplace_country');

        return view('pages.practitioners.index', compact('practitioners', 'stats', 'professions', 'countries'));
    }

    public function show(string $locale, int $id)
    {
        $profile = PractitionerProfile::where('user_id', $id)
            ->whereHas('user.practitionerApplications', fn ($q) => $q->where('status', 'approved'))
            ->with('user')
            ->firstOrFail();

        $approvedApplications = PractitionerApplication::where('practitioner_id', $id)
            ->where('status', 'approved')
            ->with('program')
            ->orderByDesc('reviewed_at')
            ->get();

        $publishedFindings = PractitionerFinding::where('practitioner_id', $id)
            ->where('is_published', true)
            ->with('application.program')
            ->orderByDesc('created_at')
            ->take(10)
            ->get();

        $ratingRow = PractitionerFinding::where('practitioner_id', $id)
            ->where('is_published', true)
            ->selectRaw('AVG(overall_rating) as overall, AVG(usability_rating) as usability, AVG(wait_time_rating) as wait_time, AVG(data_integrity_rating) as data_integrity')
            ->first();

        $ratingBreakdown = [
            'overall'        => $ratingRow?->overall ? round((float) $ratingRow->overall, 1) : null,
            'usability'      => $ratingRow?->usability ? round((float) $ratingRow->usability, 1) : null,
            'wait_time'      => $ratingRow?->wait_time ? round((float) $ratingRow->wait_time, 1) : null,
            'data_integrity' => $ratingRow?->data_integrity ? round((float) $ratingRow->data_integrity, 1) : null,
        ];

        return view('pages.practitioners.show', compact(
            'profile', 'approvedApplications', 'publishedFindings', 'ratingBreakdown'
        ));
    }
}

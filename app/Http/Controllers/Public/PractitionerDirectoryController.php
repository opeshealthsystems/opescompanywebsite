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
            ->with('user')
            ->orderByDesc('is_verified')
            ->orderByDesc('years_of_experience');

        if ($request->filled('profession')) {
            $query->where('profession', $request->profession);
        }
        if ($request->filled('country')) {
            $query->where('workplace_country', $request->country);
        }

        $practitioners = $query->paginate(12)->withQueryString();

        $stats = [];
        foreach ($practitioners as $profile) {
            $stats[$profile->user_id] = [
                'programs'  => PractitionerApplication::where('practitioner_id', $profile->user_id)->where('status', 'approved')->count(),
                'findings'  => PractitionerFinding::where('practitioner_id', $profile->user_id)->where('is_published', true)->count(),
                'avgRating' => PractitionerFinding::where('practitioner_id', $profile->user_id)->whereNotNull('overall_rating')->avg('overall_rating'),
            ];
            if ($stats[$profile->user_id]['avgRating']) {
                $stats[$profile->user_id]['avgRating'] = round($stats[$profile->user_id]['avgRating'], 1);
            }
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

        $ratingBreakdown = [
            'overall'        => $publishedFindings->avg('overall_rating'),
            'usability'      => $publishedFindings->avg('usability_rating'),
            'wait_time'      => $publishedFindings->avg('wait_time_rating'),
            'data_integrity' => $publishedFindings->avg('data_integrity_rating'),
        ];

        return view('pages.practitioners.show', compact(
            'profile', 'approvedApplications', 'publishedFindings', 'ratingBreakdown'
        ));
    }
}

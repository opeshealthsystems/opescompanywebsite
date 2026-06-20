<?php
namespace App\Http\Controllers\Practitioner;

use App\Http\Controllers\Controller;
use App\Models\PractitionerApplication;
use App\Models\PractitionerFinding;
use App\Models\PractitionerProgram;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user()->load('practitionerProfile');
        $profile = $user->practitionerProfile;

        $activeApplications = PractitionerApplication::where('practitioner_id', $user->id)
            ->whereIn('status', ['pending', 'approved'])
            ->count();

        $totalFindings = PractitionerFinding::where('practitioner_id', $user->id)->count();

        $avgRating = PractitionerFinding::where('practitioner_id', $user->id)
            ->whereNotNull('overall')
            ->avg('overall');

        $recentFindings = PractitionerFinding::where('practitioner_id', $user->id)
            ->with('application.program')
            ->latest()
            ->take(3)
            ->get();

        $payoutApps = PractitionerApplication::where('practitioner_id', $user->id)
            ->where(fn ($q) => $q->where('payout_status', '!=', 'not_applicable')->orWhere('status', 'approved'))
            ->with('program')
            ->latest()
            ->take(5)
            ->get();

        $openPrograms = PractitionerProgram::where('status', 'open')
            ->where(fn ($q) => $q->whereNull('end_date')->orWhere('end_date', '>=', now()))
            ->latest()
            ->take(3)
            ->get();

        return view('practitioner.dashboard', compact(
            'user', 'profile', 'activeApplications', 'totalFindings',
            'avgRating', 'recentFindings', 'payoutApps', 'openPrograms'
        ));
    }
}

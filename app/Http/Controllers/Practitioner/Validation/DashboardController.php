<?php

namespace App\Http\Controllers\Practitioner\Validation;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function show()
    {
        $member = auth()->user()->cohortMembers()
            ->where('status', 'active')
            ->with('cohort.practitionerProgram')
            ->latest('placed_at')
            ->first();

        $stats = $member ? [
            'sessions' => $member->dailyTestSessions()->count(),
            'issues'   => $member->issueReports()->count(),
            'open'     => $member->issueReports()->whereNotIn('status', ['closed', 'rejected', 'duplicate'])->count(),
            'closed'   => $member->issueReports()->where('status', 'closed')->count(),
        ] : null;

        return view('practitioner.validation.dashboard', [
            'cohortMember' => $member,
            'stats'        => $stats,
        ]);
    }
}

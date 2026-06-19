<?php

namespace App\Support;

use App\Models\Cohort;
use App\Models\CohortMember;
use App\Models\CohortTestCase;
use App\Models\DailyTestSession;
use App\Models\DeveloperTask;
use App\Models\IssueReport;
use App\Models\Retest;
use Carbon\CarbonInterface;

class ValidationMetrics
{
    /** Issue statuses counted as "accepted" practitioner contributions. */
    private const ACCEPTED_STATUSES = ['accepted', 'closed', 'sent_to_development', 'fixed', 'retest_passed'];

    public function cohortProgress(?Cohort $cohort = null): array
    {
        $cohorts = $cohort ? collect([$cohort]) : Cohort::all();

        return $cohorts->map(function (Cohort $c) {
            $memberIds = $c->members()->pluck('id');
            $assigned  = $c->cohortTestCases()->count();

            $sessionWorkflowIds = DailyTestSession::whereIn('cohort_member_id', $memberIds)
                ->distinct()->pluck('validation_workflow_id');

            $covered = CohortTestCase::where('cohort_id', $c->id)
                ->join('validation_test_cases', 'cohort_test_cases.validation_test_case_id', '=', 'validation_test_cases.id')
                ->whereIn('validation_test_cases.validation_workflow_id', $sessionWorkflowIds)
                ->distinct('validation_test_cases.id')
                ->count('validation_test_cases.id');

            return [
                'cohort_id'           => $c->id,
                'name'                => $c->name,
                'status'              => $c->status,
                'active_members'      => $c->members()->where('status', 'active')->count(),
                'sessions'            => DailyTestSession::whereIn('cohort_member_id', $memberIds)->count(),
                'assigned_test_cases' => $assigned,
                'covered_test_cases'  => $covered,
                'coverage_pct'        => $assigned > 0 ? (int) round($covered / $assigned * 100) : 0,
                'issues'              => IssueReport::whereIn('cohort_member_id', $memberIds)->count(),
            ];
        })->values()->all();
    }

    public function issueAnalytics(?Cohort $cohort = null): array
    {
        $memberIds = $cohort ? $cohort->members()->pluck('id') : null;

        $issues = IssueReport::query()
            ->when($memberIds, fn ($q) => $q->whereIn('cohort_member_id', $memberIds))
            ->get();

        $byStatus = [];
        foreach (array_keys(IssueReport::statusOptions()) as $s) {
            $byStatus[$s] = $issues->where('status', $s)->count();
        }
        $bySeverity = [];
        foreach (array_keys(IssueReport::severityOptions()) as $s) {
            $bySeverity[$s] = $issues->where('severity', $s)->count();
        }
        $byType = [];
        foreach (array_keys(IssueReport::issueTypeOptions()) as $t) {
            $byType[$t] = $issues->where('issue_type', $t)->count();
        }

        $retests = Retest::query()
            ->when($memberIds, fn ($q) => $q->whereIn('cohort_member_id', $memberIds))
            ->get();
        $passRate = $retests->count() > 0
            ? (int) round($retests->where('result', 'passed')->count() / $retests->count() * 100)
            : 0;

        $closed = $issues->where('status', 'closed');
        $avgDaysToClose = $closed->count() > 0
            ? round($closed->avg(fn (IssueReport $i) => $i->created_at->diffInDays($i->updated_at)), 1)
            : 0;

        return [
            'total'             => $issues->count(),
            'by_status'         => $byStatus,
            'by_severity'       => $bySeverity,
            'by_type'           => $byType,
            'retest_pass_rate'  => $passRate,
            'avg_days_to_close' => $avgDaysToClose,
        ];
    }

    public function developerThroughput(): array
    {
        $tasks = DeveloperTask::with('assignedTo')->get();

        $byStatus = [];
        foreach (array_keys(DeveloperTask::statusOptions()) as $s) {
            $byStatus[$s] = $tasks->where('status', $s)->count();
        }
        $reopenedRate = $tasks->count() > 0
            ? (int) round($tasks->where('status', 'reopened')->count() / $tasks->count() * 100)
            : 0;

        $byAssignee = $tasks
            ->groupBy(fn (DeveloperTask $t) => $t->assignedTo?->name ?? 'Unassigned')
            ->map(fn ($group, $name) => ['name' => $name, 'count' => $group->count()])
            ->values()->all();

        $fixed = $tasks->whereNotNull('fixed_at');
        $avgDaysToFix = $fixed->count() > 0
            ? round($fixed->avg(fn (DeveloperTask $t) => $t->created_at->diffInDays($t->fixed_at)), 1)
            : 0;

        return [
            'total'           => $tasks->count(),
            'by_status'       => $byStatus,
            'reopened_rate'   => $reopenedRate,
            'by_assignee'     => $byAssignee,
            'avg_days_to_fix' => $avgDaysToFix,
        ];
    }

    public function practitionerContribution(CohortMember $member): array
    {
        return [
            'sessions'        => $member->dailyTestSessions()->count(),
            'issues_found'    => $member->issueReports()->count(),
            'issues_accepted' => $member->issueReports()->whereIn('status', self::ACCEPTED_STATUSES)->count(),
            'retests'         => Retest::where('cohort_member_id', $member->id)->count(),
        ];
    }

    public function practitionerLeaderboard(?Cohort $cohort = null): array
    {
        $members = CohortMember::query()
            ->with(['user', 'cohort'])
            ->when($cohort, fn ($q) => $q->where('cohort_id', $cohort->id))
            ->get();

        return $members->map(fn (CohortMember $m) => array_merge([
            'member' => $m->user?->name ?? '—',
            'cohort' => $m->cohort?->name ?? '—',
        ], $this->practitionerContribution($m)))
            ->sortByDesc('issues_found')
            ->values()->all();
    }

    public function weeklySnapshot(Cohort $cohort, CarbonInterface $weekStart): array
    {
        $start = $weekStart->copy()->startOfDay();
        $end   = $weekStart->copy()->addDays(6)->endOfDay();
        $memberIds = $cohort->members()->pluck('id');

        $sessions = DailyTestSession::whereIn('cohort_member_id', $memberIds)
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])->count();

        $issues = IssueReport::whereIn('cohort_member_id', $memberIds)
            ->whereBetween('created_at', [$start, $end])->get();
        $issuesBySeverity = [];
        foreach (array_keys(IssueReport::severityOptions()) as $s) {
            $issuesBySeverity[$s] = $issues->where('severity', $s)->count();
        }

        $retests = Retest::whereIn('cohort_member_id', $memberIds)
            ->whereBetween('retested_at', [$start, $end])->get();

        $devOpened = DeveloperTask::whereHas('issueReport', fn ($q) => $q->whereIn('cohort_member_id', $memberIds))
            ->whereBetween('created_at', [$start, $end])->count();
        $devFixed = DeveloperTask::whereHas('issueReport', fn ($q) => $q->whereIn('cohort_member_id', $memberIds))
            ->whereBetween('fixed_at', [$start, $end])->count();

        return [
            'week_start'         => $start->toDateString(),
            'week_end'           => $end->toDateString(),
            'sessions'           => $sessions,
            'issues_submitted'   => $issues->count(),
            'issues_by_severity' => $issuesBySeverity,
            'retests_passed'     => $retests->where('result', 'passed')->count(),
            'retests_failed'     => $retests->where('result', 'failed')->count(),
            'dev_tasks_opened'   => $devOpened,
            'dev_tasks_fixed'    => $devFixed,
        ];
    }

    public function memberContributionSnapshot(CohortMember $member): array
    {
        $member->loadMissing(['user', 'cohort']);
        return array_merge($this->practitionerContribution($member), [
            'member_name' => $member->user?->name ?? '—',
            'cohort_name' => $member->cohort?->name ?? '—',
            'as_of'       => now()->toDateString(),
        ]);
    }
}

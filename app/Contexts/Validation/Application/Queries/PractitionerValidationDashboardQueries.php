<?php

namespace App\Contexts\Validation\Application\Queries;

class PractitionerValidationDashboardQueries extends \App\Contexts\Shared\Application\WorkflowHandler
{
    public function show(\App\Contexts\Shared\Application\WorkflowInput $workflowInput): \App\Contexts\Shared\Application\WorkflowResult
    {
        $member = \App\Contexts\Shared\Application\CurrentWorkflow::actor()->cohortMembers()->where('status', 'active')->with('cohort.practitionerProgram')->latest('placed_at')->first();
        $stats = $member ? ['sessions' => $member->dailyTestSessions()->count(), 'issues' => $member->issueReports()->count(), 'open' => $member->issueReports()->whereNotIn('status', ['closed', 'rejected', 'duplicate'])->count(), 'closed' => $member->issueReports()->where('status', 'closed')->count()] : null;
        return \App\Contexts\Shared\Application\WorkflowResult::read('practitioner_validation_dashboardcontroller_show_1', ['cohortMember' => $member, 'stats' => $stats]);
    }
}

<?php

namespace App\Http\Controllers\Practitioner\Validation;

use App\Models\CohortTestCase;
use App\Models\ValidationModule;
use App\Models\ValidationProduct;
use App\Models\ValidationWorkflow;

trait ResolvesCohortScope
{
    /** Active CohortMember for the current user, or null. */
    protected function activeMember()
    {
        return auth()->user()->cohortMembers()
            ->where('status', 'active')
            ->latest('placed_at')
            ->first();
    }

    /**
     * Returns [Collection $products, Collection $modules, Collection $workflows, Collection $allowedWorkflowIds]
     * limited to what the member's cohort has assigned via cohort_test_cases.
     */
    protected function scopedCatalog($member): array
    {
        $allowedWorkflowIds = CohortTestCase::where('cohort_id', $member->cohort_id)
            ->join('validation_test_cases', 'cohort_test_cases.validation_test_case_id', '=', 'validation_test_cases.id')
            ->pluck('validation_test_cases.validation_workflow_id')
            ->unique()
            ->values();

        $workflows = ValidationWorkflow::whereIn('id', $allowedWorkflowIds)->get();
        $modules   = ValidationModule::whereIn('id', $workflows->pluck('validation_module_id')->unique())->get();
        $products  = ValidationProduct::whereIn('id', $modules->pluck('validation_product_id')->unique())->get();

        return [$products, $modules, $workflows, $allowedWorkflowIds];
    }
}

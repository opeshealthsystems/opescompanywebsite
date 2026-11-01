<?php

namespace App\Contexts\Publishing\Application\Queries;

use App\Models\PartnerInstitution;
class PartnershipsQueries extends \App\Contexts\Shared\Application\WorkflowHandler
{
    public function index(\App\Contexts\Shared\Application\WorkflowInput $workflowInput): \App\Contexts\Shared\Application\WorkflowResult
    {
        $partners = $workflowInput->collect(\App\Models\PartnerInstitution::featured()->orderBy('sort_order'));
        return \App\Contexts\Shared\Application\WorkflowResult::read('partnershipscontroller_index_1', compact('partners'));
    }
}

<?php

namespace App\Contexts\CustomerServices\Application\Queries;

use App\Filament\Resources\ServiceRequestResource;
use App\Models\ServiceRequest;
use App\Support\AdminNotifier;
class CustomerServiceRequestQueries extends \App\Contexts\Shared\Application\WorkflowHandler
{
    public function index(\App\Contexts\Shared\Application\WorkflowInput $workflowInput): \App\Contexts\Shared\Application\WorkflowResult
    {
        $serviceRequests = $workflowInput->collect(\App\Contexts\Shared\Application\CurrentWorkflow::actor()->serviceRequests()->latest());
        return \App\Contexts\Shared\Application\WorkflowResult::read('customer_servicerequestcontroller_index_1', compact('serviceRequests'));
    }
    public function create(\App\Contexts\Shared\Application\WorkflowInput $workflowInput): \App\Contexts\Shared\Application\WorkflowResult
    {
        $typeOptions = \App\Models\ServiceRequest::typeOptions();
        return \App\Contexts\Shared\Application\WorkflowResult::read('customer_servicerequestcontroller_create_1', compact('typeOptions'));
    }
    public function show(\App\Contexts\Shared\Application\WorkflowInput $workflowInput, $locale, \App\Models\ServiceRequest $serviceRequest): \App\Contexts\Shared\Application\WorkflowResult
    {
        abort_unless($serviceRequest->customer_id === \App\Contexts\Shared\Application\CurrentWorkflow::id(), 403);
        return \App\Contexts\Shared\Application\WorkflowResult::read('customer_servicerequestcontroller_show_1', compact('serviceRequest'));
    }
}

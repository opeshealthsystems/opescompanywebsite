<?php

namespace App\Contexts\CustomerServices\Application\Commands;

use App\Filament\Resources\ServiceRequestResource;
use App\Models\ServiceRequest;
use App\Support\AdminNotifier;
class CustomerServiceRequestCommands extends \App\Contexts\Shared\Application\WorkflowHandler
{
    public function store(\App\Contexts\Shared\Application\WorkflowInput $request): \App\Contexts\Shared\Application\WorkflowResult
    {
        $data = $request->validate(['type' => 'required|string|in:' . implode(',', array_keys(\App\Models\ServiceRequest::typeOptions())), 'product_slug' => 'nullable|string|max:100', 'description' => 'nullable|string|max:1000', 'preferred_date' => 'required|date|after:today', 'preferred_time' => 'nullable|string|max:10', 'location' => 'nullable|string|max:200']);
        $serviceRequest = \App\Contexts\Shared\Application\CurrentWorkflow::actor()->serviceRequests()->create($data);
        \App\Support\AdminNotifier::notify('New service request', $serviceRequest->reference_number . ' · ' . $serviceRequest->type, \App\Filament\Resources\ServiceRequestResource::getUrl('view', ['record' => $serviceRequest]), ['super_admin', 'admin', 'support']);
        return \App\Contexts\Shared\Application\WorkflowResult::next('customer_servicerequestcontroller_store_1', ['locale' => $request->locale()])->with('success', 'Your service request has been submitted. We will contact you to confirm.');
    }
}

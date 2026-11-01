<?php

namespace App\Contexts\Learning\Application\Queries;

class CustomerCertificateQueries extends \App\Contexts\Shared\Application\WorkflowHandler
{
    public function index(\App\Contexts\Shared\Application\WorkflowInput $workflowInput): \App\Contexts\Shared\Application\WorkflowResult
    {
        $certificates = $workflowInput->collect(\App\Contexts\Shared\Application\CurrentWorkflow::actor()->courseCertificates()->with('course')->latest('issued_at'));
        return \App\Contexts\Shared\Application\WorkflowResult::read('customer_certificatecontroller_index_1', compact('certificates'));
    }
}

<?php

namespace App\Contexts\Licensing\Application\Queries;

use App\Contexts\Shared\Application\{WorkflowInput, WorkflowResult};
use App\Models\License;

final class LicenseQueries
{
    public function index(WorkflowInput $input): WorkflowResult
    {
        $input->validate(['status' => 'sometimes|in:active,suspended,expired,cancelled']);
        return WorkflowResult::payload(['licenses' => $input->paginate(License::with('customer')->when($input->status, fn ($q) => $q->where('status', $input->status))->latest())]);
    }
    public function show(WorkflowInput $input, License $license): WorkflowResult
    {
        return WorkflowResult::payload(['license' => $license->load('customer', 'issuer')]);
    }
}

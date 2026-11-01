<?php

namespace App\Contexts\Licensing\Application\Queries;

use App\Contexts\Shared\Application\{WorkflowInput, WorkflowResult};
use App\Models\License;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

final class LicenseQueries
{
    public function licenseQuery(WorkflowInput $input): Builder
    {
        $actor = $input->user();
        abort_unless($actor instanceof User && $actor->is_active && $actor->hasAnyRole(['super_admin', 'admin', 'support']), 403);

        return License::query()->with('customer')->latest();
    }

    public function index(WorkflowInput $input): WorkflowResult
    {
        $input->validate(['status' => 'sometimes|in:active,suspended,expired,cancelled']);
        return WorkflowResult::payload(['licenses' => $input->paginate($this->licenseQuery($input)->when($input->status, fn ($q) => $q->where('status', $input->status)))]);
    }
    public function show(WorkflowInput $input, License $license): WorkflowResult
    {
        return WorkflowResult::payload(['license' => $license->load('customer', 'issuer')]);
    }
}

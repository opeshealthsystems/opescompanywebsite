<?php

namespace App\Contexts\ApiIdentity\Application\Queries;

use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;

/** Public, non-sensitive API product metadata. */
final class ApiProductQueries extends WorkflowHandler
{
    public function status(WorkflowInput $input): WorkflowResult
    {
        return WorkflowResult::payload([
            'status' => 'responding',
            'api_version' => (string) config('api_product.version'),
            'checked_at' => now('UTC')->toIso8601String(),
        ]);
    }

    public function changelog(WorkflowInput $input): WorkflowResult
    {
        return WorkflowResult::payload(['items' => config('api_product.changelog', [])]);
    }
}

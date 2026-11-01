<?php

namespace App\Http\Controllers\Api\V1;

use App\Contexts\Identity\Application\Queries\OwnBusinessActivity;
use App\Contexts\Shared\Application\WorkflowInput;
use Illuminate\Http\Request;

final class BusinessActivityController
{
    public function __invoke(Request $request, OwnBusinessActivity $query)
    {
        return response()->json($query->execute(new WorkflowInput(values: $request->query(), actor: $request->user(), api: true,
            scopes: $request->attributes->get('api_scopes', []), consumerId: $request->attributes->get('api_client')->id)));
    }
}

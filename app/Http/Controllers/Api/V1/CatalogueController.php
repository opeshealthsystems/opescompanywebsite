<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Publishing\Application\Queries\MachineCatalogueQueries;

class CatalogueController
{
    public function __invoke(Request $request, MachineCatalogueQueries $query)
    {
        $result = $query->products(new WorkflowInput(values: $request->query(), actor: $request->attributes->get('api_client')?->owner,
            language: $request->query('locale', 'en'), api: true, scopes: $request->attributes->get('api_scopes', []),
            consumerId: $request->attributes->get('api_client')?->id));
        return response()->json(['data' => $result->data['items'], 'meta' => $result->data['meta']]);
    }
}

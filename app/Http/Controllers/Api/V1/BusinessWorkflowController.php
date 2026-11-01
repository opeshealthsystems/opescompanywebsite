<?php

namespace App\Http\Controllers\Api\V1;

use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Infrastructure\ApiWorkflowSerializer;
use App\Contexts\Shared\Infrastructure\WorkflowDispatcher;
use Illuminate\Http\Request;

final class BusinessWorkflowController
{
    public function __invoke(Request $request, WorkflowDispatcher $dispatcher, ApiWorkflowSerializer $serializer)
    {
        $operation = $request->route()->defaults['business_operation'];
        $definition = config('business_workflows.definitions', [])[$operation];
        $locale = $request->header('Accept-Language', 'en');
        $locale = str_starts_with(strtolower($locale), 'fr') ? 'fr' : 'en';
        $input = new WorkflowInput($request->except('_token', '_method'), $request->route()->parameters(),
            $definition['authenticated'] ? $request->user() : null, $locale, $request->ip(), $request->allFiles(), true,
            $request->attributes->get('api_scopes', []), $request->attributes->get('api_client')?->id);
        $result = $dispatcher->execute($operation, $input, $request->header('Idempotency-Key'));
        if ($result->kind === 'binary') return response($result->bytes, $result->status, ['Content-Type' => 'application/pdf',
            'Content-Disposition' => \Symfony\Component\HttpFoundation\HeaderUtils::makeDisposition('attachment', $result->filename)]);
        if ($result->kind === 'empty') return response()->noContent($result->status);
        if ($result->errors || isset($result->messages['error'])) return response()->json(['type' => 'about:blank', 'title' => 'Operation could not be completed', 'status' => 422,
            'request_id' => $request->attributes->get('request_id'), 'errors' => $result->errors ?: ['operation' => [$result->messages['error']]]], 422, ['Content-Type' => 'application/problem+json']);
        $data = $definition['command'] ? $serializer->references($result->data) : $result->data;
        return response()->json(['data' => $serializer->serialize($data, $input), 'meta' => ['operation' => $operation],
            'messages' => $result->messages], $result->status);
    }
}

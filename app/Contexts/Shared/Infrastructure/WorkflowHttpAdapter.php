<?php

namespace App\Contexts\Shared\Infrastructure;

use App\Contexts\Shared\Application\WorkflowInput;
use Illuminate\Http\Request;

final class WorkflowHttpAdapter
{
    public function web(string $operation, Request $request): mixed
    {
        $input = new WorkflowInput($request->except('_token', '_command_key'), $request->route()?->parameters() ?? [], $request->user(),
            app()->getLocale(), $request->ip(), $request->allFiles());
        $key = $request->input('_command_key');
        if ($key !== null && !preg_match('/\A[A-Za-z0-9._:-]{16,128}\z/', $key)) throw \Illuminate\Validation\ValidationException::withMessages(['_command_key' => 'Invalid form submission key']);
        $result = app(WorkflowDispatcher::class)->execute($operation, $input, $key);
        return app(WebWorkflowPresenter::class)->present($result, $request);
    }
}

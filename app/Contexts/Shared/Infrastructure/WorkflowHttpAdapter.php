<?php

namespace App\Contexts\Shared\Infrastructure;

use App\Contexts\Shared\Application\WorkflowInput;
use Illuminate\Http\Request;

final class WorkflowHttpAdapter
{
    public function web(string $operation, Request $request): mixed
    {
        [$input, $key] = $this->webInput($request);
        $result = app(WorkflowDispatcher::class)->execute($operation, $input, $key);
        return app(WebWorkflowPresenter::class)->present($result, $request);
    }

    public function webCommand(string $operation, Request $request, string $returnRoute): mixed
    {
        [$input, $key] = $this->webInput($request);
        $result = app(WorkflowDispatcher::class)->execute($operation, $input, $key);
        if ($result->errors || isset($result->messages['error'])) {
            return back()->withErrors($result->errors ?: ['operation' => [$result->messages['error']]])
                ->withInput($request->except(['password', 'password_confirmation', 'current_password', 'client_secret']));
        }

        $redirect = redirect()->route($returnRoute, ['locale' => app()->getLocale()]);
        if (isset($result->messages['success'])) $redirect->with('status', $result->messages['success']);
        if ($operation === 'developer.oauth_applications.create') {
            $redirect->with('developer_oauth_application', [
                'client_id' => (string) ($result->data['client_id'] ?? ''),
                'name' => (string) ($result->data['name'] ?? ''),
            ]);
        }
        if (in_array($operation, ['developer.machine_clients.create', 'developer.machine_clients.rotate'], true)) {
            $redirect->with('developer_machine_client.credentials', [
                'client_id' => (string) ($result->data['client_id'] ?? ''),
                'name' => (string) ($result->data['name'] ?? ''),
                'client_secret' => (string) ($result->data['client_secret'] ?? ''),
                'token_url' => (string) ($result->data['token_url'] ?? ''),
                'scopes' => array_values($result->data['scopes'] ?? []),
            ]);
        }
        return $redirect;
    }

    private function webInput(Request $request): array
    {
        $input = new WorkflowInput($request->except('_token', '_command_key'), $request->route()?->parameters() ?? [], $request->user(),
            app()->getLocale(), $request->ip(), $request->allFiles());
        $key = $request->input('_command_key');
        if ($key !== null && !preg_match('/\A[A-Za-z0-9._:-]{16,128}\z/', $key)) throw \Illuminate\Validation\ValidationException::withMessages(['_command_key' => 'Invalid form submission key']);
        return [$input, $key];
    }
}

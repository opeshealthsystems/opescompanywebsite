<?php

namespace App\Contexts\Shared\Infrastructure;

use App\Contexts\Shared\Application\WorkflowResult;
use Illuminate\Http\Request;

final class WebWorkflowPresenter
{
    public function present(WorkflowResult $result, Request $request): mixed
    {
        if ($result->kind === 'payload') return response()->json($result->data, $result->status);
        if ($result->kind === 'empty') return response()->noContent($result->status);
        if ($result->kind === 'binary') return response($result->bytes, $result->status, [
            'Content-Type' => 'application/pdf', 'Cache-Control' => 'private, no-store',
            'Content-Disposition' => \Symfony\Component\HttpFoundation\HeaderUtils::makeDisposition('attachment', $result->filename),
        ]);
        $presentation = config('business_workflows.presentations', [])[$result->variant] ?? null;
        if (!$presentation) throw new \LogicException('Missing web presentation');
        if ($result->kind === 'data') {
            foreach ($result->data as $item) if ($item instanceof \Illuminate\Contracts\Pagination\Paginator) $item->withQueryString();
            return view($presentation['target'], $result->data);
        }
        $redirect = match ($presentation['kind']) {
            'route' => redirect()->route($presentation['target'], $result->data),
            'url' => redirect($this->localUrl($result->data['url'] ?? $presentation['target'], $request)),
            default => back(),
        };
        foreach ($result->messages as $key => $value) $redirect->with($key, $value);
        if ($result->errors) $redirect->withErrors($result->errors);
        if ($result->retainInput) $redirect->withInput($request->except(['password', 'password_confirmation', 'current_password', 'client_secret']));
        return $redirect;
    }

    private function localUrl(string $url, Request $request): string
    {
        if (str_starts_with($url, '/') && !str_starts_with($url, '//') && !str_contains($url, '\\')) return $url;
        $parts = parse_url($url);
        $origin = parse_url($request->getSchemeAndHttpHost());
        if ($parts && !isset($parts['user']) && !isset($parts['pass'])
            && ($parts['scheme'] ?? '') === ($origin['scheme'] ?? '')
            && ($parts['host'] ?? '') === ($origin['host'] ?? '')
            && ($parts['port'] ?? null) === ($origin['port'] ?? null)
            && !str_contains($url, '\\')) return $url;
        return '/';
    }
}

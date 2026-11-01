<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->prependToPriorityList(\Illuminate\Routing\Middleware\ThrottleRequests::class, \App\Http\Middleware\AuthenticateApiClient::class);
        $middleware->prependToPriorityList(\Illuminate\Routing\Middleware\ThrottleRequests::class, \App\Http\Middleware\AuthenticateBusinessUser::class);
        $middleware->api(prepend: [\App\Http\Middleware\ApiResponseMetadata::class]);
        $middleware->web(prepend: [\App\Http\Middleware\CanonicalSite::class]);
        $middleware->alias([
            'setlocale' => \App\Http\Middleware\SetLocale::class,
            'role'      => \App\Http\Middleware\RequireRole::class,
        ]);

        // Locale is a non-sensitive UI preference ("en"/"fr") — keep it unencrypted
        // so the bare-root redirect can read the visitor's remembered choice.
        $middleware->encryptCookies(except: ['locale']);

        \Illuminate\Auth\Middleware\Authenticate::redirectUsing(
            fn ($request) => route('login')
        );
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Throwable $error, Request $request) {
            if (!$request->is('api/*')) return null;
            $status = $error instanceof \Illuminate\Validation\ValidationException ? 422
                : ($error instanceof \Symfony\Component\HttpKernel\Exception\HttpExceptionInterface ? $error->getStatusCode() : 500);
            $body = ['type' => 'about:blank', 'title' => \Symfony\Component\HttpFoundation\Response::$statusTexts[$status] ?? 'Error',
                'status' => $status, 'request_id' => $request->attributes->get('request_id') ?? (string) \Illuminate\Support\Str::uuid()];
            if ($status === 422) $body['errors'] = $error->errors();
            $headers = $error instanceof \Symfony\Component\HttpKernel\Exception\HttpExceptionInterface ? $error->getHeaders() : [];
            return response()->json($body, $status, array_merge($headers, ['Content-Type' => 'application/problem+json', 'X-Request-ID' => $body['request_id'], 'Cache-Control' => 'no-store']));
        });
        $exceptions->respond(function ($response) {
            if (request()->is('api/*')) {
                if (!$response->headers->has('X-Request-ID')) {
                    $response->headers->set('X-Request-ID', request()->attributes->get('request_id') ?? (string) \Illuminate\Support\Str::uuid());
                }
                $response->headers->set('Cache-Control', 'no-store');
                $response->headers->set('X-Content-Type-Options', 'nosniff');
            }
            return $response;
        });
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })->create();

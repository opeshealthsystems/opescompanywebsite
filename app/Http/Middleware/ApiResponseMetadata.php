<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ApiResponseMetadata
{
    public function handle(Request $request, Closure $next)
    {
        $request->attributes->set('request_id', (string) Str::uuid());
        $response = $next($request);
        $response->headers->set('X-Request-ID', $request->attributes->get('request_id'));
        $route = $request->route();
        $cacheSeconds = (int) ($route?->defaults['api_public_cache_seconds'] ?? 0);
        $isPublicRead = $request->isMethod('GET')
            && !$request->user()
            && $response->isSuccessful()
            && $cacheSeconds > 0
            && $route?->defaults['business_operation'] !== null;
        if ($isPublicRead) {
            $response->headers->set('Cache-Control', 'public, max-age='.$cacheSeconds.', stale-while-revalidate=60');
            $vary = $route->defaults['api_public_cache_vary'] ?? [];
            if ($vary !== []) $response->headers->set('Vary', implode(', ', $vary));
        } else {
            $response->headers->set('Cache-Control', 'no-store');
        }
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        return $response;
    }
}

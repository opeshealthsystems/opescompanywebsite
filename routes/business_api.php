<?php

use App\Http\Controllers\Api\V1\BusinessWorkflowController;
use App\Http\Middleware\AuthenticateBusinessUser;
use Illuminate\Support\Facades\Route;

// Static paths precede variable record paths; caller input never chooses a handler.
$operations = config('business_workflows.definitions', []);
uasort($operations, fn ($a, $b) => substr_count($a['api_path'], '{') <=> substr_count($b['api_path'], '{'));
foreach ($operations as $operation => $definition) {
    $limiter = $definition['rate_limiter'] ?? ($definition['authenticated'] ? 'api-consumer' : 'public-enquiries');
    $route = Route::match($definition['http_methods'], $definition['api_path'], BusinessWorkflowController::class)
        ->defaults('business_operation', $operation);
    if (($definition['public_cache_seconds'] ?? 0) > 0 && !$definition['authenticated'] && !$definition['command'] && $definition['http_methods'] === ['GET']) {
        $route->defaults('api_public_cache_seconds', (int) $definition['public_cache_seconds']);
        $route->defaults('api_public_cache_vary', $definition['public_cache_vary'] ?? []);
    }
    $route
        ->middleware($definition['authenticated'] ? [AuthenticateBusinessUser::class, 'throttle:'.$limiter] : ['throttle:'.$limiter])
        ->name('api.v1.business.'.$operation);
}

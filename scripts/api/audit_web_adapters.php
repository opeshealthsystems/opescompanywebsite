<?php

declare(strict_types=1);

require __DIR__.'/../../vendor/autoload.php';

$app = require __DIR__.'/../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$registry = require __DIR__.'/../../config/business_workflows.php';
$routes = app('router')->getRoutes();
$operationRoutes = [];
foreach ($registry['definitions'] ?? [] as $operation => $definition) {
    $route = $definition['web_route'] ?? null;
    if (is_string($route) && $route !== '') $operationRoutes[$route][] = $operation;
}

$mutating = $business = $services = $transport = $unmapped = [];
$reads = $readWorkflows = $readAdapters = $unmappedReads = [];
foreach ($routes as $route) {
    if (str_starts_with($route->uri(), 'api/') || ! $route->getName()) continue;
    $name = $route->getName();
    if (array_intersect($route->methods(), ['GET', 'HEAD']) && ! preg_match('/\A(?:filament\.|livewire\.|dusk\.|generated::)/', $name)) {
        $reads[$name] = true;
        if (isset($operationRoutes[$name])) {
            $readWorkflows[$name] = $operationRoutes[$name];
        } elseif (isset($registry['web_adapters'][$name]['kind'])) {
            $readAdapters[$name] = $registry['web_adapters'][$name]['kind'];
        } else {
            $unmappedReads[$name] = ['uri' => $route->uri(), 'action' => $route->getActionName()];
        }
    }
    $methods = array_intersect($route->methods(), ['POST', 'PUT', 'PATCH', 'DELETE']);
    if (! $methods) continue;
    $mutating[$name] = true;
    if (isset($operationRoutes[$name])) {
        $business[$name] = $operationRoutes[$name];
        continue;
    }
    $adapter = $registry['web_adapters'][$name] ?? null;
    if (is_array($adapter) && isset($adapter['kind'])) {
        if ($adapter['kind'] === 'shared_application_service') {
            $apiRoute = $routes->getByName($adapter['api_route'] ?? '');
            if (! $apiRoute || ! str_starts_with($apiRoute->uri(), 'api/')) {
                $unmapped[$name] = ['uri' => $route->uri(), 'reason' => 'Shared API route is missing'];
                continue;
            }
            $services[$name] = ['handler' => $adapter['handler'], 'api_route' => $adapter['api_route']];
        } else {
            $transport[$name] = $adapter['kind'];
        }
        continue;
    }
    $unmapped[$name] = ['uri' => $route->uri(), 'methods' => array_values($methods), 'action' => $route->getActionName()];
}

$report = [
    'mutating_named_web_routes' => count($mutating),
    'shared_application_workflows' => count($business),
    'explicit_application_service_adapters' => count($services),
    'explicit_transport_adapters' => count($transport),
    'named_non_filament_web_read_routes' => count($reads),
    'shared_application_read_workflows' => count($readWorkflows),
    'explicit_read_view_or_transport_adapters' => count($readAdapters),
    'unmapped_reads' => $unmappedReads,
    'unmapped' => $unmapped,
];
echo json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR).PHP_EOL;
exit($unmapped || $unmappedReads ? 1 : 0);

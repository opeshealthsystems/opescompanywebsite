<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);
$spec = json_decode(file_get_contents($root.'/docs/api/openapi.json'), true, 512, JSON_THROW_ON_ERROR);
$operations = [];
foreach ($spec['paths'] as $path => $pathItem) {
    foreach ($pathItem as $method => $operation) {
        if (!isset($operation['operationId']) || !in_array(strtoupper($method), ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'], true)) continue;
        $parameters = $operation['parameters'] ?? [];
        preg_match_all('/\{([^}]+)\}/', $path, $matches);
        $operations[$operation['operationId']] = [
            'method' => strtoupper($method), 'path' => $path,
            'authenticated' => !empty($operation['security']),
            'idempotencyRequired' => count(array_filter($parameters, fn ($p) => ($p['name'] ?? null) === 'Idempotency-Key' && ($p['required'] ?? false))) > 0,
            'pathParameters' => $matches[1],
            'queryParameters' => array_values(array_map(fn ($p) => $p['name'], array_filter($parameters, fn ($p) => ($p['in'] ?? null) === 'query'))),
            'acceptLanguage' => count(array_filter($parameters, fn ($p) => ($p['name'] ?? null) === 'Accept-Language')) > 0,
            'requestBodyRequired' => (bool) ($operation['requestBody']['required'] ?? false),
            'contentTypes' => array_keys($operation['requestBody']['content'] ?? []),
        ];
    }
}
ksort($operations);
$javascript = 'export const businessOperations = Object.freeze('.json_encode($operations, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR).');'."\n";
file_put_contents($root.'/sdk/node/src/business-operations.js', $javascript);
$types = "export type BusinessOperationId =\n".implode("\n", array_map(fn ($id) => "  | '".$id."'", array_keys($operations))).";\n";
file_put_contents($root.'/sdk/node/src/business-operations.d.ts', $types);
echo json_encode(['operationIds' => count($operations), 'source' => 'sdk/node/src/business-operations.js']).PHP_EOL;

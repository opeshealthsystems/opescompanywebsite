<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);
$spec = json_decode(file_get_contents($root.'/docs/api/openapi.json'), true, 512, JSON_THROW_ON_ERROR);
$schemas = $spec['components']['schemas'] ?? [];
$schemaType = function (array $schema) use (&$schemaType): string {
    if (isset($schema['$ref'])) {
        $name = basename($schema['$ref']);
        return 'ApiSchema_'.preg_replace('/[^A-Za-z0-9_]/', '_', $name);
    }
    foreach (['oneOf', 'anyOf'] as $union) {
        if (isset($schema[$union])) return '('.implode(' | ', array_map(fn ($part) => $schemaType($part), $schema[$union])).')';
    }
    if (isset($schema['allOf'])) return '('.implode(' & ', array_map(fn ($part) => $schemaType($part), $schema['allOf'])).')';
    if (isset($schema['enum'])) {
        $values = array_map(fn ($value) => is_string($value) ? json_encode($value, JSON_THROW_ON_ERROR) : ($value === null ? 'null' : (string) $value), $schema['enum']);
        $type = implode(' | ', $values) ?: 'unknown';
    } else {
        $types = is_array($schema['type'] ?? null) ? $schema['type'] : [($schema['type'] ?? null)];
        $type = implode(' | ', array_unique(array_map(function ($kind) use ($schema, &$schemaType): string {
            return match ($kind) {
                'string' => ($schema['format'] ?? null) === 'binary' ? 'Blob' : 'string',
                'integer', 'number' => 'number',
                'boolean' => 'boolean',
                'null' => 'null',
                'array' => 'Array<'.(isset($schema['items']) ? $schemaType($schema['items']) : 'unknown').'>',
                'object' => isset($schema['properties']) || isset($schema['additionalProperties'])
                    ? (isset($schema['properties']) ? '{ '.implode(' ', array_map(function ($key, $property) use ($schema, &$schemaType): string {
                        $optional = in_array($key, $schema['required'] ?? [], true) ? '' : '?';
                        return json_encode((string) $key, JSON_THROW_ON_ERROR).$optional.': '.$schemaType($property).';';
                    }, array_keys($schema['properties']), array_values($schema['properties']))) .' }'.(isset($schema['additionalProperties']) ? ' & Record<string, unknown>' : '') : 'Record<string, '.(is_array($schema['additionalProperties']) ? $schemaType($schema['additionalProperties']) : 'unknown').'>')
                    : 'Record<string, unknown>',
                default => 'unknown',
            };
        }, $types)));
    }
    if (($schema['nullable'] ?? false) && !str_contains($type, 'null')) $type .= ' | null';
    return $type;
};
$operations = [];
$requestTypes = [];
$responseTypes = [];
foreach ($spec['paths'] as $path => $pathItem) {
    foreach ($pathItem as $method => $operation) {
        if (!isset($operation['operationId']) || !in_array(strtoupper($method), ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'], true)) continue;
        $parameters = $operation['parameters'] ?? [];
        preg_match_all('/\{([^}]+)\}/', $path, $matches);
        $entry = [
            'method' => strtoupper($method), 'path' => $path,
            'authenticated' => !empty($operation['security']),
            'idempotencyRequired' => count(array_filter($parameters, fn ($p) => ($p['name'] ?? null) === 'Idempotency-Key' && ($p['required'] ?? false))) > 0,
            'pathParameters' => $matches[1],
            'queryParameters' => array_values(array_map(fn ($p) => $p['name'], array_filter($parameters, fn ($p) => ($p['in'] ?? null) === 'query'))),
            'requiredQueryParameters' => array_values(array_map(fn ($p) => $p['name'], array_filter($parameters, fn ($p) => ($p['in'] ?? null) === 'query' && ($p['required'] ?? false)))),
            'acceptLanguage' => count(array_filter($parameters, fn ($p) => ($p['name'] ?? null) === 'Accept-Language')) > 0,
            'requestBodyRequired' => (bool) ($operation['requestBody']['required'] ?? false),
            'contentTypes' => array_keys($operation['requestBody']['content'] ?? []),
        ];
        $schemes = array_merge(...array_map('array_keys', $operation['security'] ?? []));
        if (in_array('OAuth2', $schemes, true) && !in_array('UserOAuth2', $schemes, true)) $entry['machineClientOnly'] = true;
        $operations[$operation['operationId']] = $entry;

        $pathTypes = array_fill_keys($matches[1], 'string | number');
        $queryTypes = [];
        $queryRequired = false;
        foreach ($parameters as $parameter) {
            if (($parameter['in'] ?? null) !== 'query') continue;
            $queryTypes[$parameter['name']] = ['type' => $schemaType($parameter['schema'] ?? []), 'required' => (bool) ($parameter['required'] ?? false)];
            $queryRequired = $queryRequired || ($parameter['required'] ?? false);
        }
        $requestBody = $operation['requestBody'] ?? null;
        $bodyType = null;
        if ($requestBody) {
            $content = $requestBody['content'] ?? [];
            if (isset($content['application/json'])) $bodyType = $schemaType($content['application/json']['schema'] ?? []);
            elseif (isset($content['application/x-www-form-urlencoded'])) $bodyType = 'URLSearchParams';
            elseif (isset($content['multipart/form-data'])) $bodyType = 'FormData';
            else $bodyType = 'unknown';
        }
        $requestTypes[$operation['operationId']] = [
            'path' => $pathTypes,
            'query' => $queryTypes,
            'queryRequired' => $queryRequired,
            'body' => $bodyType,
            'bodyRequired' => (bool) ($requestBody['required'] ?? false),
            'idempotencyRequired' => $entry['idempotencyRequired'],
        ];
        $successTypes = [];
        $hasNoContentSuccess = false;
        foreach ($operation['responses'] ?? [] as $status => $response) {
            if (!preg_match('/^2[0-9]{2}$/', (string) $status)) continue;
            $hasNoContentSuccess = $hasNoContentSuccess || (string) $status === '204';
            foreach ($response['content'] ?? [] as $contentType => $content) {
                if (isset($content['schema'])) {
                    $successTypes[] = str_contains($contentType, 'json') ? $schemaType($content['schema']) : 'ArrayBuffer';
                }
            }
        }
        $responseTypes[$operation['operationId']] = array_values(array_unique($successTypes))
            ? '('.implode(' | ', array_values(array_unique($successTypes))).')'
            : ($hasNoContentSuccess ? 'null' : 'unknown');
    }
}
ksort($operations);
$javascript = 'export const businessOperations = Object.freeze('.json_encode($operations, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR).');'."\n";
file_put_contents($root.'/sdk/node/src/business-operations.js', $javascript);
$schemaDeclarations = [];
foreach ($schemas as $name => $schema) {
    $safeName = 'ApiSchema_'.preg_replace('/[^A-Za-z0-9_]/', '_', $name);
    $schemaDeclarations[] = 'export type '.$safeName.' = '.$schemaType($schema).';';
}
$requestDeclarations = [];
foreach ($requestTypes as $id => $shape) {
    $pathFields = array_map(fn ($key, $type) => json_encode((string) $key, JSON_THROW_ON_ERROR).': '.$type.';', array_keys($shape['path']), array_values($shape['path']));
    $queryFields = array_map(function ($key, $field) use ($schemaType): string {
        return json_encode((string) $key, JSON_THROW_ON_ERROR).($field['required'] ? '' : '?').': '.$field['type'].';';
    }, array_keys($shape['query']), array_values($shape['query']));
    $body = $shape['body'] === null ? 'undefined' : $shape['body'];
    $requestDeclarations[] = '  '.json_encode($id, JSON_THROW_ON_ERROR).': { path: { '.implode(' ', $pathFields).' }; query: { '.implode(' ', $queryFields).' }; queryRequired: '.($shape['queryRequired'] ? 'true' : 'false').'; body: '.$body.'; bodyRequired: '.($shape['bodyRequired'] ? 'true' : 'false').'; idempotencyRequired: '.($shape['idempotencyRequired'] ? 'true' : 'false').'; };';
}
$responseDeclarations = [];
foreach ($responseTypes as $id => $type) {
    $responseDeclarations[] = '  '.json_encode($id, JSON_THROW_ON_ERROR).': '.$type.';';
}
$types = "export type BusinessOperationId =\n".implode("\n", array_map(fn ($id) => "  | '".$id."'", array_keys($operations))).";\n\n".
    implode("\n", $schemaDeclarations)."\n\n".
    "export interface BusinessOperationRequestMap {\n".implode("\n", $requestDeclarations)."\n}\n\n".
    "export interface BusinessOperationResponseMap {\n".implode("\n", $responseDeclarations)."\n}\n\n".
    "type Entry<I extends BusinessOperationId> = BusinessOperationRequestMap[I];\n".
    "type PathOption<I extends BusinessOperationId> = keyof Entry<I>['path'] extends never ? { path?: never } : { path: Entry<I>['path'] };\n".
    "type QueryOption<I extends BusinessOperationId> = keyof Entry<I>['query'] extends never ? { query?: never } : Entry<I>['queryRequired'] extends true ? { query: Entry<I>['query'] } : { query?: Entry<I>['query'] };\n".
    "type BodyOption<I extends BusinessOperationId> = Entry<I>['body'] extends undefined ? { body?: never } : Entry<I>['bodyRequired'] extends true ? { body: Exclude<Entry<I>['body'], undefined> } : { body?: Entry<I>['body'] };\n".
    "type IdempotencyOption<I extends BusinessOperationId> = Entry<I>['idempotencyRequired'] extends true ? { idempotencyKey: string } : { idempotencyKey?: string };\n".
    "export type BusinessOperationRequestOptions<I extends BusinessOperationId> = PathOption<I> & QueryOption<I> & BodyOption<I> & IdempotencyOption<I>;\n".
    "export type BusinessOperationRequiresOptions<I extends BusinessOperationId> = keyof Entry<I>['path'] extends never ? Entry<I>['queryRequired'] extends true ? true : Entry<I>['bodyRequired'] extends true ? true : Entry<I>['idempotencyRequired'] extends true ? true : false : true;\n".
    "export type BusinessOperationResponse<I extends BusinessOperationId> = BusinessOperationResponseMap[I];\n";
file_put_contents($root.'/sdk/node/src/business-operations.d.ts', $types);
echo json_encode(['operationIds' => count($operations), 'source' => 'sdk/node/src/business-operations.js']).PHP_EOL;

<?php

// Build-time source inspection only. Runtime routing uses the explicit registry.
require __DIR__.'/../../vendor/autoload.php';
$root = dirname(__DIR__, 2);
$registry = require $root.'/config/business_workflows.php';
$spec = json_decode(file_get_contents($root.'/docs/api/baselines/0.3.0.json'), true, 512, JSON_THROW_ON_ERROR);
$spec['info']['version'] = '0.33.0';
$spec['info']['description'] = 'Versioned OHS business APIs. Public enquiries and account recovery, machine catalogue access, and delegated business workflows with authorization code and S256 PKCE. Private operations require current role, scope and record authorization. OpenID Connect is not implemented.';
$scopes = [];
foreach ($registry['definitions'] as $definition) if ($definition['authenticated']) $scopes[$definition['scope']] = str_replace(':', ' ', $definition['scope']);
$spec['components']['securitySchemes']['UserOAuth2'] = ['type' => 'oauth2', 'description' => 'Authorization code with required S256 PKCE, explicit consent, active account and current role checks.', 'flows' => ['authorizationCode' => [
    'authorizationUrl' => 'https://opeshealthsystems.com/oauth/authorize', 'tokenUrl' => 'https://opeshealthsystems.com/api/v1/oauth/user-token', 'scopes' => $scopes]]];
$parser = (new \PhpParser\ParserFactory)->createForNewestSupportedVersion();
$finder = new \PhpParser\NodeFinder();
foreach ($registry['definitions'] as $operation => $definition) {
    $path = '/'.$definition['api_path'];
    $file = $root.'/'.str_replace('\\', '/', lcfirst($definition['handler'])).'.php';
    $ast = $parser->parse(file_get_contents($file));
    $method = $finder->findFirst($ast, fn ($n) => $n instanceof \PhpParser\Node\Stmt\ClassMethod && $n->name->name === $definition['method']);
    $properties = []; $required = [];
    foreach ($finder->findInstanceOf($method->stmts, \PhpParser\Node\Expr\MethodCall::class) as $call) {
        if (!$call->name instanceof \PhpParser\Node\Identifier || $call->name->name !== 'validate' || !($call->args[0]->value ?? null) instanceof \PhpParser\Node\Expr\Array_) continue;
        foreach ($call->args[0]->value->items as $item) {
            if (!$item?->key instanceof \PhpParser\Node\Scalar\String_ || !$item->value instanceof \PhpParser\Node\Scalar\String_) continue;
            $name = $item->key->value; $rule = $item->value->value;
            if (str_contains($name, '.')) continue;
            $rules = explode('|', $rule); $schema = ['type' => 'string', 'description' => 'Validation: '.$rule];
            foreach (['integer' => 'integer', 'numeric' => 'number', 'boolean' => 'boolean', 'array' => 'array'] as $r => $type) if (in_array($r, $rules, true)) $schema['type'] = $type;
            if ($schema['type'] === 'array') $schema['items'] = [];
            if (in_array('email', $rules, true)) $schema['format'] = 'email';
            if (in_array('date', $rules, true)) $schema['format'] = 'date';
            if (in_array('file', $rules, true) || in_array('image', $rules, true)) $schema['format'] = 'binary';
            foreach ($rules as $r) {
                if (str_starts_with($r, 'in:')) $schema['enum'] = explode(',', substr($r, 3));
                if (preg_match('/^(min|max):(\d+)$/', $r, $match)) {
                    $key = $schema['type'] === 'string' ? ($match[1] === 'min' ? 'minLength' : 'maxLength') : ($schema['type'] === 'array' ? ($match[1] === 'min' ? 'minItems' : 'maxItems') : ($match[1] === 'min' ? 'minimum' : 'maximum'));
                    $schema[$key] = (int) $match[2];
                }
            }
            if (in_array('nullable', $rules, true)) $schema['type'] = [$schema['type'], 'null'];
            if (in_array('required', $rules, true)) $required[] = $name;
            $properties[$name] = $schema;
        }
    }
    $parameters = [];
    preg_match_all('/\{([^}]+)\}/', $path, $matches);
    foreach ($matches[1] as $name) $parameters[] = ['name' => $name, 'description' => 'Record identifier or slug from an authorized read response.', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'string']];
    $parameters[] = ['name' => 'Accept-Language', 'description' => 'Response language; defaults to English.', 'in' => 'header', 'required' => false, 'schema' => ['type' => 'string', 'enum' => ['en', 'fr']]];
    if ($definition['command']) $parameters[] = ['name' => 'Idempotency-Key', 'in' => 'header', 'required' => true, 'schema' => ['type' => 'string', 'minLength' => 16, 'maxLength' => 128, 'pattern' => '^[A-Za-z0-9._:-]+$']];
    else foreach (['page' => 10000, 'per_page' => 100] as $name => $max) $parameters[] = ['name' => $name, 'in' => 'query', 'schema' => ['type' => 'integer', 'minimum' => 1, 'maximum' => $max]];
    foreach ($parameters as &$parameter) if (!isset($parameter['description'])) $parameter['description'] = match ($parameter['name']) {
        'Idempotency-Key' => 'A unique command key. Repeat the same body and key within 24 hours to retrieve the first outcome. A changed body returns 409.',
        'page' => 'One-based page for paginated collections. Ignored by single-record and aggregate reads.',
        default => 'Page size, up to 100 records, for paginated collections. Non-paginated lists are bounded to 100 records.',
    };
    unset($parameter);
    $response = ['description' => 'Application outcome. Read results use explicit resource field contracts; commands return references and messages.', 'content' => ['application/json' => ['schema' => ['type' => 'object', 'required' => ['data', 'meta', 'messages'], 'properties' => [
        'data' => ['type' => ['object', 'array']], 'meta' => ['type' => 'object', 'properties' => ['operation' => ['type' => 'string', 'const' => $operation]]], 'messages' => ['type' => ['object', 'array']]]]]]];
    if (str_contains(file_get_contents($file), 'WorkflowResult::download(')) $response['content']['application/pdf'] = ['schema' => ['type' => 'string', 'format' => 'binary']];
    $response['headers'] = ['Cache-Control' => ['description' => 'Authenticated and command results are not stored in shared caches.', 'schema' => ['type' => 'string', 'const' => 'no-store']],
        'X-Request-ID' => ['description' => 'Identifier used when contacting support.', 'schema' => ['type' => 'string', 'format' => 'uuid']]];
    foreach ($definition['http_methods'] as $http) {
        $methodName = strtolower($http);
        $entry = ['operationId' => str_replace('.', '_', $operation), 'summary' => trim(preg_replace('/(?<!^)[A-Z]/', ' $0', class_basename($definition['handler']))).' '.$definition['method'],
            'description' => 'Shared application operation: '.$operation.'. Existing role and record policies remain authoritative.', 'tags' => [$definition['context']],
            'security' => $definition['authenticated'] ? [['UserOAuth2' => [$definition['scope']]]] : [], 'parameters' => $parameters,
            'responses' => ['200' => $response, '401' => ['description' => 'Valid delegated user token required'], '403' => ['description' => 'Role, scope or record access denied'], '404' => ['description' => 'Resource not found'], '409' => ['description' => 'Idempotency key used with a different request'], '422' => ['description' => 'Validation failed'], '429' => ['description' => 'Rate limit exceeded']]];
        if ($operation === 'identity.register' || ($definition['command'] && $definition['method'] === 'create')) { $entry['responses']['201'] = $entry['responses']['200']; unset($entry['responses']['200']); }
        foreach (['401', '403', '404', '409', '422', '429'] as $error) $entry['responses'][$error]['content'] = ['application/problem+json' => ['schema' => ['$ref' => '#/components/schemas/Problem']]];
        if ($definition['command'] && $properties) {
            $schema = ['type' => 'object', 'properties' => $properties];
            if ($required) $schema['required'] = array_values(array_unique($required));
            $entry['requestBody'] = ['required' => (bool) $required, 'content' => ['application/json' => ['schema' => $schema], 'multipart/form-data' => ['schema' => $schema]]];
        }
        if (isset($spec['paths'][$path][$methodName])) throw new RuntimeException('Duplicate contract operation '.$http.' '.$path);
        $spec['paths'][$path][$methodName] = $entry;
    }
}
$userToken = $spec['paths']['/oauth/token']['post'];
$userToken['operationId'] = 'issueDelegatedUserToken';
$userToken['summary'] = 'Exchange an authorization code or refresh token';
$userToken['description'] = 'User authorization requires an exact registered HTTPS redirect, consent, state of at least 32 characters, and S256 PKCE. Access tokens expire in 15 minutes; refresh tokens expire in seven days. Role and client scope approval are checked again on issuance and access.';
$userToken['requestBody']['content']['application/x-www-form-urlencoded']['schema'] = ['type' => 'object', 'required' => ['grant_type', 'client_id'], 'properties' => [
    'grant_type' => ['type' => 'string', 'enum' => ['authorization_code', 'refresh_token']], 'client_id' => ['type' => 'string', 'format' => 'uuid'],
    'code' => ['type' => 'string', 'description' => 'Single-use authorization code for authorization_code.'], 'code_verifier' => ['type' => 'string', 'minLength' => 43, 'maxLength' => 128],
    'redirect_uri' => ['type' => 'string', 'format' => 'uri'], 'refresh_token' => ['type' => 'string', 'description' => 'Required for refresh_token grant.']]];
$spec['paths']['/oauth/user-token']['post'] = $userToken;
$spec['paths']['/me/activity']['get'] = ['operationId' => 'readOwnBusinessActivity', 'summary' => 'Read your command activity projection', 'tags' => ['Identity'],
    'description' => 'Versioned command events populate this actor-scoped read model after commit. Event retries use an inbox to avoid duplicate projection rows.',
    'security' => [['UserOAuth2' => ['identity:read']]], 'parameters' => [
        ['name' => 'page', 'in' => 'query', 'description' => 'One-based page.', 'schema' => ['type' => 'integer', 'minimum' => 1, 'maximum' => 10000]],
        ['name' => 'per_page', 'in' => 'query', 'description' => 'Page size, at most 100.', 'schema' => ['type' => 'integer', 'minimum' => 1, 'maximum' => 100]]],
    'responses' => ['200' => ['description' => 'Actor-owned event references and pagination.', 'content' => ['application/json' => ['schema' => ['type' => 'object', 'required' => ['data', 'meta'], 'properties' => [
        'data' => ['type' => 'array', 'items' => ['type' => 'object', 'properties' => ['event_id' => ['type' => 'string', 'format' => 'uuid'], 'operation' => ['type' => 'string'], 'references' => ['type' => 'array', 'items' => ['type' => 'object']], 'occurred_at' => ['type' => 'string']]]], 'meta' => ['type' => 'object']]]]]],
        '401' => ['description' => 'Delegated user authentication required.'], '403' => ['description' => 'identity:read scope required.'], '422' => ['description' => 'Invalid page size.'], '429' => ['description' => 'Rate limit exceeded.']]];
file_put_contents($root.'/docs/api/openapi.json', json_encode($spec, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR)."\n");
echo json_encode(['business_operations' => count($registry['definitions']), 'paths' => count($spec['paths']), 'version' => '0.33.0']).PHP_EOL;


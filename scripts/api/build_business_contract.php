<?php

// Build-time source inspection only. Runtime routing uses the explicit registry.
require __DIR__.'/../../vendor/autoload.php';
$root = dirname(__DIR__, 2);
$registry = require $root.'/config/business_workflows.php';
$product = require $root.'/config/api_product.php';
$spec = json_decode(file_get_contents($root.'/docs/api/baselines/0.3.0.json'), true, 512, JSON_THROW_ON_ERROR);
$spec['info']['version'] = $product['version'];
$spec['info']['description'] = 'Versioned OHS business APIs. Public enquiries and application intake, partner and tester administration, publishing and catalogues, role-scoped operations and workforce workflows, finance registers, reports and line-item detail reads, customer facility profiles, employee and HR administration, risk and performance management, leave requests, validation, practitioner, recruitment, learning, survey and support workflows, account recovery, machine catalogue access and owner-managed public-scope machine clients, public sitemap data, protected admin reference data and resource/relationship summaries, with delegated business workflows using authorization code and S256 PKCE. Private operations require current role, scope and record authorization. OpenID Connect is not implemented.';
$scopes = [];
foreach ($registry['definitions'] as $definition) if ($definition['authenticated']) $scopes[$definition['scope']] = str_replace(':', ' ', $definition['scope']);
$schemaDocs = [
    'EnquiryInput' => ['description' => 'Public website enquiry fields. Do not send patient or other sensitive health information.', 'example' => ['name' => 'Example sender', 'email' => 'sender@example.test', 'message' => 'Please share information about OHS products.', 'locale' => 'en']],
    'Problem' => ['description' => 'Problem Details response for a rejected or invalid API request.', 'example' => ['type' => 'https://opeshealthsystems.com/problems/validation-error', 'title' => 'Validation failed', 'status' => 422, 'request_id' => '00000000-0000-4000-8000-000000000000', 'errors' => ['email' => ['The email address is invalid.']]]],
    'OAuthError' => ['description' => 'OAuth 2.0 token endpoint error response. Error strings follow the OAuth server response.', 'example' => ['error' => 'invalid_client', 'error_description' => 'The client credentials are invalid.']],
    'OAuthToken' => ['description' => 'Short-lived OAuth 2.0 bearer token response. The example token is a placeholder.', 'example' => ['access_token' => 'REPLACE_WITH_ISSUED_TOKEN', 'token_type' => 'Bearer', 'expires_in' => 900]],
    'CatalogueProduct' => ['description' => 'Allowlisted public metadata for one published OHS software product.', 'example' => ['slug' => 'example-product', 'name' => 'Example product', 'category' => 'Software', 'url' => 'https://opeshealthsystems.com/en/products/example-product']],
];
foreach ($schemaDocs as $schemaName => $documentation) {
    if (!isset($spec['components']['schemas'][$schemaName])) continue;
    $spec['components']['schemas'][$schemaName]['description'] = $documentation['description'];
    $spec['components']['schemas'][$schemaName]['example'] = $documentation['example'];
}
$quoteOptions = \App\Contexts\Enquiries\Application\PricingQuoteSelection::keys();
$enquiryInput = &$spec['components']['schemas']['EnquiryInput'];
$enquiryInput['properties']['quote_option'] = [
    'type' => 'string', 'enum' => $quoteOptions,
    'description' => 'Optional server-defined pricing-page choice. The API records its canonical displayed product and indicative price; it is not a final commercial offer.',
];
$enquiryInput['example']['quote_option'] = 'clinic-saas';
$enquiryInput['properties']['integration_application'] = [
    'type' => 'object',
    'description' => 'Structured API developer access request. The request is reviewed before credentials are provisioned. Do not include secrets or patient information.',
    'additionalProperties' => false,
    'required' => ['application_name', 'access_type', 'use_case'],
    'properties' => [
        'application_name' => ['type' => 'string', 'maxLength' => 100],
        'access_type' => ['type' => 'string', 'enum' => ['machine', 'delegated', 'both']],
        'redirect_uri' => ['type' => ['string', 'null'], 'maxLength' => 255, 'description' => 'Delegated access requires an exact HTTPS callback or opeshealth://oauth/callback.'],
        'requested_scopes' => ['type' => 'array', 'maxItems' => 20, 'uniqueItems' => true, 'items' => ['type' => 'string', 'maxLength' => 80]],
        'use_case' => ['type' => 'string', 'minLength' => 10, 'maxLength' => 1200],
    ],
];
$enquiryInput['example']['integration_application'] = [
    'application_name' => 'Clinic scheduling integration', 'access_type' => 'delegated',
    'redirect_uri' => 'https://clinic.example/callback', 'requested_scopes' => ['customer:read'],
    'use_case' => 'Allow clinic staff to review their licensed OHS services.',
];
unset($enquiryInput);
$enquiryOperation = &$spec['paths']['/enquiries']['post'];
$enquiryOperation['requestBody']['content']['application/json']['example']['quote_option'] = 'clinic-saas';
$enquiryOperation['requestBody']['content']['application/json']['example']['integration_application'] = $spec['components']['schemas']['EnquiryInput']['example']['integration_application'];
$enquiryOperation['responses']['201']['description'] = 'Enquiry recorded with durable delivery intent. When quote_option is supplied, the selected indicative price is stored and a copy is sent to the requester. receipt_delivery_status reports acceptance by the mail transport, not inbox placement; a replay returns the saved original response.';
$enquiryResponseData = &$enquiryOperation['responses']['201']['content']['application/json']['schema']['properties']['data']['properties'];
$enquiryResponseData['receipt_delivery_status'] = ['type' => 'string', 'enum' => ['sent', 'pending'], 'description' => 'Present only for quote requests with quote_option. sent means the email transport accepted the requester copy; pending means delivery was not confirmed.'];
unset($enquiryResponseData, $enquiryOperation);
$schemaExamples = static function (array $schema): mixed {
    $type = $schema['type'] ?? 'string';
    if (is_array($type)) {
        $nonNullTypes = array_values(array_filter($type, fn ($candidate) => $candidate !== 'null'));
        $type = $nonNullTypes[0] ?? 'string';
    }
    if (!empty($schema['enum'])) return $schema['enum'][0];
    if (isset($schema['format'])) return match ($schema['format']) {
        'email' => 'person@example.test', 'date' => '2026-01-01', 'date-time' => '2026-01-01T00:00:00Z', 'uri' => 'https://example.test/resource',
        'uuid' => '00000000-0000-4000-8000-000000000000', 'binary' => null, default => 'REPLACE_WITH_VALUE',
    };
    return match ($type) {
        'integer', 'number' => $schema['minimum'] ?? 1, 'boolean' => true,
        'array' => [], 'object' => (object) [], default => 'REPLACE_WITH_VALUE',
    };
};
$spec['components']['securitySchemes']['UserOAuth2'] = ['type' => 'oauth2', 'description' => 'Authorization code with required S256 PKCE, explicit consent, active account and current role checks.', 'flows' => ['authorizationCode' => [
    'authorizationUrl' => 'https://opeshealthsystems.com/oauth/authorize', 'tokenUrl' => 'https://opeshealthsystems.com/api/v1/oauth/user-token', 'scopes' => $scopes]]];
$parser = (new \PhpParser\ParserFactory)->createForNewestSupportedVersion();
$finder = new \PhpParser\NodeFinder();
foreach ($registry['definitions'] as $operation => $definition) {
    // Laravel's `{resource:field}` route binding is an implementation detail;
    // OpenAPI path parameters contain only the externally visible parameter name.
    $apiPath = preg_replace_callback('/\{([^}:]+):[^}]+\}/', fn ($match) => '{'.$match[1].'}', $definition['api_path']);
    $path = '/'.$apiPath;
    $file = $root.'/'.str_replace('\\', '/', lcfirst($definition['handler'])).'.php';
    $ast = $parser->parse(file_get_contents($file));
    $method = $finder->findFirst($ast, fn ($n) => $n instanceof \PhpParser\Node\Stmt\ClassMethod && $n->name->name === $definition['method']);
    $classMethods = [];
    foreach ($finder->findInstanceOf($ast, \PhpParser\Node\Stmt\ClassMethod::class) as $classMethod) $classMethods[$classMethod->name->name] = $classMethod;
    $literal = null;
    $literal = function ($node, array $scope = []) use (&$literal) {
        if ($node instanceof \PhpParser\Node\Scalar\String_) return $node->value;
        if ($node instanceof \PhpParser\Node\Scalar\LNumber || $node instanceof \PhpParser\Node\Scalar\DNumber) return $node->value;
        if ($node instanceof \PhpParser\Node\Expr\Variable && is_string($node->name)) return $scope[$node->name] ?? null;
        if ($node instanceof \PhpParser\Node\Expr\ConstFetch) return match (strtolower($node->name->toString())) { 'true' => true, 'false' => false, 'null' => null, default => null };
        if ($node instanceof \PhpParser\Node\Expr\BinaryOp\Concat) { $left = $literal($node->left, $scope); $right = $literal($node->right, $scope); return is_string($left) && is_string($right) ? $left.$right : null; }
        if ($node instanceof \PhpParser\Node\Expr\Ternary) {
            $condition = $literal($node->cond, $scope);
            if ($node->cond instanceof \PhpParser\Node\Expr\Variable && is_string($node->cond->name) && array_key_exists($node->cond->name, $scope)) $condition = (bool) $condition;
            return is_bool($condition) ? $literal($condition ? ($node->if ?? $node->cond) : $node->else, $scope) : null;
        }
        return null;
    };
    $validationCalls = []; $pendingMethods = [['name' => $definition['method'], 'arguments' => []]]; $inspectedMethods = [];
    while ($pendingMethods) {
        $pending = array_pop($pendingMethods); $methodName = $pending['name']; $argumentValues = $pending['arguments'];
        if (!isset($classMethods[$methodName])) continue;
        $stateKey = $methodName.'|'.json_encode($argumentValues);
        if (isset($inspectedMethods[$stateKey])) continue;
        $inspectedMethods[$stateKey] = true; $classMethod = $classMethods[$methodName]; $scope = [];
        foreach ($classMethod->params as $index => $param) {
            if (!$param->var instanceof \PhpParser\Node\Expr\Variable || !is_string($param->var->name)) continue;
            $scope[$param->var->name] = $argumentValues[$index] ?? $literal($param->default, $scope);
        }
        foreach ($finder->findInstanceOf($classMethod->stmts ?? [], \PhpParser\Node\Expr\Assign::class) as $assignment) {
            if ($assignment->var instanceof \PhpParser\Node\Expr\Variable && is_string($assignment->var->name)) $scope[$assignment->var->name] = $literal($assignment->expr, $scope);
        }
        foreach ($finder->findInstanceOf($classMethod->stmts ?? [], \PhpParser\Node\Expr\MethodCall::class) as $call) {
            if ($call->name instanceof \PhpParser\Node\Identifier && $call->name->name === 'validate') $validationCalls[] = ['call' => $call, 'scope' => $scope];
            if ($call->var instanceof \PhpParser\Node\Expr\Variable && $call->var->name === 'this' && $call->name instanceof \PhpParser\Node\Identifier) {
                $arguments = [];
                foreach ($call->args as $argument) $arguments[] = $literal($argument->value, $scope);
                $pendingMethods[] = ['name' => $call->name->name, 'arguments' => $arguments];
            }
        }
    }
    $properties = []; $required = [];
    foreach ($validationCalls as $validation) {
        $call = $validation['call']; $scope = $validation['scope'];
        if (!$call->name instanceof \PhpParser\Node\Identifier || $call->name->name !== 'validate' || !($call->args[0]->value ?? null) instanceof \PhpParser\Node\Expr\Array_) continue;
        foreach ($call->args[0]->value->items as $item) {
            if (!$item?->key instanceof \PhpParser\Node\Scalar\String_) continue;
            $name = $item->key->value; $ruleParts = [];
            if ($item->value instanceof \PhpParser\Node\Scalar\String_) $ruleParts = explode('|', $item->value->value);
            elseif (is_string($part = $literal($item->value, $scope))) $ruleParts = explode('|', $part);
            elseif ($item->value instanceof \PhpParser\Node\Expr\Array_) {
                foreach ($item->value->items as $ruleItem) {
                    if (!$ruleItem) continue;
                    $part = $literal($ruleItem->value, $scope);
                    if (is_string($part)) array_push($ruleParts, ...explode('|', $part));
                    elseif ($ruleItem->value instanceof \PhpParser\Node\Expr\StaticCall
                        && $ruleItem->value->name instanceof \PhpParser\Node\Identifier
                        && strtolower($ruleItem->value->name->name) === 'in'
                        && ($ruleItem->value->args[0]->value ?? null) instanceof \PhpParser\Node\Expr\Array_) {
                        $values = [];
                        foreach ($ruleItem->value->args[0]->value->items as $allowed) {
                            $value = $allowed ? $literal($allowed->value, $scope) : null;
                            if (is_string($value) || is_int($value)) $values[] = (string) $value;
                        }
                        if ($values) $ruleParts[] = 'in:'.implode(',', $values);
                    }
                }
            }
            if (!$ruleParts) continue;
            $wildcard = str_ends_with($name, '.*');
            if (str_contains($name, '.') && !$wildcard) continue;
            $field = $wildcard ? substr($name, 0, -2) : $name;
            $rule = implode('|', $ruleParts); $rules = $ruleParts;
            $schema = ['type' => 'string', 'description' => 'Validation: '.$rule];
            if (in_array('prohibited', $rules, true)) continue;
            foreach (['integer' => 'integer', 'numeric' => 'number', 'boolean' => 'boolean', 'array' => 'array'] as $r => $type) if (in_array($r, $rules, true)) $schema['type'] = $type;
            if ($schema['type'] === 'array') $schema['items'] = [];
            if (in_array('email', $rules, true)) $schema['format'] = 'email';
            if (in_array('date', $rules, true)) $schema['format'] = 'date';
            if (in_array('file', $rules, true) || in_array('image', $rules, true)) $schema['format'] = 'binary';
            foreach ($rules as $r) {
                if (str_starts_with($r, 'in:')) $schema['enum'] = explode(',', substr($r, 3));
                if (str_starts_with($r, 'regex:')) {
                    $expression = substr($r, 6);
                    if (preg_match('/^\/(.*)\/[a-z]*$/s', $expression, $match)) $schema['pattern'] = $match[1];
                }
                if (preg_match('/^(min|max):(\d+)$/', $r, $match)) {
                    $binary = ($schema['format'] ?? null) === 'binary' && str_starts_with($operation, 'admin.publishing.partnerInstitutions.');
                    $key = $binary ? ($match[1] === 'min' ? 'minLength' : 'maxLength') : ($schema['type'] === 'string' ? ($match[1] === 'min' ? 'minLength' : 'maxLength') : ($schema['type'] === 'array' ? ($match[1] === 'min' ? 'minItems' : 'maxItems') : ($match[1] === 'min' ? 'minimum' : 'maximum')));
                    $schema[$key] = (int) $match[2] * ($binary ? 1024 : 1);
                }
            }
            if (in_array('nullable', $rules, true)) $schema['type'] = [$schema['type'], 'null'];
            if ($wildcard) {
                if (!in_array('array', (array) ($properties[$field]['type'] ?? null), true)) continue;
                $properties[$field]['items'] = $schema;
                continue;
            }
            if (in_array('required', $rules, true) && !in_array('sometimes', $rules, true)) $required[] = $field;
            $properties[$field] = $schema;
            if (in_array('confirmed', $rules, true)) {
                $confirmation = $field.'_confirmation';
                $properties[$confirmation] = ['type' => 'string', 'description' => 'Required when '.$field.' is supplied (Laravel confirmed rule).'];
                if (in_array('required', $rules, true) && !in_array('sometimes', $rules, true)) $required[] = $confirmation;
            }
        }
    }
    $parameters = [];
    preg_match_all('/\{([^}]+)\}/', $path, $matches);
    foreach ($matches[1] as $name) $parameters[] = ['name' => $name, 'description' => 'Record identifier or slug from an authorized read response.', 'in' => 'path', 'required' => true, 'schema' => ['type' => 'string']];
    $parameters[] = ['name' => 'Accept-Language', 'description' => 'Response language; defaults to English.', 'in' => 'header', 'required' => false, 'schema' => ['type' => 'string', 'enum' => ['en', 'fr']]];
    if ($definition['command']) $parameters[] = ['name' => 'Idempotency-Key', 'in' => 'header', 'required' => true, 'schema' => ['type' => 'string', 'minLength' => 16, 'maxLength' => 128, 'pattern' => '^[A-Za-z0-9._:-]+$']];
    else {
        foreach (['page' => 10000, 'per_page' => 100] as $name => $max) $parameters[] = ['name' => $name, 'in' => 'query', 'schema' => ['type' => 'integer', 'minimum' => 1, 'maximum' => $max]];
        $existingQueryParameters = ['page', 'per_page'];
        foreach ($properties as $name => $schema) {
            if (in_array($name, $existingQueryParameters, true)) continue;
            $description = $schema['description'] ?? 'Validated query filter.';
            unset($schema['description']);
            $parameters[] = ['name' => $name, 'in' => 'query', 'required' => in_array($name, $required, true),
                'description' => $description, 'schema' => $schema];
        }
    }
    foreach ($parameters as &$parameter) {
        if (!isset($parameter['description'])) $parameter['description'] = match ($parameter['name']) {
            'Idempotency-Key' => 'A unique command key. Repeat the same body and key within 24 hours to retrieve the first outcome. A changed body returns 409.',
            'page' => 'One-based page for paginated collections. Ignored by single-record and aggregate reads.',
            default => 'Page size, up to 100 records, for paginated collections. Non-paginated lists are bounded to 100 records.',
        };
        if (isset($parameter['schema']) && !isset($parameter['schema']['example'])) {
            $parameter['schema']['example'] = match ($parameter['name']) {
                'page' => 1, 'per_page' => 25, 'Accept-Language' => 'en', 'Idempotency-Key' => 'replace-with-unique-key',
                default => $parameter['in'] === 'path' ? 'example-id' : $schemaExamples($parameter['schema']),
            };
        }
    }
    unset($parameter);
    $response = ['description' => 'Application outcome. Read results use explicit resource field contracts; commands return references and messages.', 'content' => ['application/json' => ['schema' => ['type' => 'object', 'required' => ['data', 'meta', 'messages'], 'properties' => [
        'data' => ['type' => ['object', 'array']], 'meta' => ['type' => 'object', 'properties' => ['operation' => ['type' => 'string', 'const' => $operation]]], 'messages' => ['type' => ['object', 'array']]]]]]];
    $response['content']['application/json']['examples'] = ['envelope' => ['summary' => 'Response envelope', 'description' => 'The data fields depend on the operation and its resource contract.', 'value' => ['data' => (object) [], 'meta' => ['operation' => $operation], 'messages' => []]]];
    if (str_contains(file_get_contents($file), 'WorkflowResult::download(')) $response['content']['application/pdf'] = ['schema' => ['type' => 'string', 'format' => 'binary']];
    $response['headers'] = ['Cache-Control' => ['description' => 'Authenticated and command results are not stored in shared caches.', 'schema' => ['type' => 'string', 'const' => 'no-store']],
        'X-Request-ID' => ['description' => 'Identifier used when contacting support.', 'schema' => ['type' => 'string', 'format' => 'uuid']]];
    if (($definition['public_cache_seconds'] ?? 0) > 0 && !$definition['authenticated'] && !$definition['command'] && $definition['http_methods'] === ['GET']) {
        $seconds = (int) $definition['public_cache_seconds'];
        $response['headers']['Cache-Control'] = ['description' => 'Public static catalogue response; clients may cache it for '.$seconds.' seconds, with stale-while-revalidate for 60 seconds.', 'schema' => ['type' => 'string', 'const' => 'public, max-age='.$seconds.', stale-while-revalidate=60']];
        if (!empty($definition['public_cache_vary'])) {
            $response['headers']['Vary'] = ['description' => 'Cache key dimensions used to select the localized representation.', 'schema' => ['type' => 'string', 'const' => implode(', ', $definition['public_cache_vary'])]];
        }
    }
    if ($definition['command']) $response['headers']['Idempotency-Replayed'] = [
        'description' => 'Present with value true when this response replays the saved outcome for the supplied Idempotency-Key; absent on the first execution.',
        'schema' => ['type' => 'string', 'const' => 'true'],
    ];
    $humanize = static function (string $value): string {
        $value = preg_replace('/([a-z0-9])([A-Z])/', '$1 $2', $value);
        return trim(preg_replace('/[_-]+/', ' ', $value));
    };
    $pathSegments = array_values(array_filter(explode('/', trim($path, '/'))));
    $pathSegments = array_values(array_filter($pathSegments, fn ($segment) => !in_array($segment, ['api', 'v1', 'admin', 'public', 'files'], true)));
    $lastSegment = end($pathSegments) ?: 'resource';
    $resourceSegment = str_starts_with($lastSegment, '{') || in_array($lastSegment, ['pdf', 'download', 'preview', 'payslip', 'sign', 'approve', 'reject'], true)
        ? (array_slice($pathSegments, -2, 1)[0] ?? 'resource') : $lastSegment;
    $resourceName = $humanize(preg_replace('/\{([^}]+)\}/', '$1', $resourceSegment));
    $resourceName = preg_replace('/\b(id|uuid|slug)\b/i', '', $resourceName);
    $resourceName = trim($resourceName) ?: 'resource';
    $method = strtolower($definition['method']);
    $verb = match (true) {
        in_array($method, ['index', 'list', 'search'], true) => 'List',
        in_array($method, ['show', 'get', 'find', 'read', 'summary', 'report'], true) => 'Retrieve',
        in_array($method, ['create', 'store', 'submit'], true) => 'Create',
        in_array($method, ['update', 'edit', 'patch'], true) => 'Update',
        in_array($method, ['delete', 'destroy', 'remove'], true) => 'Delete',
        in_array($method, ['download', 'pdf', 'preview', 'payslip'], true) => 'Download',
        default => match (strtoupper($definition['http_methods'][0] ?? 'GET')) {
            'POST' => 'Submit', 'PUT', 'PATCH' => 'Update', 'DELETE' => 'Delete',
            'GET' => str_ends_with(strtolower($resourceName), 's') ? 'List' : 'Retrieve', default => 'Retrieve',
        },
    };
    if (str_starts_with($lastSegment, '{') && $verb === 'List') $verb = 'Retrieve';
    $summary = $verb.' '.ucwords($resourceName);
    if (in_array($lastSegment, ['pdf', 'download', 'payslip'], true)) $summary = 'Download '.ucwords($resourceName).' file';
    if (in_array($lastSegment, ['metrics', 'summary', 'pipeline', 'dashboard'], true)) $summary = 'Retrieve '.ucwords(implode(' ', array_map($humanize, $pathSegments)));
    $description = $summary.'. This versioned operation is backed by the '.$definition['context'].' application workflow.';
    if ($definition['authenticated']) {
        $description .= ' Requires an OAuth 2.0 delegated access token with the '.$definition['scope'].' scope; current account roles and record-level policies are also checked.';
        if (!empty($definition['roles'])) $description .= ' Permitted roles: '.implode(', ', $definition['roles']).'.';
    } else {
        $description .= ' This operation is available without an access token and is protected by public-input validation and rate limits.';
    }
    if ($definition['command']) $description .= ' Commands require an Idempotency-Key; replay the same key and payload within 24 hours after an uncertain response.';
    elseif (in_array('page', array_column($parameters, 'name'), true)) $description .= ' Collection results are paginated with a maximum page size of 100.';
    foreach ($definition['http_methods'] as $http) {
        $methodName = strtolower($http);
        $entry = ['operationId' => str_replace('.', '_', $operation), 'summary' => $summary,
            'description' => $description, 'tags' => [$definition['context']],
            'security' => $definition['authenticated'] ? [['UserOAuth2' => [$definition['scope']]]] : [], 'parameters' => $parameters,
            'responses' => ['200' => $response, '401' => ['description' => 'Valid delegated user token required'], '403' => ['description' => 'Role, scope or record access denied'], '404' => ['description' => 'Resource not found'], '409' => ['description' => 'Idempotency key used with a different request'], '422' => ['description' => 'Validation failed'], '429' => ['description' => 'Rate limit exceeded']]];
        if ($operation === 'identity.register' || in_array($operation, ['admin.support.tickets.create', 'admin.finance.invoiceTemplates.create', 'admin.apiClients.create', 'admin.oauthClients.create', 'developer.oauth_applications.create', 'partnerapplicationcontroller.submit'], true) || ($definition['command'] && ($definition['method'] === 'create'
            || in_array($operation, ['admin.recruitment.openings.create', 'admin.recruitment.applications.create'], true)))) {
            $entry['responses']['201'] = $entry['responses']['200']; unset($entry['responses']['200']);
        }
        foreach (['401', '403', '404', '409', '422', '429'] as $error) $entry['responses'][$error]['content'] = ['application/problem+json' => ['schema' => ['$ref' => '#/components/schemas/Problem']]];
        if ($definition['command'] && $properties) {
            $schema = ['type' => 'object', 'properties' => $properties];
            if ($required) $schema['required'] = array_values(array_unique($required));
            $example = [];
            foreach ($properties as $property => $propertySchema) {
                $value = $schemaExamples($propertySchema);
                if ($value !== null) $example[$property] = $value;
            }
            $binary = count(array_filter($properties, fn ($property) => ($property['format'] ?? null) === 'binary')) > 0;
            $content = ['multipart/form-data' => ['schema' => $schema]];
            if ($example) $content['multipart/form-data']['example'] = $example;
            if (!$binary) {
                $content['application/json'] = ['schema' => $schema];
                if ($example) $content['application/json']['example'] = $example;
            }
            $entry['requestBody'] = ['required' => true, 'content' => $content];
        }
        if (isset($spec['paths'][$path][$methodName])) throw new RuntimeException('Duplicate contract operation '.$http.' '.$path);
        $spec['paths'][$path][$methodName] = $entry;
    }
}
$payoutPath = '/practitioners/applications/{application}/payout';
$payoutOperation = &$spec['paths'][$payoutPath]['post'];
$payoutOperation['summary'] = 'Request practitioner application payout';
$payoutOperation['description'] = 'Durably accepts a payout request for an approved paid-program application. Requires a delegated administrator with practitioners:write and an Idempotency-Key. The provider uses a stable reference; uncertain outcomes are held for reconciliation and are never automatically retried. A 202 response means accepted for processing, not confirmed settlement.';
$payoutOperation['requestBody']['content']['application/json']['schema']['properties']['payout_amount']['minimum'] = 0.01;
$payoutOperation['requestBody']['content']['application/json']['schema']['properties']['payout_amount']['maximum'] = 99999999.99;
$payoutOperation['requestBody']['content']['application/json']['schema']['properties']['payout_currency']['minLength'] = 3;
$payoutOperation['requestBody']['content']['application/json']['schema']['properties']['payout_currency']['maxLength'] = 3;
$payoutOperation['requestBody']['content']['application/json']['schema']['properties']['payout_currency']['pattern'] = '^[A-Z]{3}$';
foreach (['application/json', 'multipart/form-data'] as $payoutContentType) {
    $payoutProperties = &$payoutOperation['requestBody']['content'][$payoutContentType]['schema']['properties'];
    $payoutProperties['payout_amount']['minimum'] = 0.01;
    $payoutProperties['payout_amount']['maximum'] = 99999999.99;
    $payoutProperties['payout_currency']['minLength'] = 3;
    $payoutProperties['payout_currency']['maxLength'] = 3;
    $payoutProperties['payout_currency']['pattern'] = '^[A-Z]{3}$';
    unset($payoutProperties);
}
unset(
    $payoutOperation['requestBody']['content']['application/json']['example'],
    $payoutOperation['requestBody']['content']['multipart/form-data']['example'],
);
$payoutAccepted = $payoutOperation['responses']['200'];
$payoutAccepted['description'] = 'Payout request durably accepted for processing. Check the application payout status before taking further action.';
$payoutData = ['type' => 'object', 'required' => ['id', 'request_status', 'payout_reference', 'payout_provider'], 'properties' => [
    'id' => ['type' => 'integer', 'description' => 'Practitioner application identifier.'],
    'request_status' => ['type' => 'string', 'const' => 'accepted'],
    'payout_reference' => ['type' => 'string', 'format' => 'uuid', 'description' => 'Stable reference for this payout attempt.'],
    'payout_provider' => ['type' => 'string', 'enum' => ['mtn', 'manual']],
]];
$payoutAccepted['content']['application/json']['schema']['properties']['data'] = $payoutData;
$payoutAccepted['content']['application/json']['examples'] = ['accepted' => [
    'summary' => 'Payout request accepted',
    'value' => ['data' => ['id' => 123, 'request_status' => 'accepted', 'payout_reference' => '00000000-0000-4000-8000-000000000000', 'payout_provider' => 'mtn'],
        'meta' => ['operation' => 'admin.practitioners.applications.pay'], 'messages' => ['success' => 'Payout request accepted. Check the application record for its current settlement status.']],
]];
$payoutOperation['responses']['202'] = $payoutAccepted;
unset($payoutOperation['responses']['200'], $payoutOperation);
$clientSummary = ['type' => 'object', 'required' => ['client_id', 'name', 'scopes', 'revoked'], 'properties' => [
    'client_id' => ['type' => 'string', 'format' => 'uuid'], 'name' => ['type' => 'string'],
    'owner_id' => ['type' => ['integer', 'null']], 'owner_name' => ['type' => ['string', 'null']],
    'scopes' => ['type' => 'array', 'items' => ['type' => 'string', 'enum' => ['catalogue:read', 'integration:read']]],
    'revoked' => ['type' => 'boolean'], 'created_at' => ['type' => ['string', 'null'], 'format' => 'date-time'],
]];
$spec['components']['schemas']['MachineApiClientSummary'] = $clientSummary;
$secretData = ['type' => 'object', 'required' => ['client_id', 'client_secret', 'token_url', 'scopes'], 'properties' => [
    'client_id' => ['type' => 'string', 'format' => 'uuid'],
    'client_secret' => ['type' => 'string', 'minLength' => 40, 'description' => 'Plaintext secret returned only in this no-store create or rotation response. It is not returned in client listings.'],
    'token_url' => ['type' => 'string', 'format' => 'uri'],
    'scopes' => ['type' => 'array', 'items' => ['type' => 'string', 'enum' => ['catalogue:read', 'integration:read']]],
]];
$successEnvelope = static fn (array $data, string $operation) => ['type' => 'object', 'required' => ['data', 'meta', 'messages'], 'properties' => [
    'data' => $data, 'meta' => ['type' => 'object', 'properties' => ['operation' => ['type' => 'string', 'const' => $operation]]],
    'messages' => ['type' => 'object'],
]];
$developerApplication = ['type' => 'object', 'additionalProperties' => false,
    'required' => ['client_id', 'name', 'redirect_uris', 'scopes', 'revoked'], 'properties' => [
        'client_id' => ['type' => 'string', 'format' => 'uuid'], 'name' => ['type' => 'string'],
        'redirect_uris' => ['type' => 'array', 'items' => ['type' => 'string', 'format' => 'uri']],
        'scopes' => ['type' => 'array', 'items' => ['type' => 'string']], 'revoked' => ['type' => 'boolean'],
        'created_at' => ['type' => ['string', 'null'], 'format' => 'date-time'],
    ]];
$developerApplicationInput = ['type' => 'object', 'additionalProperties' => false,
    'required' => ['name', 'redirect_uris', 'scopes'], 'properties' => [
        'name' => ['type' => 'string', 'minLength' => 3, 'maxLength' => 150],
        'redirect_uris' => ['type' => 'array', 'minItems' => 1, 'maxItems' => 10, 'items' => ['type' => 'string', 'maxLength' => 255],
            'description' => 'Exact HTTPS callbacks, localhost/loopback HTTP for development, or opeshealth://oauth/callback. User information and fragments are prohibited.'],
        'scopes' => ['type' => 'array', 'minItems' => 1, 'maxItems' => 100, 'uniqueItems' => true, 'items' => ['type' => 'string'],
            'description' => 'Must be a subset of the currently active roles and scopes available to the signed-in user.'],
    ]];
$developerApplications = &$spec['paths']['/developer/applications'];
$developerApplications['get']['summary'] = 'List your registered public OAuth applications';
$developerApplications['get']['description'] = 'Lists only authorization-code applications owned by the authenticated user. Client secrets are never issued for public clients. Results are paginated.';
$developerApplications['get']['responses']['200']['description'] = 'The authenticated user’s own public OAuth applications.';
$developerApplications['get']['responses']['200']['content']['application/json']['schema'] = $successEnvelope([
    'type' => 'object', 'required' => ['items', 'pagination'], 'properties' => [
        'items' => ['type' => 'array', 'items' => $developerApplication],
        'pagination' => ['type' => 'object', 'required' => ['current_page', 'per_page', 'total', 'last_page'], 'properties' => [
            'current_page' => ['type' => 'integer'], 'per_page' => ['type' => 'integer'], 'total' => ['type' => 'integer'], 'last_page' => ['type' => 'integer'],
        ]],
    ],
], 'developer.oauth_applications.index');
$developerApplications['get']['responses']['200']['content']['application/json']['example'] = ['data' => [
    'items' => [['client_id' => '00000000-0000-4000-8000-000000000001', 'name' => 'Example learning app',
        'redirect_uris' => ['https://app.example.test/oauth/callback'], 'scopes' => ['identity:read', 'learning:read'], 'revoked' => false,
        'created_at' => '2026-11-05T12:00:00Z']],
    'pagination' => ['current_page' => 1, 'per_page' => 20, 'total' => 1, 'last_page' => 1],
], 'meta' => ['operation' => 'developer.oauth_applications.index'], 'messages' => []];
$developerApplications['post']['summary'] = 'Register a public OAuth application for your account';
$developerApplications['post']['description'] = 'Creates a non-confidential authorization-code client owned by the authenticated user. Redirects must exactly match approved HTTPS, loopback-development, or reserved OPES callback values. Requested scopes must currently be available to the owner. The account is limited to five active applications. Authorization requires user consent and S256 PKCE; no client secret is issued.';
$developerApplications['post']['requestBody'] = ['required' => true, 'content' => ['application/json' => ['schema' => $developerApplicationInput,
    'example' => ['name' => 'Example learning app', 'redirect_uris' => ['https://app.example.test/oauth/callback'], 'scopes' => ['identity:read', 'learning:read']]]]];
$developerApplications['post']['responses']['201']['description'] = 'Public OAuth application registered without a client secret.';
$developerApplications['post']['responses']['201']['content']['application/json']['schema'] = $successEnvelope([
    'type' => 'object', 'required' => ['client_id', 'name', 'redirect_uris', 'scopes', 'revoked', 'authorization_url', 'token_url', 'pkce_method', 'client_secret_issued'],
    'properties' => array_merge($developerApplication['properties'], [
        'authorization_url' => ['type' => 'string', 'format' => 'uri'], 'token_url' => ['type' => 'string', 'format' => 'uri'],
        'pkce_method' => ['type' => 'string', 'const' => 'S256'], 'client_secret_issued' => ['type' => 'boolean', 'const' => false],
    ]),
], 'developer.oauth_applications.create');
$developerApplicationUpdate = &$spec['paths']['/developer/applications/{client}']['patch'];
$developerApplicationUpdate['summary'] = 'Update one of your public OAuth applications';
$developerApplicationUpdate['description'] = 'Replaces the application name, exact redirect URI allowlist and approved scopes. Every existing access/refresh token is revoked so old grants cannot outlive the new configuration. Ownership is checked against the authenticated account.';
$developerApplicationUpdate['requestBody'] = ['required' => true, 'content' => ['application/json' => ['schema' => $developerApplicationInput]]];
$developerApplicationUpdate['responses']['200']['description'] = 'Updated public OAuth application; prior tokens have been revoked.';
$developerApplicationUpdate['responses']['200']['content']['application/json']['schema'] = $successEnvelope([
    'type' => 'object', 'required' => ['client_id', 'name', 'redirect_uris', 'scopes', 'revoked', 'authorization_url', 'token_url', 'pkce_method', 'client_secret_issued'],
    'properties' => array_merge($developerApplication['properties'], [
        'authorization_url' => ['type' => 'string', 'format' => 'uri'], 'token_url' => ['type' => 'string', 'format' => 'uri'],
        'pkce_method' => ['type' => 'string', 'const' => 'S256'], 'client_secret_issued' => ['type' => 'boolean', 'const' => false],
    ]),
], 'developer.oauth_applications.update');
$developerApplicationDelete = &$spec['paths']['/developer/applications/{client}']['delete'];
$developerApplicationDelete['summary'] = 'Revoke one of your public OAuth applications';
$developerApplicationDelete['description'] = 'Revokes the authenticated user’s own application and all its existing tokens. Other owners’ applications and machine clients cannot be revoked through this operation.';
$developerApplicationDelete['responses']['200']['description'] = 'Application and all tokens revoked.';
$developerApplicationDelete['responses']['200']['content']['application/json']['schema'] = $successEnvelope([
    'type' => 'object', 'required' => ['client_id', 'revoked'], 'properties' => ['client_id' => ['type' => 'string', 'format' => 'uuid'], 'revoked' => ['type' => 'boolean', 'const' => true]],
], 'developer.oauth_applications.revoke');
unset($developerApplications, $developerApplicationUpdate, $developerApplicationDelete);
$partnerApplication = &$spec['paths']['/public/become-a-partner']['post'];
$partnerApplication['summary'] = 'Submit a partner application, including health AI partnership proposals';
$partnerApplication['description'] = 'Creates a partner application for OHS review. The request is idempotent and returns a minimal submission acknowledgement without returning contact or application content. Do not include patient information. Healthcare AI companies may propose integration or evaluation; universities, research labs, teaching hospitals and health-training institutions may propose academic programmes and Medical Informatics collaborations. Data availability and access require separate assessment and approval.';
$partnerRequestContent = &$partnerApplication['requestBody']['content'];
foreach ($partnerRequestContent as &$media) {
    if (!isset($media['schema']['properties']['partner_type'])) continue;
    $media['schema']['properties']['partner_type']['enum'] = array_keys(\App\Models\PartnerApplication::$partnerTypes);
    $media['schema']['properties']['partner_type']['description'] = 'Choose the collaboration type. Use health_ai for healthcare AI proposals or academic for a university, research, teaching-hospital or health-training partnership.';
    if (isset($media['example'])) $media['example']['partner_type'] = 'health_ai';
}
unset($media, $partnerRequestContent);
$partnerApplication['responses']['201']['description'] = 'Partner application recorded; the response contains only a receipt reference.';
$partnerApplication['responses']['201']['content']['application/json']['schema'] = $successEnvelope([
    'type' => 'object', 'additionalProperties' => false, 'required' => ['status', 'received_at'],
    'properties' => [
        'status' => ['type' => 'string', 'const' => 'submitted'],
        'received_at' => ['type' => ['string', 'null'], 'format' => 'date-time'],
    ],
], 'partnerapplicationcontroller.submit');
unset($partnerApplication);
$payrollRunShow = &$spec['paths']['/finance/payroll-runs/{run}']['get'];
$payrollRunShow['summary'] = 'Retrieve a payroll run and its aggregate totals';
$payrollRunShow['description'] = 'Returns the administrator-authorized payroll run projection, including total deductions aggregated from its entries. Employee entry details are available separately through the paginated entries operation.';
$payrollRunShow['responses']['200']['description'] = 'Allowlisted payroll run detail and aggregate totals.';
$payrollRunShow['responses']['200']['content']['application/json']['schema'] = $successEnvelope([
    'type' => 'object', 'required' => ['run'], 'properties' => ['run' => [
        'type' => 'object', 'additionalProperties' => false,
        'required' => ['id', 'reference', 'period_start', 'period_end', 'status', 'total_gross', 'total_deductions', 'total_net', 'currency'],
        'properties' => [
            'id' => ['type' => 'integer'], 'reference' => ['type' => 'string'],
            'period_start' => ['type' => 'string', 'format' => 'date'], 'period_end' => ['type' => 'string', 'format' => 'date'],
            'status' => ['type' => 'string', 'enum' => array_keys(\App\Models\PayrollRun::statusOptions())],
            'total_gross' => ['type' => 'number', 'minimum' => 0], 'total_deductions' => ['type' => 'number', 'minimum' => 0],
            'total_net' => ['type' => 'number', 'minimum' => 0], 'currency' => ['type' => 'string'],
            'notes' => ['type' => ['string', 'null']], 'processed_by' => ['type' => ['integer', 'null']],
            'processor_name' => ['type' => ['string', 'null']], 'completed_at' => ['type' => ['string', 'null'], 'format' => 'date-time'],
            'created_at' => ['type' => ['string', 'null'], 'format' => 'date-time'], 'updated_at' => ['type' => ['string', 'null'], 'format' => 'date-time'],
        ],
    ]],
], 'admin.finance.payrollRuns.show');
$payrollRunShow['responses']['200']['content']['application/json']['example'] = ['data' => ['run' => [
    'id' => 321, 'reference' => 'PAY-2026-0042', 'period_start' => '2026-10-01', 'period_end' => '2026-10-31',
    'status' => 'processing', 'total_gross' => 450000, 'total_deductions' => 18000, 'total_net' => 432000,
    'currency' => 'XAF', 'notes' => null, 'processed_by' => 12, 'processor_name' => 'Example Admin',
    'completed_at' => null, 'created_at' => '2026-10-01T08:00:00+00:00', 'updated_at' => '2026-10-02T08:00:00+00:00',
]], 'meta' => ['operation' => 'admin.finance.payrollRuns.show'], 'messages' => []];
unset($payrollRunShow);
$noStoreHeader = ['description' => 'Responses containing credentials are not stored by caches.', 'schema' => ['type' => 'string', 'const' => 'no-store']];
$clientList = &$spec['paths']['/admin/api-clients']['get'];
$clientList['summary'] = 'List approved machine API clients';
$clientList['description'] = 'Returns safe metadata for administrator-owned client_credentials integrations. Client secrets, tokens and secret hashes are never included.';
$clientList['responses']['200']['description'] = 'Paginated machine-client metadata.';
$clientList['responses']['200']['content']['application/json']['schema'] = $successEnvelope([
    'type' => 'object', 'required' => ['items', 'pagination'], 'properties' => [
        'items' => ['type' => 'array', 'items' => ['$ref' => '#/components/schemas/MachineApiClientSummary']],
        'pagination' => ['type' => 'object', 'properties' => ['current_page' => ['type' => 'integer'], 'per_page' => ['type' => 'integer'], 'total' => ['type' => 'integer'], 'last_page' => ['type' => 'integer']]],
    ],
], 'admin.apiClients.index');
$clientCreate = &$spec['paths']['/admin/api-clients']['post'];
$clientCreate['summary'] = 'Create an approved machine API client';
$clientCreate['description'] = 'Creates a server-to-server client for an active administrator. The plaintext secret is included only in the no-store response and encrypted idempotency receipt; store it securely. Only catalogue:read and integration:read are available to client_credentials clients.';
$clientCreate['responses']['201']['description'] = 'Machine client created; secret is returned in the no-store response.';
$clientCreate['responses']['201']['content']['application/json']['schema'] = $successEnvelope($secretData, 'admin.apiClients.create');
$clientCreate['responses']['201']['headers']['Cache-Control'] = $noStoreHeader;
$clientCreate['responses']['201']['content']['application/json']['example'] = ['data' => [
    'client_id' => '00000000-0000-4000-8000-000000000000', 'client_secret' => 'REPLACE_WITH_SECRET',
    'token_url' => 'https://opeshealthsystems.com/api/v1/oauth/token', 'scopes' => ['catalogue:read'],
], 'meta' => ['operation' => 'admin.apiClients.create'], 'messages' => ['success' => 'Store the secret securely.']];
$clientRotate = &$spec['paths']['/admin/api-clients/{client}/rotate']['post'];
$clientRotate['summary'] = 'Rotate a machine API client secret';
$clientRotate['description'] = 'Creates a replacement secret for an active machine client and revokes its existing tokens. The plaintext secret is included only in the no-store response and encrypted idempotency receipt; store it securely.';
$clientRotate['responses']['200']['description'] = 'Client secret rotated; existing tokens were revoked.';
$clientRotate['responses']['200']['content']['application/json']['schema'] = $successEnvelope($secretData, 'admin.apiClients.rotate');
$clientRotate['responses']['200']['headers']['Cache-Control'] = $noStoreHeader;
$clientRotate['responses']['200']['content']['application/json']['example'] = ['data' => [
    'client_id' => '00000000-0000-4000-8000-000000000000', 'client_secret' => 'REPLACE_WITH_SECRET',
    'token_url' => 'https://opeshealthsystems.com/api/v1/oauth/token', 'scopes' => ['catalogue:read'],
], 'meta' => ['operation' => 'admin.apiClients.rotate'], 'messages' => ['success' => 'Store the new secret securely.']];
$clientRevoke = &$spec['paths']['/admin/api-clients/{client}']['delete'];
$clientRevoke['summary'] = 'Revoke a machine API client';
$clientRevoke['description'] = 'Revokes the client and all existing access tokens. Revocation is idempotent.';
$clientRevoke['responses']['200']['content']['application/json']['schema'] = $successEnvelope([
    'type' => 'object', 'required' => ['client_id', 'revoked'], 'properties' => ['client_id' => ['type' => 'string', 'format' => 'uuid'], 'revoked' => ['type' => 'boolean', 'const' => true]],
], 'admin.apiClients.revoke');
$quotaHeaders = [
    'X-Quota-Daily-Limit' => ['description' => 'Configured UTC daily request limit.', 'schema' => ['type' => 'integer', 'minimum' => 1]],
    'X-Quota-Daily-Remaining' => ['description' => 'Requests remaining before the UTC daily limit.', 'schema' => ['type' => 'integer', 'minimum' => 0]],
    'X-Quota-Daily-Reset' => ['description' => 'Unix timestamp when the UTC daily quota resets.', 'schema' => ['type' => 'integer']],
    'X-Quota-Monthly-Limit' => ['description' => 'Configured UTC calendar-month request limit.', 'schema' => ['type' => 'integer', 'minimum' => 1]],
    'X-Quota-Monthly-Remaining' => ['description' => 'Requests remaining before the UTC calendar-month limit.', 'schema' => ['type' => 'integer', 'minimum' => 0]],
    'X-Quota-Monthly-Reset' => ['description' => 'Unix timestamp when the UTC calendar-month quota resets.', 'schema' => ['type' => 'integer']],
];
$machineQuotaSchema = ['type' => 'object', 'additionalProperties' => false,
    'required' => ['period', 'requests_today', 'requests_this_month', 'daily_limit', 'daily_remaining', 'daily_resets_at', 'monthly_limit', 'monthly_remaining', 'monthly_resets_at'],
    'properties' => [
        'period' => ['type' => 'string', 'const' => 'UTC calendar day and calendar month'],
        'requests_today' => ['type' => 'integer', 'minimum' => 0], 'requests_this_month' => ['type' => 'integer', 'minimum' => 0],
        'daily_limit' => ['type' => ['integer', 'null'], 'minimum' => 1], 'daily_remaining' => ['type' => ['integer', 'null'], 'minimum' => 0],
        'daily_resets_at' => ['type' => 'string', 'format' => 'date-time'],
        'monthly_limit' => ['type' => ['integer', 'null'], 'minimum' => 1], 'monthly_remaining' => ['type' => ['integer', 'null'], 'minimum' => 0],
        'monthly_resets_at' => ['type' => 'string', 'format' => 'date-time'],
    ]];
$spec['components']['schemas']['MachineClientQuota'] = $machineQuotaSchema;
$clientQuota = &$spec['paths']['/admin/api-clients/{client}/quota']['get'];
$clientQuota['summary'] = 'Inspect a machine client quota';
$clientQuota['description'] = 'Returns configured UTC daily and calendar-month limits and current usage for an administrator-managed machine client. Requires api-clients:read.';
$clientQuota['responses']['200']['content']['application/json']['schema'] = $successEnvelope([
    'type' => 'object', 'required' => ['client', 'quota'], 'properties' => [
        'client' => ['type' => 'object', 'required' => ['client_id', 'name', 'revoked'], 'properties' => [
            'client_id' => ['type' => 'string', 'format' => 'uuid'], 'name' => ['type' => 'string'], 'revoked' => ['type' => 'boolean'],
        ]],
        'quota' => ['$ref' => '#/components/schemas/MachineClientQuota'],
    ],
], 'admin.apiClients.quota');
$clientQuota['responses']['404'] = ['description' => 'No managed machine client has this identifier.'];
$setClientQuota = &$spec['paths']['/admin/api-clients/{client}/quota']['patch'];
$setClientQuota['summary'] = 'Set or clear machine client quotas';
$setClientQuota['description'] = 'Sets the per-client UTC daily and calendar-month request quotas. Existing clients remain unlimited until an administrator sets a limit. Send null to clear a limit. The command is idempotent with an Idempotency-Key.';
$quotaInput = ['type' => 'object', 'additionalProperties' => false, 'required' => ['daily_request_limit', 'monthly_request_limit'], 'properties' => [
    'daily_request_limit' => ['type' => ['integer', 'null'], 'minimum' => 1, 'maximum' => 1000000000],
    'monthly_request_limit' => ['type' => ['integer', 'null'], 'minimum' => 1, 'maximum' => 1000000000],
]];
$setClientQuota['requestBody'] = ['required' => true, 'content' => ['application/json' => ['schema' => $quotaInput, 'example' => ['daily_request_limit' => 5000, 'monthly_request_limit' => 100000]]]];
$setClientQuota['responses']['200']['content']['application/json']['schema'] = $successEnvelope([
    'type' => 'object', 'required' => ['client_id', 'daily_request_limit', 'monthly_request_limit'], 'properties' => [
        'client_id' => ['type' => 'string', 'format' => 'uuid'],
        'daily_request_limit' => ['type' => ['integer', 'null'], 'minimum' => 1],
        'monthly_request_limit' => ['type' => ['integer', 'null'], 'minimum' => 1],
    ],
], 'admin.apiClients.setQuota');
$setClientQuota['responses']['404'] = ['description' => 'No managed machine client has this identifier.'];
$spec['paths']['/integration/quota']['get'] = [
    'operationId' => 'readIntegrationQuota', 'summary' => 'Read the current machine client quota', 'tags' => ['Identity'],
    'description' => 'Returns the authenticated machine client’s UTC daily and calendar-month request totals, configured limits and remaining requests. The quota is scoped to this client only. Unconfigured limits are null and mean unlimited. This request is included in the counters.',
    'security' => [['OAuth2' => ['integration:read']]],
    'responses' => [
        '200' => ['description' => 'Current client-only request usage and quota windows.', 'headers' => ['Cache-Control' => $noStoreHeader, 'X-Request-ID' => ['schema' => ['type' => 'string', 'format' => 'uuid']]] + $quotaHeaders,
            'content' => ['application/json' => ['schema' => ['type' => 'object', 'required' => ['data'], 'properties' => ['data' => ['$ref' => '#/components/schemas/MachineClientQuota']]]]]],
        '401' => ['description' => 'Missing, invalid, revoked or expired machine access.', 'content' => ['application/problem+json' => ['schema' => ['$ref' => '#/components/schemas/Problem']]]],
        '403' => ['description' => 'integration:read scope is required.', 'content' => ['application/problem+json' => ['schema' => ['$ref' => '#/components/schemas/Problem']]]],
        '429' => ['description' => 'Per-client daily or monthly quota, or the 60-per-minute request limit, was reached.', 'headers' => ['Retry-After' => ['schema' => ['type' => 'integer', 'minimum' => 1]]] + $quotaHeaders,
            'content' => ['application/problem+json' => ['schema' => ['$ref' => '#/components/schemas/Problem']]]],
    ],
];
unset($clientList, $clientCreate, $clientRotate, $clientRevoke, $clientQuota, $setClientQuota, $quotaHeaders, $quotaInput, $machineQuotaSchema);
$oauthClientSummary = ['type' => 'object', 'required' => ['client_id', 'name', 'redirect_uris', 'scopes', 'revoked'], 'properties' => [
    'client_id' => ['type' => 'string', 'format' => 'uuid'], 'name' => ['type' => 'string'],
    'owner_id' => ['type' => ['integer', 'null']], 'owner_name' => ['type' => ['string', 'null']],
    'redirect_uris' => ['type' => 'array', 'items' => ['type' => 'string', 'format' => 'uri']],
    'scopes' => ['type' => 'array', 'items' => ['type' => 'string']],
    'revoked' => ['type' => 'boolean'], 'created_at' => ['type' => ['string', 'null'], 'format' => 'date-time'],
]];
$spec['components']['schemas']['PublicOAuthClientSummary'] = $oauthClientSummary;
$oauthList = &$spec['paths']['/admin/oauth-clients']['get'];
$oauthList['summary'] = 'List reviewed public OAuth clients';
$oauthList['description'] = 'Returns safe metadata for administrator-owned authorization-code clients. Client secrets are never issued or returned; clients use user consent and S256 PKCE.';
$oauthList['responses']['200']['description'] = 'Paginated public OAuth client metadata.';
$oauthList['responses']['200']['content']['application/json']['schema'] = $successEnvelope([
    'type' => 'object', 'required' => ['items', 'pagination'], 'properties' => [
        'items' => ['type' => 'array', 'items' => ['$ref' => '#/components/schemas/PublicOAuthClientSummary']],
        'pagination' => ['type' => 'object', 'properties' => ['current_page' => ['type' => 'integer'], 'per_page' => ['type' => 'integer'], 'total' => ['type' => 'integer'], 'last_page' => ['type' => 'integer']]],
    ],
], 'admin.oauthClients.index');
$oauthCreate = &$spec['paths']['/admin/oauth-clients']['post'];
$oauthCreate['summary'] = 'Register an administrator-reviewed public OAuth client';
$oauthCreate['description'] = 'Creates a non-confidential authorization-code client for an active administrator owner. Redirects must be exact HTTPS URLs or the reserved OPES Health native callback. Registered scopes are the reviewed application ceiling; each user receives only scopes currently allowed by their own active roles. The client uses user consent and S256 PKCE; no client secret is created.';
foreach (['application/json', 'multipart/form-data'] as $oauthContentType) {
    $oauthCreate['requestBody']['content'][$oauthContentType]['schema']['properties']['redirect_uris']['items'] = ['oneOf' => [
        ['type' => 'string', 'format' => 'uri', 'pattern' => '^https://'],
        ['type' => 'string', 'const' => 'opeshealth://oauth/callback'],
    ]];
}
$oauthCreate['responses']['201']['description'] = 'Public OAuth client registered without a client secret.';
$oauthCreate['responses']['201']['content']['application/json']['schema'] = $successEnvelope([
    'type' => 'object', 'required' => ['client_id', 'name', 'redirect_uris', 'scopes', 'authorization_url', 'token_url', 'pkce_method'], 'properties' => [
        'client_id' => ['type' => 'string', 'format' => 'uuid'], 'name' => ['type' => 'string'],
        'redirect_uris' => ['type' => 'array', 'items' => ['type' => 'string', 'format' => 'uri']],
        'scopes' => ['type' => 'array', 'items' => ['type' => 'string']],
        'authorization_url' => ['type' => 'string', 'format' => 'uri'], 'token_url' => ['type' => 'string', 'format' => 'uri'],
        'pkce_method' => ['type' => 'string', 'const' => 'S256'],
    ],
], 'admin.oauthClients.create');
$oauthCreate['responses']['201']['content']['application/json']['example'] = ['data' => [
    'client_id' => '00000000-0000-4000-8000-000000000000', 'name' => 'Reviewed partner app',
    'redirect_uris' => ['https://partner.example/oauth/callback'], 'scopes' => ['identity:read'],
    'authorization_url' => 'https://opeshealthsystems.com/oauth/authorize',
    'token_url' => 'https://opeshealthsystems.com/api/v1/oauth/user-token', 'pkce_method' => 'S256',
], 'meta' => ['operation' => 'admin.oauthClients.create'], 'messages' => ['success' => 'Public OAuth client created.']];
$oauthRevoke = &$spec['paths']['/admin/oauth-clients/{client}']['delete'];
$oauthRevoke['summary'] = 'Revoke a reviewed public OAuth client';
$oauthRevoke['description'] = 'Revokes the authorization-code client and all existing access tokens. Revocation is idempotent.';
$oauthRevoke['responses']['200']['content']['application/json']['schema'] = $successEnvelope([
    'type' => 'object', 'required' => ['client_id', 'revoked'], 'properties' => ['client_id' => ['type' => 'string', 'format' => 'uuid'], 'revoked' => ['type' => 'boolean', 'const' => true]],
], 'admin.oauthClients.revoke');
unset($oauthList, $oauthCreate, $oauthRevoke);
$discoveryOperation = &$spec['paths']['/discovery']['get'];
$discoveryOperation['summary'] = 'Discover OPES Health API entry points and client access';
$discoveryOperation['description'] = 'Returns the current API version, supported OAuth 2.0 access patterns and canonical links to the OpenAPI contract, status, product and service catalogues, changelog, SDK metadata and developer portal. This public discovery document is cacheable for five minutes.';
$discoveryOperation['responses']['200']['description'] = 'Public API discovery document.';
$discoveryOperation['responses']['200']['content']['application/json']['schema'] = $successEnvelope([
    'type' => 'object', 'required' => ['name', 'api_version', 'openapi_version', 'authentication', '_links'], 'properties' => [
        'name' => ['type' => 'string'], 'api_version' => ['type' => 'string'], 'openapi_version' => ['type' => 'string', 'const' => '3.1.1'],
        'authentication' => ['type' => 'object', 'required' => ['machine', 'delegated'], 'properties' => [
            'machine' => ['type' => 'string'], 'delegated' => ['type' => 'string'],
        ]],
        '_links' => ['type' => 'object', 'required' => ['self', 'openapi', 'status', 'api_products', 'service_offerings', 'changelog', 'sdk_metadata', 'developer_portal'],
            'additionalProperties' => false, 'properties' => array_fill_keys(['self', 'openapi', 'status', 'api_products', 'service_offerings', 'changelog', 'sdk_metadata', 'developer_portal'], [
                'type' => 'object', 'required' => ['href'], 'properties' => ['href' => ['type' => 'string', 'format' => 'uri-reference']],
            ])],
    ],
], 'api.product.discovery');
$discoveryOperation['responses']['200']['content']['application/json']['example'] = ['data' => [
    'name' => 'OPES Health Systems API', 'api_version' => $product['version'], 'openapi_version' => '3.1.1',
    'authentication' => ['machine' => 'OAuth 2.0 client credentials with approved scopes', 'delegated' => 'OAuth 2.0 authorization code with S256 PKCE and user consent'],
    '_links' => array_map(static fn (string $href): array => ['href' => $href], [
        'self' => '/api/v1/discovery', 'openapi' => '/api/v1/openapi.json', 'status' => '/api/v1/status',
        'api_products' => '/api/v1/api-products', 'service_offerings' => '/api/v1/service-offerings',
        'changelog' => '/api/v1/changelog', 'sdk_metadata' => '/downloads/opes-api-sdk.json', 'developer_portal' => '/en/developers',
    ]),
], 'meta' => ['operation' => 'api.product.discovery'], 'messages' => []];
$discoveryOperation['responses']['429'] = ['description' => 'Public read rate limit exceeded.'];
unset($discoveryOperation);
$statusOperation = &$spec['paths']['/status']['get'];
$statusOperation['summary'] = 'Check whether the API application is responding';
$statusOperation['description'] = 'Returns a point-in-time response from this API application and its contract version. It does not test databases, external providers, dependent services or service-level availability. Public read limit: 60 requests per minute per IP address.';
$statusOperation['responses']['200']['description'] = 'The API application handled this status request.';
$statusOperation['responses']['200']['content']['application/json']['schema'] = $successEnvelope([
    'type' => 'object', 'required' => ['status', 'api_version', 'checked_at'], 'properties' => [
        'status' => ['type' => 'string', 'const' => 'responding'],
        'api_version' => ['type' => 'string'], 'checked_at' => ['type' => 'string', 'format' => 'date-time'],
    ],
], 'api.product.status');
$statusOperation['responses']['200']['content']['application/json']['example'] = ['data' => [
    'status' => 'responding', 'api_version' => $product['version'], 'checked_at' => '2026-11-01T12:00:00+00:00',
], 'meta' => ['operation' => 'api.product.status'], 'messages' => []];
$statusOperation['responses']['429'] = ['description' => 'Public read rate limit exceeded.'];
$changelogOperation = &$spec['paths']['/changelog']['get'];
$changelogOperation['summary'] = 'Read the API release changelog';
$changelogOperation['description'] = 'Returns the latest published API release notes. Entries describe API changes and do not imply a service-level commitment. Public read limit: 60 requests per minute per IP address.';
$changelogOperation['responses']['200']['description'] = 'Latest published API release notes.';
$changelogOperation['responses']['200']['content']['application/json']['schema'] = $successEnvelope([
    'type' => 'object', 'required' => ['items'], 'properties' => ['items' => ['type' => 'array', 'items' => [
        'type' => 'object', 'required' => ['version', 'released_at', 'summary'], 'properties' => [
            'version' => ['type' => 'string'], 'released_at' => ['type' => 'string', 'format' => 'date'], 'summary' => ['type' => 'string'],
        ],
    ]]],
], 'api.product.changelog');
$changelogOperation['responses']['200']['content']['application/json']['example'] = ['data' => ['items' => $product['changelog']], 'meta' => ['operation' => 'api.product.changelog'], 'messages' => []];
$changelogOperation['responses']['429'] = ['description' => 'Public read rate limit exceeded.'];
$productCatalogueOperation = &$spec['paths']['/api-products']['get'];
$productCatalogueOperation['summary'] = 'Discover available API products and their access boundaries';
$productCatalogueOperation['description'] = 'Lists the currently offered API product families, preview status, authentication model, scopes, access paths and data boundaries. This catalogue describes API availability; it does not grant access or promise service availability. Accept-Language selects English or French labels. Public read limit: 60 requests per minute per IP address.';
$productCatalogueOperation['responses']['200']['description'] = 'Localized API product catalogue.';
$productCatalogueOperation['responses']['200']['content']['application/json']['schema'] = $successEnvelope([
    'type' => 'object', 'required' => ['api_version', 'locale', 'items'], 'properties' => [
        'api_version' => ['type' => 'string'], 'locale' => ['type' => 'string', 'enum' => ['en', 'fr']],
        'items' => ['type' => 'array', 'items' => ['type' => 'object', 'additionalProperties' => false,
            'required' => ['id', 'name', 'availability', 'authentication', 'access', 'summary', 'data_boundary'],
            'properties' => [
                'id' => ['type' => 'string'], 'name' => ['type' => 'string'], 'availability' => ['type' => 'string', 'enum' => ['preview', 'available', 'deprecated']],
                'authentication' => ['type' => 'string'], 'scope' => ['type' => 'string'], 'access' => ['type' => 'string'],
                'summary' => ['type' => 'string'], 'data_boundary' => ['type' => 'string'],
            ],
        ]],
    ],
], 'api.product.catalogue');
$productCatalogueExampleItems = array_map(static function (array $item): array {
    unset($item['name_fr'], $item['summary_fr'], $item['data_boundary_fr']);
    return $item;
}, $product['products']);
$productCatalogueOperation['responses']['200']['content']['application/json']['example'] = ['data' => [
    'api_version' => $product['version'], 'locale' => 'en', 'items' => $productCatalogueExampleItems,
], 'meta' => ['operation' => 'api.product.catalogue'], 'messages' => []];
$productCatalogueOperation['responses']['429'] = ['description' => 'Public read rate limit exceeded.'];
$serviceOfferingsOperation = &$spec['paths']['/service-offerings']['get'];
$serviceOfferingsOperation['operationId'] = 'publishing_service_offerings_catalogue';
$serviceOfferingsOperation['summary'] = 'Discover OPES Health data and clinical decision support services';
$serviceOfferingsOperation['description'] = 'Returns the public service offerings shown on the OPES Health website, with English or French labels selected by Accept-Language. This describes services available for discussion and enquiry; it is not a clinical decision API or a promise of clinical availability. Public read limit: 60 requests per minute per IP address.';
$serviceOfferingsOperation['responses']['200']['description'] = 'Localized public service offerings and enquiry links.';
$serviceOfferingsOperation['responses']['200']['content']['application/json']['schema'] = $successEnvelope([
    'type' => 'object', 'required' => ['locale', 'items'], 'properties' => [
        'locale' => ['type' => 'string', 'enum' => ['en', 'fr']],
        'items' => ['type' => 'array', 'items' => ['type' => 'object', 'additionalProperties' => false,
            'required' => ['id', 'name', 'summary', 'icon', 'cta', 'data_boundary', 'contact_url'],
            'properties' => [
                'id' => ['type' => 'string'], 'name' => ['type' => 'string'], 'summary' => ['type' => 'string'],
                'icon' => ['type' => 'string'], 'cta' => ['type' => 'string'], 'data_boundary' => ['type' => 'string'],
                'contact_url' => ['type' => 'string'],
            ],
        ]],
    ],
], 'publishing.service_offerings.catalogue');
$serviceOfferingsOperation['responses']['200']['content']['application/json']['example'] = ['data' => [
    'locale' => 'en', 'items' => array_map(static function (array $offering): array {
        unset($offering['name_fr'], $offering['summary_fr'], $offering['cta_fr'], $offering['data_boundary_fr']);
        $offering['contact_url'] = '/en/contact?service='.rawurlencode($offering['id']);
        return $offering;
    }, require $root.'/config/service_offerings.php'),
], 'meta' => ['operation' => 'publishing.service_offerings.catalogue'], 'messages' => []];
$serviceOfferingsOperation['responses']['429'] = ['description' => 'Public read rate limit exceeded.'];
unset($statusOperation, $changelogOperation, $productCatalogueOperation, $serviceOfferingsOperation);
$assessmentQuestionAdmin = ['type' => 'object', 'required' => ['id', 'prompt', 'choices', 'correct_choice_id'], 'properties' => [
    'id' => ['type' => 'string'], 'prompt' => ['type' => 'string'], 'choices' => ['type' => 'array', 'minItems' => 2, 'maxItems' => 8, 'items' => ['type' => 'object', 'required' => ['id', 'label'], 'properties' => ['id' => ['type' => 'string'], 'label' => ['type' => 'string']]]],
    'correct_choice_id' => ['type' => 'string'], 'points' => ['type' => 'integer', 'minimum' => 1, 'maximum' => 100], 'explanation' => ['type' => ['string', 'null']],
]];
$assessmentAdmin = ['type' => 'object', 'required' => ['id', 'course_id', 'title', 'pass_mark', 'is_published', 'questions'], 'properties' => [
    'id' => ['type' => 'integer'], 'course_id' => ['type' => 'integer'], 'title' => ['type' => 'string'], 'instructions' => ['type' => ['string', 'null']],
    'pass_mark' => ['type' => 'integer', 'minimum' => 1, 'maximum' => 100], 'attempt_limit' => ['type' => ['integer', 'null']],
    'duration_minutes' => ['type' => ['integer', 'null']], 'is_published' => ['type' => 'boolean'], 'questions' => ['type' => 'array', 'items' => $assessmentQuestionAdmin],
    'attempts_count' => ['type' => 'integer'], 'created_at' => ['type' => 'string', 'format' => 'date-time'], 'updated_at' => ['type' => 'string', 'format' => 'date-time'],
]];
$assessmentQuestionLearner = $assessmentQuestionAdmin;
$assessmentQuestionLearner['required'] = array_values(array_diff($assessmentQuestionLearner['required'], ['correct_choice_id']));
unset($assessmentQuestionLearner['properties']['correct_choice_id']);
unset($assessmentQuestionLearner['properties']['explanation']);
$assessmentLearner = ['type' => 'object', 'required' => ['id', 'course_id', 'title', 'pass_mark', 'questions'], 'properties' => [
    'id' => ['type' => 'integer'], 'course_id' => ['type' => 'integer'], 'course_slug' => ['type' => 'string'], 'title' => ['type' => 'string'],
    'instructions' => ['type' => ['string', 'null']], 'pass_mark' => ['type' => 'integer'], 'attempt_limit' => ['type' => ['integer', 'null']],
    'duration_minutes' => ['type' => ['integer', 'null']], 'questions' => ['type' => 'array', 'items' => $assessmentQuestionLearner],
]];
$assessmentAdminCollection = &$spec['paths']['/learning/courses/{course}/assessments']['get'];
$assessmentAdminCollection['summary'] = 'List course assessments for administrators';
$assessmentAdminCollection['description'] = 'Returns paginated assessment configuration, including answer keys, for active administrators with learning:read.';
$assessmentAdminCollection['responses']['200']['content']['application/json']['schema'] = $successEnvelope(['type' => 'object', 'required' => ['items', 'pagination'], 'properties' => [
    'items' => ['type' => 'array', 'items' => $assessmentAdmin], 'pagination' => ['type' => 'object', 'properties' => ['current_page' => ['type' => 'integer'], 'per_page' => ['type' => 'integer'], 'total' => ['type' => 'integer'], 'last_page' => ['type' => 'integer']]],
]], 'admin.learning.assessments.index');
$assessmentAdminCollection['responses']['200']['content']['application/json']['example'] = ['data' => ['items' => [], 'pagination' => ['current_page' => 1, 'per_page' => 15, 'total' => 0, 'last_page' => 1]], 'meta' => ['operation' => 'admin.learning.assessments.index'], 'messages' => []];
$assessmentAdminCreate = &$spec['paths']['/learning/courses/{course}/assessments']['post'];
$assessmentAdminCreate['summary'] = 'Create and optionally publish a course assessment';
$assessmentAdminCreate['description'] = 'Creates a course assessment. Drafts are private. Publishing requires an active course and at least one valid question. Questions are immutable after the first learner attempt. Requires learning:write and an Idempotency-Key.';
$assessmentWriteSchema = ['type' => 'object', 'required' => ['title', 'questions'], 'additionalProperties' => false, 'properties' => [
    'title' => ['type' => 'string', 'maxLength' => 200], 'instructions' => ['type' => ['string', 'null'], 'maxLength' => 10000],
    'pass_mark' => ['type' => 'integer', 'minimum' => 1, 'maximum' => 100], 'attempt_limit' => ['type' => ['integer', 'null'], 'minimum' => 1, 'maximum' => 100],
    'duration_minutes' => ['type' => ['integer', 'null'], 'minimum' => 1, 'maximum' => 1440], 'is_published' => ['type' => 'boolean'],
    'questions' => ['type' => 'array', 'minItems' => 1, 'maxItems' => 100, 'items' => $assessmentQuestionAdmin],
]];
$assessmentAdminCreate['requestBody']['content'] = ['application/json' => ['schema' => $assessmentWriteSchema]];
$assessmentAdminCreate['responses']['201']['description'] = 'Assessment created.';
$assessmentAdminCreate['responses']['201']['content']['application/json']['schema'] = $successEnvelope(['type' => 'object', 'required' => ['assessment', 'id'], 'properties' => ['assessment' => $assessmentAdmin, 'id' => ['type' => 'integer']]], 'admin.learning.assessments.create');
$assessmentAdminShow = &$spec['paths']['/learning/courses/{course}/assessments/{assessment}'];
$assessmentAdminShow['get']['summary'] = 'Read an assessment configuration';
$assessmentAdminShow['get']['responses']['200']['content']['application/json']['schema'] = $successEnvelope(['type' => 'object', 'required' => ['assessment'], 'properties' => ['assessment' => $assessmentAdmin]], 'admin.learning.assessments.show');
$assessmentAdminShow['get']['responses']['200']['description'] = 'Assessment details for an authorized administrator.';
$assessmentAdminShow['put']['summary'] = 'Update or publish a course assessment';
$assessmentAdminShow['put']['description'] = 'Updates an assessment. Question/scoring changes are rejected after any learner attempt. Unpublish instead of deleting records with attempt history. Requires learning:write and an Idempotency-Key.';
$assessmentAdminUpdateSchema = $assessmentWriteSchema; unset($assessmentAdminUpdateSchema['required']); $assessmentAdminUpdateSchema['minProperties'] = 1;
$assessmentAdminShow['put']['requestBody']['content'] = ['application/json' => ['schema' => $assessmentAdminUpdateSchema]];
$assessmentAdminShow['put']['responses']['200']['content']['application/json']['schema'] = $successEnvelope(['type' => 'object', 'required' => ['assessment'], 'properties' => ['assessment' => $assessmentAdmin]], 'admin.learning.assessments.update');
$assessmentAdminShow['delete']['summary'] = 'Delete an assessment without learner attempt history';
$assessmentAdminShow['delete']['description'] = 'Assessments with learner attempts cannot be deleted. Unpublish them to retain the learning record.';
$assessmentPractitionerList = &$spec['paths']['/practitioner/courses/{course}/assessments']['get'];
$assessmentPractitionerList['summary'] = 'List published assessments for an enrolled practitioner';
$assessmentPractitionerList['description'] = 'Returns only published assessments for the active course and signed-in practitioner’s enrollment. Correct answers and explanations are omitted.';
$assessmentPractitionerList['responses']['200']['content']['application/json']['schema'] = $successEnvelope(['type' => 'object', 'required' => ['assessments'], 'properties' => ['assessments' => ['type' => 'array', 'items' => ['allOf' => [$assessmentLearner, ['type' => 'object', 'properties' => [
    'attempts_used' => ['type' => 'integer'], 'attempts_remaining' => ['type' => ['integer', 'null']], 'latest_attempt' => ['type' => ['object', 'null']],
]]]]]]], 'practitioner.assessments.index');
$assessmentPractitionerShow = &$spec['paths']['/practitioner/assessments/{assessment}']['get'];
$assessmentPractitionerShow['summary'] = 'Read an available assessment without answer keys';
$assessmentPractitionerShow['responses']['200']['content']['application/json']['schema'] = $successEnvelope(['type' => 'object', 'required' => ['assessment', 'attempts', 'attempts_used', 'attempts_remaining'], 'properties' => [
    'assessment' => $assessmentLearner, 'attempts' => ['type' => 'array', 'items' => ['type' => 'object']], 'attempts_used' => ['type' => 'integer'], 'attempts_remaining' => ['type' => ['integer', 'null']],
]], 'practitioner.assessments.show');
$assessmentStart = &$spec['paths']['/practitioner/assessments/{assessment}/attempts']['post'];
$assessmentStart['summary'] = 'Start or resume an assessment attempt';
$assessmentStart['description'] = 'Requires a current enrollment in the course. Starting again resumes an unexpired attempt. Attempt limits and optional duration are enforced by the server. Requires practitioner:write and an Idempotency-Key.';
$assessmentStart['responses']['201']['description'] = 'A new attempt was started or the caller’s existing in-progress attempt was resumed.';
$assessmentStart['responses']['201']['content']['application/json']['schema'] = $successEnvelope(['type' => 'object', 'required' => ['attempt', 'assessment', 'deadline'], 'properties' => [
    'attempt' => ['type' => 'object', 'required' => ['id', 'status', 'started_at'], 'properties' => ['id' => ['type' => 'integer'], 'status' => ['type' => 'string'], 'started_at' => ['type' => 'string', 'format' => 'date-time'], 'answers' => ['type' => 'object', 'additionalProperties' => ['type' => 'string']]]],
    'assessment' => $assessmentLearner, 'deadline' => ['type' => ['string', 'null'], 'format' => 'date-time'],
]], 'practitioner.assessments.attempts.start');
$assessmentSubmit = &$spec['paths']['/practitioner/assessments/{assessment}/attempts/{attempt}/submit']['post'];
$assessmentSubmit['summary'] = 'Submit answers and receive the scored result';
$assessmentSubmit['description'] = 'Submits exactly one choice per question. The server scores the attempt once, records a passed/failed domain event, and reveals correct answers/explanations only after submission. Expired and submitted attempts cannot be changed.';
$assessmentSubmit['requestBody']['content']['application/json']['schema'] = ['type' => 'object', 'required' => ['answers'], 'additionalProperties' => false, 'properties' => ['answers' => ['type' => 'object', 'minProperties' => 1, 'additionalProperties' => ['type' => 'string']]]];
$assessmentSubmit['responses']['200']['content']['application/json']['schema'] = $successEnvelope(['type' => 'object', 'required' => ['attempt', 'review', 'score', 'passed', 'pass_mark'], 'properties' => [
    'attempt' => ['type' => 'object'], 'review' => ['type' => 'array', 'items' => ['type' => 'object']], 'score' => ['type' => 'number'], 'passed' => ['type' => 'boolean'], 'pass_mark' => ['type' => 'integer'],
]], 'practitioner.assessments.attempts.submit');
$assessmentAttempt = &$spec['paths']['/practitioner/assessments/{assessment}/attempts/{attempt}']['get'];
$assessmentAttempt['summary'] = 'Review a submitted assessment attempt';
$assessmentAttempt['description'] = 'Returns only the signed-in practitioner’s submitted attempt, with selected/correct answers and explanations. In-progress results are not exposed.';
$assessmentAttempt['responses']['200']['content']['application/json']['schema'] = $successEnvelope(['type' => 'object', 'required' => ['attempt', 'review'], 'properties' => ['attempt' => ['type' => 'object'], 'review' => ['type' => 'array', 'items' => ['type' => 'object']]]], 'practitioner.assessments.attempt');
$assessmentSave = &$spec['paths']['/practitioner/assessments/{assessment}/attempts/{attempt}']['patch'];
$assessmentSave['summary'] = 'Save in-progress assessment answers';
$assessmentSave['description'] = 'Saves a partial answer map for the authenticated practitioner’s in-progress attempt so work survives app restarts. Only valid question/choice pairs are accepted. Submitted or expired attempts cannot be changed. Requires practitioner:write and an Idempotency-Key.';
$assessmentSave['requestBody']['content']['application/json']['schema'] = ['type' => 'object', 'required' => ['answers'], 'additionalProperties' => false,
    'properties' => ['answers' => ['type' => 'object', 'minProperties' => 1, 'additionalProperties' => ['type' => 'string']]]];
$assessmentSave['responses']['200']['content']['application/json']['schema'] = $successEnvelope(['type' => 'object', 'required' => ['attempt', 'answered_count', 'question_count'], 'properties' => [
    'attempt' => ['type' => 'object'], 'answered_count' => ['type' => 'integer', 'minimum' => 0], 'question_count' => ['type' => 'integer', 'minimum' => 1],
]], 'practitioner.assessments.answers.save');
$assessmentSave['responses']['422']['description'] = 'Attempt has ended, timer expired, or one or more answers are invalid.';
unset($assessmentAdminCollection, $assessmentAdminCreate, $assessmentAdminShow, $assessmentPractitionerList, $assessmentPractitionerShow, $assessmentStart, $assessmentSubmit, $assessmentAttempt, $assessmentSave);
$eligiblePartnerMembers = &$spec['paths']['/crm/partner-member-options']['get'];
$eligiblePartnerMembers['summary'] = 'Find eligible partner accounts for an organization membership';
$eligiblePartnerMembers['description'] = 'Administrator-only directory of active, email-verified accounts with the partner role. Returns only identity fields needed to grant organization membership; search is bounded and paginated. Requires crm:read.';
$eligiblePartnerMembers['responses']['200']['content']['application/json']['schema'] = $successEnvelope(['type' => 'object', 'required' => ['items', 'pagination'], 'properties' => [
    'items' => ['type' => 'array', 'items' => ['type' => 'object', 'required' => ['id', 'name', 'email'], 'properties' => ['id' => ['type' => 'integer'], 'name' => ['type' => 'string'], 'email' => ['type' => 'string', 'format' => 'email']]]],
    'pagination' => ['type' => 'object', 'required' => ['current_page', 'per_page', 'total', 'last_page'], 'properties' => ['current_page' => ['type' => 'integer'], 'per_page' => ['type' => 'integer'], 'total' => ['type' => 'integer'], 'last_page' => ['type' => 'integer']]],
]], 'admin.enquiries.partnerApplications.members.eligible');
$eligiblePartnerMembers['responses']['200']['content']['application/json']['example'] = ['data' => ['items' => [['id' => 101, 'name' => 'Partner User', 'email' => 'partner@example.test']], 'pagination' => ['current_page' => 1, 'per_page' => 15, 'total' => 1, 'last_page' => 1]], 'meta' => ['operation' => 'admin.enquiries.partnerApplications.members.eligible'], 'messages' => []];
unset($eligiblePartnerMembers);
$partnerAccounts = &$spec['paths']['/partner/accounts']['get'];
$partnerAccounts['summary'] = 'List the authenticated partner’s approved organizations';
$partnerAccounts['description'] = 'Returns only approved partner applications explicitly linked to the current active partner account. Membership is granted and revoked by an administrator; matching by email is never used. Sensitive application notes and other applicants are excluded. Requires identity:read and the partner role.';
$partnerAccounts['responses']['200']['description'] = 'Approved partner organizations linked to the authenticated partner account.';
$partnerAccounts['responses']['200']['content']['application/json']['schema'] = $successEnvelope([
    'type' => 'object', 'required' => ['accounts'], 'properties' => ['accounts' => ['type' => 'array', 'items' => [
        'type' => 'object', 'required' => ['application_id', 'organization_name', 'partner_type', 'country', 'member_since'], 'properties' => [
            'application_id' => ['type' => 'integer', 'minimum' => 1], 'organization_name' => ['type' => 'string'],
            'partner_type' => ['type' => 'string'], 'organization_type' => ['type' => ['string', 'null']],
            'country' => ['type' => 'string'], 'city' => ['type' => ['string', 'null']],
            'website' => ['type' => ['string', 'null'], 'format' => 'uri'], 'membership_role' => ['type' => 'string', 'enum' => ['owner', 'member']],
            'member_since' => ['type' => 'string', 'format' => 'date-time'],
        ],
    ]]]], 'partner.accounts.index');
$partnerAccounts['responses']['200']['content']['application/json']['example'] = ['data' => ['accounts' => [[
    'application_id' => 123, 'organization_name' => 'Example Health Network', 'partner_type' => 'technology',
    'organization_type' => 'hospital_network', 'country' => 'CM', 'city' => 'Yaounde',
    'website' => 'https://partner.example', 'membership_role' => 'owner', 'member_since' => '2026-11-02T15:00:00+00:00',
]]], 'meta' => ['operation' => 'partner.accounts.index'], 'messages' => []];
$partnerMembers = &$spec['paths']['/crm/partner-applications/{partnerApplication}/members'];
$partnerMemberList = &$partnerMembers['get'];
$partnerMemberList['summary'] = 'List authorized accounts for an approved partner organization';
$partnerMemberList['description'] = 'Administrator-only member directory for one approved partner application. Only active, email-verified users with the partner role are returned.';
$partnerMemberSchema = ['type' => 'object', 'required' => ['items', 'pagination'], 'properties' => [
    'items' => ['type' => 'array', 'items' => ['type' => 'object', 'required' => ['id', 'user_id', 'name', 'email', 'member_since'], 'properties' => [
        'id' => ['type' => 'integer', 'minimum' => 1], 'user_id' => ['type' => 'integer', 'minimum' => 1],
        'name' => ['type' => 'string'], 'email' => ['type' => 'string', 'format' => 'email'], 'member_since' => ['type' => 'string', 'format' => 'date-time'],
    ]]],
    'pagination' => ['type' => 'object', 'required' => ['current_page', 'per_page', 'total', 'last_page'], 'properties' => [
        'current_page' => ['type' => 'integer', 'minimum' => 1], 'per_page' => ['type' => 'integer', 'minimum' => 1, 'maximum' => 100],
        'total' => ['type' => 'integer', 'minimum' => 0], 'last_page' => ['type' => 'integer', 'minimum' => 1],
    ]],
]];
$partnerMemberList['responses']['200']['description'] = 'Paginated partner members with safe identity fields.';
$partnerMemberList['responses']['200']['content']['application/json']['schema'] = $successEnvelope($partnerMemberSchema, 'admin.enquiries.partnerApplications.members.index');
$partnerMemberCreate = &$partnerMembers['post'];
$partnerMemberCreate['summary'] = 'Grant an approved partner account membership';
$partnerMemberCreate['description'] = 'Administrator-only. Links an existing active, email-verified user with the partner role to an approved partner application. It does not create users or grant roles. Requires an Idempotency-Key.';
$partnerMemberCreate['responses']['201'] = $partnerMemberCreate['responses']['200'];
unset($partnerMemberCreate['responses']['200']);
$partnerMemberCreate['responses']['201']['description'] = 'Partner account membership created or already exists.';
$partnerMemberCreate['responses']['201']['content']['application/json']['schema'] = $successEnvelope([
    'type' => 'object', 'required' => ['membership_id', 'application_id', 'user_id'], 'properties' => [
        'membership_id' => ['type' => 'integer', 'minimum' => 1], 'application_id' => ['type' => 'integer', 'minimum' => 1],
        'user_id' => ['type' => 'integer', 'minimum' => 1],
    ],
], 'admin.enquiries.partnerApplications.members.add');
$partnerMemberCreate['requestBody']['content']['application/json']['schema'] = [
    'type' => 'object', 'additionalProperties' => false, 'required' => ['user_id', 'membership_role'], 'properties' => [
        'user_id' => ['type' => 'integer', 'minimum' => 1, 'description' => 'Existing active, verified partner account.'],
        'membership_role' => ['type' => 'string', 'enum' => ['owner', 'member']],
    ],
];
$spec['paths']['/crm/partner-applications/{partnerApplication}/members']['get']['responses']['200']['content']['application/json']['schema']['properties']['data']['properties']['items']['items']['required'][] = 'membership_role';
$spec['paths']['/crm/partner-applications/{partnerApplication}/members']['get']['responses']['200']['content']['application/json']['schema']['properties']['data']['properties']['items']['items']['properties']['membership_role'] = ['type' => 'string', 'enum' => ['owner', 'member']];
$partnerAccountUpdate = &$spec['paths']['/partner/accounts/{partnerApplication}']['patch'];
$partnerAccountUpdate['summary'] = 'Update an approved partner organization profile';
$partnerAccountUpdate['description'] = 'Organization owners may update only operational contact and description fields on an explicitly linked, approved partner account. Organization name, approval status, partner type, email and administrator notes cannot be changed through this operation. Requires partner:write.';
$partnerAccountUpdate['requestBody']['content']['application/json']['schema'] = [
    'type' => 'object', 'additionalProperties' => false, 'minProperties' => 1, 'properties' => [
        'phone' => ['type' => ['string', 'null'], 'maxLength' => 40],
        'country' => ['type' => 'string', 'maxLength' => 100], 'city' => ['type' => ['string', 'null'], 'maxLength' => 100],
        'organization_type' => ['type' => ['string', 'null'], 'maxLength' => 120],
        'target_market' => ['type' => ['string', 'null'], 'maxLength' => 1000],
        'description' => ['type' => 'string', 'maxLength' => 5000],
        'website' => ['type' => ['string', 'null'], 'format' => 'uri', 'maxLength' => 255],
    ],
];
$partnerAccountUpdate['responses']['200']['description'] = 'Updated approved organization profile.';
$partnerAccountUpdate['responses']['200']['content']['application/json']['schema'] = $successEnvelope([
    'type' => 'object', 'required' => ['account'], 'properties' => ['account' => [
        'type' => 'object', 'required' => ['application_id', 'organization_name', 'partner_type', 'country'], 'properties' => [
            'application_id' => ['type' => 'integer'], 'organization_name' => ['type' => 'string'], 'partner_type' => ['type' => 'string'],
            'organization_type' => ['type' => ['string', 'null']], 'country' => ['type' => 'string'], 'city' => ['type' => ['string', 'null']],
            'website' => ['type' => ['string', 'null'], 'format' => 'uri'], 'target_market' => ['type' => ['string', 'null']],
            'description' => ['type' => 'string'], 'phone' => ['type' => ['string', 'null']],
        ],
    ]],
], 'partner.accounts.update');
$partnerAccountUpdate['responses']['403'] = ['description' => 'Caller is not an owner of this approved organization.'];
$partnerAccountUpdate['responses']['404'] = ['description' => 'Approved organization not found.'];
$partnerAccountUpdate['responses']['422'] = ['description' => 'Invalid or empty update.'];
$partnerMemberRemove = &$spec['paths']['/crm/partner-applications/{partnerApplication}/members/{member}']['delete'];
$partnerMemberRemove['summary'] = 'Revoke partner account membership';
$partnerMemberRemove['description'] = 'Administrator-only and idempotent. Revokes only the selected user’s membership in the selected partner application. It does not deactivate the user or change roles.';
$partnerMemberRemove['responses']['204'] = ['description' => 'Membership was removed or was already absent.'];
unset($partnerMemberRemove['responses']['200']);
unset($partnerAccounts, $partnerMembers, $partnerMemberList, $partnerMemberCreate, $partnerMemberRemove, $partnerAccountUpdate);
$integrationRequestItem = [
    'type' => 'object', 'additionalProperties' => false,
    'required' => ['id', 'status', 'applicant', 'application', 'received_at'],
    'properties' => [
        'id' => ['type' => 'integer', 'minimum' => 1],
        'status' => ['type' => 'string', 'enum' => ['new', 'contacted', 'qualified', 'closed']],
        'applicant' => ['type' => 'object', 'additionalProperties' => false, 'required' => ['name', 'email', 'phone'], 'properties' => [
            'name' => ['type' => 'string'], 'email' => ['type' => 'string', 'format' => 'email'], 'phone' => ['type' => ['string', 'null']],
        ]],
        'application' => ['type' => 'object', 'additionalProperties' => false, 'required' => ['name', 'access_type', 'redirect_uri', 'requested_scopes', 'use_case'], 'properties' => [
            'name' => ['type' => 'string'], 'access_type' => ['type' => 'string', 'enum' => ['machine', 'delegated', 'both']],
            'redirect_uri' => ['type' => ['string', 'null'], 'format' => 'uri'],
            'requested_scopes' => ['type' => 'array', 'items' => ['type' => 'string']], 'use_case' => ['type' => 'string'],
        ]],
        'received_at' => ['type' => 'string', 'format' => 'date-time'],
    ],
];
$integrationRequestIndex = &$spec['paths']['/admin/enquiries/integration-requests']['get'];
$integrationRequestIndex['summary'] = 'Review API integration access requests';
$integrationRequestIndex['description'] = 'Lists structured developer access requests with applicant contact, requested access type, callbacks, requested scopes and intended use. Requires an active administrator with the enquiries:read scope. Requests are reviewed before credentials are provisioned.';
$integrationRequestIndex['responses']['200']['description'] = 'Paginated integration access requests. No credentials or secrets are included.';
$integrationRequestIndex['responses']['200']['content']['application/json']['schema'] = $successEnvelope([
    'type' => 'object', 'additionalProperties' => false, 'required' => ['items', 'pagination'], 'properties' => [
        'items' => ['type' => 'array', 'items' => $integrationRequestItem],
        'pagination' => ['type' => 'object', 'required' => ['current_page', 'per_page', 'total', 'last_page'], 'properties' => [
            'current_page' => ['type' => 'integer'], 'per_page' => ['type' => 'integer'], 'total' => ['type' => 'integer'], 'last_page' => ['type' => 'integer'],
        ]],
    ],
], 'admin.enquiries.integrationRequests.index');
$integrationRequestShow = &$spec['paths']['/admin/enquiries/integration-requests/{lead}']['get'];
$integrationRequestShow['summary'] = 'Inspect a developer access request';
$integrationRequestShow['description'] = 'Returns the structured application request only when the selected enquiry is an integration request. Requires an active administrator with the enquiries:read scope.';
$integrationRequestShow['responses']['200']['description'] = 'Allowlisted application and applicant details; no credentials, patient data or internal delivery metadata.';
$integrationRequestShow['responses']['200']['content']['application/json']['schema'] = $successEnvelope([
    'type' => 'object', 'additionalProperties' => false, 'required' => ['integration_request'],
    'properties' => ['integration_request' => $integrationRequestItem],
], 'admin.enquiries.integrationRequests.show');
unset($integrationRequestIndex, $integrationRequestShow, $integrationRequestItem);
$spec['paths']['/catalogue/products']['get']['summary'] = 'List published software products';
$spec['paths']['/catalogue/products']['get']['description'] = 'Returns published software catalogue metadata only, with bounded pagination and English or French labels. Requires an approved server client and the catalogue:read scope. This endpoint does not expose clinical or customer records.';
$spec['paths']['/integration']['get']['summary'] = 'Read the current integration client';
$spec['paths']['/integration']['get']['description'] = 'Returns the authenticated machine client identity, its approved scopes and its UTC daily authenticated-request count. Requires the integration:read scope. A caller cannot select another client.';
$usageOperation = [
    'operationId' => 'readIntegrationUsage', 'summary' => 'Read daily usage for the current integration', 'tags' => ['Identity'],
    'description' => 'Returns only the authenticated machine client’s daily API request counts for the trailing 30 UTC calendar days, including today. The count is limited to approved client-credentials requests on machine API routes and includes this request. It excludes public traffic, delegated-user OAuth requests, route-level breakdowns and other clients. Requires the integration:read scope.',
    'security' => [['OAuth2' => ['integration:read']]],
    'responses' => [
        '200' => ['description' => 'Client-scoped daily usage series with exactly 30 UTC date buckets.', 'headers' => [
            'Cache-Control' => ['description' => 'Authenticated results are not stored by shared caches.', 'schema' => ['type' => 'string', 'const' => 'no-store']],
            'X-Request-ID' => ['description' => 'Identifier used when contacting support.', 'schema' => ['type' => 'string', 'format' => 'uuid']],
        ], 'content' => ['application/json' => ['schema' => ['type' => 'object', 'required' => ['data'], 'additionalProperties' => false, 'properties' => ['data' => null]]]]],
        '401' => ['description' => 'Missing, invalid, revoked or expired machine access; disabled owner or revoked client.', 'content' => ['application/problem+json' => ['schema' => ['$ref' => '#/components/schemas/Problem']]]],
        '403' => ['description' => 'Required integration:read scope is absent from the token or current client approval.', 'content' => ['application/problem+json' => ['schema' => ['$ref' => '#/components/schemas/Problem']]]],
        '429' => ['description' => '60 authenticated attempts per minute per client exceeded.', 'headers' => ['Retry-After' => ['schema' => ['type' => 'integer', 'minimum' => 0]]], 'content' => ['application/problem+json' => ['schema' => ['$ref' => '#/components/schemas/Problem']]]],
    ],
];
$integrationData = ['type' => 'object', 'additionalProperties' => false,
    'required' => ['period_start', 'period_end', 'requests_today', 'requests_last_30_days', 'daily_usage'],
    'properties' => [
        'period_start' => ['type' => 'string', 'format' => 'date', 'description' => 'First included UTC date; 29 calendar days before period_end.'],
        'period_end' => ['type' => 'string', 'format' => 'date', 'description' => 'Current UTC date.'],
        'requests_today' => ['type' => 'integer', 'minimum' => 0],
        'requests_last_30_days' => ['type' => 'integer', 'minimum' => 0],
        'daily_usage' => ['type' => 'array', 'minItems' => 30, 'maxItems' => 30, 'items' => ['type' => 'object', 'additionalProperties' => false,
            'required' => ['date', 'requests'], 'properties' => ['date' => ['type' => 'string', 'format' => 'date'], 'requests' => ['type' => 'integer', 'minimum' => 0]]]],
    ]];
$usageOperation['responses']['200']['content']['application/json']['schema']['properties']['data'] = $integrationData;
$usageExampleStart = \Carbon\CarbonImmutable::parse('2026-10-04', 'UTC');
$usageExample = [];
for ($day = 0; $day < 30; $day++) $usageExample[] = ['date' => $usageExampleStart->addDays($day)->toDateString(), 'requests' => $day === 0 ? 9 : ($day === 28 ? 2 : ($day === 29 ? 1 : 0))];
$usageOperation['responses']['200']['content']['application/json']['example'] = ['data' => [
    'period_start' => '2026-10-04', 'period_end' => '2026-11-02', 'requests_today' => 1, 'requests_last_30_days' => 12,
    'daily_usage' => $usageExample,
]];
$spec['paths']['/integration/usage']['get'] = $usageOperation;
unset($usageOperation);
$requestMetricPeriod = [
    'period_start' => ['type' => 'string', 'format' => 'date'],
    'period_end' => ['type' => 'string', 'format' => 'date'],
    'requests' => ['type' => 'integer', 'minimum' => 0],
    'responses_2xx' => ['type' => 'integer', 'minimum' => 0],
    'responses_3xx' => ['type' => 'integer', 'minimum' => 0],
    'responses_4xx' => ['type' => 'integer', 'minimum' => 0],
    'responses_5xx' => ['type' => 'integer', 'minimum' => 0],
    'average_response_time_ms' => ['type' => 'number', 'minimum' => 0],
    'max_response_time_ms' => ['type' => 'integer', 'minimum' => 0],
    'daily' => ['type' => 'array', 'minItems' => 30, 'maxItems' => 30, 'items' => ['type' => 'object', 'required' => ['date', 'requests', 'responses_4xx', 'responses_5xx'], 'properties' => [
        'date' => ['type' => 'string', 'format' => 'date'], 'requests' => ['type' => 'integer', 'minimum' => 0],
        'responses_4xx' => ['type' => 'integer', 'minimum' => 0], 'responses_5xx' => ['type' => 'integer', 'minimum' => 0],
    ]]],
    'operations' => ['type' => 'array', 'maxItems' => 25, 'items' => ['type' => 'object', 'required' => ['operation', 'method', 'requests', 'responses_4xx', 'responses_5xx', 'average_response_time_ms', 'max_response_time_ms'], 'properties' => [
        'operation' => ['type' => 'string'], 'method' => ['type' => 'string'], 'requests' => ['type' => 'integer', 'minimum' => 0],
        'responses_4xx' => ['type' => 'integer', 'minimum' => 0], 'responses_5xx' => ['type' => 'integer', 'minimum' => 0],
        'average_response_time_ms' => ['type' => 'number', 'minimum' => 0], 'max_response_time_ms' => ['type' => 'integer', 'minimum' => 0],
    ]]],
];
$spec['components']['schemas']['ApiRequestMetrics'] = ['type' => 'object', 'required' => array_keys($requestMetricPeriod), 'properties' => $requestMetricPeriod];
$metricSummary = ['type' => 'object', 'required' => ['data'], 'properties' => ['data' => ['$ref' => '#/components/schemas/ApiRequestMetrics']]];
$spec['paths']['/integration/metrics']['get'] = [
    'operationId' => 'readOwnMachineRequestMetrics', 'summary' => 'Read aggregate request metrics for this machine client', 'tags' => ['Identity'],
    'description' => 'Returns the current machine client’s own 30-day UTC daily totals, HTTP status classes, top 25 API operations and aggregate response times. Only requests that passed machine-client authentication are attributable. The report contains route templates and aggregate counters only; it does not retain request URLs, query parameters, payloads, headers or record identifiers. Measurements are delayed until each request completes and are not an availability objective or SLA.',
    'security' => [['OAuth2' => ['integration:read']]],
    'responses' => [
        '200' => ['description' => 'Current client-only request metrics.', 'headers' => ['Cache-Control' => $noStoreHeader, 'X-Request-ID' => ['schema' => ['type' => 'string', 'format' => 'uuid']]], 'content' => ['application/json' => ['schema' => $metricSummary]]],
        '401' => ['description' => 'Missing, invalid, revoked or expired machine access.'],
        '403' => ['description' => 'integration:read scope is required.'],
        '429' => ['description' => 'The client quota or request rate limit was reached.'],
    ],
];
$userToken = $spec['paths']['/oauth/token']['post'];
$userToken['operationId'] = 'issueDelegatedUserToken';
$userToken['summary'] = 'Exchange an authorization code or refresh token';
$userToken['description'] = 'User authorization requires an exact registered HTTPS redirect or the reserved OPES Health native callback, consent, state of at least 32 characters, and S256 PKCE. Access tokens expire in 15 minutes; refresh tokens expire in seven days. Role and client scope approval are checked again on issuance and access.';
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
$spec['paths']['/me/usage']['get'] = ['operationId' => 'readOwnBusinessUsage', 'summary' => 'Read your own API usage for this application', 'tags' => ['Identity'],
    'description' => 'Returns exactly 30 UTC daily request totals for the authenticated user and this OAuth application. It excludes requests made by other users or other applications; no clinical or business record data is included.',
    'security' => [['UserOAuth2' => ['identity:read']]], 'responses' => [
        '200' => ['description' => 'Authenticated user and OAuth-client scoped daily usage.', 'content' => ['application/json' => ['schema' => $successEnvelope([
            'type' => 'object', 'required' => ['period_start', 'period_end', 'requests_today', 'requests_last_30_days', 'daily_usage'], 'properties' => [
                'period_start' => ['type' => 'string', 'format' => 'date'], 'period_end' => ['type' => 'string', 'format' => 'date'],
                'requests_today' => ['type' => 'integer', 'minimum' => 0], 'requests_last_30_days' => ['type' => 'integer', 'minimum' => 0],
                'daily_usage' => ['type' => 'array', 'minItems' => 30, 'maxItems' => 30, 'items' => ['type' => 'object', 'required' => ['date', 'requests'], 'properties' => [
                    'date' => ['type' => 'string', 'format' => 'date'], 'requests' => ['type' => 'integer', 'minimum' => 0],
                ]]],
            ],
        ], 'identity.me.usage')]]],
        '401' => ['description' => 'Delegated user authentication required.'], '403' => ['description' => 'identity:read scope required.'], '429' => ['description' => 'Rate limit exceeded.']]];
$spec['paths']['/me/metrics']['get'] = [
    'operationId' => 'readOwnBusinessRequestMetrics', 'summary' => 'Read aggregate request metrics for your account and this application', 'tags' => ['Identity'],
    'description' => 'Returns the authenticated user’s own 30-day UTC daily totals, HTTP status classes, top 25 API operations and aggregate response times for this OAuth application. It excludes other users and applications. The report contains route templates and aggregate counters only; request URLs, query parameters, payloads, headers and record identifiers are not retained. Measurements are delayed until each request completes and are not an availability objective or SLA.',
    'security' => [['UserOAuth2' => ['identity:read']]],
    'responses' => [
        '200' => ['description' => 'Authenticated user and OAuth-client scoped request metrics.', 'headers' => ['Cache-Control' => $noStoreHeader, 'X-Request-ID' => ['schema' => ['type' => 'string', 'format' => 'uuid']]], 'content' => ['application/json' => ['schema' => $metricSummary]]],
        '401' => ['description' => 'Delegated user authentication required.'],
        '403' => ['description' => 'identity:read scope is required.'],
        '429' => ['description' => 'The request rate limit was reached.'],
    ],
];
$spec['paths']['/me']['get'] = ['operationId' => 'readOwnProfile', 'summary' => 'Read your own account and granted roles', 'tags' => ['Identity'],
    'description' => 'Returns the authenticated active user’s own account name, email, current roles and the intersection of role-approved and token-granted business scopes. Intended for role-aware client navigation; it does not grant additional access.',
    'security' => [['UserOAuth2' => ['identity:read']]], 'responses' => [
        '200' => ['description' => 'The authenticated user’s own account and current effective scopes.', 'content' => ['application/json' => ['schema' => $successEnvelope([
            'type' => 'object', 'required' => ['id', 'name', 'email', 'roles', 'scopes'], 'properties' => [
                'id' => ['type' => 'string'], 'name' => ['type' => 'string'], 'email' => ['type' => 'string', 'format' => 'email'],
                'roles' => ['type' => 'array', 'items' => ['type' => 'string']], 'scopes' => ['type' => 'array', 'items' => ['type' => 'string']],
            ],
        ], 'identity.me')]]],
        '401' => ['description' => 'Delegated user authentication required.'], '403' => ['description' => 'identity:read scope required.'], '429' => ['description' => 'Rate limit exceeded.']]];
$quoteItemSchema = ['type' => 'object', 'required' => ['id', 'quote_id', 'product_name', 'description', 'quantity', 'unit_price', 'total', 'created_at', 'updated_at'], 'properties' => [
    'id' => ['type' => 'integer'], 'quote_id' => ['type' => 'integer'], 'product_name' => ['type' => 'string'], 'description' => ['type' => ['string', 'null']],
    'quantity' => ['type' => 'number'], 'unit_price' => ['type' => 'number'], 'total' => ['type' => 'number'],
    'created_at' => ['type' => 'string', 'format' => 'date-time'], 'updated_at' => ['type' => 'string', 'format' => 'date-time'],
]];
$quoteRecordSchema = ['type' => 'object', 'required' => ['id', 'reference', 'lead_id', 'title', 'status', 'valid_until', 'subtotal', 'tax_rate', 'tax_amount', 'total', 'currency', 'notes', 'created_by', 'created_at', 'updated_at', 'lead', 'creator', 'items'], 'properties' => [
    'id' => ['type' => 'integer'], 'reference' => ['type' => 'string'], 'lead_id' => ['type' => ['integer', 'null']], 'title' => ['type' => 'string'],
    'status' => ['type' => 'string', 'enum' => ['draft', 'sent', 'accepted', 'rejected', 'expired']], 'valid_until' => ['type' => ['string', 'null'], 'format' => 'date'],
    'subtotal' => ['type' => 'number'], 'tax_rate' => ['type' => 'number'], 'tax_amount' => ['type' => 'number'], 'total' => ['type' => 'number'],
    'currency' => ['type' => 'string'], 'notes' => ['type' => ['string', 'null']], 'created_by' => ['type' => 'integer'],
    'created_at' => ['type' => 'string', 'format' => 'date-time'], 'updated_at' => ['type' => 'string', 'format' => 'date-time'],
    'lead' => ['type' => ['object', 'null'], 'properties' => ['id' => ['type' => 'integer'], 'name' => ['type' => 'string'], 'email' => ['type' => 'string', 'format' => 'email'], 'phone' => ['type' => ['string', 'null']], 'facility_type' => ['type' => ['string', 'null']]]],
    'creator' => ['type' => ['object', 'null'], 'properties' => ['id' => ['type' => 'integer'], 'name' => ['type' => 'string'], 'email' => ['type' => 'string', 'format' => 'email']]],
    'items' => ['type' => 'array', 'items' => $quoteItemSchema],
]];
$quoteList = &$spec['paths']['/finance/quotes']['get'];
$quoteList['responses']['200']['description'] = 'Paginated quotes with lead, creator and line-item details for quote review.';
$quoteList['responses']['200']['content']['application/json']['schema'] = $successEnvelope(['type' => 'object', 'required' => ['items', 'pagination'], 'properties' => [
    'items' => ['type' => 'array', 'items' => $quoteRecordSchema],
    'pagination' => ['type' => 'object', 'required' => ['current_page', 'per_page', 'total', 'last_page'], 'properties' => [
        'current_page' => ['type' => 'integer'], 'per_page' => ['type' => 'integer'], 'total' => ['type' => 'integer'], 'last_page' => ['type' => 'integer'],
    ]],
]], 'admin.finance.quotes.index');
unset($quoteList);
$customerSummarySchema = ['type' => 'object', 'required' => ['id', 'user_id', 'facility_name', 'facility_type', 'country', 'city', 'address', 'created_at', 'updated_at', 'customer'], 'properties' => [
    'id' => ['type' => 'integer'], 'user_id' => ['type' => 'integer'], 'facility_name' => ['type' => ['string', 'null']], 'facility_type' => ['type' => ['string', 'null']],
    'country' => ['type' => 'string'], 'city' => ['type' => ['string', 'null']], 'address' => ['type' => ['string', 'null']],
    'created_at' => ['type' => 'string', 'format' => 'date-time'], 'updated_at' => ['type' => 'string', 'format' => 'date-time'],
    'customer' => ['type' => ['object', 'null'], 'required' => ['id', 'name', 'email'], 'properties' => ['id' => ['type' => 'integer'], 'name' => ['type' => 'string'], 'email' => ['type' => 'string', 'format' => 'email']]],
]];
$customerIndex = &$spec['paths']['/admin/customer-profiles']['get'];
$customerIndex['responses']['200']['content']['application/json']['schema'] = $successEnvelope(['type' => 'object', 'required' => ['items', 'pagination'], 'properties' => [
    'items' => ['type' => 'array', 'items' => $customerSummarySchema],
    'pagination' => ['type' => 'object', 'required' => ['current_page', 'per_page', 'total', 'last_page'], 'properties' => [
        'current_page' => ['type' => 'integer'], 'per_page' => ['type' => 'integer'], 'total' => ['type' => 'integer'], 'last_page' => ['type' => 'integer'],
    ]],
]], 'admin.customerProfiles.index');
unset($customerIndex);
$customerShow = &$spec['paths']['/admin/customer-profiles/{customerProfile}']['get'];
$customerDetailProperties = $customerSummarySchema['properties'];
$customerDetailProperties['customer']['required'][] = 'phone';
$customerDetailProperties['customer']['properties']['phone'] = ['type' => ['string', 'null']];
$customerShow['responses']['200']['content']['application/json']['schema'] = $successEnvelope(['type' => 'object', 'required' => ['customer_profile'], 'properties' => [
    'customer_profile' => ['type' => 'object', 'required' => ['id', 'user_id', 'facility_name', 'facility_type', 'country', 'city', 'address', 'created_at', 'updated_at', 'customer'], 'properties' => $customerDetailProperties],
]], 'admin.customerProfiles.show');
unset($customerShow);
$learningLesson = ['type' => 'object', 'required' => ['id', 'course_id', 'title', 'title_fr', 'content', 'content_fr', 'video_url', 'duration_minutes', 'sort_order', 'created_at', 'updated_at'], 'properties' => [
    'id' => ['type' => 'integer'], 'course_id' => ['type' => 'integer'], 'title' => ['type' => 'string'], 'title_fr' => ['type' => ['string', 'null']],
    'content' => ['type' => 'string'], 'content_fr' => ['type' => ['string', 'null']], 'video_url' => ['type' => ['string', 'null']],
    'duration_minutes' => ['type' => ['integer', 'null']], 'sort_order' => ['type' => 'integer'],
    'created_at' => ['type' => 'string', 'format' => 'date-time'], 'updated_at' => ['type' => 'string', 'format' => 'date-time'],
]];
$learningCourse = ['type' => 'object', 'required' => ['id', 'product_slug', 'title', 'title_fr', 'slug', 'description', 'description_fr', 'level', 'duration_hours', 'cover_image', 'is_active', 'is_featured', 'sort_order', 'created_at', 'updated_at'], 'properties' => [
    'id' => ['type' => 'integer'], 'product_slug' => ['type' => ['string', 'null']], 'title' => ['type' => 'string'], 'title_fr' => ['type' => ['string', 'null']], 'slug' => ['type' => 'string'],
    'description' => ['type' => ['string', 'null']], 'description_fr' => ['type' => ['string', 'null']], 'level' => ['type' => 'string', 'enum' => ['beginner', 'intermediate', 'advanced']],
    'duration_hours' => ['type' => ['number', 'null']], 'cover_image' => ['type' => ['string', 'null']], 'is_active' => ['type' => 'boolean'], 'is_featured' => ['type' => 'boolean'],
    'sort_order' => ['type' => 'integer'], 'created_at' => ['type' => 'string', 'format' => 'date-time'], 'updated_at' => ['type' => 'string', 'format' => 'date-time'],
    'lessons' => ['type' => 'array', 'items' => $learningLesson],
]];
$learningEnrollment = ['type' => 'object', 'required' => ['id', 'course_id', 'user_id', 'status', 'enrolled_at', 'completed_at', 'created_at', 'updated_at'], 'properties' => [
    'id' => ['type' => 'integer'], 'course_id' => ['type' => 'integer'], 'user_id' => ['type' => 'integer'], 'status' => ['type' => 'string', 'enum' => ['enrolled', 'in_progress', 'completed', 'dropped']],
    'enrolled_at' => ['type' => ['string', 'null'], 'format' => 'date-time'], 'completed_at' => ['type' => ['string', 'null'], 'format' => 'date-time'],
    'created_at' => ['type' => 'string', 'format' => 'date-time'], 'updated_at' => ['type' => 'string', 'format' => 'date-time'],
]];
$learningProgress = ['type' => 'object', 'required' => ['id', 'course_id', 'status', 'progress_percent', 'completed_lesson_ids', 'total_lessons', 'certificate_id', 'enrolled_at', 'completed_at'], 'properties' => [
    'id' => ['type' => 'integer'], 'course_id' => ['type' => 'integer'], 'status' => ['type' => 'string', 'enum' => ['enrolled', 'in_progress', 'completed', 'dropped']],
    'progress_percent' => ['type' => 'integer', 'minimum' => 0, 'maximum' => 100], 'completed_lesson_ids' => ['type' => 'array', 'items' => ['type' => 'integer']],
    'total_lessons' => ['type' => 'integer', 'minimum' => 0], 'certificate_id' => ['type' => ['integer', 'null']],
    'enrolled_at' => ['type' => ['string', 'null'], 'format' => 'date-time'], 'completed_at' => ['type' => ['string', 'null'], 'format' => 'date-time'],
]];
$courseCertificate = ['type' => 'object', 'required' => ['id', 'enrollment_id', 'user_id', 'course_id', 'certificate_number', 'issued_at', 'created_at', 'updated_at'], 'properties' => [
    'id' => ['type' => 'integer'], 'enrollment_id' => ['type' => 'integer'], 'user_id' => ['type' => 'integer'], 'course_id' => ['type' => 'integer'],
    'certificate_number' => ['type' => 'string'], 'issued_at' => ['type' => 'string', 'format' => 'date-time'], 'created_at' => ['type' => 'string', 'format' => 'date-time'], 'updated_at' => ['type' => 'string', 'format' => 'date-time'],
]];
$validationCertificate = ['type' => 'object', 'required' => ['id', 'cohort_member_id', 'final_evaluation_id', 'certificate_number', 'score', 'tier', 'issued_by', 'issued_at', 'created_at', 'updated_at'], 'properties' => [
    'id' => ['type' => 'integer'], 'cohort_member_id' => ['type' => 'integer'], 'final_evaluation_id' => ['type' => 'integer'], 'certificate_number' => ['type' => 'string'],
    'score' => ['type' => 'integer'], 'tier' => ['type' => 'string'], 'issued_by' => ['type' => ['integer', 'null']], 'issued_at' => ['type' => 'string', 'format' => 'date-time'],
    'created_at' => ['type' => 'string', 'format' => 'date-time'], 'updated_at' => ['type' => 'string', 'format' => 'date-time'],
]];
$courseIndex = &$spec['paths']['/practitioner/courses']['get'];
$courseIndex['responses']['200']['description'] = 'Active courses and this practitioner’s per-course lesson progress and certificate references.';
$courseIndex['responses']['200']['content']['application/json']['schema'] = $successEnvelope(['type' => 'object', 'required' => ['courses', 'my_enrollments'], 'properties' => [
    'courses' => ['type' => 'array', 'items' => $learningCourse], 'my_enrollments' => ['type' => 'array', 'items' => $learningProgress],
]], 'practitioner.coursecontroller.index');
unset($courseIndex);
$courseShow = &$spec['paths']['/practitioner/courses/{course}']['get'];
$courseShow['responses']['200']['content']['application/json']['schema'] = $successEnvelope(['type' => 'object', 'required' => ['course', 'enrollment', 'completed_lesson_ids'], 'properties' => [
    'course' => $learningCourse, 'enrollment' => ['oneOf' => [$learningEnrollment, ['type' => 'null']]], 'completed_lesson_ids' => ['type' => 'array', 'items' => ['type' => 'integer']],
]], 'practitioner.coursecontroller.show');
unset($courseShow);
$lessonShow = &$spec['paths']['/practitioner/courses/{course}/lessons/{lesson}']['get'];
$lessonShow['responses']['200']['content']['application/json']['schema'] = $successEnvelope(['type' => 'object', 'required' => ['course', 'lesson', 'enrollment', 'completed_lesson_ids', 'prev_lesson', 'next_lesson'], 'properties' => [
    'course' => $learningCourse, 'lesson' => $learningLesson, 'enrollment' => $learningEnrollment,
    'completed_lesson_ids' => ['type' => 'array', 'items' => ['type' => 'integer']], 'prev_lesson' => ['oneOf' => [$learningLesson, ['type' => 'null']]], 'next_lesson' => ['oneOf' => [$learningLesson, ['type' => 'null']]],
]], 'practitioner.lessoncontroller.show');
unset($lessonShow);
$certificateIndex = &$spec['paths']['/practitioner/certificates']['get'];
$certificateIndex['responses']['200']['content']['application/json']['schema'] = $successEnvelope(['type' => 'object', 'required' => ['certificates', 'validation_certificates', 'council_membership'], 'properties' => [
    'certificates' => ['type' => 'array', 'items' => $courseCertificate], 'validation_certificates' => ['type' => 'array', 'items' => $validationCertificate],
    'council_membership' => ['oneOf' => [['type' => 'object', 'required' => ['id', 'user_id', 'validation_certificate_id', 'title', 'term_start', 'term_end', 'status', 'invited_by', 'invited_at', 'created_at', 'updated_at'], 'properties' => [
        'id' => ['type' => 'integer'], 'user_id' => ['type' => 'integer'], 'validation_certificate_id' => ['type' => ['integer', 'null']], 'title' => ['type' => ['string', 'null']],
        'term_start' => ['type' => ['string', 'null'], 'format' => 'date'], 'term_end' => ['type' => ['string', 'null'], 'format' => 'date'], 'status' => ['type' => 'string'],
        'invited_by' => ['type' => ['integer', 'null']], 'invited_at' => ['type' => ['string', 'null'], 'format' => 'date-time'], 'created_at' => ['type' => 'string', 'format' => 'date-time'], 'updated_at' => ['type' => 'string', 'format' => 'date-time'],
    ]], ['type' => 'null']]],
]], 'practitioner.certificatecontroller.index');
unset($certificateIndex['responses']['200']['content']['application/pdf']);
unset($certificateIndex);
$examChoice = ['type' => 'object', 'required' => ['id', 'label'], 'properties' => ['id' => ['type' => 'string'], 'label' => ['type' => 'string']]];
$examQuestion = ['type' => 'object', 'required' => ['id', 'prompt', 'choices', 'points'], 'properties' => [
    'id' => ['type' => 'string'], 'prompt' => ['type' => 'string'], 'choices' => ['type' => 'array', 'minItems' => 2, 'items' => $examChoice], 'points' => ['type' => 'integer', 'minimum' => 1],
]];
$learnerAssessment = ['type' => 'object', 'required' => ['id', 'course_id', 'course_slug', 'title', 'instructions', 'pass_mark', 'attempt_limit', 'duration_minutes', 'questions'], 'properties' => [
    'id' => ['type' => 'integer'], 'course_id' => ['type' => 'integer'], 'course_slug' => ['type' => ['string', 'null']], 'title' => ['type' => 'string'], 'instructions' => ['type' => ['string', 'null']],
    'pass_mark' => ['type' => 'integer', 'minimum' => 1, 'maximum' => 100], 'attempt_limit' => ['type' => ['integer', 'null']], 'duration_minutes' => ['type' => ['integer', 'null']],
    'questions' => ['type' => 'array', 'items' => $examQuestion],
]];
$assessmentAttemptSummary = ['type' => 'object', 'required' => ['id', 'status', 'score', 'passed', 'started_at', 'submitted_at'], 'properties' => [
    'id' => ['type' => 'integer'], 'status' => ['type' => 'string', 'enum' => ['in_progress', 'submitted', 'expired']], 'score' => ['type' => ['number', 'string', 'null']],
    'passed' => ['type' => ['boolean', 'null']], 'started_at' => ['type' => ['string', 'null'], 'format' => 'date-time'], 'submitted_at' => ['type' => ['string', 'null'], 'format' => 'date-time'],
]];
$attemptsRemaining = ['type' => ['integer', 'null'], 'minimum' => 0];
$examList = &$spec['paths']['/practitioner/courses/{course}/assessments']['get'];
$examList['responses']['200']['description'] = 'Published exams for an enrolled practitioner, with attempt counts and the latest result. Correct answers are not returned.';
$examList['responses']['200']['content']['application/json']['schema'] = $successEnvelope(['type' => 'object', 'required' => ['assessments'], 'properties' => ['assessments' => ['type' => 'array', 'items' => [
    'type' => 'object', 'required' => ['id', 'course_id', 'title', 'instructions', 'pass_mark', 'attempt_limit', 'duration_minutes', 'attempts_used', 'attempts_remaining', 'latest_attempt'], 'properties' => [
        'id' => ['type' => 'integer'], 'course_id' => ['type' => 'integer'], 'title' => ['type' => 'string'], 'instructions' => ['type' => ['string', 'null']],
        'pass_mark' => ['type' => 'integer'], 'attempt_limit' => ['type' => ['integer', 'null']], 'duration_minutes' => ['type' => ['integer', 'null']],
        'attempts_used' => ['type' => 'integer', 'minimum' => 0], 'attempts_remaining' => $attemptsRemaining,
        'latest_attempt' => ['oneOf' => [$assessmentAttemptSummary, ['type' => 'null']]],
    ],
]]]], 'practitioner.assessments.index');
unset($examList);
$examShow = &$spec['paths']['/practitioner/assessments/{assessment}']['get'];
$examShow['responses']['200']['description'] = 'Published exam questions and this practitioner’s attempts. Learner question data excludes correct choice identifiers.';
$examShow['responses']['200']['content']['application/json']['schema'] = $successEnvelope(['type' => 'object', 'required' => ['assessment', 'attempts', 'attempts_used', 'attempts_remaining'], 'properties' => [
    'assessment' => $learnerAssessment, 'attempts' => ['type' => 'array', 'items' => $assessmentAttemptSummary], 'attempts_used' => ['type' => 'integer', 'minimum' => 0], 'attempts_remaining' => $attemptsRemaining,
]], 'practitioner.assessments.show');
unset($examShow);
$examAttempt = &$spec['paths']['/practitioner/assessments/{assessment}/attempts/{attempt}']['get'];
$examAttempt['responses']['200']['description'] = 'Reads only the authenticated practitioner’s attempt. Correct answer review appears only after submission.';
$examAttempt['responses']['200']['content']['application/json']['schema'] = $successEnvelope(['type' => 'object', 'required' => ['attempt'], 'properties' => [
    'attempt' => $assessmentAttemptSummary, 'answers' => ['type' => 'object', 'additionalProperties' => ['type' => 'string']],
    'deadline' => ['type' => ['string', 'null'], 'format' => 'date-time'],
    'review' => ['type' => 'array', 'items' => ['type' => 'object', 'required' => ['question_id', 'prompt', 'selected_choice_id', 'selected_choice_label', 'correct_choice_id', 'correct_choice_label', 'correct', 'explanation'], 'properties' => [
        'question_id' => ['type' => 'string'], 'prompt' => ['type' => 'string'], 'selected_choice_id' => ['type' => ['string', 'null']], 'selected_choice_label' => ['type' => ['string', 'null']],
        'correct_choice_id' => ['type' => 'string'], 'correct_choice_label' => ['type' => ['string', 'null']], 'correct' => ['type' => 'boolean'], 'explanation' => ['type' => ['string', 'null']],
    ]]],
]], 'practitioner.assessments.attempt');
unset($examAttempt);
$saveAnswers = &$spec['paths']['/practitioner/assessments/{assessment}/attempts/{attempt}']['patch'];
$saveAnswers['responses']['200']['content']['application/json']['schema'] = $successEnvelope(['type' => 'object', 'required' => ['attempt', 'answered_count', 'question_count'], 'properties' => [
    'attempt' => ['type' => 'object', 'required' => ['id', 'status', 'started_at'], 'properties' => ['id' => ['type' => 'integer'], 'status' => ['type' => 'string', 'const' => 'in_progress'], 'started_at' => ['type' => 'string', 'format' => 'date-time']]],
    'answered_count' => ['type' => 'integer', 'minimum' => 0], 'question_count' => ['type' => 'integer', 'minimum' => 0],
]], 'practitioner.assessments.answers.save');
unset($saveAnswers);
$startAttempt = &$spec['paths']['/practitioner/assessments/{assessment}/attempts']['post'];
$startAttempt['responses']['201']['content']['application/json']['schema'] = $successEnvelope(['type' => 'object', 'required' => ['attempt', 'assessment', 'deadline'], 'properties' => [
    'attempt' => ['type' => 'object', 'required' => ['id', 'status', 'started_at', 'answers'], 'properties' => ['id' => ['type' => 'integer'], 'status' => ['type' => 'string', 'const' => 'in_progress'], 'started_at' => ['type' => 'string', 'format' => 'date-time'], 'answers' => ['type' => 'object', 'additionalProperties' => ['type' => 'string']]]],
    'assessment' => $learnerAssessment, 'deadline' => ['type' => ['string', 'null'], 'format' => 'date-time'],
]], 'practitioner.assessments.attempts.start');
unset($startAttempt['responses']['200']);
unset($startAttempt);
$submitAttempt = &$spec['paths']['/practitioner/assessments/{assessment}/attempts/{attempt}/submit']['post'];
$submitAttempt['responses']['200']['content']['application/json']['schema'] = $successEnvelope(['type' => 'object', 'required' => ['attempt', 'review', 'score', 'passed', 'pass_mark'], 'properties' => [
    'attempt' => $assessmentAttemptSummary,
    'review' => ['type' => 'array', 'items' => ['type' => 'object', 'required' => ['question_id', 'selected_choice_id', 'correct_choice_id', 'correct', 'explanation'], 'properties' => [
        'question_id' => ['type' => 'string'], 'selected_choice_id' => ['type' => 'string'], 'correct_choice_id' => ['type' => 'string'], 'correct' => ['type' => 'boolean'], 'explanation' => ['type' => ['string', 'null']],
    ]]],
    'score' => ['type' => 'number', 'minimum' => 0, 'maximum' => 100], 'passed' => ['type' => 'boolean'], 'pass_mark' => ['type' => 'integer', 'minimum' => 1, 'maximum' => 100],
]], 'practitioner.assessments.attempts.submit');
unset($submitAttempt);
$customerProfileResponse = $successEnvelope(['type' => 'object', 'required' => ['user', 'profile'], 'properties' => [
    'user' => ['type' => 'object', 'required' => ['id', 'name', 'email', 'phone', 'is_active', 'position', 'avatar', 'employee_id', 'department_id'], 'properties' => [
        'id' => ['type' => 'integer'], 'name' => ['type' => 'string'], 'email' => ['type' => 'string', 'format' => 'email'], 'phone' => ['type' => ['string', 'null']],
        'is_active' => ['type' => 'boolean'], 'position' => ['type' => ['string', 'null']], 'avatar' => ['type' => ['string', 'null']],
        'employee_id' => ['type' => ['string', 'null']], 'department_id' => ['type' => ['integer', 'null']],
    ]],
    'profile' => ['type' => 'object', 'required' => ['id', 'user_id', 'facility_name', 'facility_type', 'country', 'city', 'address', 'created_at', 'updated_at'], 'properties' => [
        'id' => ['type' => ['integer', 'null']], 'user_id' => ['type' => 'integer'], 'facility_name' => ['type' => ['string', 'null']], 'facility_type' => ['type' => ['string', 'null']],
        'country' => ['type' => ['string', 'null']], 'city' => ['type' => ['string', 'null']], 'address' => ['type' => ['string', 'null']],
        'created_at' => ['type' => ['string', 'null'], 'format' => 'date-time'], 'updated_at' => ['type' => ['string', 'null'], 'format' => 'date-time'],
    ]],
]], 'customer.profilecontroller.show');
$customerProfileShow = &$spec['paths']['/customer/profile']['get'];
$customerProfileShow['responses']['200']['content']['application/json']['schema'] = $customerProfileResponse;
unset($customerProfileShow);
$customerProfileUpdate = &$spec['paths']['/customer/profile']['put'];
$customerPasswordUpdate = &$spec['paths']['/customer/profile/password']['put'];
$customerCommandResponse = static function (string $operation): array { return [
    'type' => 'object', 'required' => ['data', 'meta', 'messages'], 'properties' => [
        'data' => ['type' => 'object', 'required' => ['locale'], 'properties' => ['locale' => ['type' => 'string', 'enum' => ['en', 'fr']]]],
        'meta' => ['type' => 'object', 'required' => ['operation'], 'properties' => ['operation' => ['type' => 'string', 'const' => $operation]]],
        'messages' => ['type' => 'object', 'required' => ['success'], 'properties' => ['success' => ['type' => 'string']]],
    ],
]; };
$customerProfileUpdate['responses']['200']['description'] = 'Returns the locale and success acknowledgement after updating the authenticated customer profile.';
$customerProfileUpdate['responses']['200']['content']['application/json']['schema'] = $customerCommandResponse('customer.profilecontroller.update');
$customerPasswordUpdate['responses']['200']['description'] = 'Returns the locale and success acknowledgement after changing the authenticated customer password.';
$customerPasswordUpdate['responses']['200']['content']['application/json']['schema'] = $customerCommandResponse('customer.profilecontroller.changePassword');
unset($customerProfileUpdate, $customerPasswordUpdate, $customerCommandResponse);
$courseLessonResource = ['type' => 'object', 'required' => ['id', 'course_id', 'title', 'title_fr', 'content', 'content_fr', 'video_url', 'duration_minutes', 'sort_order', 'created_at', 'updated_at'], 'properties' => [
    'id' => ['type' => 'integer'], 'course_id' => ['type' => 'integer'], 'title' => ['type' => 'string'], 'title_fr' => ['type' => ['string', 'null']],
    'content' => ['type' => ['string', 'null']], 'content_fr' => ['type' => ['string', 'null']], 'video_url' => ['type' => ['string', 'null']],
    'duration_minutes' => ['type' => ['integer', 'null']], 'sort_order' => ['type' => 'integer'],
    'created_at' => ['type' => ['string', 'null'], 'format' => 'date-time'], 'updated_at' => ['type' => ['string', 'null'], 'format' => 'date-time'],
]];
$courseResource = ['type' => 'object', 'required' => ['id', 'product_slug', 'title', 'title_fr', 'slug', 'description', 'description_fr', 'level', 'duration_hours', 'cover_image', 'is_active', 'is_featured', 'sort_order', 'created_at', 'updated_at'], 'properties' => [
    'id' => ['type' => 'integer'], 'product_slug' => ['type' => ['string', 'null']], 'title' => ['type' => 'string'], 'title_fr' => ['type' => ['string', 'null']],
    'slug' => ['type' => 'string'], 'description' => ['type' => ['string', 'null']], 'description_fr' => ['type' => ['string', 'null']],
    'level' => ['type' => 'string', 'enum' => ['beginner', 'intermediate', 'advanced']], 'duration_hours' => ['type' => ['integer', 'null']],
    'cover_image' => ['type' => ['string', 'null']], 'is_active' => ['type' => 'boolean'], 'is_featured' => ['type' => 'boolean'], 'sort_order' => ['type' => 'integer'],
    'created_at' => ['type' => ['string', 'null'], 'format' => 'date-time'], 'updated_at' => ['type' => ['string', 'null'], 'format' => 'date-time'],
    'lessons' => ['type' => 'array', 'items' => $courseLessonResource],
]];
$courseEnrollmentResource = ['type' => 'object', 'required' => ['id', 'course_id', 'user_id', 'status', 'enrolled_at', 'completed_at', 'created_at', 'updated_at'], 'properties' => [
    'id' => ['type' => 'integer'], 'course_id' => ['type' => 'integer'], 'user_id' => ['type' => 'integer'],
    'status' => ['type' => 'string', 'enum' => ['enrolled', 'in_progress', 'completed', 'dropped']],
    'enrolled_at' => ['type' => ['string', 'null'], 'format' => 'date-time'], 'completed_at' => ['type' => ['string', 'null'], 'format' => 'date-time'],
    'created_at' => ['type' => ['string', 'null'], 'format' => 'date-time'], 'updated_at' => ['type' => ['string', 'null'], 'format' => 'date-time'],
    'course' => $courseResource,
]];
$courseCertificateResource = ['type' => 'object', 'required' => ['id', 'enrollment_id', 'user_id', 'course_id', 'certificate_number', 'issued_at', 'created_at', 'updated_at'], 'properties' => [
    'id' => ['type' => 'integer'], 'enrollment_id' => ['type' => 'integer'], 'user_id' => ['type' => 'integer'], 'course_id' => ['type' => 'integer'],
    'certificate_number' => ['type' => 'string'], 'issued_at' => ['type' => 'string', 'format' => 'date-time'],
    'created_at' => ['type' => ['string', 'null'], 'format' => 'date-time'], 'updated_at' => ['type' => ['string', 'null'], 'format' => 'date-time'],
]];
$customerCourses = &$spec['paths']['/customer/courses']['get'];
$customerCourses['responses']['200']['description'] = 'Returns active courses and this customer account’s course enrollments.';
$customerCourses['responses']['200']['content']['application/json']['schema'] = $successEnvelope(['type' => 'object', 'required' => ['courses', 'my_enrollments'], 'properties' => [
    'courses' => ['type' => 'array', 'items' => $courseResource],
    'my_enrollments' => ['oneOf' => [
        ['type' => 'array', 'items' => $courseEnrollmentResource],
        ['type' => 'object', 'additionalProperties' => $courseEnrollmentResource],
    ]],
]], 'customer.coursecontroller.index');
unset($customerCourses);
$customerCourse = &$spec['paths']['/customer/courses/{course}']['get'];
$customerCourse['responses']['200']['description'] = 'Returns an active course, the authenticated customer’s enrollment if present, and completed lesson identifiers.';
$customerCourse['responses']['200']['content']['application/json']['schema'] = $successEnvelope(['type' => 'object', 'required' => ['course', 'enrollment', 'completed_lesson_ids'], 'properties' => [
    'course' => $courseResource, 'enrollment' => ['oneOf' => [$courseEnrollmentResource, ['type' => 'null']]],
    'completed_lesson_ids' => ['type' => 'array', 'items' => ['type' => 'integer']],
]], 'customer.coursecontroller.show');
unset($customerCourse);
$customerLesson = &$spec['paths']['/customer/courses/{course}/lessons/{lesson}']['get'];
$customerLesson['responses']['200']['description'] = 'Returns an enrolled customer’s lesson, course navigation, enrollment progress and completed lesson identifiers.';
$customerLesson['responses']['200']['content']['application/json']['schema'] = $successEnvelope(['type' => 'object', 'required' => ['course', 'lesson', 'enrollment', 'completed_lesson_ids', 'prev_lesson', 'next_lesson'], 'properties' => [
    'course' => $courseResource, 'lesson' => $courseLessonResource, 'enrollment' => $courseEnrollmentResource,
    'completed_lesson_ids' => ['type' => 'array', 'items' => ['type' => 'integer']],
    'prev_lesson' => ['oneOf' => [$courseLessonResource, ['type' => 'null']]], 'next_lesson' => ['oneOf' => [$courseLessonResource, ['type' => 'null']]],
]], 'customer.lessoncontroller.show');
unset($customerLesson);
$customerCertificates = &$spec['paths']['/customer/certificates']['get'];
$customerCertificates['responses']['200']['description'] = 'Returns certificates issued to the authenticated customer account.';
$customerCertificates['responses']['200']['content']['application/json']['schema'] = $successEnvelope(['type' => 'object', 'required' => ['certificates'], 'properties' => [
    'certificates' => ['type' => 'array', 'items' => $courseCertificateResource],
]], 'customer.certificatecontroller.index');
unset($customerCertificates);
$customerEnroll = &$spec['paths']['/customer/courses/{course}/enroll']['post'];
$customerEnroll['responses']['200']['description'] = 'Returns the requested course and locale; a new enrollment includes a success message.';
$customerEnroll['responses']['200']['content']['application/json']['schema'] = ['type' => 'object', 'required' => ['data', 'meta', 'messages'], 'properties' => [
    'data' => ['type' => 'object', 'required' => ['locale', 'course'], 'properties' => ['locale' => ['type' => 'string', 'enum' => ['en', 'fr']], 'course' => ['type' => 'string']]],
    'meta' => ['type' => 'object', 'required' => ['operation'], 'properties' => ['operation' => ['type' => 'string', 'const' => 'customer.coursecontroller.enroll']]],
    'messages' => ['oneOf' => [['type' => 'array', 'maxItems' => 0], ['type' => 'object', 'required' => ['success'], 'properties' => ['success' => ['type' => 'string']]]]],
]];
unset($customerEnroll);
$customerLessonDone = &$spec['paths']['/customer/courses/{course}/lessons/{lesson}/done']['post'];
$customerLessonDone['responses']['200']['description'] = 'Returns completion acknowledgement and the next lesson when available; completing the course may issue a certificate.';
$customerLessonDone['responses']['200']['content']['application/json']['schema'] = ['type' => 'object', 'required' => ['data', 'meta', 'messages'], 'properties' => [
    'data' => ['type' => 'object', 'required' => ['locale', 'course'], 'properties' => [
        'locale' => ['type' => 'string', 'enum' => ['en', 'fr']], 'course' => ['type' => 'string'], 'lesson' => ['type' => 'integer'],
    ]],
    'meta' => ['type' => 'object', 'required' => ['operation'], 'properties' => ['operation' => ['type' => 'string', 'const' => 'customer.lessoncontroller.markDone']]],
    'messages' => ['type' => 'object', 'required' => ['success'], 'properties' => ['success' => ['type' => 'string']]],
]];
unset($customerLessonDone);
$customerTicketResource = ['type' => 'object', 'required' => ['id', 'user_id', 'assigned_to', 'subject', 'description', 'type', 'status', 'priority', 'resolution', 'resolved_at', 'closed_at', 'tester_assignment_id', 'sla_rule_id', 'sla_response_due_at', 'sla_resolution_due_at', 'created_at', 'updated_at'], 'properties' => [
    'id' => ['type' => 'integer'], 'user_id' => ['type' => ['integer', 'null']], 'assigned_to' => ['type' => ['integer', 'null']],
    'subject' => ['type' => 'string'], 'description' => ['type' => 'string'],
    'type' => ['type' => 'string', 'enum' => ['support', 'billing', 'technical', 'bug_report', 'other']],
    'status' => ['type' => 'string', 'enum' => ['open', 'in_progress', 'pending_customer', 'resolved', 'closed']],
    'priority' => ['type' => 'string', 'enum' => ['low', 'medium', 'high', 'urgent']], 'resolution' => ['type' => ['string', 'null']],
    'resolved_at' => ['type' => ['string', 'null'], 'format' => 'date-time'], 'closed_at' => ['type' => ['string', 'null'], 'format' => 'date-time'],
    'tester_assignment_id' => ['type' => ['integer', 'null']], 'sla_rule_id' => ['type' => ['integer', 'null']],
    'sla_response_due_at' => ['type' => ['string', 'null'], 'format' => 'date-time'], 'sla_resolution_due_at' => ['type' => ['string', 'null'], 'format' => 'date-time'],
    'created_at' => ['type' => 'string', 'format' => 'date-time'], 'updated_at' => ['type' => 'string', 'format' => 'date-time'],
    'public_replies' => ['type' => 'array', 'items' => ['type' => 'object', 'required' => ['id', 'ticket_id', 'user_id', 'body', 'is_internal', 'created_at', 'updated_at', 'author'], 'properties' => [
        'id' => ['type' => 'integer'], 'ticket_id' => ['type' => 'integer'], 'user_id' => ['type' => 'integer'], 'body' => ['type' => 'string'],
        'is_internal' => ['type' => 'boolean', 'const' => false], 'created_at' => ['type' => 'string', 'format' => 'date-time'], 'updated_at' => ['type' => 'string', 'format' => 'date-time'],
        'author' => ['type' => 'object', 'required' => ['id', 'name'], 'properties' => [
            'id' => ['type' => 'integer'], 'name' => ['type' => 'string'], 'email' => ['type' => 'string', 'format' => 'email'],
            'phone' => ['type' => ['string', 'null']], 'is_active' => ['type' => 'boolean'], 'position' => ['type' => ['string', 'null']],
            'avatar' => ['type' => ['string', 'null']], 'employee_id' => ['type' => ['string', 'null']], 'department_id' => ['type' => ['integer', 'null']],
        ]],
    ]]],
]];
$customerTicketList = &$spec['paths']['/customer/tickets']['get'];
$customerTicketList['responses']['200']['description'] = 'Returns tickets belonging to the authenticated customer with pagination metadata.';
$customerTicketList['responses']['200']['content']['application/json']['schema'] = $successEnvelope(['type' => 'object', 'required' => ['tickets'], 'properties' => [
    'tickets' => ['type' => 'object', 'required' => ['items', 'meta'], 'properties' => [
        'items' => ['type' => 'array', 'items' => $customerTicketResource],
        'meta' => ['type' => 'object', 'required' => ['page', 'per_page', 'total', 'last_page'], 'properties' => [
            'page' => ['type' => 'integer'], 'per_page' => ['type' => 'integer'], 'total' => ['type' => 'integer'], 'last_page' => ['type' => 'integer'],
        ]],
    ]],
]], 'customer.ticketcontroller.index');
unset($customerTicketList);
$customerTicketShow = &$spec['paths']['/customer/tickets/{id}']['get'];
$customerTicketShow['responses']['200']['description'] = 'Returns one customer-owned support ticket and its public replies; internal replies are not included.';
$customerTicketShow['responses']['200']['content']['application/json']['schema'] = $successEnvelope(['type' => 'object', 'required' => ['ticket'], 'properties' => [
    'ticket' => $customerTicketResource,
]], 'customer.ticketcontroller.show');
unset($customerTicketShow);
$customerTicketOptions = &$spec['paths']['/customer/tickets/options']['get'];
$customerTicketOptions['responses']['200']['description'] = 'Returns the empty data acknowledgement for the customer ticket creation page.';
$customerTicketOptions['responses']['200']['content']['application/json']['schema'] = ['type' => 'object', 'required' => ['data', 'meta', 'messages'], 'properties' => [
    'data' => ['type' => 'array', 'maxItems' => 0],
    'meta' => ['type' => 'object', 'required' => ['operation'], 'properties' => ['operation' => ['type' => 'string', 'const' => 'customer.ticketcontroller.create']]],
    'messages' => ['type' => 'array', 'maxItems' => 0],
]];
unset($customerTicketOptions);
$customerTicketCommandResponse = static function (string $operation, array $data): array { return [
    'type' => 'object', 'required' => ['data', 'meta', 'messages'], 'properties' => [
        'data' => ['type' => 'object', 'required' => array_keys($data), 'properties' => $data],
        'meta' => ['type' => 'object', 'required' => ['operation'], 'properties' => ['operation' => ['type' => 'string', 'const' => $operation]]],
        'messages' => ['type' => 'object', 'required' => ['success'], 'properties' => ['success' => ['type' => 'string']]],
    ],
]; };
$customerTicketCreate = &$spec['paths']['/customer/tickets']['post'];
$customerTicketCreate['responses']['200']['description'] = 'Returns the locale and success acknowledgement after the customer ticket is accepted.';
$customerTicketCreate['responses']['200']['content']['application/json']['schema'] = $customerTicketCommandResponse('customer.ticketcontroller.store', [
    'locale' => ['type' => 'string', 'enum' => ['en', 'fr']],
]);
unset($customerTicketCreate);
$customerTicketReply = &$spec['paths']['/customer/tickets/{id}/reply']['post'];
$customerTicketReply['responses']['200']['description'] = 'Returns the locale, ticket identifier and acknowledgement after the customer reply is accepted.';
$customerTicketReply['responses']['200']['content']['application/json']['schema'] = $customerTicketCommandResponse('customer.ticketcontroller.reply', [
    'locale' => ['type' => 'string', 'enum' => ['en', 'fr']], 'id' => ['type' => 'integer'],
]);
unset($customerTicketReply, $customerTicketCommandResponse);
$customerLicenseResource = ['type' => 'object', 'required' => ['id', 'user_id', 'issued_by', 'product_slug', 'product_name', 'license_key', 'plan', 'seats', 'status', 'start_date', 'end_date', 'price', 'currency', 'notes', 'created_at', 'updated_at'], 'properties' => [
    'id' => ['type' => 'integer'], 'user_id' => ['type' => 'integer'], 'issued_by' => ['type' => 'integer'], 'product_slug' => ['type' => 'string'],
    'product_name' => ['type' => 'string'], 'license_key' => ['type' => 'string'], 'plan' => ['type' => 'string'], 'seats' => ['type' => 'integer'],
    'status' => ['type' => 'string', 'enum' => ['active', 'suspended', 'expired', 'cancelled']],
    'start_date' => ['type' => 'string', 'format' => 'date-time'], 'end_date' => ['type' => 'string', 'format' => 'date-time'],
    'price' => ['type' => ['integer', 'null']], 'currency' => ['type' => 'string'], 'notes' => ['type' => ['string', 'null']],
    'created_at' => ['type' => 'string', 'format' => 'date-time'], 'updated_at' => ['type' => 'string', 'format' => 'date-time'],
]];
$customerLicenseList = &$spec['paths']['/customer/licenses']['get'];
$customerLicenseList['responses']['200']['description'] = 'Returns licenses belonging to the authenticated customer with pagination metadata, including that customer’s license keys.';
$customerLicenseList['responses']['200']['content']['application/json']['schema'] = $successEnvelope(['type' => 'object', 'required' => ['licenses'], 'properties' => [
    'licenses' => ['type' => 'object', 'required' => ['items', 'meta'], 'properties' => [
        'items' => ['type' => 'array', 'items' => $customerLicenseResource],
        'meta' => ['type' => 'object', 'required' => ['page', 'per_page', 'total', 'last_page'], 'properties' => [
            'page' => ['type' => 'integer'], 'per_page' => ['type' => 'integer'], 'total' => ['type' => 'integer'], 'last_page' => ['type' => 'integer'],
        ]],
    ]],
]], 'customer.licensecontroller.index');
unset($customerLicenseList);
$customerLicenseShow = &$spec['paths']['/customer/licenses/{id}']['get'];
$customerLicenseShow['responses']['200']['description'] = 'Returns a license owned by the authenticated customer, including its license key.';
$customerLicenseShow['responses']['200']['content']['application/json']['schema'] = $successEnvelope(['type' => 'object', 'required' => ['license'], 'properties' => [
    'license' => $customerLicenseResource,
]], 'customer.licensecontroller.show');
unset($customerLicenseShow);
$customerInvoiceItem = ['type' => 'object', 'required' => ['id', 'invoice_id', 'description', 'quantity', 'unit_price', 'created_at', 'updated_at'], 'properties' => [
    'id' => ['type' => 'integer'], 'invoice_id' => ['type' => 'integer'], 'description' => ['type' => 'string'], 'quantity' => ['type' => 'integer'],
    'unit_price' => ['type' => 'integer'], 'created_at' => ['type' => 'string', 'format' => 'date-time'], 'updated_at' => ['type' => 'string', 'format' => 'date-time'],
]];
$customerInvoiceResource = ['type' => 'object', 'required' => ['id', 'customer_id', 'issued_by', 'license_id', 'status', 'currency', 'tax_rate', 'notes', 'due_date', 'paid_at', 'created_at', 'updated_at', 'invoice_number', 'subtotal', 'tax_amount', 'grand_total', 'amount_paid', 'amount_outstanding'], 'properties' => [
    'id' => ['type' => 'integer'], 'customer_id' => ['type' => ['integer', 'null']], 'issued_by' => ['type' => ['integer', 'null']], 'license_id' => ['type' => ['integer', 'null']],
    'status' => ['type' => 'string', 'enum' => ['draft', 'sent', 'paid', 'overdue', 'cancelled']], 'currency' => ['type' => 'string'],
    'tax_rate' => ['type' => 'string'], 'notes' => ['type' => ['string', 'null']], 'due_date' => ['type' => ['string', 'null'], 'format' => 'date-time'],
    'paid_at' => ['type' => ['string', 'null'], 'format' => 'date-time'], 'created_at' => ['type' => 'string', 'format' => 'date-time'],
    'updated_at' => ['type' => 'string', 'format' => 'date-time'], 'invoice_number' => ['type' => 'string'], 'subtotal' => ['type' => 'integer'],
    'tax_amount' => ['type' => 'integer'], 'grand_total' => ['type' => 'integer'], 'amount_paid' => ['type' => 'number'], 'amount_outstanding' => ['type' => 'number'],
    'items' => ['type' => 'array', 'items' => $customerInvoiceItem],
]];
$customerInvoiceList = &$spec['paths']['/customer/invoices']['get'];
$customerInvoiceList['responses']['200']['description'] = 'Returns the authenticated customer’s sent, paid and overdue invoices with line items, totals and pagination metadata.';
$customerInvoiceList['responses']['200']['content']['application/json']['schema'] = $successEnvelope(['type' => 'object', 'required' => ['invoices'], 'properties' => [
    'invoices' => ['type' => 'object', 'required' => ['items', 'meta'], 'properties' => [
        'items' => ['type' => 'array', 'items' => $customerInvoiceResource],
        'meta' => ['type' => 'object', 'required' => ['page', 'per_page', 'total', 'last_page'], 'properties' => [
            'page' => ['type' => 'integer'], 'per_page' => ['type' => 'integer'], 'total' => ['type' => 'integer'], 'last_page' => ['type' => 'integer'],
        ]],
    ]],
]], 'customer.invoicecontroller.index');
unset($customerInvoiceList);
$customerInvoiceShow = &$spec['paths']['/customer/invoices/{id}']['get'];
$customerInvoiceShow['responses']['200']['description'] = 'Returns one invoice owned by the authenticated customer with its invoice line items and totals.';
$customerInvoiceShow['responses']['200']['content']['application/json']['schema'] = $successEnvelope(['type' => 'object', 'required' => ['invoice'], 'properties' => [
    'invoice' => $customerInvoiceResource,
]], 'customer.invoicecontroller.show');
unset($customerInvoiceShow);
$accountantInvoiceResource = $customerInvoiceResource;
$accountantInvoiceResource['properties']['customer'] = ['type' => ['object', 'null'], 'required' => ['id', 'name'], 'properties' => [
    'id' => ['type' => 'integer'], 'name' => ['type' => 'string'],
]];
$accountantInvoiceResource['properties']['issuer'] = ['type' => ['object', 'null'], 'required' => ['id', 'name'], 'properties' => [
    'id' => ['type' => 'integer'], 'name' => ['type' => 'string'], 'email' => ['type' => 'string', 'format' => 'email'],
    'phone' => ['type' => ['string', 'null']], 'employee_id' => ['type' => ['string', 'null']],
    'position' => ['type' => ['string', 'null']], 'hire_date' => ['type' => ['string', 'null'], 'format' => 'date-time'],
    'is_active' => ['type' => 'boolean'], 'department_id' => ['type' => ['integer', 'null']],
]];
$accountantInvoiceList = &$spec['paths']['/accountant/invoices']['get'];
$accountantInvoiceList['responses']['200']['description'] = 'Returns invoices visible to the authenticated accountant with current totals, loaded line items, customer summary and pagination metadata.';
$accountantInvoiceList['responses']['200']['content']['application/json']['schema'] = $successEnvelope(['type' => 'object', 'required' => ['invoices'], 'properties' => [
    'invoices' => ['type' => 'object', 'required' => ['items', 'meta'], 'properties' => [
        'items' => ['type' => 'array', 'items' => $accountantInvoiceResource],
        'meta' => ['type' => 'object', 'required' => ['page', 'per_page', 'total', 'last_page'], 'properties' => [
            'page' => ['type' => 'integer'], 'per_page' => ['type' => 'integer'], 'total' => ['type' => 'integer'], 'last_page' => ['type' => 'integer'],
        ]],
    ]],
]], 'accountant.invoicecontroller.index');
unset($accountantInvoiceList);
$accountantInvoiceShow = &$spec['paths']['/accountant/invoices/{invoice}']['get'];
$accountantInvoiceShow['responses']['200']['description'] = 'Returns one invoice and its loaded line items with customer and issuer summaries for the authenticated accountant.';
$accountantInvoiceShow['responses']['200']['content']['application/json']['schema'] = $successEnvelope(['type' => 'object', 'required' => ['invoice'], 'properties' => [
    'invoice' => $accountantInvoiceResource,
]], 'accountant.invoicecontroller.show');
unset($accountantInvoiceShow, $accountantInvoiceResource);
$customerDocumentResource = ['type' => 'object', 'required' => ['id', 'document_template_id', 'type', 'title', 'reference_number', 'body_rendered', 'issued_by', 'addressee_user_id', 'addressee_name', 'addressee_email', 'status', 'requires_signature', 'signed_at', 'signed_by_name', 'valid_until', 'notes', 'created_at', 'updated_at'], 'properties' => [
    'id' => ['type' => 'integer'], 'document_template_id' => ['type' => ['integer', 'null']],
    'type' => ['type' => 'string', 'enum' => ['receipt', 'letterhead', 'contract_employee', 'contract_business']], 'title' => ['type' => 'string'],
    'reference_number' => ['type' => 'string'], 'body_rendered' => ['type' => 'string'], 'issued_by' => ['type' => 'integer'],
    'addressee_user_id' => ['type' => ['integer', 'null']], 'addressee_name' => ['type' => 'string'], 'addressee_email' => ['type' => ['string', 'null'], 'format' => 'email'],
    'status' => ['type' => 'string', 'enum' => ['sent', 'pending_signature', 'signed', 'voided']], 'requires_signature' => ['type' => 'boolean'],
    'signed_at' => ['type' => ['string', 'null'], 'format' => 'date-time'], 'signed_by_name' => ['type' => ['string', 'null']],
    'valid_until' => ['type' => ['string', 'null'], 'format' => 'date-time'], 'notes' => ['type' => ['string', 'null']],
    'created_at' => ['type' => 'string', 'format' => 'date-time'], 'updated_at' => ['type' => 'string', 'format' => 'date-time'],
]];
$customerDocumentList = &$spec['paths']['/customer/documents']['get'];
$customerDocumentList['responses']['200']['description'] = 'Returns documents addressed to the authenticated customer, excluding drafts, with pagination metadata. Signature tokens, signing IPs and submitted signature data are not returned.';
$customerDocumentList['responses']['200']['content']['application/json']['schema'] = $successEnvelope(['type' => 'object', 'required' => ['documents'], 'properties' => [
    'documents' => ['type' => 'object', 'required' => ['items', 'meta'], 'properties' => [
        'items' => ['type' => 'array', 'items' => $customerDocumentResource],
        'meta' => ['type' => 'object', 'required' => ['page', 'per_page', 'total', 'last_page'], 'properties' => [
            'page' => ['type' => 'integer'], 'per_page' => ['type' => 'integer'], 'total' => ['type' => 'integer'], 'last_page' => ['type' => 'integer'],
        ]],
    ]],
]], 'customer.documentcontroller.index');
unset($customerDocumentList);
$customerDocumentShow = &$spec['paths']['/customer/documents/{id}']['get'];
$customerDocumentShow['responses']['200']['description'] = 'Returns one non-draft document addressed to the authenticated customer. Signature tokens, signing IPs and submitted signature data are not returned.';
$customerDocumentShow['responses']['200']['content']['application/json']['schema'] = $successEnvelope(['type' => 'object', 'required' => ['document'], 'properties' => [
    'document' => $customerDocumentResource,
]], 'customer.documentcontroller.show');
unset($customerDocumentShow);
$customerSurveyQuestion = ['type' => 'object', 'required' => ['id', 'survey_id', 'question', 'question_fr', 'type', 'options', 'options_fr', 'is_required', 'sort_order', 'created_at', 'updated_at'], 'properties' => [
    'id' => ['type' => 'integer'], 'survey_id' => ['type' => 'integer'], 'question' => ['type' => 'string'], 'question_fr' => ['type' => ['string', 'null']],
    'type' => ['type' => 'string', 'enum' => ['text', 'rating', 'multiple_choice', 'yes_no']],
    'options' => ['type' => ['array', 'null'], 'items' => ['type' => 'string']], 'options_fr' => ['type' => ['array', 'null'], 'items' => ['type' => 'string']],
    'is_required' => ['type' => 'boolean'], 'sort_order' => ['type' => 'integer'],
    'created_at' => ['type' => 'string', 'format' => 'date-time'], 'updated_at' => ['type' => 'string', 'format' => 'date-time'],
]];
$customerSurveyResource = ['type' => 'object', 'required' => ['id', 'title', 'title_fr', 'description', 'description_fr', 'audience', 'status', 'starts_at', 'ends_at', 'created_at', 'updated_at'], 'properties' => [
    'id' => ['type' => 'integer'], 'title' => ['type' => 'string'], 'title_fr' => ['type' => ['string', 'null']],
    'description' => ['type' => ['string', 'null']], 'description_fr' => ['type' => ['string', 'null']],
    'audience' => ['type' => 'string', 'enum' => ['practitioners', 'customers', 'all']], 'status' => ['type' => 'string', 'enum' => ['active']],
    'starts_at' => ['type' => ['string', 'null'], 'format' => 'date-time'], 'ends_at' => ['type' => ['string', 'null'], 'format' => 'date-time'],
    'created_at' => ['type' => 'string', 'format' => 'date-time'], 'updated_at' => ['type' => 'string', 'format' => 'date-time'],
    'questions' => ['type' => 'array', 'items' => $customerSurveyQuestion],
]];
$customerSurveyList = &$spec['paths']['/customer/surveys']['get'];
$customerSurveyList['responses']['200']['description'] = 'Returns currently active surveys open to customers and the customer’s submitted survey identifiers; other users’ answers are never included.';
$customerSurveyList['responses']['200']['content']['application/json']['schema'] = $successEnvelope(['type' => 'object', 'required' => ['surveys', 'my_response_ids'], 'properties' => [
    'surveys' => ['type' => 'array', 'items' => $customerSurveyResource], 'my_response_ids' => ['type' => 'array', 'items' => ['type' => 'integer']],
]], 'customer.surveycontroller.index');
unset($customerSurveyList);
$customerSurveyShow = &$spec['paths']['/customer/surveys/{survey}']['get'];
$customerSurveyShow['responses']['200']['description'] = 'Returns an active customer survey, its questions, this customer’s response state and saved text answers only.';
$customerSurveyShow['responses']['200']['content']['application/json']['schema'] = $successEnvelope(['type' => 'object', 'required' => ['survey', 'response', 'existing_answers'], 'properties' => [
    'survey' => $customerSurveyResource,
    'response' => ['type' => 'object', 'required' => ['survey_id', 'user_id'], 'properties' => [
        'id' => ['type' => 'integer'], 'survey_id' => ['type' => 'integer'], 'user_id' => ['type' => 'integer'],
        'submitted_at' => ['type' => ['string', 'null'], 'format' => 'date-time'], 'created_at' => ['type' => ['string', 'null'], 'format' => 'date-time'], 'updated_at' => ['type' => ['string', 'null'], 'format' => 'date-time'],
    ]],
    'existing_answers' => ['type' => 'object', 'additionalProperties' => ['type' => ['string', 'null']]],
]], 'customer.surveycontroller.show');
unset($customerSurveyShow);
$customerSurveySubmit = &$spec['paths']['/customer/surveys/{survey}']['post'];
$customerSurveySubmit['responses']['200']['description'] = 'Returns the locale and success acknowledgement after submitting the authenticated customer’s survey answers.';
$customerSurveySubmit['responses']['200']['content']['application/json']['schema'] = ['type' => 'object', 'required' => ['data', 'meta', 'messages'], 'properties' => [
    'data' => ['type' => 'object', 'required' => ['locale'], 'properties' => ['locale' => ['type' => 'string', 'enum' => ['en', 'fr']]]],
    'meta' => ['type' => 'object', 'required' => ['operation'], 'properties' => ['operation' => ['type' => 'string', 'const' => 'customer.surveycontroller.submit']]],
    'messages' => ['type' => 'object', 'required' => ['success'], 'properties' => ['success' => ['type' => 'string']]],
]];
unset($customerSurveySubmit);
$customerKnowledgeCategory = ['type' => 'object', 'required' => ['id', 'name', 'slug', 'description', 'parent_id', 'is_public', 'sort_order', 'is_active', 'created_at', 'updated_at'], 'properties' => [
    'id' => ['type' => 'integer'], 'name' => ['type' => 'string'], 'slug' => ['type' => 'string'], 'description' => ['type' => ['string', 'null']],
    'parent_id' => ['type' => ['integer', 'null']], 'is_public' => ['type' => 'boolean'], 'sort_order' => ['type' => 'integer'], 'is_active' => ['type' => 'boolean'],
    'created_at' => ['type' => 'string', 'format' => 'date-time'], 'updated_at' => ['type' => 'string', 'format' => 'date-time'],
]];
$customerKnowledgeArticle = ['type' => 'object', 'required' => ['id', 'category_id', 'author_id', 'title', 'slug', 'content', 'status', 'is_public', 'views', 'tags', 'published_at', 'created_at', 'updated_at'], 'properties' => [
    'id' => ['type' => 'integer'], 'category_id' => ['type' => ['integer', 'null']], 'author_id' => ['type' => 'integer'], 'title' => ['type' => 'string'],
    'slug' => ['type' => 'string'], 'content' => ['type' => 'string'], 'status' => ['type' => 'string', 'enum' => ['published']], 'is_public' => ['type' => 'boolean', 'const' => true],
    'views' => ['type' => 'integer'], 'tags' => ['type' => ['array', 'null'], 'items' => ['type' => 'string']],
    'published_at' => ['type' => ['string', 'null'], 'format' => 'date-time'], 'created_at' => ['type' => 'string', 'format' => 'date-time'], 'updated_at' => ['type' => 'string', 'format' => 'date-time'],
]];
$customerKnowledgeIndex = &$spec['paths']['/customer/knowledge-base']['get'];
$customerKnowledgeIndex['responses']['200']['description'] = 'Returns active public root categories and up to six recent published public articles.';
$customerKnowledgeIndex['responses']['200']['content']['application/json']['schema'] = $successEnvelope(['type' => 'object', 'required' => ['categories', 'recent'], 'properties' => [
    'categories' => ['type' => 'array', 'items' => $customerKnowledgeCategory], 'recent' => ['type' => 'array', 'maxItems' => 6, 'items' => $customerKnowledgeArticle],
]], 'customer.knowledgebasecontroller.index');
unset($customerKnowledgeIndex);
$customerKnowledgeCategoryShow = &$spec['paths']['/customer/knowledge-base/category/{slug}']['get'];
$customerKnowledgeCategoryShow['responses']['200']['description'] = 'Returns one public category and its paginated published public articles.';
$customerKnowledgeCategoryShow['responses']['200']['content']['application/json']['schema'] = $successEnvelope(['type' => 'object', 'required' => ['category', 'articles'], 'properties' => [
    'category' => $customerKnowledgeCategory,
    'articles' => ['type' => 'object', 'required' => ['items', 'meta'], 'properties' => [
        'items' => ['type' => 'array', 'items' => $customerKnowledgeArticle],
        'meta' => ['type' => 'object', 'required' => ['page', 'per_page', 'total', 'last_page'], 'properties' => [
            'page' => ['type' => 'integer'], 'per_page' => ['type' => 'integer'], 'total' => ['type' => 'integer'], 'last_page' => ['type' => 'integer'],
        ]],
    ]],
]], 'customer.knowledgebasecontroller.category');
unset($customerKnowledgeCategoryShow);
$customerKnowledgeShow = &$spec['paths']['/customer/knowledge-base/{slug}']['get'];
$customerKnowledgeShow['responses']['200']['description'] = 'Returns one published public knowledge-base article and up to four related public articles.';
$customerKnowledgeShow['responses']['200']['content']['application/json']['schema'] = $successEnvelope(['type' => 'object', 'required' => ['article', 'related'], 'properties' => [
    'article' => $customerKnowledgeArticle, 'related' => ['type' => 'array', 'maxItems' => 4, 'items' => $customerKnowledgeArticle],
]], 'customer.knowledgebasecontroller.show');
unset($customerKnowledgeShow);
$customerDashboard = &$spec['paths']['/customer/dashboard']['get'];
$dashboardTicket = ['type' => 'object', 'required' => ['id', 'user_id', 'assigned_to', 'subject', 'description', 'type', 'status', 'priority', 'resolution', 'resolved_at', 'closed_at', 'tester_assignment_id', 'sla_rule_id', 'sla_response_due_at', 'sla_resolution_due_at', 'created_at', 'updated_at'], 'properties' => [
    'id' => ['type' => 'integer'], 'user_id' => ['type' => ['integer', 'null']], 'assigned_to' => ['type' => ['integer', 'null']],
    'subject' => ['type' => 'string'], 'description' => ['type' => 'string'],
    'type' => ['type' => 'string', 'enum' => ['support', 'billing', 'technical', 'bug_report', 'other']],
    'status' => ['type' => 'string', 'enum' => ['open', 'in_progress', 'pending_customer', 'resolved', 'closed']],
    'priority' => ['type' => 'string', 'enum' => ['low', 'medium', 'high', 'urgent']], 'resolution' => ['type' => ['string', 'null']],
    'resolved_at' => ['type' => ['string', 'null'], 'format' => 'date-time'], 'closed_at' => ['type' => ['string', 'null'], 'format' => 'date-time'],
    'tester_assignment_id' => ['type' => ['integer', 'null']], 'sla_rule_id' => ['type' => ['integer', 'null']],
    'sla_response_due_at' => ['type' => ['string', 'null'], 'format' => 'date-time'], 'sla_resolution_due_at' => ['type' => ['string', 'null'], 'format' => 'date-time'],
    'created_at' => ['type' => 'string', 'format' => 'date-time'], 'updated_at' => ['type' => 'string', 'format' => 'date-time'],
]];
$customerDashboard['responses']['200']['description'] = 'Returns the authenticated customer account, facility profile, operational counts, three most recently updated tickets and configured catalogue items.';
$customerDashboard['responses']['200']['content']['application/json']['schema'] = $successEnvelope(['type' => 'object', 'required' => ['user', 'profile', 'active_licenses', 'open_tickets', 'pending_invoices', 'recent_tickets', 'all_products'], 'properties' => [
    'user' => ['type' => 'object', 'required' => ['id', 'name', 'email', 'phone', 'is_active', 'position', 'avatar', 'employee_id', 'department_id'], 'properties' => [
        'id' => ['type' => 'integer'], 'name' => ['type' => 'string'], 'email' => ['type' => 'string', 'format' => 'email'], 'phone' => ['type' => ['string', 'null']],
        'is_active' => ['type' => 'boolean'], 'position' => ['type' => ['string', 'null']], 'avatar' => ['type' => ['string', 'null']],
        'employee_id' => ['type' => ['string', 'null']], 'department_id' => ['type' => ['integer', 'null']],
    ]],
    'profile' => ['oneOf' => [
        ['type' => 'object', 'required' => ['id', 'user_id', 'facility_name', 'facility_type', 'country', 'city', 'address', 'created_at', 'updated_at'], 'properties' => [
            'id' => ['type' => 'integer'], 'user_id' => ['type' => 'integer'], 'facility_name' => ['type' => ['string', 'null']], 'facility_type' => ['type' => ['string', 'null']],
            'country' => ['type' => ['string', 'null']], 'city' => ['type' => ['string', 'null']], 'address' => ['type' => ['string', 'null']],
            'created_at' => ['type' => ['string', 'null'], 'format' => 'date-time'], 'updated_at' => ['type' => ['string', 'null'], 'format' => 'date-time'],
        ]], ['type' => 'null'],
    ]],
    'active_licenses' => ['type' => 'integer', 'minimum' => 0], 'open_tickets' => ['type' => 'integer', 'minimum' => 0],
    'pending_invoices' => ['type' => 'integer', 'minimum' => 0], 'recent_tickets' => ['type' => 'array', 'maxItems' => 3, 'items' => $dashboardTicket],
    'all_products' => ['type' => 'array', 'items' => ['type' => 'object', 'required' => ['slug', 'name'], 'properties' => [
        'slug' => ['type' => 'string'], 'name' => ['type' => 'string'],
    ], 'additionalProperties' => true]],
]], 'customer.dashboardcontroller.index');
unset($customerDashboard);
$serviceRequestOptions = &$spec['paths']['/customer/service-requests/options']['get'];
$serviceRequestOptions['responses']['200']['description'] = 'Returns the service-request types currently accepted by the customer portal.';
$serviceRequestOptions['responses']['200']['content']['application/json']['schema'] = $successEnvelope(['type' => 'object', 'required' => ['type_options'], 'properties' => [
    'type_options' => ['type' => 'object', 'required' => ['installation', 'maintenance', 'training', 'other'], 'additionalProperties' => false, 'properties' => [
        'installation' => ['type' => 'string'], 'maintenance' => ['type' => 'string'], 'training' => ['type' => 'string'], 'other' => ['type' => 'string'],
    ]],
]], 'customer.servicerequestcontroller.create');
unset($serviceRequestOptions);
$serviceRequestSchema = ['type' => 'object', 'required' => ['id', 'reference_number', 'customer_id', 'type', 'product_slug', 'description', 'preferred_date', 'preferred_time', 'location', 'status', 'assigned_technician_id', 'confirmed_date', 'confirmed_time', 'created_at', 'updated_at'], 'properties' => [
    'id' => ['type' => 'integer'], 'reference_number' => ['type' => 'string'], 'customer_id' => ['type' => 'integer'],
    'type' => ['type' => 'string', 'enum' => ['installation', 'maintenance', 'training', 'other']], 'product_slug' => ['type' => ['string', 'null']],
    'description' => ['type' => ['string', 'null']], 'preferred_date' => ['type' => 'string', 'format' => 'date'], 'preferred_time' => ['type' => ['string', 'null']],
    'location' => ['type' => ['string', 'null']], 'status' => ['type' => 'string', 'enum' => ['pending', 'confirmed', 'assigned', 'completed', 'cancelled']],
    'assigned_technician_id' => ['type' => ['integer', 'null']], 'confirmed_date' => ['type' => ['string', 'null'], 'format' => 'date'],
    'confirmed_time' => ['type' => ['string', 'null']], 'created_at' => ['type' => 'string', 'format' => 'date-time'], 'updated_at' => ['type' => 'string', 'format' => 'date-time'],
]];
$serviceRequests = &$spec['paths']['/customer/service-requests']['get'];
$serviceRequests['responses']['200']['description'] = 'Service requests belonging only to the authenticated customer.';
$serviceRequests['responses']['200']['content']['application/json']['schema'] = $successEnvelope(['type' => 'object', 'required' => ['service_requests'], 'properties' => [
    'service_requests' => ['type' => 'array', 'items' => $serviceRequestSchema],
]], 'customer.servicerequestcontroller.index');
unset($serviceRequests);
$serviceRequestShow = &$spec['paths']['/customer/service-requests/{serviceRequest}']['get'];
$serviceRequestShow['responses']['200']['content']['application/json']['schema'] = $successEnvelope(['type' => 'object', 'required' => ['service_request'], 'properties' => ['service_request' => $serviceRequestSchema]], 'customer.servicerequestcontroller.show');
unset($serviceRequestShow);
$serviceRequestCreate = &$spec['paths']['/customer/service-requests']['post'];
$serviceRequestCreate['requestBody']['content']['application/json']['schema']['properties']['type'] = [
    'type' => 'string', 'enum' => ['installation', 'maintenance', 'training', 'other'],
];
$serviceRequestCreate['requestBody']['content']['application/json']['schema']['required'] = array_values(array_unique(array_merge(
    $serviceRequestCreate['requestBody']['content']['application/json']['schema']['required'] ?? [], ['type'],
)));
$serviceRequestCreate['responses']['200']['description'] = 'Creates an owned service request and returns its reference, customer-visible status and the existing locale acknowledgement.';
$serviceRequestCreate['responses']['200']['content']['application/json']['schema'] = $successEnvelope(['type' => 'object', 'required' => ['locale', 'service_request'], 'properties' => [
    'locale' => ['type' => 'string', 'enum' => ['en', 'fr']], 'service_request' => $serviceRequestSchema,
]], 'customer.servicerequestcontroller.store');
unset($serviceRequestCreate);
$contractPath = $root.'/docs/api/openapi.json';
$contractTemp = $contractPath.'.tmp';
if (file_put_contents($contractTemp, json_encode($spec, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR)."\n") === false || !rename($contractTemp, $contractPath)) {
    throw new RuntimeException('Unable to atomically write docs/api/openapi.json');
}
echo json_encode(['business_operations' => count($registry['definitions']), 'paths' => count($spec['paths']), 'version' => $product['version']]).PHP_EOL;


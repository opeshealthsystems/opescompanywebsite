<?php

// Build-time source inspection only. Runtime routing uses the explicit registry.
require __DIR__.'/../../vendor/autoload.php';
$root = dirname(__DIR__, 2);
$registry = require $root.'/config/business_workflows.php';
$product = require $root.'/config/api_product.php';
$spec = json_decode(file_get_contents($root.'/docs/api/baselines/0.3.0.json'), true, 512, JSON_THROW_ON_ERROR);
$spec['info']['version'] = $product['version'];
$spec['info']['description'] = 'Versioned OHS business APIs. Public enquiries and application intake, partner and tester administration, publishing and catalogues, role-scoped operations and workforce workflows, finance registers, reports and line-item detail reads, customer facility profiles, employee and HR administration, risk and performance management, leave requests, validation, practitioner, recruitment, learning, survey and support workflows, account recovery, machine catalogue access, public sitemap data, protected admin reference data and resource/relationship summaries, with delegated business workflows using authorization code and S256 PKCE. Private operations require current role, scope and record authorization. OpenID Connect is not implemented.';
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
unset($enquiryInput);
$enquiryOperation = &$spec['paths']['/enquiries']['post'];
$enquiryOperation['requestBody']['content']['application/json']['example']['quote_option'] = 'clinic-saas';
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
    $path = '/'.$definition['api_path'];
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
        if ($operation === 'identity.register' || in_array($operation, ['admin.support.tickets.create', 'admin.finance.invoiceTemplates.create', 'admin.apiClients.create', 'admin.oauthClients.create'], true) || ($definition['command'] && ($definition['method'] === 'create'
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
unset($clientList, $clientCreate, $clientRotate, $clientRevoke);
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
unset($statusOperation, $changelogOperation);
$spec['paths']['/catalogue/products']['get']['summary'] = 'List published software products';
$spec['paths']['/catalogue/products']['get']['description'] = 'Returns published software catalogue metadata only, with bounded pagination and English or French labels. Requires an approved server client and the catalogue:read scope. This endpoint does not expose clinical or customer records.';
$spec['paths']['/integration']['get']['summary'] = 'Read the current integration client';
$spec['paths']['/integration']['get']['description'] = 'Returns the authenticated machine client identity, its approved scopes and its UTC daily authenticated-request count. Requires the integration:read scope. A caller cannot select another client.';
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
$contractPath = $root.'/docs/api/openapi.json';
$contractTemp = $contractPath.'.tmp';
if (file_put_contents($contractTemp, json_encode($spec, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR)."\n") === false || !rename($contractTemp, $contractPath)) {
    throw new RuntimeException('Unable to atomically write docs/api/openapi.json');
}
echo json_encode(['business_operations' => count($registry['definitions']), 'paths' => count($spec['paths']), 'version' => $product['version']]).PHP_EOL;


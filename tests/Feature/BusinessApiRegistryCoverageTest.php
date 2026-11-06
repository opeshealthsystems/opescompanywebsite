<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class BusinessApiRegistryCoverageTest extends TestCase
{
    public function test_every_business_workflow_has_a_runtime_route_contract_and_handler(): void
    {
        $definitions = config('business_workflows.definitions', []);
        $contract = json_decode(file_get_contents(base_path('docs/api/openapi.json')), true, 512, JSON_THROW_ON_ERROR);
        $operationIds = [];

        foreach ($definitions as $name => $definition) {
            $this->assertTrue(class_exists($definition['handler']), "{$name} handler class is missing");
            $this->assertTrue(method_exists($definition['handler'], $definition['method']), "{$name} handler method is missing");

            $routeName = 'api.v1.business.'.$name;
            $route = Route::getRoutes()->getByName($routeName);
            $this->assertNotNull($route, "{$routeName} runtime route is missing");
            foreach ($definition['http_methods'] as $method) {
                $this->assertContains($method, $route->methods(), "{$routeName} does not register {$method}");
                $path = '/'.preg_replace_callback('/\{([^}:]+):[^}]+\}/', fn ($match) => '{'.$match[1].'}', $definition['api_path']);
                $contractOperation = $contract['paths'][$path][strtolower($method)] ?? null;
                $this->assertIsArray($contractOperation, "{$method} {$path} is missing from OpenAPI");
                $operationId = $contractOperation['operationId'] ?? null;
                $this->assertIsString($operationId, "{$method} {$path} has no operationId");
                $this->assertArrayNotHasKey($operationId, $operationIds, "Duplicate operationId {$operationId}");
                $operationIds[$operationId] = true;

                $security = $contractOperation['security'] ?? [];
                if ($definition['authenticated']) {
                    $this->assertNotEmpty($security, "{$method} {$path} is missing OAuth security");
                    $scopes = [];
                    foreach ($security as $scheme) $scopes = array_merge($scopes, $scheme['UserOAuth2'] ?? [], $scheme['OAuth2'] ?? []);
                    $this->assertContains($definition['scope'], $scopes, "{$method} {$path} is missing scope {$definition['scope']}");
                }

                if ($definition['command']) {
                    $idempotency = collect($contractOperation['parameters'] ?? [])->first(fn ($parameter) =>
                        ($parameter['in'] ?? null) === 'header' && ($parameter['name'] ?? null) === 'Idempotency-Key');
                    $this->assertIsArray($idempotency, "{$method} {$path} is missing Idempotency-Key documentation");
                    $this->assertTrue($idempotency['required'] ?? false, "{$method} {$path} does not require Idempotency-Key");
                }
            }
        }

        $this->assertGreaterThan(600, count($definitions), 'The API registry unexpectedly lost business operations.');
        $this->assertCount(count($definitions), $operationIds, 'Each workflow must map to one unique OpenAPI operation.');
    }
}

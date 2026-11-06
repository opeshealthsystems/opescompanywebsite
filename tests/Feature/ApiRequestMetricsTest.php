<?php

namespace Tests\Feature;

use App\Contexts\ApiIdentity\Application\Queries\ApiRequestMetricsQueries;
use App\Contexts\ApiIdentity\Application\Services\ApiRequestMetricRecorder;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Tests\TestCase;

class ApiRequestMetricsTest extends TestCase
{
    use RefreshDatabase;

    private static array $keys;

    protected function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow('2026-11-06 12:00:00');
        if (!isset(self::$keys)) {
            $options = ['private_key_bits' => 2048, 'private_key_type' => OPENSSL_KEYTYPE_RSA];
            if (PHP_OS_FAMILY === 'Windows') $options['config'] = dirname(PHP_BINARY).'/extras/ssl/openssl.cnf';
            $key = openssl_pkey_new($options);
            openssl_pkey_export($key, $private, null, $options);
            self::$keys = ['private' => $private, 'public' => openssl_pkey_get_details($key)['key']];
        }
        config(['passport.private_key' => self::$keys['private'], 'passport.public_key' => self::$keys['public']]);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_metrics_are_aggregated_by_stable_route_and_isolated_by_client_and_user(): void
    {
        $route = new Route('GET', 'api/v1/records/{record}', fn () => null);
        $route->name('api.v1.records.show');
        $request = Request::create('/api/v1/records/private-record-91?email=private%40example.test');
        $request->setRouteResolver(fn () => $route);
        $recorder = app(ApiRequestMetricRecorder::class);

        $recorder->record($request, 'machine-client-a', 'machine', 'machine-client-a', 200, 50);
        $recorder->record($request, 'machine-client-a', 'machine', 'machine-client-a', 422, 120);
        $recorder->record($request, 'machine-client-b', 'machine', 'machine-client-b', 500, 100);

        $metrics = app(ApiRequestMetricsQueries::class)->execute('machine-client-a', 'machine', 'machine-client-a');
        $this->assertSame(2, $metrics['requests']);
        $this->assertSame(1, $metrics['responses_2xx']);
        $this->assertSame(1, $metrics['responses_4xx']);
        $this->assertSame(0, $metrics['responses_5xx']);
        $this->assertSame(85.0, $metrics['average_response_time_ms']);
        $this->assertSame(120, $metrics['max_response_time_ms']);
        $this->assertCount(30, $metrics['daily']);
        $this->assertSame('api.v1.records.show', $metrics['operations'][0]['operation']);
        $this->assertSame(2, $metrics['operations'][0]['requests']);
        $this->assertSame(0, app(ApiRequestMetricsQueries::class)->execute('machine-client-a', 'user', '17')['requests']);
        $this->assertDatabaseHas('api_request_metrics', ['client_id' => 'machine-client-a', 'route_key' => 'api.v1.records.show']);
        $this->assertDatabaseMissing('api_request_metrics', ['client_id' => 'machine-client-a', 'route_key' => 'private-record-91']);
        $this->assertFalse(\Illuminate\Support\Facades\Schema::hasColumn('api_request_metrics', 'url'));
        $this->assertFalse(\Illuminate\Support\Facades\Schema::hasColumn('api_request_metrics', 'payload'));
    }

    public function test_machine_and_delegated_metrics_routes_require_their_own_authentication(): void
    {
        $this->getJson('/api/v1/integration/metrics')->assertUnauthorized();
        $this->getJson('/api/v1/me/metrics')->assertUnauthorized();
    }

    public function test_metrics_retention_command_removes_aggregates_older_than_35_days(): void
    {
        \Illuminate\Support\Facades\DB::table('api_request_metrics')->insert([
            [
                'client_id' => 'client-a', 'principal_type' => 'machine', 'principal_id' => 'client-a',
                'metric_date' => '2026-10-01', 'route_key' => 'api.old', 'http_method' => 'GET',
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'client_id' => 'client-a', 'principal_type' => 'machine', 'principal_id' => 'client-a',
                'metric_date' => '2026-10-20', 'route_key' => 'api.recent', 'http_method' => 'GET',
                'created_at' => now(), 'updated_at' => now(),
            ],
        ]);

        $this->artisan('api:metrics-prune')->expectsOutput('Pruned 1 API metric aggregates older than 35 days.')->assertExitCode(0);
        $this->assertDatabaseMissing('api_request_metrics', ['route_key' => 'api.old']);
        $this->assertDatabaseHas('api_request_metrics', ['route_key' => 'api.recent']);
    }

    public function test_recording_runs_retention_cleanup_at_most_once_per_utc_day(): void
    {
        \Illuminate\Support\Facades\DB::table('api_request_metrics')->insert([
            'client_id' => 'client-a', 'principal_type' => 'machine', 'principal_id' => 'client-a',
            'metric_date' => '2026-10-01', 'route_key' => 'api.expired', 'http_method' => 'GET',
            'created_at' => now(), 'updated_at' => now(),
        ]);
        \Illuminate\Support\Facades\Cache::flush();

        $request = Request::create('/api/v1/integration', 'GET');
        $route = new Route('GET', 'api/v1/integration', fn () => null);
        $route->name('api.v1.integration');
        $request->setRouteResolver(fn () => $route);
        $recorder = app(ApiRequestMetricRecorder::class);
        $recorder->record($request, 'client-a', 'machine', 'client-a', 200, 1);
        $recorder->record($request, 'client-a', 'machine', 'client-a', 200, 1);

        $this->assertDatabaseMissing('api_request_metrics', ['route_key' => 'api.expired']);
        $this->assertDatabaseHas('api_request_metrics', ['route_key' => 'api.v1.integration', 'requests' => 2]);
    }
}

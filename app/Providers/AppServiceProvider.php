<?php

namespace App\Providers;

use App\Services\Payouts\ManualPayoutGateway;
use App\Services\Payouts\MtnMomoPayoutGateway;
use App\Services\Payouts\OrangeMoneyPayoutGateway;
use App\Services\Payouts\PayoutGateway;
use App\Services\Payouts\PayoutGatewayManager;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->scoped(\App\Contexts\Shared\Application\CurrentWorkflow::class, fn () => new \App\Contexts\Shared\Application\CurrentWorkflow());
        \Laravel\Passport\Passport::ignoreRoutes();
        $this->app->singleton(\Laravel\Passport\ClientRepository::class, \App\Contexts\ApiIdentity\Infrastructure\ApprovedClientRepository::class);
        $this->app->bind(\Laravel\Passport\Bridge\ScopeRepository::class, \App\Contexts\ApiIdentity\Infrastructure\ApprovedScopeRepository::class);
        // Resolve the configured payout rail. Defaults to manual (offline) settlement;
        // provider drivers (MoMo, Orange) are drop-in once credentials are supplied.
        $this->app->bind(PayoutGateway::class, function () {
            return match (config('payouts.driver')) {
                'mtn_momo'     => new MtnMomoPayoutGateway(),
                'orange_money' => new OrangeMoneyPayoutGateway(),
                default        => new ManualPayoutGateway(),
            };
        });

        // Per-payout driver selection based on the practitioner's resolved network.
        $this->app->singleton(PayoutGatewayManager::class, fn ($app) => new PayoutGatewayManager($app));

        // On Windows, PHP's rename() can briefly fail with "Access is denied (code: 5)"
        // when the OS (file indexer, AV real-time scan) holds a handle on the just-created
        // .tmp file. This override retries up to 5 times with an exponential back-off
        // before falling back to a direct write, eliminating the intermittent 500 on
        // POST /livewire/update caused by Blade's view compiler.
        if (PHP_OS_FAMILY === 'Windows') {
            $this->app->singleton('files', function () {
                return new class extends Filesystem {
                    public function replace($path, $content, $lock = false): void
                    {
                        $tmpFile = tempnam(dirname($path), basename($path));

                        if ($tmpFile === false) {
                            file_put_contents($path, $content, $lock ? LOCK_EX : 0);
                            return;
                        }

                        file_put_contents($tmpFile, $content, $lock ? LOCK_EX : 0);

                        // Retry rename with exponential back-off (10ms → 20ms → 40ms → 80ms → 160ms)
                        $delay = 10000; // microseconds
                        for ($i = 0; $i < 5; $i++) {
                            if (@rename($tmpFile, $path)) {
                                return;
                            }
                            usleep($delay);
                            $delay *= 2;
                        }

                        // Final fallback: direct write + clean up temp file
                        file_put_contents($path, $content, $lock ? LOCK_EX : 0);
                        @unlink($tmpFile);
                    }
                };
            });
        }
    }

    public function boot(): void
    {
        \Illuminate\Support\Facades\Event::listen(['eloquent.created: *', 'eloquent.updated: *', 'eloquent.deleted: *'], function ($name, $payload) {
            $context = app(\App\Contexts\Shared\Application\CurrentWorkflow::class);
            $model = $payload[0] ?? null;
            if (!$context->command || \App\Contexts\Shared\Infrastructure\DomainOutbox::$delivering || !$model instanceof \Illuminate\Database\Eloquent\Model || !str_starts_with(get_class($model), 'App\\Models\\')) return;
            if (in_array(class_basename($model), ['WebsiteFormDelivery', 'ApiIdempotencyRecord'], true)) return;
            $context->changes[] = ['resource' => class_basename($model), 'id' => $model->getKey(), 'change' => explode(':', $name)[0], 'fields' => array_keys($model->getChanges())];
        });
        \Laravel\Passport\Passport::loadKeysFrom(storage_path('app/private/oauth'));
        \Laravel\Passport\Passport::tokensCan(['catalogue:read' => 'Read published OHS product metadata', 'integration:read' => 'Inspect this integration client'] + \App\Contexts\ApiIdentity\Application\BusinessScopes::definitions());
        \Laravel\Passport\Passport::authorizationView('auth.oauth-authorize');
        \Laravel\Passport\Passport::tokensExpireIn(\Carbon\CarbonInterval::minutes(15));
        \Laravel\Passport\Passport::refreshTokensExpireIn(\Carbon\CarbonInterval::days(7));
        \Laravel\Passport\Passport::clientCredentialsTokensExpireIn(\Carbon\CarbonInterval::minutes(15));
        \Illuminate\Support\Facades\RateLimiter::for('oauth-token', fn (\Illuminate\Http\Request $request) => \Illuminate\Cache\RateLimiting\Limit::perMinute(20)->by('oauth:'.$request->ip()));
        \Illuminate\Support\Facades\RateLimiter::for('api-consumer', fn (\Illuminate\Http\Request $request) => \Illuminate\Cache\RateLimiting\Limit::perMinute(60)->by('consumer:'.$request->attributes->get('api_client')->id));
        \Illuminate\Support\Facades\RateLimiter::for('public-api-read', fn (\Illuminate\Http\Request $request) => \Illuminate\Cache\RateLimiting\Limit::perMinute(60)->by('public-api:'.$request->ip()));
        \Illuminate\Support\Facades\RateLimiter::for('public-enquiries', fn (\Illuminate\Http\Request $request) => [
            \Illuminate\Cache\RateLimiting\Limit::perMinute(5)->by('enquiry-minute:'.$request->ip()),
            \Illuminate\Cache\RateLimiting\Limit::perDay(30)->by('enquiry-day:'.$request->ip()),
        ]);
        if ($this->app->isProduction()) {
            \Illuminate\Support\Facades\URL::forceRootUrl(config('seo.canonical_url'));
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }
        \Illuminate\Support\Facades\Gate::policy(\App\Models\Ticket::class, \App\Policies\TicketPolicy::class);
        \Illuminate\Support\Facades\Gate::policy(\App\Models\TesterAssignment::class, \App\Policies\TesterAssignmentPolicy::class);
        \Illuminate\Support\Facades\Gate::policy(\App\Models\PractitionerApplication::class, \App\Policies\PractitionerApplicationPolicy::class);
        \Illuminate\Support\Facades\Gate::policy(\App\Models\PractitionerFinding::class, \App\Policies\PractitionerFindingPolicy::class);
    }
}

<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\Passport;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\ResourceServer;
use Symfony\Bridge\PsrHttpMessage\Factory\PsrHttpFactory;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class AuthenticateApiClient
{
    public function __construct(private ResourceServer $server) {}

    public function handle(Request $request, Closure $next, string $scope)
    {
        $startedAt = hrtime(true);
        try {
            $psr = $this->server->validateAuthenticatedRequest((new PsrHttpFactory)->createRequest($request));
        } catch (OAuthServerException) { throw new UnauthorizedHttpException('Bearer'); }
        $id = $psr->getAttribute('oauth_client_id');
        $client = Passport::client()->newQuery()->find($id);
        if (!$client || $client->revoked || !$client->owner instanceof User || !$client->owner->is_active
            || !is_array($client->scopes) || !in_array('client_credentials', $client->grant_types, true)
            || !in_array($psr->getAttribute('oauth_user_id'), [null, $id], true)) throw new UnauthorizedHttpException('Bearer');
        $token = Passport::token()->newQuery()->find($psr->getAttribute('oauth_access_token_id'));
        if (!$token || $token->client_id !== $client->id || $token->revoked || !$token->expires_at || $token->expires_at->isPast()) throw new UnauthorizedHttpException('Bearer');
        abort_unless(in_array($scope, $psr->getAttribute('oauth_scopes', []), true) && in_array($scope, $client->scopes, true), 403);
        $quota = app(\App\Contexts\ApiIdentity\Application\Services\MachineClientQuota::class)->consume($client);
        $request->attributes->set('api_client', $client);
        $request->attributes->set('api_scopes', $psr->getAttribute('oauth_scopes', []));
        $metrics = app(\App\Contexts\ApiIdentity\Application\Services\ApiRequestMetricRecorder::class);
        if (!$quota['allowed']) {
            $response = app(\App\Contexts\ApiIdentity\Application\Services\MachineClientQuota::class)->reject($request, $quota);
            $metrics->record($request, (string) $client->id, 'machine', (string) $client->id, 429,
                \App\Contexts\ApiIdentity\Application\Services\ApiRequestMetricRecorder::durationMilliseconds($startedAt));
            return $response;
        }

        try {
            $response = $next($request);
        } catch (\Throwable $error) {
            $metrics->record($request, (string) $client->id, 'machine', (string) $client->id,
                \App\Contexts\ApiIdentity\Application\Services\ApiRequestMetricRecorder::exceptionStatus($error),
                \App\Contexts\ApiIdentity\Application\Services\ApiRequestMetricRecorder::durationMilliseconds($startedAt));
            throw $error;
        }

        $response = app(\App\Contexts\ApiIdentity\Application\Services\MachineClientQuota::class)->attach($response, $quota);
        $metrics->record($request, (string) $client->id, 'machine', (string) $client->id, $response->getStatusCode(),
            \App\Contexts\ApiIdentity\Application\Services\ApiRequestMetricRecorder::durationMilliseconds($startedAt));
        return $response;
    }
}

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
        $request->attributes->set('api_client', $client);
        $request->attributes->set('api_scopes', $psr->getAttribute('oauth_scopes', []));
        $usageDate = now('UTC')->toDateString();
        DB::table('api_client_usage')->insertOrIgnore(['client_id' => $client->id, 'usage_date' => $usageDate, 'requests' => 0]);
        DB::table('api_client_usage')->where('client_id', $client->id)->where('usage_date', $usageDate)->increment('requests');
        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use App\Contexts\ApiIdentity\Application\BusinessScopes;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Laravel\Passport\Passport;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\ResourceServer;
use Symfony\Bridge\PsrHttpMessage\Factory\PsrHttpFactory;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

final class AuthenticateBusinessUser
{
    public function __construct(private ResourceServer $server) {}

    public function handle(Request $request, Closure $next)
    {
        try { $psr = $this->server->validateAuthenticatedRequest((new PsrHttpFactory)->createRequest($request)); }
        catch (OAuthServerException) { throw new UnauthorizedHttpException('Bearer'); }
        $subject = (string) $psr->getAttribute('oauth_user_id');
        if (!ctype_digit($subject) || (int) $subject < 1) throw new UnauthorizedHttpException('Bearer');
        $client = app(\Laravel\Passport\ClientRepository::class)->findActive($psr->getAttribute('oauth_client_id'));
        $token = Passport::token()->newQuery()->find($psr->getAttribute('oauth_access_token_id'));
        $user = User::find($subject);
        if (!$client || !in_array('authorization_code', $client->grant_types, true)
            || !$token || (string) $token->user_id !== $subject || (string) $token->client_id !== (string) $client->id
            || $token->revoked || !$token->expires_at || $token->expires_at->isPast() || !$user?->is_active) throw new UnauthorizedHttpException('Bearer');
        $scopes = array_values(array_intersect($psr->getAttribute('oauth_scopes', []), $client->scopes, BusinessScopes::allowed($user)));
        $resolver = $request->getUserResolver();
        $request->setUserResolver(fn () => $user);
        $request->attributes->set('api_client', $client);
        $request->attributes->set('api_scopes', $scopes);
        try { return $next($request); }
        finally { $request->setUserResolver($resolver); }
    }
}

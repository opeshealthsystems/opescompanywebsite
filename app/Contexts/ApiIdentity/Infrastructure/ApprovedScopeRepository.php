<?php

namespace App\Contexts\ApiIdentity\Infrastructure;

use Laravel\Passport\Bridge\ScopeRepository;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Exception\OAuthServerException;

class ApprovedScopeRepository extends ScopeRepository
{
    public function finalizeScopes(array $scopes, string $grantType, ClientEntityInterface $clientEntity, ?string $userIdentifier = null, ?string $authCodeId = null): array
    {
        $client = $this->clients->findActive($clientEntity->getIdentifier());
        foreach ($scopes as $scope) {
            if (!$client || !$client->hasScope($scope->getIdentifier())) throw OAuthServerException::invalidScope($scope->getIdentifier());
            if ($grantType !== 'client_credentials') {
                $user = \App\Models\User::find($userIdentifier);
                if (!$user || !in_array($scope->getIdentifier(), \App\Contexts\ApiIdentity\Application\BusinessScopes::allowed($user), true)) throw OAuthServerException::invalidScope($scope->getIdentifier());
            }
        }
        return parent::finalizeScopes($scopes, $grantType, $clientEntity, $userIdentifier, $authCodeId);
    }
}

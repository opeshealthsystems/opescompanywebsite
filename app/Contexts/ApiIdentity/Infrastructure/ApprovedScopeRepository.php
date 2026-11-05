<?php

namespace App\Contexts\ApiIdentity\Infrastructure;

use App\Contexts\ApiIdentity\Application\BusinessScopes;
use App\Models\User;
use Laravel\Passport\Bridge\ScopeRepository;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Exception\OAuthServerException;

class ApprovedScopeRepository extends ScopeRepository
{
    public function finalizeScopes(array $scopes, string $grantType, ClientEntityInterface $clientEntity, ?string $userIdentifier = null, ?string $authCodeId = null): array
    {
        $client = $this->clients->findActive($clientEntity->getIdentifier());
        // The client's registered scopes are a hard ceiling: asking for anything outside it is an error.
        foreach ($scopes as $scope) {
            if (!$client || !$client->hasScope($scope->getIdentifier())) throw OAuthServerException::invalidScope($scope->getIdentifier());
        }

        if ($grantType !== 'client_credentials') {
            // A reviewed multi-role app (e.g. OPES Health mobile) requests its whole ceiling. Each delegated
            // token keeps only the scopes the user's active roles allow; it fails only when none remain.
            $user = User::find($userIdentifier);
            $allowed = $user ? BusinessScopes::allowed($user) : [];
            $requested = $scopes;
            $scopes = array_values(array_filter($scopes, fn ($scope) => in_array($scope->getIdentifier(), $allowed, true)));
            if ($requested !== [] && $scopes === []) throw OAuthServerException::invalidScope($requested[0]->getIdentifier());
        }

        return parent::finalizeScopes($scopes, $grantType, $clientEntity, $userIdentifier, $authCodeId);
    }
}

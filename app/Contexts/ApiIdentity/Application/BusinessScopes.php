<?php

namespace App\Contexts\ApiIdentity\Application;

use App\Models\User;

final class BusinessScopes
{
    public static function definitions(): array
    {
        $scopes = [];
        foreach (config('business_workflows.definitions', []) as $definition) {
            if ($definition['authenticated']) $scopes[$definition['scope']] = 'Access '.str_replace(':', ' ', $definition['scope']).' operations';
        }
        return $scopes;
    }

    public static function allowed(User $user): array
    {
        if (!$user->is_active) return [];
        $scopes = [];
        foreach (config('business_workflows.definitions', []) as $definition) {
            if ($definition['authenticated'] && (!$definition['roles'] || $user->hasAnyRole($definition['roles']))) $scopes[] = $definition['scope'];
        }
        return array_values(array_unique($scopes));
    }
}

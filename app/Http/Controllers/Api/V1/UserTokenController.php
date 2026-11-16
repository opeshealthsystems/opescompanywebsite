<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\Http\Controllers\AccessTokenController;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

final class UserTokenController
{
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, AccessTokenController $tokens)
    {
        $body = (array) $request->getParsedBody();
        $grant = $body['grant_type'] ?? null;

        if (!in_array($grant, ['authorization_code', 'refresh_token', 'password'], true)) {
            return response()->json(['error' => 'unsupported_grant_type'], 400);
        }

        // First-party in-app email/password sign-in. The web OAuth flow enforces the active-account
        // gate through the login page; the password grant must enforce it here before issuing a token,
        // and return one generic message so it never reveals whether an email is registered.
        if ($grant === 'password') {
            $email = is_string($body['username'] ?? null) ? trim($body['username']) : '';
            $password = is_string($body['password'] ?? null) ? $body['password'] : '';
            $user = $email !== '' ? User::where('email', $email)->first() : null;

            if (!$user || $password === '' || !Hash::check($password, (string) $user->password)) {
                return response()->json(['error' => 'invalid_grant', 'error_description' => 'The email or password is incorrect.'], 400);
            }
            if (!$user->is_active) {
                return response()->json(['error' => 'invalid_grant', 'error_description' => 'This account is not active. Contact your administrator.'], 403);
            }
        }

        return $tokens->issueToken($request, $response);
    }
}

<?php

namespace App\Http\Controllers\Api\V1;

use Laravel\Passport\Http\Controllers\AccessTokenController;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

final class UserTokenController
{
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, AccessTokenController $tokens)
    {
        if (!in_array($request->getParsedBody()['grant_type'] ?? null, ['authorization_code', 'refresh_token'], true)) {
            return response()->json(['error' => 'unsupported_grant_type'], 400);
        }
        return $tokens->issueToken($request, $response);
    }
}

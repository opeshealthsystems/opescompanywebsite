<?php

namespace App\Http\Controllers\Api\V1;

use Laravel\Passport\Http\Controllers\AccessTokenController;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class MachineTokenController
{
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, AccessTokenController $tokens)
    {
        if (($request->getParsedBody()['grant_type'] ?? null) !== 'client_credentials') {
            return response()->json(['error' => 'unsupported_grant_type', 'error_description' => 'Only client_credentials is enabled.'], 400);
        }
        return $tokens->issueToken($request, $response);
    }
}

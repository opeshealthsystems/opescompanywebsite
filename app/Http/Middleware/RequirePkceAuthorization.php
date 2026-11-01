<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

final class RequirePkceAuthorization
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->isMethod('GET')) {
            $request->validate(['response_type' => 'required|in:code', 'client_id' => 'required|uuid',
                'state' => 'required|string|min:32|max:512', 'code_challenge_method' => 'required|in:S256',
                'code_challenge' => ['required', 'regex:/\A[A-Za-z0-9_-]{43}\z/'], 'scope' => 'required|string|max:2000']);
        }
        abort_if($request->user() && !$request->user()->is_active, 403);
        return $next($request);
    }
}

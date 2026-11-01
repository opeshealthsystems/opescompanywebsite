<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CanonicalSite
{
    public function handle(Request $request, Closure $next)
    {
        if (app()->isProduction() && $request->isMethodSafe() && $request->getHost() === 'www.opeshealthsystems.com') {
            return redirect(config('seo.canonical_url').$request->getRequestUri(), 301);
        }
        return $next($request);
    }
}

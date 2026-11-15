<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/** Web adapter for the public search application query. */
final class PublicSearchController extends Controller
{
    public function index(Request $request): mixed
    {
        return app(\App\Contexts\Shared\Infrastructure\WorkflowHttpAdapter::class)->web('public.search', $request);
    }
}

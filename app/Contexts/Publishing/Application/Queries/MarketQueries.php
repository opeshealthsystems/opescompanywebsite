<?php

namespace App\Contexts\Publishing\Application\Queries;

class MarketQueries extends \App\Contexts\Shared\Application\WorkflowHandler
{
    public function index(\App\Contexts\Shared\Application\WorkflowInput $workflowInput, string $locale): \App\Contexts\Shared\Application\WorkflowResult
    {
        $markets = config('markets');
        return \App\Contexts\Shared\Application\WorkflowResult::read('marketcontroller_index_1', compact('markets'));
    }
    public function show(\App\Contexts\Shared\Application\WorkflowInput $workflowInput, string $locale, string $slug): \App\Contexts\Shared\Application\WorkflowResult
    {
        $markets = config('markets');
        if (!array_key_exists($slug, $markets)) {
            abort(404);
        }
        $market = $markets[$slug];
        $relatedPost = \App\Models\BlogPost::published()->where('slug', $market['related_blog'])->exists();
        // The other CEMAC markets, for cross-linking at the foot of each page.
        $others = array_values(array_filter($markets, fn($m) => $m['slug'] !== $slug));
        return \App\Contexts\Shared\Application\WorkflowResult::read('marketcontroller_show_1', compact('market', 'others', 'relatedPost'));
    }
}

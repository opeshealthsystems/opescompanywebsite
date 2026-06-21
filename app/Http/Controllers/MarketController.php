<?php

namespace App\Http\Controllers;

class MarketController extends Controller
{
    public function index(string $locale)
    {
        $markets = config('markets');

        return view('pages.markets.index', compact('markets'));
    }

    public function show(string $locale, string $slug)
    {
        $markets = config('markets');

        if (! array_key_exists($slug, $markets)) {
            abort(404);
        }

        $market = $markets[$slug];

        // The other CEMAC markets, for cross-linking at the foot of each page.
        $others = array_values(array_filter(
            $markets,
            fn ($m) => $m['slug'] !== $slug
        ));

        return view('pages.markets.show', compact('market', 'others'));
    }
}

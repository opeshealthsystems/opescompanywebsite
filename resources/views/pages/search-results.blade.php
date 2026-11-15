@php($locale = app()->getLocale())
<x-layouts.app :indexable="false">
    <section class="search-page" aria-labelledby="searchPageTitle">
        <p class="search-result-type">{{ $locale === 'fr' ? 'Recherche OPES' : 'OPES search' }}</p>
        <h1 id="searchPageTitle">{{ $locale === 'fr' ? 'Résultats de recherche' : 'Search results' }}</h1>
        <p class="search-page-lede">
            {{ $locale === 'fr' ? 'Articles publiés, formations actives et produits publics.' : 'Published articles, active courses and public products.' }}
        </p>
        <form class="site-search" action="{{ route('search', ['locale' => $locale]) }}" method="GET" role="search" style="max-width:720px;justify-self:start;margin-bottom:26px">
            <label class="sr-only" for="resultsSearchInput">{{ $locale === 'fr' ? 'Rechercher' : 'Search' }}</label>
            <i data-lucide="search" aria-hidden="true"></i>
            <input id="resultsSearchInput" type="search" name="q" minlength="2" maxlength="100" required value="{{ $query }}">
            <button type="submit">{{ $locale === 'fr' ? 'Rechercher' : 'Search' }}</button>
        </form>
        <h2 style="font-size:18px;color:#243b53;margin:0 0 14px">{{ $locale === 'fr' ? 'Résultats pour' : 'Results for' }} “{{ $query }}” <span style="font-weight:500;color:#627d98">({{ count($results) }})</span></h2>
        @if(count($results))
            <div class="search-results-list">
                @foreach($results as $result)
                    <a class="search-result" href="{{ $result['url'] }}">
                        <span class="search-result-type">{{ match($result['type']) { 'article' => $locale === 'fr' ? 'Article' : 'Article', 'course' => $locale === 'fr' ? 'Formation' : 'Course', default => $locale === 'fr' ? 'Produit' : 'Product' } }}</span>
                        <h2>{{ $result['title'] }}</h2>
                        @if($result['summary']) <p>{{ \Illuminate\Support\Str::limit(strip_tags($result['summary']), 260) }}</p> @endif
                    </a>
                @endforeach
            </div>
        @else
            <div class="search-empty">{{ $locale === 'fr' ? 'Aucun résultat public ne correspond. Essayez un autre terme.' : 'No public results matched that search. Try another term.' }}</div>
        @endif
    </section>
</x-layouts.app>

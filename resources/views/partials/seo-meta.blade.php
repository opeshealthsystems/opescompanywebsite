@php
    $locale    = app()->getLocale();
    $segments  = request()->segments();
    if (empty($segments)) { $segments = [$locale]; }

    $enSegments = $segments; $enSegments[0] = 'en';
    $frSegments = $segments; $frSegments[0] = 'fr';
    $enUrl     = url(implode('/', $enSegments));
    $frUrl     = url(implode('/', $frSegments));
    $canonical = url(implode('/', $segments));
    if (request()->integer('page', 1) > 1) {
        $canonical .= '?page='.request()->integer('page');
        $enUrl .= '?page='.request()->integer('page');
        $frUrl .= '?page='.request()->integer('page');
    }

    $siteName  = 'OPES Health Systems';
    $path = implode('/', array_slice($segments, 1));
    // The home route is the empty config key; dot lookup cannot address it.
    $pageLabel = config('seo.pages')[$path] ?? null;
    $baseTitle = $pageLabel[$locale === 'fr' ? 1 : 0] ?? $title ?? ($locale === 'fr' ? 'Logiciels de santé' : 'Healthcare software');
    $baseTitle = html_entity_decode(strip_tags($baseTitle), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $baseTitle = preg_replace('/(?:\s*[—–|]\s*OPES Health Systems)+\s*$/u', '', $baseTitle);
    $pageTitle = str_contains($baseTitle, $siteName) ? $baseTitle : $baseTitle . ' — ' . $siteName;
    $defaultDesc = $pageLabel
        ? ($locale === 'fr' ? $pageLabel[1].'. Découvrez les informations OPES et contactez notre équipe pour discuter de votre projet.' : $pageLabel[0].'. Explore OPES information and contact our team to discuss your project.')
        : ($locale === 'fr' ? 'Découvrez les logiciels OPES pour les hôpitaux, cliniques, laboratoires et pharmacies. Contactez notre équipe.' : 'Explore OPES software for hospitals, clinics, laboratories and pharmacies. Contact our team.');
    $pageDesc = \Illuminate\Support\Str::limit(preg_replace('/\s+/u', ' ', html_entity_decode(strip_tags($description ?: $defaultDesc), ENT_QUOTES | ENT_HTML5, 'UTF-8')), 170, '…');
    $ogImage = $ogImage ?? asset('images/opes-seal.png');
    $private = collect(request()->route()?->gatherMiddleware() ?? [])->contains(fn ($m) => $m === 'auth' || str_starts_with($m, 'auth:'));
    $filtered = request()->hasAny(['search', 'category', 'profession', 'country']);
    $robots = $private || $filtered || !$indexable ? 'noindex, follow' : 'index, follow';
@endphp

<link rel="icon" href="{{ asset('images/opes-seal.png') }}" type="image/png">
<title>{{ $pageTitle }}</title>
<meta name="description" content="{{ $pageDesc }}">
<meta name="robots" content="{{ $robots }}">
<link rel="canonical" href="{{ $canonical }}">
<link rel="alternate" hreflang="en" href="{{ $enUrl }}">
<link rel="alternate" hreflang="fr" href="{{ $frUrl }}">
<link rel="alternate" hreflang="x-default" href="{{ $enUrl }}">

{{-- Open Graph --}}
<meta property="og:type" content="{{ $ogType ?? 'website' }}">
<meta property="og:site_name" content="{{ $siteName }}">
<meta property="og:title" content="{{ $pageTitle }}">
<meta property="og:description" content="{{ $pageDesc }}">
<meta property="og:url" content="{{ $canonical }}">
<meta property="og:image" content="{{ $ogImage }}">
<meta property="og:locale" content="{{ $locale === 'fr' ? 'fr_CM' : 'en_CM' }}">

{{-- Twitter Card --}}
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $pageTitle }}">
<meta name="twitter:description" content="{{ $pageDesc }}">
<meta name="twitter:image" content="{{ $ogImage }}">

<script type="application/ld+json"><?php echo json_encode([
    '@context' => 'https://schema.org', '@type' => 'WebPage', '@id' => $canonical.'#webpage',
    'url' => $canonical, 'name' => $pageTitle, 'description' => $pageDesc,
    'inLanguage' => $locale, 'isPartOf' => ['@id' => url('/').'#website'],
    'about' => ['@id' => url('/').'#organization'],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP); ?></script>
<script type="application/ld+json"><?php echo json_encode([
    '@context' => 'https://schema.org', '@type' => 'WebSite', '@id' => url('/').'#website',
    'url' => url('/'), 'name' => $siteName, 'inLanguage' => ['en', 'fr'],
    'publisher' => ['@id' => url('/').'#organization'],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP); ?></script>

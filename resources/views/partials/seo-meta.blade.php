@php
    $locale    = app()->getLocale();
    $segments  = request()->segments();
    if (empty($segments)) { $segments = [$locale]; }

    $enSegments = $segments; $enSegments[0] = 'en';
    $frSegments = $segments; $frSegments[0] = 'fr';
    $enUrl     = url(implode('/', $enSegments));
    $frUrl     = url(implode('/', $frSegments));
    $canonical = url(implode('/', $segments));

    $siteName  = 'OPES Health Systems';
    $baseTitle = $title ?? 'Africa\'s Most Complete Healthcare Ecosystem';
    $pageTitle = $baseTitle . ' — ' . $siteName;
    $pageDesc  = $description ?? '22 integrated healthcare software systems for Cameroon, CEMAC and Africa — bilingual (EN/FR), HL7 FHIR, aligned with MoH 2026–2030 digitalization plan.';
    $ogImage   = $ogImage ?? config('app.url').'/build/assets/og-image.png';
@endphp

<title>{{ $pageTitle }}</title>
<meta name="description" content="{{ $pageDesc }}">
<meta name="robots" content="index, follow">
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

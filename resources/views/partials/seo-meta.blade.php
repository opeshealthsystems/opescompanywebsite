@php
    $segments = request()->segments();
    if (empty($segments)) { $segments = [app()->getLocale()]; }
    $enSegments = $segments; $enSegments[0] = 'en';
    $frSegments = $segments; $frSegments[0] = 'fr';
    $enUrl = url(implode('/', $enSegments));
    $frUrl = url(implode('/', $frSegments));
    $canonical = url(implode('/', $segments));
    $pageTitle = ($title ?? __('home.hero_title')).' — '.__('common.company');
    $pageDescription = $description ?? __('home.hero_tagline');
@endphp

<title>{{ $pageTitle }}</title>
<meta name="description" content="{{ $pageDescription }}">
<link rel="canonical" href="{{ $canonical }}">
<link rel="alternate" hreflang="en" href="{{ $enUrl }}">
<link rel="alternate" hreflang="fr" href="{{ $frUrl }}">
<link rel="alternate" hreflang="x-default" href="{{ $enUrl }}">

<meta property="og:type" content="website">
<meta property="og:title" content="{{ $pageTitle }}">
<meta property="og:description" content="{{ $pageDescription }}">
<meta property="og:url" content="{{ $canonical }}">

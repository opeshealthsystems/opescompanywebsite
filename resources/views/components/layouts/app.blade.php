@props(['title' => null, 'description' => null, 'ogType' => 'website', 'ogImage' => null])
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @include('partials.seo-meta')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <x-navbar />

    <main>
        {{ $slot }}
    </main>

    <x-footer />

    <script src="{{ asset('vendor/lucide.min.js') }}"></script>
    <script>lucide.createIcons();</script>
    @stack('schema')
    <script type="application/ld+json"><?php echo json_encode([
        '@context'     => 'https://schema.org',
        '@type'        => 'Organization',
        'name'         => 'OPES Health Systems',
        'legalName'    => 'OPES Health Systems SARL',
        'url'          => config('app.url'),
        'logo'         => config('app.url').'/favicon.ico',
        'address'      => [
            '@type'           => 'PostalAddress',
            'addressLocality' => 'Bonamousadi, Douala',
            'addressCountry'  => 'CM',
        ],
        'contactPoint' => [
            '@type'             => 'ContactPoint',
            'contactType'       => 'sales',
            'email'             => 'contact@opeshealthsystems.com',
            'availableLanguage' => ['French', 'English'],
        ],
        'sameAs' => [],
    ], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT); ?></script>
</body>
</html>

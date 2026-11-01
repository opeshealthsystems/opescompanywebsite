@props(['title' => 'OPES Health Systems'])
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'OPES Health Systems' }}</title>
    <meta name="robots" content="noindex, follow">
    <link rel="icon" type="image/png" href="{{ asset('images/opes-seal.png') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="auth-body">
    <div class="auth-wrapper">
        <a href="{{ url('/') }}" class="auth-brand">
            <img src="{{ asset('images/opes-seal.png') }}" alt="OPES Health Systems seal" width="72" height="72" style="object-fit:contain;margin:0 auto 12px">
            <span class="auth-brand-opes">OPES</span>
            <span class="auth-brand-name"> Health Systems</span>
        </a>
        {{ $slot }}
        <p class="auth-footer-note">
            &copy; {{ date('Y') }} OPES Health Systems SARL — Douala, Cameroon
        </p>
    </div>
    <script src="{{ asset('vendor/lucide.min.js') }}"></script>
    <script>lucide.createIcons();</script>
</body>
</html>

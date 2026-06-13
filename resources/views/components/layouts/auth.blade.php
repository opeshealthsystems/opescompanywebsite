@props(['title' => 'OPES Health Systems'])
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'OPES Health Systems' }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="auth-body">
    <div class="auth-wrapper">
        <a href="{{ url('/') }}" class="auth-brand">
            <span class="auth-brand-opes">OPES</span>
            <span class="auth-brand-name"> Health Systems</span>
        </a>
        {{ $slot }}
        <p class="auth-footer-note">
            &copy; {{ date('Y') }} OPES Health Systems SARL — Douala, Cameroon
        </p>
    </div>
    <script src="https://unpkg.com/lucide@0.511.0/dist/umd/lucide.min.js" crossorigin="anonymous"></script>
    <script>lucide.createIcons();</script>
</body>
</html>

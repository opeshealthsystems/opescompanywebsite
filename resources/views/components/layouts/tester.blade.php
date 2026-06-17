@props(['title' => 'Tester Portal'])
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }} — OPES Tester Portal</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="cp-body">
    <nav class="cp-nav portal-nav">
        <a href="{{ route('tester.dashboard', ['locale' => app()->getLocale()]) }}" class="cp-nav-brand">
            <span class="cp-brand-opes">OPES</span>
            <span class="cp-brand-name"> Tester</span>
        </a>
        <button type="button" class="portal-burger" data-portal-burger aria-label="Toggle menu">
            <i data-lucide="menu" class="portal-burger-open" style="width:22px;height:22px"></i>
            <i data-lucide="x" class="portal-burger-close" style="width:22px;height:22px"></i>
        </button>
        <div class="cp-nav-links portal-menu">
            <a href="{{ route('tester.dashboard', ['locale' => app()->getLocale()]) }}"
               class="cp-nav-link {{ request()->routeIs('tester.dashboard') ? 'cp-nav-link-active' : '' }}">
                <i data-lucide="layout-dashboard" style="width:16px;height:16px"></i> Dashboard
            </a>
            <a href="{{ route('tester.assignments', ['locale' => app()->getLocale()]) }}"
               class="cp-nav-link {{ request()->routeIs('tester.assignments*') ? 'cp-nav-link-active' : '' }}">
                <i data-lucide="clipboard-list" style="width:16px;height:16px"></i> Assignments
            </a>
        </div>
        <div class="cp-nav-user portal-actions">
            <span class="cp-nav-username">{{ auth()->user()->name }}</span>
            <form method="POST" action="{{ route('logout') }}" style="margin:0">
                @csrf
                <button type="submit" class="cp-logout-btn" title="Sign out">
                    <i data-lucide="log-out" style="width:16px;height:16px"></i>
                </button>
            </form>
        </div>
    </nav>

    @if (session('success'))
        <div class="cp-flash-success">{{ session('success') }}</div>
    @endif

    <main class="cp-main">
        <div class="cp-container">
            {{ $slot }}
        </div>
    </main>

    <script src="{{ asset('vendor/lucide.min.js') }}"></script>
    <script>lucide.createIcons();</script>
</body>
</html>

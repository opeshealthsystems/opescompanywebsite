@props(['title' => 'Customer Portal'])
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }} — OPES Health Systems</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="cp-body">
    <nav class="cp-nav">
        <a href="{{ route('customer.dashboard', ['locale' => app()->getLocale()]) }}" class="cp-nav-brand">
            <span class="cp-brand-opes">OPES</span>
            <span class="cp-brand-name"> Portal</span>
        </a>
        <div class="cp-nav-links">
            <a href="{{ route('customer.dashboard', ['locale' => app()->getLocale()]) }}"
               class="cp-nav-link {{ request()->routeIs('customer.dashboard') ? 'cp-nav-link-active' : '' }}">
                <i data-lucide="layout-dashboard" style="width:16px;height:16px"></i> Dashboard
            </a>
            <a href="{{ route('customer.documents', ['locale' => app()->getLocale()]) }}"
               class="cp-nav-link {{ request()->routeIs('customer.documents*') ? 'cp-nav-link-active' : '' }}">
                <i data-lucide="file-text" style="width:16px;height:16px"></i> Documents
            </a>
            <a href="{{ route('customer.licenses', ['locale' => app()->getLocale()]) }}"
               class="cp-nav-link {{ request()->routeIs('customer.licenses*') ? 'cp-nav-link-active' : '' }}">
                <i data-lucide="key" style="width:16px;height:16px"></i> Licenses
            </a>
            <a href="{{ route('customer.tickets', ['locale' => app()->getLocale()]) }}"
               class="cp-nav-link {{ request()->routeIs('customer.tickets*') ? 'cp-nav-link-active' : '' }}">
                <i data-lucide="ticket" style="width:16px;height:16px"></i> Support
            </a>
        </div>
        <div class="cp-nav-user">
            <span class="cp-nav-username">{{ auth()->user()->name }}</span>
            <a href="{{ route('customer.profile', ['locale' => app()->getLocale()]) }}"
               class="cp-nav-link {{ request()->routeIs('customer.profile') ? 'cp-nav-link-active' : '' }}">
                <i data-lucide="user" style="width:16px;height:16px"></i>
            </a>
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

    <script src="https://unpkg.com/lucide@0.511.0/dist/umd/lucide.min.js" crossorigin="anonymous"></script>
    <script>lucide.createIcons();</script>
</body>
</html>

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
            <a href="{{ route('customer.invoices', ['locale' => app()->getLocale()]) }}"
               class="cp-nav-link {{ request()->routeIs('customer.invoices*') ? 'cp-nav-link-active' : '' }}">
                <i data-lucide="receipt" style="width:16px;height:16px"></i> Invoices
            </a>
            <a href="{{ route('customer.tickets', ['locale' => app()->getLocale()]) }}"
               class="cp-nav-link {{ request()->routeIs('customer.tickets*') ? 'cp-nav-link-active' : '' }}">
                <i data-lucide="ticket" style="width:16px;height:16px"></i> Support
            </a>
            <a href="{{ route('customer.knowledge-base.index', ['locale' => app()->getLocale()]) }}"
               class="cp-nav-link {{ request()->routeIs('customer.knowledge-base*') ? 'cp-nav-link-active' : '' }}">
                <i data-lucide="book-open" style="width:16px;height:16px"></i> Help Center
            </a>
            @if(Route::has('customer.surveys'))
            <a href="{{ route('customer.surveys', ['locale' => app()->getLocale()]) }}"
               class="cp-nav-link {{ request()->routeIs('customer.surveys*') ? 'cp-nav-link-active' : '' }}">
                <i data-lucide="bar-chart-2" style="width:16px;height:16px"></i> Surveys
            </a>
            @endif
            @if(Route::has('customer.service-requests'))
            <a href="{{ route('customer.service-requests', ['locale' => app()->getLocale()]) }}"
               class="{{ request()->routeIs('customer.service-requests*') ? 'cp-nav-link-active' : '' }} cp-nav-link">
                <i data-lucide="wrench" style="width:16px;height:16px"></i> Service Requests
            </a>
            @endif
            @if(Route::has('customer.courses'))
            <a href="{{ route('customer.courses', ['locale' => app()->getLocale()]) }}"
               class="cp-nav-link {{ request()->routeIs('customer.courses*') || request()->routeIs('customer.lessons*') ? 'cp-nav-link-active' : '' }}">
                <i data-lucide="graduation-cap" style="width:16px;height:16px"></i> Courses
            </a>
            @endif
            @if(Route::has('customer.certificates'))
            <a href="{{ route('customer.certificates', ['locale' => app()->getLocale()]) }}"
               class="cp-nav-link {{ request()->routeIs('customer.certificates*') ? 'cp-nav-link-active' : '' }}">
                <i data-lucide="award" style="width:16px;height:16px"></i> Certificates
            </a>
            @endif
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

    <script src="{{ asset('vendor/lucide.min.js') }}"></script>
    <script>lucide.createIcons();</script>
</body>
</html>

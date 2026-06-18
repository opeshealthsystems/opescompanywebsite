@props(['title' => 'Support Portal'])
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }} — OPES Support</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="cp-body">
<nav class="cp-nav" id="cp-portal-nav">
    <a href="{{ route('support.dashboard', ['locale' => app()->getLocale()]) }}" class="cp-nav-brand">
        <span class="cp-brand-opes">OPES</span>
        <span class="cp-brand-name" style="color:#F97316"> Support</span>
    </a>
    <button class="cp-burger" onclick="document.getElementById('cp-portal-nav').classList.toggle('nav-open')" aria-label="Menu">
        <i data-lucide="menu" style="width:22px;height:22px"></i>
    </button>
    <div class="cp-nav-body">
        <div class="cp-nav-links">
            <a href="{{ route('support.dashboard', ['locale' => app()->getLocale()]) }}"
               class="cp-nav-link {{ request()->routeIs('support.dashboard') ? 'cp-nav-link-active' : '' }}">
                <i data-lucide="layout-dashboard" style="width:16px;height:16px"></i> Dashboard
            </a>
            <a href="{{ route('support.tickets', ['locale' => app()->getLocale()]) }}"
               class="cp-nav-link {{ request()->routeIs('support.tickets*') ? 'cp-nav-link-active' : '' }}">
                <i data-lucide="ticket" style="width:16px;height:16px"></i> Ticket Queue
            </a>
        </div>
        <div class="cp-nav-user">
            <span class="cp-nav-username">{{ auth()->user()->name }}</span>
            <form method="POST" action="{{ route('logout') }}" style="margin:0">
                @csrf
                <button type="submit" class="cp-logout-btn" title="Sign out">
                    <i data-lucide="log-out" style="width:16px;height:16px"></i>
                </button>
            </form>
        </div>
    </div>
</nav>
@if(session('success'))
<div class="cp-flash-success">{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="cp-flash-error">{{ session('error') }}</div>
@endif
<main class="cp-main">
    <div class="cp-container">{{ $slot }}</div>
</main>
<script src="{{ asset('vendor/lucide.min.js') }}"></script>
<script>lucide.createIcons();</script>
</body>
</html>

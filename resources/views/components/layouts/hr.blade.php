@props(['title' => 'HR Portal'])
@php $locale = app()->getLocale(); @endphp
<!DOCTYPE html>
<html lang="{{ $locale }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }} — OPES Health Systems</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="cp-body">
    <nav class="cp-nav" id="cp-portal-nav" style="position:relative">
        <a href="{{ route('hr.dashboard', ['locale' => $locale]) }}" class="cp-nav-brand" style="text-decoration:none">
            <span class="cp-brand-opes">OPES</span>
            <span class="cp-brand-name"> HR</span>
        </a>
        <button type="button" class="cp-burger" data-portal-burger aria-label="Menu">
            <i data-lucide="menu" class="cp-burger-open" style="width:22px;height:22px"></i>
            <i data-lucide="x"    class="cp-burger-close" style="width:22px;height:22px"></i>
        </button>
        <div class="cp-nav-body">
            <div class="cp-nav-links">
                <a href="{{ route('hr.dashboard', ['locale' => $locale]) }}"
                   class="cp-nav-link {{ request()->routeIs('hr.dashboard') ? 'cp-nav-link-active-purple' : '' }}">
                    <i data-lucide="layout-dashboard" style="width:15px;height:15px"></i> Dashboard
                </a>
                <a href="{{ route('hr.employees.index', ['locale' => $locale]) }}"
                   class="cp-nav-link {{ request()->routeIs('hr.employees*') ? 'cp-nav-link-active-purple' : '' }}">
                    <i data-lucide="users" style="width:15px;height:15px"></i> Employees
                </a>
                <a href="{{ route('hr.leave.index', ['locale' => $locale]) }}"
                   class="cp-nav-link {{ request()->routeIs('hr.leave*') ? 'cp-nav-link-active-purple' : '' }}">
                    <i data-lucide="calendar-check" style="width:15px;height:15px"></i> Leave
                </a>
                <a href="{{ route('hr.payroll.index', ['locale' => $locale]) }}"
                   class="cp-nav-link {{ request()->routeIs('hr.payroll*') ? 'cp-nav-link-active-purple' : '' }}">
                    <i data-lucide="dollar-sign" style="width:15px;height:15px"></i> Payroll
                </a>
                <a href="{{ route('hr.performance.index', ['locale' => $locale]) }}"
                   class="cp-nav-link {{ request()->routeIs('hr.performance*') ? 'cp-nav-link-active-purple' : '' }}">
                    <i data-lucide="star" style="width:15px;height:15px"></i> Performance
                </a>
                <a href="{{ route('hr.departments.index', ['locale' => $locale]) }}"
                   class="cp-nav-link {{ request()->routeIs('hr.departments*') ? 'cp-nav-link-active-purple' : '' }}">
                    <i data-lucide="building-2" style="width:15px;height:15px"></i> Departments
                </a>
                <a href="{{ route('hr.profile', ['locale' => $locale]) }}"
                   class="cp-nav-link {{ request()->routeIs('hr.profile') ? 'cp-nav-link-active-purple' : '' }}">
                    <i data-lucide="user" style="width:15px;height:15px"></i> My Profile
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
        <div class="cp-container">
            {{ $slot }}
        </div>
    </main>

    <script src="{{ asset('vendor/lucide.min.js') }}"></script>
    <script>
        lucide.createIcons();
        (function () {
            var nav = document.getElementById('cp-portal-nav');
            var btn = nav ? nav.querySelector('[data-portal-burger]') : null;
            if (btn) btn.addEventListener('click', function () { nav.classList.toggle('nav-open'); });
        })();
    </script>
</body>
</html>

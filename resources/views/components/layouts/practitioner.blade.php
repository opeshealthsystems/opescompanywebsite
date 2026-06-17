@props(['title' => 'Practitioner Portal'])
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }} — OPES Health Systems</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-950 text-slate-200 min-h-screen font-sans">
    <nav class="bg-slate-900 border-b border-slate-800 px-6 py-3 flex items-center gap-6 portal-nav">
        <a href="{{ route('practitioner.dashboard', ['locale' => app()->getLocale()]) }}" class="flex items-center gap-1 text-white font-bold text-lg no-underline mr-4">
            <span class="text-emerald-400">OPES</span>
            <span class="text-slate-200"> Practitioner</span>
        </a>
        <button type="button" class="portal-burger" data-portal-burger aria-label="Toggle menu">
            <i data-lucide="menu" class="portal-burger-open" style="width:22px;height:22px"></i>
            <i data-lucide="x" class="portal-burger-close" style="width:22px;height:22px"></i>
        </button>
        <div class="flex items-center gap-1 flex-1 portal-menu">
            <a href="{{ route('practitioner.dashboard', ['locale' => app()->getLocale()]) }}"
               class="flex items-center gap-1.5 px-3 py-1.5 rounded text-sm no-underline transition-colors {{ request()->routeIs('practitioner.dashboard') ? 'bg-slate-700 text-white' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                <i data-lucide="layout-dashboard" style="width:16px;height:16px"></i> Dashboard
            </a>
            @if(Route::has('practitioner.programs'))
            <a href="{{ route('practitioner.programs', ['locale' => app()->getLocale()]) }}"
               class="flex items-center gap-1.5 px-3 py-1.5 rounded text-sm no-underline transition-colors {{ request()->routeIs('practitioner.programs*') ? 'bg-slate-700 text-white' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                <i data-lucide="beaker" style="width:16px;height:16px"></i> Programs
            </a>
            @endif
            @if(Route::has('practitioner.applications'))
            <a href="{{ route('practitioner.applications', ['locale' => app()->getLocale()]) }}"
               class="flex items-center gap-1.5 px-3 py-1.5 rounded text-sm no-underline transition-colors {{ request()->routeIs('practitioner.applications*') ? 'bg-slate-700 text-white' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                <i data-lucide="clipboard-list" style="width:16px;height:16px"></i> My Applications
            </a>
            @endif
            @if(Route::has('practitioner.surveys'))
            <a href="{{ route('practitioner.surveys', ['locale' => app()->getLocale()]) }}"
               class="flex items-center gap-1.5 px-3 py-1.5 rounded text-sm no-underline transition-colors {{ request()->routeIs('practitioner.surveys*') ? 'bg-slate-700 text-white' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                <i data-lucide="bar-chart-2" style="width:16px;height:16px"></i> Surveys
            </a>
            @endif
            @if(Route::has('practitioner.suggestions'))
            <a href="{{ route('practitioner.suggestions', ['locale' => app()->getLocale()]) }}"
               class="flex items-center gap-1.5 px-3 py-1.5 rounded text-sm no-underline transition-colors {{ request()->routeIs('practitioner.suggestions*') ? 'bg-slate-700 text-white' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                <i data-lucide="lightbulb" style="width:16px;height:16px"></i> Suggestions
            </a>
            @endif
            @if(Route::has('practitioner.bug-reports'))
            <a href="{{ route('practitioner.bug-reports', ['locale' => app()->getLocale()]) }}"
               class="flex items-center gap-1.5 px-3 py-1.5 rounded text-sm no-underline transition-colors {{ request()->routeIs('practitioner.bug-reports*') ? 'bg-slate-700 text-white' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                <i data-lucide="bug" style="width:16px;height:16px"></i> Bug Reports
            </a>
            @endif
            @if(Route::has('practitioner.courses'))
            <a href="{{ route('practitioner.courses', ['locale' => app()->getLocale()]) }}"
               class="flex items-center gap-1.5 px-3 py-1.5 rounded text-sm no-underline transition-colors {{ request()->routeIs('practitioner.courses*') || request()->routeIs('practitioner.lessons*') ? 'bg-slate-700 text-white' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                <i data-lucide="graduation-cap" style="width:16px;height:16px"></i> Courses
            </a>
            @endif
            @if(Route::has('practitioner.certificates'))
            <a href="{{ route('practitioner.certificates', ['locale' => app()->getLocale()]) }}"
               class="flex items-center gap-1.5 px-3 py-1.5 rounded text-sm no-underline transition-colors {{ request()->routeIs('practitioner.certificates*') ? 'bg-slate-700 text-white' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                <i data-lucide="award" style="width:16px;height:16px"></i> Certificates
            </a>
            @endif
        </div>
        <div class="flex items-center gap-3 portal-actions">
            <span class="text-sm text-slate-400">{{ auth()->user()->name }}</span>
            <a href="{{ route('practitioner.profile', ['locale' => app()->getLocale()]) }}"
               class="flex items-center justify-center w-8 h-8 rounded transition-colors {{ request()->routeIs('practitioner.profile') ? 'bg-slate-700 text-white' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                <i data-lucide="user" style="width:16px;height:16px"></i>
            </a>
            <form method="POST" action="{{ route('logout') }}" style="margin:0">
                @csrf
                <button type="submit"
                    class="flex items-center justify-center w-8 h-8 rounded text-slate-400 hover:text-white hover:bg-slate-800 transition-colors bg-transparent border-0 cursor-pointer"
                    title="Sign out">
                    <i data-lucide="log-out" style="width:16px;height:16px"></i>
                </button>
            </form>
        </div>
    </nav>

    @if (session('success'))
        <div class="bg-emerald-900 border border-emerald-700 text-emerald-200 text-sm px-6 py-3">
            {{ session('success') }}
        </div>
    @endif

    <main class="px-6 py-8">
        <div class="max-w-6xl mx-auto">
            {{ $slot }}
        </div>
    </main>

    <script src="{{ asset('vendor/lucide.min.js') }}"></script>
    <script>lucide.createIcons();</script>
</body>
</html>

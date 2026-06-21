<x-layouts.practitioner title="Practitioner Dashboard">
@php
    $locale = app()->getLocale();
    $tier   = $user->practitionerTier();
@endphp

{{-- Header --}}
<div class="flex items-start justify-between mb-6 flex-wrap gap-3">
    <div>
        <h1 class="text-2xl font-bold text-white mb-0.5">Welcome back, {{ $user->name }}</h1>
        <p class="text-slate-400 text-sm">
            @if($profile)
                {{ $profile->profession ?? 'Practitioner' }}
                @if($profile->workplace_name) · {{ $profile->workplace_name }} @endif
            @else
                OPES Practitioner Account
            @endif
        </p>
        <div class="mt-2 flex items-center gap-2 flex-wrap">
            <span class="text-xs font-semibold px-2.5 py-1 rounded-full {{ $tier->tailwindBadge() }}">{{ $tier->label() }}</span>
            @if($tier->next())
                <span class="text-xs text-slate-500">{{ $tier->description() }}</span>
            @endif
        </div>
    </div>
    <a href="{{ route('practitioner.profile', ['locale' => $locale]) }}"
       class="flex items-center gap-2 px-4 py-2 rounded border border-slate-600 text-slate-300 text-sm hover:border-slate-400 hover:text-white transition-colors no-underline">
        <i data-lucide="settings" style="width:15px;height:15px"></i> Edit Profile
    </a>
</div>

@if(!$profile)
<div class="bg-amber-900/30 border border-amber-700 rounded-lg px-5 py-4 mb-6 flex items-start gap-3">
    <i data-lucide="alert-triangle" style="width:20px;height:20px;color:#f59e0b;flex-shrink:0;margin-top:2px"></i>
    <div>
        <p class="text-amber-300 font-medium text-sm mb-0.5">Complete your profile to unlock paid programs</p>
        <p class="text-amber-400/80 text-sm">Fill in your professional details so we can match you with the right programs.</p>
        <a href="{{ route('practitioner.profile', ['locale' => $locale]) }}"
           class="inline-block mt-2 text-sm text-amber-300 underline hover:text-amber-100">Go to Profile →</a>
    </div>
</div>
@endif

{{-- KPI Row --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
    <div class="bg-slate-900 rounded-xl border border-slate-800 p-5 flex items-center gap-4">
        <div class="w-11 h-11 rounded-lg flex items-center justify-center flex-shrink-0" style="background:rgba(0,200,150,0.1)">
            <i data-lucide="clipboard-list" style="width:22px;height:22px;color:#00C896"></i>
        </div>
        <div>
            <p class="text-2xl font-bold text-white">{{ $activeApplications }}</p>
            <p class="text-xs text-slate-400">Active Applications</p>
        </div>
    </div>
    <div class="bg-slate-900 rounded-xl border border-slate-800 p-5 flex items-center gap-4">
        <div class="w-11 h-11 rounded-lg flex items-center justify-center flex-shrink-0" style="background:rgba(26,111,232,0.1)">
            <i data-lucide="file-search" style="width:22px;height:22px;color:#1A6FE8"></i>
        </div>
        <div>
            <p class="text-2xl font-bold text-white">{{ $totalFindings }}</p>
            <p class="text-xs text-slate-400">Findings Submitted</p>
        </div>
    </div>
    <div class="bg-slate-900 rounded-xl border border-slate-800 p-5 flex items-center gap-4">
        <div class="w-11 h-11 rounded-lg flex items-center justify-center flex-shrink-0" style="background:rgba(245,158,11,0.1)">
            <i data-lucide="star" style="width:22px;height:22px;color:#F59E0B"></i>
        </div>
        <div>
            <p class="text-2xl font-bold text-white">{{ $avgRating ? number_format($avgRating, 1) : '—' }}</p>
            <p class="text-xs text-slate-400">Avg Finding Rating</p>
        </div>
    </div>
    <div class="bg-slate-900 rounded-xl border border-slate-800 p-5 flex items-center gap-4">
        <div class="w-11 h-11 rounded-lg flex items-center justify-center flex-shrink-0" style="background:rgba(139,92,246,0.1)">
            <i data-lucide="award" style="width:22px;height:22px;color:#8B5CF6"></i>
        </div>
        <div>
            <p class="text-xl font-bold text-white">{{ $tier->label() }}</p>
            <p class="text-xs text-slate-400">Current Tier</p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    {{-- Payout Tracker --}}
    <div class="bg-slate-900 border border-slate-800 rounded-xl p-5">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-semibold text-white flex items-center gap-2 text-sm">
                <i data-lucide="banknote" style="width:16px;height:16px;color:#00C896"></i> Payout Tracker
            </h2>
            <a href="{{ route('practitioner.applications', ['locale' => $locale]) }}" class="text-xs text-slate-400 hover:text-white no-underline">View All →</a>
        </div>
        @if($payoutApps->isNotEmpty())
        <div style="overflow-x:auto">
        <table style="width:100%;border-collapse:collapse;font-size:.8125rem">
            <thead>
                <tr>
                    <th style="text-align:left;color:var(--text-muted);font-size:.6875rem;text-transform:uppercase;letter-spacing:.05em;padding:.375rem .5rem;border-bottom:1px solid #1e293b">Program</th>
                    <th style="text-align:left;color:var(--text-muted);font-size:.6875rem;text-transform:uppercase;letter-spacing:.05em;padding:.375rem .5rem;border-bottom:1px solid #1e293b">Status</th>
                    <th style="text-align:left;color:var(--text-muted);font-size:.6875rem;text-transform:uppercase;letter-spacing:.05em;padding:.375rem .5rem;border-bottom:1px solid #1e293b">Payout</th>
                </tr>
            </thead>
            <tbody>
                @foreach($payoutApps as $app)
                <tr>
                    <td style="padding:.5rem;color:#cbd5e1;border-bottom:1px solid #0f172a">{{ $app->program->title ?? 'Program' }}</td>
                    <td style="padding:.5rem;border-bottom:1px solid #0f172a">
                        @php $sc = match($app->status) { 'approved' => '#00C896', 'rejected' => '#ef4444', default => '#F59E0B' }; @endphp
                        <span style="color:{{ $sc }};font-size:.6875rem;font-weight:700;text-transform:uppercase">{{ ucfirst($app->status) }}</span>
                    </td>
                    <td style="padding:.5rem;border-bottom:1px solid #0f172a">
                        @if($app->payout_status)
                            @php $pc = match($app->payout_status) { 'paid' => '#00C896', 'initiated' => '#1A6FE8', default => 'var(--text-muted)' }; @endphp
                            <span style="color:{{ $pc }};font-size:.6875rem;font-weight:700;text-transform:uppercase">{{ ucfirst($app->payout_status) }}</span>
                        @else
                            <span style="color:#334155;font-size:.75rem">N/A</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        </div>
        @else
        <div class="flex flex-col items-center gap-2 py-6 text-slate-500 text-sm text-center">
            <i data-lucide="inbox" style="width:28px;height:28px;color:#334155"></i>
            <p>No applications yet. <a href="{{ route('practitioner.programs', ['locale' => $locale]) }}" class="text-emerald-400 hover:underline">Browse programs →</a></p>
        </div>
        @endif
    </div>

    {{-- Recent Findings --}}
    <div class="bg-slate-900 border border-slate-800 rounded-xl p-5">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-semibold text-white flex items-center gap-2 text-sm">
                <i data-lucide="file-search" style="width:16px;height:16px;color:#1A6FE8"></i> Recent Findings
            </h2>
        </div>
        @if($recentFindings->isNotEmpty())
        <div class="flex flex-col gap-3">
            @foreach($recentFindings as $finding)
            <div class="rounded-lg p-3.5 border border-slate-800" style="background:#0F172A">
                <div class="flex items-center justify-between gap-2 mb-1">
                    <p class="text-sm text-slate-300 font-medium">{{ $finding->application->program->product_name ?? 'Program' }}</p>
                    <span style="color:#F59E0B;font-size:.875rem">
                        @php $r = (int)($finding->overall ?? 0); @endphp
                        @for($i = 1; $i <= 5; $i++){{ $i <= $r ? '★' : '☆' }}@endfor
                    </span>
                </div>
                <p class="text-xs text-slate-500 line-clamp-2">{{ Str::limit($finding->findings_text, 100) }}</p>
                <p class="text-xs text-slate-600 mt-1">{{ $finding->created_at?->diffForHumans() }}</p>
            </div>
            @endforeach
        </div>
        @else
        <div class="flex flex-col items-center gap-2 py-6 text-slate-500 text-sm text-center">
            <i data-lucide="file-question" style="width:28px;height:28px;color:#334155"></i>
            <p>No findings submitted yet</p>
        </div>
        @endif
    </div>
</div>

{{-- Open Programs --}}
@if($openPrograms->isNotEmpty())
<div class="bg-slate-900 border border-slate-800 rounded-xl p-5 mb-6">
    <div class="flex items-center justify-between mb-4">
        <h2 class="font-semibold text-white flex items-center gap-2 text-sm">
            <i data-lucide="beaker" style="width:16px;height:16px;color:#00C896"></i> Open Programs
        </h2>
        <a href="{{ route('practitioner.programs', ['locale' => $locale]) }}" class="text-xs text-slate-400 hover:text-white no-underline">View All →</a>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
        @foreach($openPrograms as $prog)
        <div class="rounded-lg p-4 border border-slate-800 flex flex-col gap-2" style="background:#0F172A">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-emerald-400 uppercase tracking-wide">{{ ucfirst($prog->type) }}</span>
                @if($prog->compensation)
                    <span class="text-xs text-slate-400">{{ number_format($prog->compensation) }} XAF</span>
                @endif
            </div>
            <p class="text-sm font-medium text-slate-200 leading-snug">{{ $prog->product_name }}</p>
            @if($prog->end_date)
                <p class="text-xs text-slate-500">Closes {{ $prog->end_date->format('M j') }}</p>
            @endif
            <a href="{{ route('practitioner.programs.show', ['locale' => $locale, 'program' => $prog->id]) }}"
               class="mt-auto inline-flex items-center gap-1 text-xs text-emerald-400 hover:underline no-underline">
                Apply now <i data-lucide="arrow-right" style="width:13px;height:13px"></i>
            </a>
        </div>
        @endforeach
    </div>
</div>
@endif

{{-- Help --}}
<div class="bg-slate-900 border border-slate-800 rounded-xl p-5 flex items-start gap-4">
    <i data-lucide="life-buoy" style="width:22px;height:22px;color:#00C896;flex-shrink:0;margin-top:2px"></i>
    <div>
        <p class="font-semibold text-white mb-1 text-sm">Need help?</p>
        <p class="text-sm text-slate-400">
            Mon–Fri 8 am – 6 pm (WAT) · Email
            <a href="mailto:support@opeshealthsystems.com" class="text-emerald-400 hover:underline">support@opeshealthsystems.com</a>
            or visit our <a href="{{ route('contact', ['locale' => $locale]) }}" class="text-emerald-400 hover:underline">contact page</a>.
        </p>
    </div>
</div>
</x-layouts.practitioner>

<x-layouts.practitioner title="Validation Hub">
@php $locale = app()->getLocale(); @endphp

<div class="flex items-start justify-between mb-6 flex-wrap gap-3">
    <div>
        <h1 class="text-2xl font-bold text-white mb-0.5">Validation Hub</h1>
        <p class="text-slate-400 text-sm">Clinical Validation &amp; Innovation Program</p>
    </div>
</div>

@if($cohortMember === null)
    {{-- No active cohort --}}
    <div class="max-w-xl mx-auto bg-slate-900 border border-slate-800 rounded-xl p-8 text-center">
        <div class="w-14 h-14 rounded-full flex items-center justify-center mx-auto mb-4" style="background:rgba(0,200,150,0.1)">
            <i data-lucide="clipboard-check" style="width:28px;height:28px;color:#00C896"></i>
        </div>
        <h2 class="text-lg font-semibold text-white mb-2">You have not been placed in a validation cohort yet</h2>
        <p class="text-sm text-slate-400">
            Once your practitioner application is approved, an administrator will place you in a
            validation cohort. You'll then be able to log daily test sessions and report issues here.
        </p>
    </div>
@else
    {{-- Cohort header --}}
    <div class="bg-slate-900 border border-slate-800 rounded-xl p-5 mb-6">
        <div class="flex items-start justify-between flex-wrap gap-3">
            <div>
                <h2 class="text-lg font-semibold text-white mb-1">{{ $cohortMember->cohort->name }}</h2>
                <div class="flex items-center gap-2 flex-wrap text-sm text-slate-400">
                    @if($cohortMember->cohort->specialty)
                        <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-slate-800 text-emerald-400">{{ $cohortMember->cohort->specialty }}</span>
                    @endif
                    <span>
                        {{ $cohortMember->cohort->start_date?->format('M j, Y') ?? '—' }}
                        @if($cohortMember->cohort->end_date)
                            – {{ $cohortMember->cohort->end_date->format('M j, Y') }}
                        @endif
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- Stat cards --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
        <div class="bg-slate-900 rounded-xl border border-slate-800 p-5 flex items-center gap-4">
            <div class="w-11 h-11 rounded-lg flex items-center justify-center flex-shrink-0" style="background:rgba(0,200,150,0.1)">
                <i data-lucide="flask-conical" style="width:22px;height:22px;color:#00C896"></i>
            </div>
            <div>
                <p class="text-2xl font-bold text-white">{{ $stats['sessions'] }}</p>
                <p class="text-xs text-slate-400">Sessions</p>
            </div>
        </div>
        <div class="bg-slate-900 rounded-xl border border-slate-800 p-5 flex items-center gap-4">
            <div class="w-11 h-11 rounded-lg flex items-center justify-center flex-shrink-0" style="background:rgba(26,111,232,0.1)">
                <i data-lucide="file-warning" style="width:22px;height:22px;color:#1A6FE8"></i>
            </div>
            <div>
                <p class="text-2xl font-bold text-white">{{ $stats['issues'] }}</p>
                <p class="text-xs text-slate-400">Issues</p>
            </div>
        </div>
        <div class="bg-slate-900 rounded-xl border border-slate-800 p-5 flex items-center gap-4">
            <div class="w-11 h-11 rounded-lg flex items-center justify-center flex-shrink-0" style="background:rgba(245,158,11,0.1)">
                <i data-lucide="circle-dot" style="width:22px;height:22px;color:#F59E0B"></i>
            </div>
            <div>
                <p class="text-2xl font-bold text-white">{{ $stats['open'] }}</p>
                <p class="text-xs text-slate-400">Open</p>
            </div>
        </div>
        <div class="bg-slate-900 rounded-xl border border-slate-800 p-5 flex items-center gap-4">
            <div class="w-11 h-11 rounded-lg flex items-center justify-center flex-shrink-0" style="background:rgba(139,92,246,0.1)">
                <i data-lucide="check-circle" style="width:22px;height:22px;color:#8B5CF6"></i>
            </div>
            <div>
                <p class="text-2xl font-bold text-white">{{ $stats['closed'] }}</p>
                <p class="text-xs text-slate-400">Closed</p>
            </div>
        </div>
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-4">
            <div class="text-2xl font-bold text-amber-400">{{ $cohortMember->issueReports()->where('status', 'ready_for_retest')->count() }}</div>
            <div class="text-xs text-slate-400 mt-1">Awaiting your retest</div>
        </div>
    </div>

    {{-- Actions --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <a href="{{ route('practitioner.validation.sessions.index', ['locale' => $locale]) }}"
           class="bg-slate-900 border border-slate-800 rounded-xl p-5 flex items-center justify-between no-underline hover:border-slate-600 transition-colors">
            <div class="flex items-center gap-3">
                <i data-lucide="flask-conical" style="width:20px;height:20px;color:#00C896"></i>
                <span class="text-sm font-medium text-white">Daily Sessions</span>
            </div>
            <i data-lucide="arrow-right" style="width:16px;height:16px;color:#64748b"></i>
        </a>
        <a href="{{ route('practitioner.validation.issues.index', ['locale' => $locale]) }}"
           class="bg-slate-900 border border-slate-800 rounded-xl p-5 flex items-center justify-between no-underline hover:border-slate-600 transition-colors">
            <div class="flex items-center gap-3">
                <i data-lucide="file-warning" style="width:20px;height:20px;color:#1A6FE8"></i>
                <span class="text-sm font-medium text-white">Issue Reports</span>
            </div>
            <i data-lucide="arrow-right" style="width:16px;height:16px;color:#64748b"></i>
        </a>
    </div>
@endif
</x-layouts.practitioner>

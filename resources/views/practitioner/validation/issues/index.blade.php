<x-layouts.practitioner title="Issue Reports">
@php
    $locale = app()->getLocale();
    $severityStyles = [
        'critical' => 'bg-red-500/10 text-red-400 border-red-500/30',
        'high'     => 'bg-orange-500/10 text-orange-400 border-orange-500/30',
        'medium'   => 'bg-yellow-500/10 text-yellow-400 border-yellow-500/30',
        'low'      => 'bg-sky-500/10 text-sky-400 border-sky-500/30',
    ];
    $statusLabels = \App\Models\IssueReport::statusOptions();
@endphp

<div class="flex items-start justify-between mb-6 flex-wrap gap-3">
    <div>
        <h1 class="text-2xl font-bold text-white mb-0.5">Issue Reports</h1>
        <p class="text-slate-400 text-sm">Formal issues you've raised for {{ $member->cohort->name }}</p>
    </div>
    <a href="{{ route('practitioner.validation.issues.create', ['locale' => $locale]) }}"
       class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition no-underline">
        + Report New Issue
    </a>
</div>

@if(session('success'))
    <div class="mb-4 bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 px-4 py-3 rounded-lg text-sm">
        {{ session('success') }}
    </div>
@endif

@if($issues->isEmpty())
    <div class="bg-slate-900 border border-slate-800 rounded-xl p-10 text-center">
        <div class="w-14 h-14 rounded-full flex items-center justify-center mx-auto mb-4" style="background:rgba(0,200,150,0.1)">
            <i data-lucide="bug" style="width:28px;height:28px;color:#00C896"></i>
        </div>
        <h2 class="text-lg font-semibold text-white mb-2">No issues reported yet</h2>
        <p class="text-sm text-slate-400">Found a problem? Report a new issue so the team can triage it.</p>
    </div>
@else
    <div class="bg-slate-900 border border-slate-800 rounded-xl overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-slate-400 border-b border-slate-800">
                    <th class="px-5 py-3 font-medium">Title</th>
                    <th class="px-5 py-3 font-medium">Severity</th>
                    <th class="px-5 py-3 font-medium">Status</th>
                    <th class="px-5 py-3 font-medium">Reported</th>
                </tr>
            </thead>
            <tbody>
                @foreach($issues as $issue)
                    <tr class="border-b border-slate-800/60 last:border-0 text-slate-300">
                        <td class="px-5 py-3">
                            <a href="{{ route('practitioner.validation.issues.show', ['locale' => $locale, 'issue' => $issue->id]) }}"
                               class="text-emerald-400 hover:underline">{{ $issue->title }}</a>
                        </td>
                        <td class="px-5 py-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium border {{ $severityStyles[$issue->severity] ?? 'bg-slate-700/40 text-slate-300 border-slate-600' }}">
                                {{ ucfirst($issue->severity) }}
                            </span>
                        </td>
                        <td class="px-5 py-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-slate-700/40 text-slate-300 border border-slate-600">
                                {{ $statusLabels[$issue->status] ?? $issue->status }}
                            </span>
                        </td>
                        <td class="px-5 py-3 whitespace-nowrap">{{ $issue->created_at?->format('M j, Y') ?? '—' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $issues->links() }}
    </div>
@endif
</x-layouts.practitioner>

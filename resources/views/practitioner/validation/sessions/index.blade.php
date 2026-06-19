<x-layouts.practitioner title="Daily Sessions">
@php $locale = app()->getLocale(); @endphp

<div class="flex items-start justify-between mb-6 flex-wrap gap-3">
    <div>
        <h1 class="text-2xl font-bold text-white mb-0.5">Daily Sessions</h1>
        <p class="text-slate-400 text-sm">Log your daily testing activity for {{ $member->cohort->name }}</p>
    </div>
    <a href="{{ route('practitioner.validation.sessions.create', ['locale' => $locale]) }}"
       class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition no-underline">
        + Start New Session
    </a>
</div>

@if(session('success'))
    <div class="mb-4 bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 px-4 py-3 rounded-lg text-sm">
        {{ session('success') }}
    </div>
@endif

@if($sessions->isEmpty())
    <div class="bg-slate-900 border border-slate-800 rounded-xl p-10 text-center">
        <div class="w-14 h-14 rounded-full flex items-center justify-center mx-auto mb-4" style="background:rgba(0,200,150,0.1)">
            <i data-lucide="flask-conical" style="width:28px;height:28px;color:#00C896"></i>
        </div>
        <h2 class="text-lg font-semibold text-white mb-2">No sessions logged yet</h2>
        <p class="text-sm text-slate-400">Start a new session to record the testing you've completed today.</p>
    </div>
@else
    <div class="bg-slate-900 border border-slate-800 rounded-xl overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-slate-400 border-b border-slate-800">
                    <th class="px-5 py-3 font-medium">Date</th>
                    <th class="px-5 py-3 font-medium">Product</th>
                    <th class="px-5 py-3 font-medium">Module</th>
                    <th class="px-5 py-3 font-medium">Workflow</th>
                    <th class="px-5 py-3 font-medium text-center">Tasks</th>
                    <th class="px-5 py-3 font-medium text-center">Issues</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sessions as $session)
                    <tr class="border-b border-slate-800/60 last:border-0 text-slate-300">
                        <td class="px-5 py-3 whitespace-nowrap">{{ $session->date?->format('M j, Y') ?? '—' }}</td>
                        <td class="px-5 py-3">{{ $session->product?->name ?? '—' }}</td>
                        <td class="px-5 py-3">{{ $session->module?->name ?? '—' }}</td>
                        <td class="px-5 py-3">{{ $session->workflow?->name ?? '—' }}</td>
                        <td class="px-5 py-3 text-center">{{ $session->tasks_completed }}</td>
                        <td class="px-5 py-3 text-center">{{ $session->issueReports()->count() }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $sessions->links() }}
    </div>
@endif
</x-layouts.practitioner>

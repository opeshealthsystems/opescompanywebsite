<x-layouts.practitioner title="My Bug Reports">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">My Bug Reports</h1>
            <p class="text-slate-500 mt-1">Report bugs you find while testing OPES products.</p>
        </div>
        <a href="{{ route('practitioner.bug-reports.create', ['locale' => app()->getLocale()]) }}"
           class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition">
            + Report a Bug
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">{{ session('success') }}</div>
    @endif

    @if($bugReports->isEmpty())
        <div class="bg-white rounded-xl border border-slate-200 p-10 text-center text-slate-400">
            You haven't reported any bugs yet. Help us improve by reporting what you find!
        </div>
    @else
        @php
            $severityColors = [
                'low'      => 'bg-slate-100 text-slate-600',
                'medium'   => 'bg-blue-100 text-blue-700',
                'high'     => 'bg-amber-100 text-amber-700',
                'critical' => 'bg-red-100 text-red-700',
            ];
            $statusColors = [
                'open'        => 'bg-amber-100 text-amber-700',
                'triaged'     => 'bg-blue-100 text-blue-700',
                'in_progress' => 'bg-indigo-100 text-indigo-700',
                'resolved'    => 'bg-green-100 text-green-700',
                'closed'      => 'bg-slate-100 text-slate-600',
                'wont_fix'    => 'bg-red-100 text-red-700',
            ];
        @endphp
        <div class="space-y-4">
            @foreach($bugReports as $bugReport)
                <a href="{{ route('practitioner.bug-reports.show', ['locale' => app()->getLocale(), 'bugReport' => $bugReport->id]) }}"
                   class="block bg-white rounded-xl border border-slate-200 p-5 hover:border-emerald-300 transition no-underline">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <h3 class="font-semibold text-slate-800">{{ $bugReport->title }}</h3>
                            <p class="text-sm text-slate-500 mt-1">{{ \Illuminate\Support\Str::limit($bugReport->description, 120) }}</p>
                        </div>
                        <div class="ml-4 shrink-0 flex flex-col items-end gap-2">
                            <span class="px-3 py-1 rounded-full text-xs font-medium {{ $severityColors[$bugReport->severity] ?? 'bg-slate-100 text-slate-600' }}">
                                {{ \App\Models\PractitionerBugReport::severityOptions()[$bugReport->severity] ?? $bugReport->severity }}
                            </span>
                            <span class="px-3 py-1 rounded-full text-xs font-medium {{ $statusColors[$bugReport->status] ?? 'bg-slate-100 text-slate-600' }}">
                                {{ \App\Models\PractitionerBugReport::statusOptions()[$bugReport->status] ?? $bugReport->status }}
                            </span>
                            <span class="text-xs text-slate-400">{{ $bugReport->created_at->format('d M Y') }}</span>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</x-layouts.practitioner>

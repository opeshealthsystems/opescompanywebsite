<x-layouts.practitioner title="Bug Report">
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

    <div class="mb-6">
        <a href="{{ route('practitioner.bug-reports', ['locale' => app()->getLocale()]) }}"
           class="text-sm text-emerald-600 hover:underline">← Back to Bug Reports</a>
        <h1 class="text-2xl font-bold text-slate-900 mt-2">{{ $bugReport->title }}</h1>
        <div class="flex items-center gap-2 mt-2">
            <span class="px-3 py-1 rounded-full text-xs font-medium {{ $severityColors[$bugReport->severity] ?? 'bg-slate-100 text-slate-600' }}">
                {{ \App\Models\PractitionerBugReport::severityOptions()[$bugReport->severity] ?? $bugReport->severity }}
            </span>
            <span class="px-3 py-1 rounded-full text-xs font-medium {{ $statusColors[$bugReport->status] ?? 'bg-slate-100 text-slate-600' }}">
                {{ \App\Models\PractitionerBugReport::statusOptions()[$bugReport->status] ?? $bugReport->status }}
            </span>
            <span class="text-xs text-slate-400">{{ $bugReport->created_at->format('d M Y') }}</span>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 p-6 space-y-5">
        @if($bugReport->product_slug)
            <div>
                <p class="text-xs font-medium text-slate-500 uppercase tracking-wide mb-1">Product</p>
                <p class="text-sm text-slate-700">{{ $bugReport->product_slug }}</p>
            </div>
        @endif

        <div>
            <p class="text-xs font-medium text-slate-500 uppercase tracking-wide mb-1">Description</p>
            <p class="text-sm text-slate-700 whitespace-pre-line">{{ $bugReport->description }}</p>
        </div>

        @if($bugReport->steps_to_reproduce)
            <div>
                <p class="text-xs font-medium text-slate-500 uppercase tracking-wide mb-1">Steps to Reproduce</p>
                <p class="text-sm text-slate-700 whitespace-pre-line">{{ $bugReport->steps_to_reproduce }}</p>
            </div>
        @endif

        @if($bugReport->screenshot_url)
            <div>
                <p class="text-xs font-medium text-slate-500 uppercase tracking-wide mb-1">Screenshot</p>
                <a href="{{ $bugReport->screenshot_url }}" target="_blank" rel="noopener"
                   class="text-sm text-emerald-600 hover:underline break-all">{{ $bugReport->screenshot_url }}</a>
            </div>
        @endif

        @if($bugReport->admin_response)
            <div class="pt-4 border-t border-slate-100">
                <p class="text-xs font-medium text-slate-500 uppercase tracking-wide mb-1">OPES Response</p>
                <p class="text-sm text-slate-700 whitespace-pre-line">{{ $bugReport->admin_response }}</p>
            </div>
        @endif
    </div>
</x-layouts.practitioner>

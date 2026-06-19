<x-layouts.practitioner title="Issue Report">
@php
    $locale = app()->getLocale();
    $statusLabels = \App\Models\IssueReport::statusOptions();
    $severityStyles = [
        'critical' => 'bg-red-500/10 text-red-400 border-red-500/30',
        'high'     => 'bg-orange-500/10 text-orange-400 border-orange-500/30',
        'medium'   => 'bg-yellow-500/10 text-yellow-400 border-yellow-500/30',
        'low'      => 'bg-sky-500/10 text-sky-400 border-sky-500/30',
    ];
    $timeline = ['submitted' => 'Submitted', 'clinical_review' => 'Clinical Review', 'product_review' => 'Product Review', 'resolved' => 'Resolved / Closed'];
    $resolvedStatuses = ['accepted', 'rejected', 'duplicate', 'sent_to_development', 'fixed', 'closed'];
    $timelineKey = in_array($issue->status, $resolvedStatuses) ? 'resolved' : $issue->status;
    $timelineOrder = array_keys($timeline);
    $currentIndex = array_search($timelineKey, $timelineOrder);
    $currentIndex = $currentIndex === false ? 0 : $currentIndex;
@endphp

<div class="mb-6">
    <a href="{{ route('practitioner.validation.issues.index', ['locale' => $locale]) }}"
       class="text-sm text-emerald-400 hover:underline">&larr; Back to Issue Reports</a>
    <div class="flex items-start justify-between flex-wrap gap-3 mt-2">
        <div>
            <h1 class="text-2xl font-bold text-white">{{ $issue->title }}</h1>
            <p class="text-slate-400 text-sm mt-1">Reported {{ $issue->created_at?->format('M j, Y') ?? '—' }}</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium border {{ $severityStyles[$issue->severity] ?? 'bg-slate-700/40 text-slate-300 border-slate-600' }}">
                {{ ucfirst($issue->severity) }}
            </span>
            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-500/10 text-emerald-400 border border-emerald-500/30">
                {{ $statusLabels[$issue->status] ?? $issue->status }}
            </span>
        </div>
    </div>
</div>

{{-- Status timeline --}}
<div class="bg-slate-900 border border-slate-800 rounded-xl p-5 mb-6">
    <div class="flex items-center justify-between gap-2">
        @foreach($timeline as $key => $label)
            @php $reached = $loop->index <= $currentIndex; @endphp
            <div class="flex-1 flex flex-col items-center text-center">
                <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-semibold {{ $reached ? 'bg-emerald-600 text-white' : 'bg-slate-800 text-slate-500 border border-slate-700' }}">
                    {{ $loop->iteration }}
                </div>
                <span class="mt-2 text-xs {{ $reached ? 'text-emerald-400' : 'text-slate-500' }}">{{ $label }}</span>
            </div>
            @if(! $loop->last)
                <div class="flex-1 h-px {{ $loop->index < $currentIndex ? 'bg-emerald-600' : 'bg-slate-700' }}"></div>
            @endif
        @endforeach
    </div>
</div>

@if($latestNote)
    <div class="bg-slate-900 border border-emerald-500/30 rounded-xl p-5 mb-6">
        <h2 class="text-sm font-semibold text-emerald-400 mb-2">Latest review note</h2>
        <p class="text-sm text-slate-300 whitespace-pre-line">{{ $latestNote }}</p>
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    {{-- Classification --}}
    <div class="bg-slate-900 border border-slate-800 rounded-xl p-6">
        <h2 class="text-sm font-semibold text-white uppercase tracking-wide mb-4">Classification</h2>
        <dl class="space-y-3 text-sm">
            <div class="flex justify-between gap-4"><dt class="text-slate-400">Product</dt><dd class="text-slate-200 text-right">{{ $issue->product?->name ?? '—' }}</dd></div>
            <div class="flex justify-between gap-4"><dt class="text-slate-400">Module</dt><dd class="text-slate-200 text-right">{{ $issue->module?->name ?? '—' }}</dd></div>
            <div class="flex justify-between gap-4"><dt class="text-slate-400">Workflow</dt><dd class="text-slate-200 text-right">{{ $issue->workflow?->name ?? '—' }}</dd></div>
            <div class="flex justify-between gap-4"><dt class="text-slate-400">Test Case</dt><dd class="text-slate-200 text-right">{{ $issue->testCase?->title ?? $issue->testCase?->name ?? '—' }}</dd></div>
            <div class="flex justify-between gap-4"><dt class="text-slate-400">Issue Type</dt><dd class="text-slate-200 text-right">{{ \App\Models\IssueReport::issueTypeOptions()[$issue->issue_type] ?? $issue->issue_type }}</dd></div>
            <div class="flex justify-between gap-4"><dt class="text-slate-400">Severity</dt><dd class="text-slate-200 text-right">{{ ucfirst($issue->severity) }}</dd></div>
        </dl>
    </div>

    {{-- Details --}}
    <div class="bg-slate-900 border border-slate-800 rounded-xl p-6 space-y-4">
        <h2 class="text-sm font-semibold text-white uppercase tracking-wide">Details</h2>
        <div>
            <h3 class="text-xs font-medium text-slate-400 uppercase tracking-wide mb-1">Description</h3>
            <p class="text-sm text-slate-300 whitespace-pre-line">{{ $issue->description }}</p>
        </div>
        <div>
            <h3 class="text-xs font-medium text-slate-400 uppercase tracking-wide mb-1">Steps to Reproduce</h3>
            <p class="text-sm text-slate-300 whitespace-pre-line">{{ $issue->steps_to_reproduce }}</p>
        </div>
        <div>
            <h3 class="text-xs font-medium text-slate-400 uppercase tracking-wide mb-1">Expected Result</h3>
            <p class="text-sm text-slate-300 whitespace-pre-line">{{ $issue->expected_result }}</p>
        </div>
        <div>
            <h3 class="text-xs font-medium text-slate-400 uppercase tracking-wide mb-1">Actual Result</h3>
            <p class="text-sm text-slate-300 whitespace-pre-line">{{ $issue->actual_result }}</p>
        </div>
        <div>
            <h3 class="text-xs font-medium text-slate-400 uppercase tracking-wide mb-1">Clinical Impact</h3>
            <p class="text-sm text-slate-300 whitespace-pre-line">{{ $issue->clinical_impact }}</p>
        </div>
        @if($issue->recommendation)
            <div>
                <h3 class="text-xs font-medium text-slate-400 uppercase tracking-wide mb-1">Recommendation</h3>
                <p class="text-sm text-slate-300 whitespace-pre-line">{{ $issue->recommendation }}</p>
            </div>
        @endif
    </div>
</div>

@if(! empty($issue->attachments))
    <div class="bg-slate-900 border border-slate-800 rounded-xl p-6 mt-6">
        <h2 class="text-sm font-semibold text-white uppercase tracking-wide mb-3">Attachments</h2>
        <ul class="space-y-2">
            @foreach($issue->attachments as $path)
                <li>
                    <a href="{{ asset('storage/'.$path) }}" target="_blank" rel="noopener"
                       class="text-sm text-emerald-400 hover:underline inline-flex items-center gap-1">
                        <i data-lucide="paperclip" style="width:14px;height:14px"></i>
                        {{ basename($path) }}
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
@endif
</x-layouts.practitioner>

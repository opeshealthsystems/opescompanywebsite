<x-layouts.practitioner title="Application Details">
    <div class="mb-6">
        <a href="{{ route('practitioner.applications', ['locale' => app()->getLocale()]) }}"
           class="text-sm text-slate-400 hover:text-white no-underline transition-colors flex items-center gap-1 w-fit">
            <i data-lucide="arrow-left" style="width:14px;height:14px"></i> Back to Applications
        </a>
    </div>

    @php
        $statusColors = [
            'pending'  => 'bg-amber-900 text-amber-300',
            'approved' => 'bg-emerald-900 text-emerald-300',
            'rejected' => 'bg-red-900 text-red-300',
        ];
    @endphp

    <div class="bg-slate-900 border border-slate-800 rounded-xl p-6 mb-6">
        <div class="flex items-start justify-between gap-4 mb-4">
            <div>
                <h1 class="text-xl font-bold text-white mb-2">{{ $application->program->title ?? 'Application' }}</h1>
                <div class="flex items-center gap-3">
                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full {{ $statusColors[$application->status] ?? 'bg-slate-700 text-slate-300' }} capitalize">
                        {{ $application->status }}
                    </span>
                    <span class="text-xs text-slate-500">Submitted {{ $application->created_at->format('M j, Y') }}</span>
                </div>
            </div>
            @if($application->status === 'approved')
                <a href="{{ route('practitioner.findings.create', ['locale' => app()->getLocale(), 'application' => $application->id]) }}"
                   class="flex items-center gap-2 px-4 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-semibold no-underline transition-colors shrink-0">
                    <i data-lucide="file-plus" style="width:15px;height:15px"></i> Submit Findings
                </a>
            @endif
        </div>

        @if($application->motivation)
        <div class="pt-4 border-t border-slate-800">
            <p class="text-xs text-slate-500 mb-2 font-semibold uppercase tracking-wide">Your Motivation</p>
            <p class="text-slate-300 text-sm leading-relaxed whitespace-pre-line">{{ $application->motivation }}</p>
        </div>
        @endif

        @if($application->admin_notes)
        <div class="pt-4 border-t border-slate-800 mt-4">
            <p class="text-xs text-slate-500 mb-2 font-semibold uppercase tracking-wide">Admin Notes</p>
            <p class="text-slate-300 text-sm leading-relaxed">{{ $application->admin_notes }}</p>
        </div>
        @endif
    </div>

    @if(optional($application->program)->type === 'paid')
    <div class="bg-slate-900 border border-slate-800 rounded-xl p-6 mb-6">
        <div class="flex items-center gap-3 mb-4">
            <i data-lucide="banknote" style="width:20px;height:20px;color:#00C896"></i>
            <h2 class="text-white font-semibold text-base">Compensation</h2>
            @php
                $payoutColors = [
                    'paid'           => 'bg-emerald-900 text-emerald-300',
                    'pending'        => 'bg-amber-900 text-amber-300',
                    'not_applicable' => 'bg-slate-700 text-slate-400',
                ];
            @endphp
            <span class="text-xs font-semibold px-2.5 py-1 rounded-full ml-auto {{ $payoutColors[$application->payout_status] ?? 'bg-slate-700 text-slate-400' }}">
                {{ \App\Models\PractitionerApplication::payoutStatusOptions()[$application->payout_status] ?? $application->payout_status }}
            </span>
        </div>

        @if($application->program->compensation)
        <p class="text-slate-300 text-sm mb-3">{{ $application->program->compensation }}</p>
        @endif

        @if($application->payout_status === 'paid')
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 pt-3 border-t border-slate-800">
            <div>
                <p class="text-xs text-slate-500 mb-0.5">Amount Paid</p>
                <p class="text-white font-semibold text-sm">{{ number_format((float) $application->payout_amount, 2) }} {{ $application->payout_currency }}</p>
            </div>
            @if($application->paid_at)
            <div>
                <p class="text-xs text-slate-500 mb-0.5">Paid On</p>
                <p class="text-white font-semibold text-sm">{{ $application->paid_at->format('M j, Y') }}</p>
            </div>
            @endif
            @if($application->payout_reference)
            <div>
                <p class="text-xs text-slate-500 mb-0.5">Reference</p>
                <p class="text-white font-semibold text-sm">{{ $application->payout_reference }}</p>
            </div>
            @endif
        </div>
        @elseif($application->payout_status === 'pending')
        <p class="text-xs text-slate-500 pt-3 border-t border-slate-800">Your payout is pending. OPES will process it after your participation is complete.</p>
        @endif
    </div>
    @endif

    @if($application->findings->isNotEmpty())
    <div class="bg-slate-900 border border-slate-800 rounded-xl p-6">
        <div class="flex items-center gap-3 mb-4">
            <i data-lucide="bar-chart-2" style="width:20px;height:20px;color:#00C896"></i>
            <h2 class="text-white font-semibold text-base">Submitted Findings</h2>
            <span class="text-xs text-slate-500 ml-auto">{{ $application->findings->count() }} finding(s)</span>
        </div>

        <div class="divide-y divide-slate-800">
            @foreach($application->findings as $finding)
            <div class="py-4 @if(!$loop->first) first:pt-0 @endif">
                <div class="flex items-center justify-between gap-4 mb-3">
                    <p class="text-xs text-slate-500">Submitted {{ $finding->created_at->format('M j, Y') }}</p>
                    @if($finding->is_published)
                        <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-emerald-900 text-emerald-300">Published</span>
                    @else
                        <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-slate-700 text-slate-400">Under Review</span>
                    @endif
                </div>

                @if($finding->overall_rating || $finding->wait_time_rating || $finding->data_integrity_rating || $finding->usability_rating)
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-3">
                    @if($finding->overall_rating)
                    <div class="bg-slate-800 rounded-lg p-3 text-center">
                        <p class="text-lg font-bold text-white">{{ $finding->overall_rating }}<span class="text-slate-500 text-sm">/5</span></p>
                        <p class="text-xs text-slate-500 mt-0.5">Overall</p>
                    </div>
                    @endif
                    @if($finding->wait_time_rating)
                    <div class="bg-slate-800 rounded-lg p-3 text-center">
                        <p class="text-lg font-bold text-white">{{ $finding->wait_time_rating }}<span class="text-slate-500 text-sm">/5</span></p>
                        <p class="text-xs text-slate-500 mt-0.5">Wait Time</p>
                    </div>
                    @endif
                    @if($finding->data_integrity_rating)
                    <div class="bg-slate-800 rounded-lg p-3 text-center">
                        <p class="text-lg font-bold text-white">{{ $finding->data_integrity_rating }}<span class="text-slate-500 text-sm">/5</span></p>
                        <p class="text-xs text-slate-500 mt-0.5">Data Integrity</p>
                    </div>
                    @endif
                    @if($finding->usability_rating)
                    <div class="bg-slate-800 rounded-lg p-3 text-center">
                        <p class="text-lg font-bold text-white">{{ $finding->usability_rating }}<span class="text-slate-500 text-sm">/5</span></p>
                        <p class="text-xs text-slate-500 mt-0.5">Usability</p>
                    </div>
                    @endif
                </div>
                @endif

                @if($finding->findings_text)
                <p class="text-slate-300 text-sm leading-relaxed whitespace-pre-line">{{ $finding->findings_text }}</p>
                @endif

                @if($finding->video_url)
                <a href="{{ $finding->video_url }}" target="_blank" rel="noopener"
                   class="inline-flex items-center gap-1.5 text-sm text-emerald-400 hover:text-emerald-300 no-underline mt-2">
                    <i data-lucide="video" style="width:14px;height:14px"></i> Watch Video
                </a>
                @endif

                @if($finding->screenshot_path)
                <div class="mt-3">
                    <img src="{{ \Illuminate\Support\Facades\Storage::url($finding->screenshot_path) }}"
                         alt="Finding screenshot"
                         class="max-w-md w-full rounded-lg border border-slate-800">
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @elseif($application->status === 'approved')
    <div class="bg-slate-900 border border-slate-700 rounded-xl p-6 text-center">
        <i data-lucide="file-plus" style="width:32px;height:32px;color:#475569;margin:0 auto 12px"></i>
        <p class="text-slate-400 text-sm mb-4">You haven't submitted any findings for this application yet.</p>
        <a href="{{ route('practitioner.findings.create', ['locale' => app()->getLocale(), 'application' => $application->id]) }}"
           class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-semibold no-underline transition-colors">
            <i data-lucide="plus" style="width:15px;height:15px"></i> Submit Your Findings
        </a>
    </div>
    @endif
</x-layouts.practitioner>

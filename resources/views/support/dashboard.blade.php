<x-layouts.support title="Dashboard">
@php $locale = app()->getLocale(); @endphp

<div class="flex items-start justify-between mb-6 flex-wrap gap-3">
    <div>
        <h1 class="text-2xl font-bold text-white mb-0.5">Support Dashboard</h1>
        <p class="text-slate-400 text-sm">Welcome back, {{ auth()->user()->name }}</p>
    </div>
    <a href="{{ route('support.tickets', ['locale' => $locale]) }}"
       class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white"
       style="background:#F97316">
        <i data-lucide="inbox" style="width:15px;height:15px"></i> View Full Queue
    </a>
</div>

<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
    @php
    $kpis = [
        ['label'=>'My Open Tickets',  'value'=>$myOpenCount,      'icon'=>'inbox',         'color'=>'#F97316'],
        ['label'=>'Resolved Today',   'value'=>$myResolvedToday,  'icon'=>'check-circle',  'color'=>'#00C896'],
        ['label'=>'Unassigned',       'value'=>$unassignedCount,  'icon'=>'alert-circle',  'color'=>'#F59E0B'],
        ['label'=>'SLA Breached',     'value'=>$slaBreachedCount, 'icon'=>'clock',         'color'=>'#ef4444'],
    ];
    @endphp
    @foreach($kpis as $kpi)
    <div class="bg-slate-900 rounded-xl border border-slate-800 p-5 flex items-center gap-4">
        <div class="w-11 h-11 rounded-lg flex items-center justify-center flex-shrink-0"
             style="background:{{ $kpi['color'] }}1a">
            <i data-lucide="{{ $kpi['icon'] }}" style="width:22px;height:22px;color:{{ $kpi['color'] }}"></i>
        </div>
        <div>
            <p class="text-2xl font-bold text-white">{{ $kpi['value'] }}</p>
            <p class="text-xs text-slate-400">{{ $kpi['label'] }}</p>
        </div>
    </div>
    @endforeach
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-slate-900 border border-slate-800 rounded-xl p-5">
        <h2 class="font-semibold text-white text-sm mb-4 flex items-center gap-2">
            <i data-lucide="inbox" style="width:16px;height:16px;color:#F97316"></i> My Queue
        </h2>
        @forelse($myQueue as $ticket)
        @php $pc = match($ticket->priority) { 'urgent'=>'#ef4444','high'=>'#f97316','medium'=>'#F59E0B', default=>'#64748b' }; @endphp
        <div class="flex items-start justify-between gap-3 mb-3 pb-3 border-b border-slate-800 last:border-0 last:mb-0 last:pb-0">
            <div>
                <p class="text-sm text-slate-200 font-medium">{{ Str::limit($ticket->subject, 45) }}</p>
                <p class="text-xs text-slate-500">{{ $ticket->customer?->name ?? 'Unknown' }}</p>
            </div>
            <div class="flex items-center gap-2 flex-shrink-0">
                <span style="color:{{ $pc }};font-size:.6875rem;font-weight:700;text-transform:uppercase">{{ $ticket->priority }}</span>
                <a href="{{ route('support.tickets.show', ['locale'=>$locale,'ticket'=>$ticket->id]) }}"
                   class="text-xs text-orange-400 hover:underline no-underline">Open &rarr;</a>
            </div>
        </div>
        @empty
        <p class="text-slate-500 text-sm text-center py-4">Your queue is clear.</p>
        @endforelse
    </div>

    <div class="bg-slate-900 border border-slate-800 rounded-xl p-5">
        <h2 class="font-semibold text-white text-sm mb-4 flex items-center gap-2">
            <i data-lucide="alert-circle" style="width:16px;height:16px;color:#F59E0B"></i> Unassigned Tickets
        </h2>
        @forelse($unassigned as $ticket)
        @php $pc = match($ticket->priority) { 'urgent'=>'#ef4444','high'=>'#f97316','medium'=>'#F59E0B', default=>'#64748b' }; @endphp
        <div class="flex items-start justify-between gap-3 mb-3 pb-3 border-b border-slate-800 last:border-0 last:mb-0 last:pb-0">
            <div>
                <p class="text-sm text-slate-200 font-medium">{{ Str::limit($ticket->subject, 45) }}</p>
                <p class="text-xs text-slate-500">{{ $ticket->customer?->name ?? 'Unknown' }}</p>
            </div>
            <div class="flex items-center gap-2 flex-shrink-0">
                <span style="color:{{ $pc }};font-size:.6875rem;font-weight:700;text-transform:uppercase">{{ $ticket->priority }}</span>
                <form method="POST" action="{{ route('support.tickets.assign', ['locale'=>$locale,'ticket'=>$ticket->id]) }}" style="margin:0">
                    @csrf @method('PATCH')
                    <button type="submit" style="font-size:.75rem;color:#F97316;background:none;border:none;cursor:pointer;padding:0">
                        Claim &rarr;
                    </button>
                </form>
            </div>
        </div>
        @empty
        <p class="text-slate-500 text-sm text-center py-4">No unassigned tickets.</p>
        @endforelse
    </div>
</div>
</x-layouts.support>

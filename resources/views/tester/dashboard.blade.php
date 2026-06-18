<x-layouts.tester title="Dashboard">
@php $locale = app()->getLocale(); @endphp

<div class="flex items-start justify-between mb-6 flex-wrap gap-3">
    <div>
        <h1 class="text-2xl font-bold text-white mb-0.5">Welcome back, {{ $user->name }}</h1>
        <p class="text-slate-400 text-sm">OPES Tester Dashboard</p>
    </div>
</div>

{{-- KPI row --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
    @php
    $kpis = [
        ['label'=>'Total Assigned',   'value'=>$totalAssigned,   'icon'=>'clipboard-list',  'color'=>'#1A6FE8'],
        ['label'=>'Active',           'value'=>$activeCount,     'icon'=>'activity',        'color'=>'#F59E0B'],
        ['label'=>'Completed',        'value'=>$completedCount,  'icon'=>'check-circle',    'color'=>'#00C896'],
        ['label'=>'Bug Reports Filed','value'=>$bugReportsCount, 'icon'=>'bug',             'color'=>'#ef4444'],
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

@if($overdueCount > 0)
<div class="bg-red-900/20 border border-red-800 rounded-lg px-5 py-4 mb-6 flex items-center gap-3">
    <i data-lucide="alert-triangle" style="width:18px;height:18px;color:#ef4444;flex-shrink:0"></i>
    <p class="text-red-300 text-sm font-medium">You have {{ $overdueCount }} overdue assignment{{ $overdueCount > 1 ? 's' : '' }}. Please update their status.</p>
</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    {{-- Active assignments --}}
    <div class="bg-slate-900 border border-slate-800 rounded-xl p-5">
        <h2 class="font-semibold text-white text-sm mb-4 flex items-center gap-2">
            <i data-lucide="activity" style="width:16px;height:16px;color:#F59E0B"></i> Active Assignments
        </h2>
        @forelse($active as $a)
        @php $overdue = $a->isOverdue(); @endphp
        <div class="flex items-start justify-between gap-3 mb-3 pb-3 border-b border-slate-800 last:border-0 last:mb-0 last:pb-0">
            <div>
                <p class="text-sm text-slate-200 font-medium">{{ $a->title }}</p>
                <p class="text-xs text-slate-500">{{ $a->product_name }}</p>
                @if($a->due_date)
                <p class="text-xs mt-0.5 {{ $overdue ? 'text-red-400 font-semibold' : 'text-slate-500' }}">
                    Due {{ $a->due_date->format('d M Y') }}{{ $overdue ? ' — OVERDUE' : '' }}
                </p>
                @endif
            </div>
            <a href="{{ route('tester.assignments.show', ['locale'=>$locale,'id'=>$a->id]) }}"
               class="text-xs text-emerald-400 hover:underline no-underline flex-shrink-0">View →</a>
        </div>
        @empty
        <p class="text-slate-500 text-sm text-center py-4">No active assignments. Check back soon.</p>
        @endforelse
    </div>

    {{-- Recently completed --}}
    <div class="bg-slate-900 border border-slate-800 rounded-xl p-5">
        <h2 class="font-semibold text-white text-sm mb-4 flex items-center gap-2">
            <i data-lucide="check-circle" style="width:16px;height:16px;color:#00C896"></i> Recently Completed
        </h2>
        @forelse($completed as $a)
        <div class="flex items-center justify-between mb-3 pb-3 border-b border-slate-800 last:border-0 last:mb-0 last:pb-0">
            <div>
                <p class="text-sm text-slate-300">{{ $a->title }}</p>
                <p class="text-xs text-slate-500">{{ $a->product_name }}</p>
            </div>
            <span class="text-xs text-emerald-400 font-semibold">Completed</span>
        </div>
        @empty
        <p class="text-slate-500 text-sm text-center py-4">No completed assignments yet.</p>
        @endforelse
    </div>
</div>
</x-layouts.tester>

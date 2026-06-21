<x-layouts.accountant title="Payroll — {{ $run->reference }}">
@php $locale = app()->getLocale(); @endphp

<div class="cp-page-header">
    <div>
        <h1 class="cp-page-title">{{ $run->reference }}</h1>
        <p class="cp-page-subtitle">{{ $run->period_start?->format('M j') }} – {{ $run->period_end?->format('M j, Y') }}</p>
    </div>
    <a href="{{ route('accountant.payroll.index', ['locale' => $locale]) }}" class="cp-btn-outline">
        <i data-lucide="arrow-left" style="width:15px;height:15px"></i> Back
    </a>
</div>

{{-- Summary --}}
<div class="cp-stats-row" style="margin-bottom:1.5rem">
    <div class="cp-stat-card">
        <div class="cp-stat-icon cp-stat-icon-amber"><i data-lucide="users" style="width:22px;height:22px"></i></div>
        <div><p class="cp-stat-value">{{ $entries->total() }}</p><p class="cp-stat-label">Employees</p></div>
    </div>
    <div class="cp-stat-card">
        <div class="cp-stat-icon cp-stat-icon-blue"><i data-lucide="trending-up" style="width:22px;height:22px"></i></div>
        <div><p class="cp-stat-value">{{ number_format($run->total_gross ?? 0, 0) }}</p><p class="cp-stat-label">Gross Total</p></div>
    </div>
    <div class="cp-stat-card">
        <div class="cp-stat-icon cp-stat-icon-green"><i data-lucide="dollar-sign" style="width:22px;height:22px"></i></div>
        <div><p class="cp-stat-value">{{ number_format($run->total_net ?? 0, 0) }}</p><p class="cp-stat-label">Net Total</p></div>
    </div>
</div>

{{-- By Department --}}
@if($byDept->isNotEmpty())
<div class="cp-section-card" style="margin-bottom:1.5rem">
    <div class="cp-section-header">
        <h2 class="cp-section-title"><i data-lucide="building-2" style="width:17px;height:17px;color:#F59E0B"></i> Cost by Department</h2>
    </div>
    <table class="portal-table">
        <thead><tr><th>Department</th><th>Employees</th><th>Gross</th><th>Net</th></tr></thead>
        <tbody>
            @foreach($byDept as $row)
            <tr>
                <td style="font-weight:500;color:#f1f5f9">{{ $row->dept_name }}</td>
                <td>{{ $row->count }}</td>
                <td>{{ number_format($row->total_gross, 0) }}</td>
                <td style="color:#00C896;font-weight:600">{{ number_format($row->total_net, 0) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

{{-- All Entries --}}
<div class="cp-section-card">
    <div class="cp-section-header">
        <h2 class="cp-section-title"><i data-lucide="list" style="width:17px;height:17px;color:#F59E0B"></i> Individual Entries</h2>
    </div>
    <div style="overflow-x:auto">
    <table class="portal-table">
        <thead><tr><th>Employee</th><th>Gross</th><th>Deductions</th><th>Net</th><th>Status</th></tr></thead>
        <tbody>
            @forelse($entries as $entry)
            <tr>
                <td style="font-weight:500;color:#f1f5f9">{{ $entry->employee->name ?? '—' }}</td>
                <td>{{ number_format($entry->gross_salary ?? 0, 2) }}</td>
                <td style="color:#ef4444">{{ number_format($entry->total_deductions ?? 0, 2) }}</td>
                <td style="color:#00C896;font-weight:600">{{ number_format($entry->net_salary ?? 0, 2) }}</td>
                <td><span class="portal-badge {{ $entry->status === 'paid' ? 'portal-badge-green' : 'portal-badge-amber' }}">{{ ucfirst($entry->status) }}</span></td>
            </tr>
            @empty
            <tr><td colspan="5" style="text-align:center;padding:2rem;color:var(--text-faint)">No entries.</td></tr>
            @endforelse
        </tbody>
    </table>
    </div>
    <div style="margin-top:1rem">{{ $entries->links() }}</div>
</div>
</x-layouts.accountant>

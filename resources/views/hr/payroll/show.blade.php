<x-layouts.hr title="Payroll Run — {{ $run->reference }}">
@php $locale = app()->getLocale(); @endphp

<div class="cp-page-header">
    <div>
        <h1 class="cp-page-title">{{ $run->reference }}</h1>
        <p class="cp-page-subtitle">
            {{ $run->period_start?->format('M j') }} – {{ $run->period_end?->format('M j, Y') }} ·
            Processed by {{ $run->processor->name ?? 'System' }}
        </p>
    </div>
    <a href="{{ route('hr.payroll.index', ['locale' => $locale]) }}" class="cp-btn-outline">
        <i data-lucide="arrow-left" style="width:15px;height:15px"></i> Back
    </a>
</div>

{{-- Summary cards --}}
<div class="cp-stats-row" style="margin-bottom:1.5rem">
    <div class="cp-stat-card">
        <div class="cp-stat-icon cp-stat-icon-purple"><i data-lucide="users" style="width:22px;height:22px"></i></div>
        <div>
            <p class="cp-stat-value">{{ $entries->total() }}</p>
            <p class="cp-stat-label">Employees</p>
        </div>
    </div>
    <div class="cp-stat-card">
        <div class="cp-stat-icon cp-stat-icon-blue"><i data-lucide="trending-up" style="width:22px;height:22px"></i></div>
        <div>
            <p class="cp-stat-value">{{ number_format($run->total_gross ?? 0, 0) }}</p>
            <p class="cp-stat-label">Total Gross ({{ $run->currency ?? 'XAF' }})</p>
        </div>
    </div>
    <div class="cp-stat-card">
        <div class="cp-stat-icon cp-stat-icon-green"><i data-lucide="dollar-sign" style="width:22px;height:22px"></i></div>
        <div>
            <p class="cp-stat-value">{{ number_format($run->total_net ?? 0, 0) }}</p>
            <p class="cp-stat-label">Total Net ({{ $run->currency ?? 'XAF' }})</p>
        </div>
    </div>
</div>

<div class="cp-section-card">
    <div class="cp-section-header">
        <h2 class="cp-section-title"><i data-lucide="list" style="width:17px;height:17px;color:#8B5CF6"></i> Payroll Entries</h2>
    </div>
    <div style="overflow-x:auto">
    <table class="portal-table">
        <thead>
            <tr>
                <th>Employee</th>
                <th>Gross</th>
                <th>Deductions</th>
                <th>Net</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($entries as $entry)
            @php $badge = $entry->status === 'paid' ? 'portal-badge-green' : 'portal-badge-amber'; @endphp
            <tr>
                <td style="font-weight:500;color:#f1f5f9">{{ $entry->employee->name ?? '—' }}</td>
                <td>{{ number_format($entry->gross_salary ?? 0, 2) }}</td>
                <td style="color:#ef4444">{{ number_format($entry->total_deductions ?? 0, 2) }}</td>
                <td style="font-weight:600;color:#00C896">{{ number_format($entry->net_salary ?? 0, 2) }}</td>
                <td><span class="portal-badge {{ $badge }}">{{ ucfirst($entry->status) }}</span></td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align:center;padding:2rem;color:var(--text-faint)">No entries.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    </div>
    <div style="margin-top:1rem">{{ $entries->links() }}</div>
</div>
</x-layouts.hr>

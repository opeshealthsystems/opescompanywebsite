<x-layouts.accountant title="Payroll Costs">
@php $locale = app()->getLocale(); @endphp

<div class="cp-page-header">
    <div>
        <h1 class="cp-page-title">Payroll Costs</h1>
        <p class="cp-page-subtitle">All payroll runs and cost breakdown</p>
    </div>
</div>

<div class="cp-section-card">
    <div style="overflow-x:auto">
    <table class="portal-table">
        <thead>
            <tr><th>Reference</th><th>Period</th><th>Employees</th><th>Gross</th><th>Net</th><th>Currency</th><th>Status</th><th></th></tr>
        </thead>
        <tbody>
            @forelse($runs as $run)
            @php $badge = match($run->status) { 'completed' => 'portal-badge-green', 'processing' => 'portal-badge-blue', 'cancelled' => 'portal-badge-red', default => 'portal-badge-amber' }; @endphp
            <tr>
                <td style="font-family:monospace;font-size:.8125rem;color:var(--text-muted)">{{ $run->reference }}</td>
                <td style="color:#f1f5f9">{{ $run->period_start?->format('M j') }} – {{ $run->period_end?->format('M j, Y') }}</td>
                <td>{{ $run->entries_count }}</td>
                <td>{{ number_format($run->total_gross ?? 0, 0) }}</td>
                <td style="font-weight:600;color:#f1f5f9">{{ number_format($run->total_net ?? 0, 0) }}</td>
                <td style="color:var(--text-muted)">{{ $run->currency ?? 'XAF' }}</td>
                <td><span class="portal-badge {{ $badge }}">{{ ucfirst($run->status) }}</span></td>
                <td>
                    <a href="{{ route('accountant.payroll.show', ['locale' => $locale, 'run' => $run->id]) }}" class="cp-btn-outline" style="font-size:.75rem;padding:.25rem .625rem">Details</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" style="text-align:center;padding:2rem;color:var(--text-faint)">No payroll runs found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    </div>
    <div style="margin-top:1rem">{{ $runs->links() }}</div>
</div>
</x-layouts.accountant>

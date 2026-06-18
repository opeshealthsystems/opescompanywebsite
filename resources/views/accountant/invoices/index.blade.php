<x-layouts.accountant title="Invoices">
@php $locale = app()->getLocale(); @endphp

<div class="cp-page-header">
    <div>
        <h1 class="cp-page-title">Invoices</h1>
        <p class="cp-page-subtitle">{{ $invoices->total() }} total invoices</p>
    </div>
</div>

<div class="cp-section-card">
    <form method="GET" class="portal-filter-bar">
        <select name="status">
            <option value="">All Statuses</option>
            <option value="draft"    {{ request('status') === 'draft'    ? 'selected' : '' }}>Draft</option>
            <option value="sent"     {{ request('status') === 'sent'     ? 'selected' : '' }}>Sent</option>
            <option value="paid"     {{ request('status') === 'paid'     ? 'selected' : '' }}>Paid</option>
            <option value="overdue"  {{ request('status') === 'overdue'  ? 'selected' : '' }}>Overdue</option>
            <option value="cancelled"{{ request('status') === 'cancelled'? 'selected' : '' }}>Cancelled</option>
        </select>
        <input type="date" name="from" value="{{ request('from') }}">
        <input type="date" name="to"   value="{{ request('to') }}">
        <button type="submit" class="cp-btn-primary">Filter</button>
        @if(request()->anyFilled(['status','from','to']))
            <a href="{{ route('accountant.invoices.index', ['locale' => $locale]) }}" class="cp-btn-outline">Clear</a>
        @endif
    </form>

    <div style="overflow-x:auto">
    <table class="portal-table">
        <thead>
            <tr><th>Reference</th><th>Customer</th><th>Total</th><th>Status</th><th>Due Date</th><th>Issued</th><th></th></tr>
        </thead>
        <tbody>
            @forelse($invoices as $inv)
            @php
                $badge = match($inv->status) {
                    'paid'      => 'portal-badge-green',
                    'overdue'   => 'portal-badge-red',
                    'sent'      => 'portal-badge-blue',
                    'cancelled' => 'portal-badge-gray',
                    default     => 'portal-badge-amber',
                };
            @endphp
            <tr>
                <td style="font-family:monospace;font-size:.8125rem;color:#94a3b8">{{ $inv->reference }}</td>
                <td style="font-weight:500;color:#f1f5f9">{{ $inv->customer->name ?? '—' }}</td>
                <td style="font-weight:600">{{ number_format($inv->grand_total ?? 0, 2) }}</td>
                <td><span class="portal-badge {{ $badge }}">{{ ucfirst($inv->status) }}</span></td>
                <td style="color:{{ $inv->status === 'overdue' ? '#ef4444' : '#94a3b8' }};font-size:.8125rem">{{ $inv->due_date?->format('M j, Y') }}</td>
                <td style="color:#64748b;font-size:.8125rem">{{ $inv->created_at?->format('M j, Y') }}</td>
                <td>
                    <a href="{{ route('accountant.invoices.show', ['locale' => $locale, 'invoice' => $inv->id]) }}" class="cp-btn-outline" style="font-size:.75rem;padding:.25rem .625rem">View</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align:center;padding:2rem;color:#475569">No invoices found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    </div>
    <div style="margin-top:1rem">{{ $invoices->links() }}</div>
</div>
</x-layouts.accountant>

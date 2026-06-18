<x-layouts.accountant title="Invoice {{ $invoice->reference }}">
@php $locale = app()->getLocale(); @endphp

<div class="cp-page-header">
    <div>
        <h1 class="cp-page-title">{{ $invoice->reference }}</h1>
        <p class="cp-page-subtitle">
            {{ $invoice->customer->name ?? 'Unknown Customer' }} ·
            Issued {{ $invoice->created_at?->format('M j, Y') }}
        </p>
    </div>
    <div style="display:flex;gap:.75rem">
        @if(in_array($invoice->status, ['sent','overdue']))
        <form method="POST" action="{{ route('accountant.invoices.mark-paid', ['locale' => $locale, 'invoice' => $invoice->id]) }}" style="margin:0">
            @csrf
            <button type="submit" class="cp-btn-primary" onclick="return confirm('Mark this invoice as paid?')">
                <i data-lucide="check-circle-2" style="width:15px;height:15px"></i> Mark as Paid
            </button>
        </form>
        @endif
        <a href="{{ route('accountant.invoices.index', ['locale' => $locale]) }}" class="cp-btn-outline">
            <i data-lucide="arrow-left" style="width:15px;height:15px"></i> Back
        </a>
    </div>
</div>

<div class="cp-section-grid">
    {{-- Invoice Details --}}
    <div class="cp-section-card">
        <div class="cp-section-header">
            <h2 class="cp-section-title"><i data-lucide="file-text" style="width:17px;height:17px;color:#F59E0B"></i> Invoice Details</h2>
        </div>
        <div style="display:flex;flex-direction:column;gap:.625rem">
            @php
                $badge = match($invoice->status) {
                    'paid'      => 'portal-badge-green',
                    'overdue'   => 'portal-badge-red',
                    'sent'      => 'portal-badge-blue',
                    'cancelled' => 'portal-badge-gray',
                    default     => 'portal-badge-amber',
                };
                $rows = [
                    'Status'    => null,
                    'Customer'  => $invoice->customer->name ?? '—',
                    'Issued By' => $invoice->issuer->name ?? '—',
                    'Due Date'  => $invoice->due_date?->format('M j, Y'),
                    'Paid At'   => $invoice->paid_at?->format('M j, Y') ?? '—',
                    'Currency'  => $invoice->currency ?? 'XAF',
                    'Tax Rate'  => ($invoice->tax_rate ?? 0).'%',
                ];
            @endphp
            <div style="display:flex;justify-content:space-between;padding:.5rem 0;border-bottom:1px solid #1e293b">
                <span style="color:#64748b;font-size:.875rem">Status</span>
                <span class="portal-badge {{ $badge }}">{{ ucfirst($invoice->status) }}</span>
            </div>
            @foreach(array_slice($rows, 1, null, true) as $label => $value)
            <div style="display:flex;justify-content:space-between;padding:.5rem 0;border-bottom:1px solid #1e293b">
                <span style="color:#64748b;font-size:.875rem">{{ $label }}</span>
                <span style="color:#e2e8f0;font-size:.875rem">{{ $value }}</span>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Financial Summary --}}
    <div class="cp-section-card">
        <div class="cp-section-header">
            <h2 class="cp-section-title"><i data-lucide="dollar-sign" style="width:17px;height:17px;color:#F59E0B"></i> Financial Summary</h2>
        </div>
        <div style="display:flex;flex-direction:column;gap:.625rem">
            @php
                $fin = [
                    'Subtotal'           => number_format($invoice->subtotal ?? 0, 2),
                    'Tax'                => number_format($invoice->tax_amount ?? 0, 2),
                    'Grand Total'        => number_format($invoice->grand_total ?? 0, 2),
                    'Amount Paid'        => number_format($invoice->amount_paid ?? 0, 2),
                    'Amount Outstanding' => number_format($invoice->amount_outstanding ?? 0, 2),
                ];
            @endphp
            @foreach($fin as $label => $value)
            <div style="display:flex;justify-content:space-between;padding:.5rem 0;border-bottom:1px solid #1e293b">
                <span style="color:#64748b;font-size:.875rem">{{ $label }}</span>
                <span style="color:{{ $label === 'Grand Total' ? '#f1f5f9' : ($label === 'Amount Outstanding' ? '#ef4444' : '#e2e8f0') }};font-size:.875rem;font-weight:{{ $label === 'Grand Total' ? '700' : '400' }}">{{ $value }}</span>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Line Items --}}
    @if($invoice->items && $invoice->items->isNotEmpty())
    <div class="cp-section-card" style="grid-column:1/-1">
        <div class="cp-section-header">
            <h2 class="cp-section-title"><i data-lucide="list" style="width:17px;height:17px;color:#F59E0B"></i> Line Items</h2>
        </div>
        <div style="overflow-x:auto">
        <table class="portal-table">
            <thead>
                <tr><th>Description</th><th>Qty</th><th>Unit Price</th><th>Total</th></tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $item)
                <tr>
                    <td style="color:#f1f5f9">{{ $item->description ?? '—' }}</td>
                    <td>{{ $item->quantity ?? 1 }}</td>
                    <td>{{ number_format($item->unit_price ?? 0, 2) }}</td>
                    <td style="font-weight:600">{{ number_format(($item->quantity ?? 1) * ($item->unit_price ?? 0), 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        </div>
    </div>
    @endif
</div>
</x-layouts.accountant>

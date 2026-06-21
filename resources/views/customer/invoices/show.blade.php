<x-layouts.customer title="{{ $invoice->invoice_number }}">
    <div class="cp-page-header">
        <div>
            <h1 class="cp-page-title">{{ $invoice->invoice_number }}</h1>
            <p class="cp-page-subtitle">Invoice detail</p>
        </div>
        <div style="display:flex;gap:0.75rem;align-items:center;">
            <a href="{{ route('invoices.pdf', ['invoice' => $invoice->id]) }}"
               target="_blank" class="cp-btn-outline" style="font-size:0.875rem;">
                <i data-lucide="download" style="width:14px;height:14px;vertical-align:middle;margin-right:4px;"></i> Download PDF
            </a>
            <a href="{{ route('customer.invoices', ['locale' => app()->getLocale()]) }}" class="cp-btn-outline">&larr; Back</a>
        </div>
    </div>

    @php
        $statusColor = match($invoice->status) {
            'sent'    => '#3b82f6',
            'paid'    => '#00C896',
            'overdue' => '#ef4444',
            default   => 'var(--text-muted)',
        };
    @endphp

    @if($invoice->status === 'overdue')
        <div style="background:rgba(239,68,68,0.08);border:1px solid rgba(239,68,68,0.25);border-radius:10px;padding:1rem 1.25rem;margin-bottom:1.5rem;">
            <p style="color:#ef4444;font-weight:600;font-size:0.875rem;margin:0;">&#9888; This invoice is overdue</p>
            <p style="color:var(--text-muted);font-size:0.8rem;margin:0.25rem 0 0;">Due date was {{ $invoice->due_date?->format('d M Y') }}. Please contact support.</p>
        </div>
    @endif

    <div class="cp-section-grid" style="margin-bottom:1rem;">
        <div class="cp-section-card">
            <p style="color:var(--text-muted);font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.5rem;">Status</p>
            <p style="color:{{ $statusColor }};font-weight:700;font-size:1rem;">{{ \App\Models\Invoice::statusOptions()[$invoice->status] ?? $invoice->status }}</p>
        </div>
        <div class="cp-section-card">
            <p style="color:var(--text-muted);font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.5rem;">Due Date</p>
            <p style="color:#e2e8f0;font-weight:600;">{{ $invoice->due_date?->format('d M Y') ?? '—' }}</p>
        </div>
        <div class="cp-section-card">
            <p style="color:var(--text-muted);font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.5rem;">Total</p>
            <p style="color:#00C896;font-weight:700;font-size:1.125rem;">{{ number_format($invoice->grandTotal) }} {{ $invoice->currency }}</p>
        </div>
        @if($invoice->paid_at)
        <div class="cp-section-card">
            <p style="color:var(--text-muted);font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.5rem;">Paid On</p>
            <p style="color:#00C896;font-weight:600;">{{ $invoice->paid_at->format('d M Y') }}</p>
        </div>
        @endif
    </div>

    <div class="cp-section-card">
        <h3 style="color:#e2e8f0;font-size:0.9rem;font-weight:600;margin-bottom:1rem;">Line Items</h3>
        <table style="width:100%;border-collapse:collapse;">
            <thead>
                <tr style="border-bottom:1px solid #334155;">
                    <th style="text-align:left;padding:0.5rem 0.75rem;color:var(--text-muted);font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;">Description</th>
                    <th style="text-align:right;padding:0.5rem 0.75rem;color:var(--text-muted);font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;">Qty</th>
                    <th style="text-align:right;padding:0.5rem 0.75rem;color:var(--text-muted);font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;">Unit Price</th>
                    <th style="text-align:right;padding:0.5rem 0.75rem;color:var(--text-muted);font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $item)
                <tr style="border-bottom:1px solid #1e293b;">
                    <td style="padding:0.625rem 0.75rem;color:#e2e8f0;font-size:0.875rem;">{{ $item->description }}</td>
                    <td style="padding:0.625rem 0.75rem;color:var(--text-muted);font-size:0.875rem;text-align:right;">{{ $item->quantity }}</td>
                    <td style="padding:0.625rem 0.75rem;color:var(--text-muted);font-size:0.875rem;text-align:right;">{{ number_format($item->unit_price) }}</td>
                    <td style="padding:0.625rem 0.75rem;color:#e2e8f0;font-size:0.875rem;text-align:right;font-weight:500;">{{ number_format($item->total) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="border-top:1px solid #334155;">
                    <td colspan="3" style="padding:0.625rem 0.75rem;color:var(--text-muted);font-size:0.875rem;text-align:right;">Subtotal</td>
                    <td style="padding:0.625rem 0.75rem;color:#e2e8f0;font-size:0.875rem;text-align:right;">{{ number_format($invoice->subtotal) }}</td>
                </tr>
                @if($invoice->tax_rate > 0)
                <tr>
                    <td colspan="3" style="padding:0.375rem 0.75rem;color:var(--text-muted);font-size:0.875rem;text-align:right;">Tax ({{ $invoice->tax_rate }}%)</td>
                    <td style="padding:0.375rem 0.75rem;color:#e2e8f0;font-size:0.875rem;text-align:right;">{{ number_format($invoice->taxAmount) }}</td>
                </tr>
                @endif
                <tr style="border-top:2px solid #334155;">
                    <td colspan="3" style="padding:0.75rem;color:#e2e8f0;font-size:0.9375rem;font-weight:700;text-align:right;">Total</td>
                    <td style="padding:0.75rem;color:#00C896;font-size:0.9375rem;font-weight:700;text-align:right;">{{ number_format($invoice->grandTotal) }} {{ $invoice->currency }}</td>
                </tr>
            </tfoot>
        </table>
    </div>

    @if($invoice->notes)
    <div class="cp-section-card" style="margin-top:1rem;">
        <p style="color:var(--text-muted);font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.5rem;">Notes</p>
        <p style="color:var(--text-muted);font-size:0.875rem;line-height:1.6;">{{ $invoice->notes }}</p>
    </div>
    @endif
</x-layouts.customer>

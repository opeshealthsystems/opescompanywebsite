<x-layouts.customer title="My Invoices">
    <div class="cp-page-header">
        <div>
            <h1 class="cp-page-title">My Invoices</h1>
            <p class="cp-page-subtitle">Invoices issued to your account</p>
        </div>
    </div>

    @if($invoices->isEmpty())
        <div class="cp-section-card" style="text-align:center;padding:3rem;">
            <div class="cp-empty-state">
                <i data-lucide="receipt" style="width:48px;height:48px;color:#334155"></i>
                <p>No invoices yet.</p>
                <p style="font-size:0.8125rem">Invoices issued to your account will appear here.</p>
            </div>
        </div>
    @else
        <div class="cp-section-card" style="padding:0;">
            <table style="width:100%;border-collapse:collapse;">
                <thead>
                    <tr style="border-bottom:1px solid #334155;">
                        <th style="text-align:left;padding:0.75rem;color:#94a3b8;font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;">Invoice #</th>
                        <th style="text-align:left;padding:0.75rem;color:#94a3b8;font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;">Status</th>
                        <th style="text-align:left;padding:0.75rem;color:#94a3b8;font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;">Due Date</th>
                        <th style="text-align:right;padding:0.75rem;color:#94a3b8;font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;">Date</th>
                        <th style="padding:0.75rem;"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoices as $invoice)
                    @php
                        $statusColor = match($invoice->status) {
                            'sent'    => '#3b82f6',
                            'paid'    => '#00C896',
                            'overdue' => '#ef4444',
                            default   => '#94a3b8',
                        };
                    @endphp
                    <tr style="border-bottom:1px solid #1e293b;">
                        <td style="padding:0.75rem;color:#e2e8f0;font-size:0.875rem;font-weight:500;">{{ $invoice->invoice_number }}</td>
                        <td style="padding:0.75rem;">
                            <span style="color:{{ $statusColor }};font-size:0.8125rem;font-weight:600;text-transform:capitalize;">
                                {{ \App\Models\Invoice::statusOptions()[$invoice->status] ?? $invoice->status }}
                            </span>
                        </td>
                        <td style="padding:0.75rem;color:#64748b;font-size:0.8125rem;">{{ $invoice->due_date?->format('d M Y') ?? '—' }}</td>
                        <td style="padding:0.75rem;color:#64748b;font-size:0.8125rem;text-align:right;">{{ $invoice->created_at->format('d M Y') }}</td>
                        <td style="padding:0.75rem;text-align:right;">
                            <a href="{{ route('customer.invoices.show', ['locale' => app()->getLocale(), 'id' => $invoice->id]) }}"
                               class="cp-btn-outline" style="font-size:0.75rem;padding:0.375rem 0.75rem;">View</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div style="padding:1rem 0.75rem 0;">
                {{ $invoices->links() }}
            </div>
        </div>
    @endif
</x-layouts.customer>

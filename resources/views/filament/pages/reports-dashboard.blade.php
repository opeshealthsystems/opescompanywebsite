<x-filament-panels::page>
    @php $m = $this->metrics; @endphp

    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:1rem;margin-bottom:2rem;">

        <div style="background:#1e293b;border-radius:12px;padding:1.25rem;">
            <div style="color:#94a3b8;font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.5rem;">Customers</div>
            <div style="color:#e2e8f0;font-size:1.875rem;font-weight:700;">{{ $m['totalCustomers'] }}</div>
            <div style="color:#00C896;font-size:0.8125rem;margin-top:0.25rem;">+{{ $m['newCustomers'] }} this month</div>
        </div>

        <div style="background:#1e293b;border-radius:12px;padding:1.25rem;">
            <div style="color:#94a3b8;font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.5rem;">Active Licenses</div>
            <div style="color:#e2e8f0;font-size:1.875rem;font-weight:700;">{{ $m['activeLicenses'] }}</div>
            @if($m['expiringSoon'] > 0)
                <div style="color:#f59e0b;font-size:0.8125rem;margin-top:0.25rem;">{{ $m['expiringSoon'] }} expiring within 30d</div>
            @else
                <div style="color:#64748b;font-size:0.8125rem;margin-top:0.25rem;">None expiring soon</div>
            @endif
        </div>

        <div style="background:#1e293b;border-radius:12px;padding:1.25rem;">
            <div style="color:#94a3b8;font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.5rem;">Tickets</div>
            <div style="color:#e2e8f0;font-size:1.875rem;font-weight:700;">{{ $m['openTickets'] }}</div>
            <div style="color:#64748b;font-size:0.8125rem;margin-top:0.25rem;">{{ $m['resolvedThisMonth'] }} resolved this month</div>
        </div>

        <div style="background:#1e293b;border-radius:12px;padding:1.25rem;">
            <div style="color:#94a3b8;font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.5rem;">Open Bug Reports</div>
            <div style="color:{{ $m['openBugReports'] > 0 ? '#ef4444' : '#e2e8f0' }};font-size:1.875rem;font-weight:700;">{{ $m['openBugReports'] }}</div>
            <div style="color:#64748b;font-size:0.8125rem;margin-top:0.25rem;">From tester reports</div>
        </div>

        <div style="background:#1e293b;border-radius:12px;padding:1.25rem;">
            <div style="color:#94a3b8;font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.5rem;">Outstanding Invoices</div>
            <div style="color:{{ $m['overdueInvoices'] > 0 ? '#ef4444' : '#e2e8f0' }};font-size:1.875rem;font-weight:700;">{{ $m['outstandingInvoices'] }}</div>
            @if($m['overdueInvoices'] > 0)
                <div style="color:#ef4444;font-size:0.8125rem;margin-top:0.25rem;">{{ $m['overdueInvoices'] }} overdue</div>
            @else
                <div style="color:#64748b;font-size:0.8125rem;margin-top:0.25rem;">{{ $m['paidThisMonth'] }} paid this month</div>
            @endif
        </div>

        <div style="background:#1e293b;border-radius:12px;padding:1.25rem;">
            <div style="color:#94a3b8;font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.5rem;">Tester Assignments</div>
            <div style="color:#e2e8f0;font-size:1.875rem;font-weight:700;">{{ $m['activeAssignments'] }}</div>
            <div style="color:#64748b;font-size:0.8125rem;margin-top:0.25rem;">{{ $m['pendingAssignments'] }} pending &bull; {{ $m['completedThisMonth'] }} done this month</div>
        </div>

    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;">

        <div style="background:#1e293b;border-radius:12px;padding:1.25rem;">
            <h3 style="color:#e2e8f0;font-size:0.875rem;font-weight:600;margin-bottom:1rem;text-transform:uppercase;letter-spacing:0.05em;">Recent Open Tickets</h3>
            @forelse($m['recentTickets'] as $ticket)
            @php
                $priorityColor = match($ticket->priority) {
                    'urgent' => '#ef4444', 'high' => '#f97316',
                    'medium' => '#3b82f6', 'low'  => '#64748b', default => '#94a3b8',
                };
            @endphp
            <div style="display:flex;justify-content:space-between;align-items:flex-start;padding:0.625rem 0;border-bottom:1px solid #0f172a;">
                <div>
                    <p style="color:#e2e8f0;font-size:0.8125rem;font-weight:500;margin:0 0 0.125rem;">{{ Str::limit($ticket->subject, 40) }}</p>
                    <p style="color:#64748b;font-size:0.75rem;margin:0;">{{ $ticket->reference_number }} &bull; {{ $ticket->customer?->name ?? 'Unknown' }}</p>
                </div>
                <span style="color:{{ $priorityColor }};font-size:0.7rem;font-weight:700;text-transform:uppercase;white-space:nowrap;margin-left:0.5rem;">{{ $ticket->priority }}</span>
            </div>
            @empty
            <p style="color:#64748b;font-size:0.875rem;">No open tickets.</p>
            @endforelse
            <div style="margin-top:0.75rem;">
                <a href="{{ \App\Filament\Resources\TicketResource::getUrl('index') }}" style="color:#00C896;font-size:0.75rem;text-decoration:none;">View all tickets &rarr;</a>
            </div>
        </div>

        <div style="background:#1e293b;border-radius:12px;padding:1.25rem;">
            <h3 style="color:#e2e8f0;font-size:0.875rem;font-weight:600;margin-bottom:1rem;text-transform:uppercase;letter-spacing:0.05em;">Outstanding Invoices</h3>
            @forelse($m['recentInvoices'] as $invoice)
            @php
                $statusColor = match($invoice->status) {
                    'overdue' => '#ef4444', 'sent' => '#3b82f6', default => '#94a3b8',
                };
            @endphp
            <div style="display:flex;justify-content:space-between;align-items:flex-start;padding:0.625rem 0;border-bottom:1px solid #0f172a;">
                <div>
                    <p style="color:#e2e8f0;font-size:0.8125rem;font-weight:500;margin:0 0 0.125rem;">{{ $invoice->invoice_number }}</p>
                    <p style="color:#64748b;font-size:0.75rem;margin:0;">{{ $invoice->customer?->name ?? 'Unknown' }} &bull; Due {{ $invoice->due_date?->format('d M Y') ?? '&mdash;' }}</p>
                </div>
                <span style="color:{{ $statusColor }};font-size:0.7rem;font-weight:700;text-transform:uppercase;white-space:nowrap;margin-left:0.5rem;">{{ $invoice->status }}</span>
            </div>
            @empty
            <p style="color:#64748b;font-size:0.875rem;">No outstanding invoices.</p>
            @endforelse
            <div style="margin-top:0.75rem;">
                <a href="{{ \App\Filament\Resources\InvoiceResource::getUrl('index') }}" style="color:#00C896;font-size:0.75rem;text-decoration:none;">View all invoices &rarr;</a>
            </div>
        </div>

    </div>
</x-filament-panels::page>

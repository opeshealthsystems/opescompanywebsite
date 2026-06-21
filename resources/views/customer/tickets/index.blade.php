<x-layouts.customer title="Support Tickets">
    <div class="cp-page-header">
        <div>
            <h1 class="cp-page-title">Support Tickets</h1>
            <p class="cp-page-subtitle">Track your support requests</p>
        </div>
        <a href="{{ route('customer.tickets.create', ['locale' => app()->getLocale()]) }}"
           class="cp-btn-primary">+ New Ticket</a>
    </div>

    @if($tickets->isEmpty())
        <div class="cp-section-card" style="text-align:center;padding:3rem;">
            <div class="cp-empty-state">
                <i data-lucide="ticket" style="width:48px;height:48px;color:#334155"></i>
                <p>No support tickets yet.</p>
                <p style="font-size:0.8125rem">Submit a ticket if you need help and we'll get back to you.</p>
                <a href="{{ route('customer.tickets.create', ['locale' => app()->getLocale()]) }}"
                   class="cp-btn-primary" style="display:inline-block;margin-top:1rem;">Open a Ticket</a>
            </div>
        </div>
    @else
        <div class="cp-section-card" style="padding:0;">
            <table style="width:100%;border-collapse:collapse;">
                <thead>
                    <tr style="border-bottom:1px solid #334155;">
                        <th style="text-align:left;padding:0.75rem;color:var(--text-muted);font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;">Ref</th>
                        <th style="text-align:left;padding:0.75rem;color:var(--text-muted);font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;">Subject</th>
                        <th style="text-align:left;padding:0.75rem;color:var(--text-muted);font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;">Type</th>
                        <th style="text-align:left;padding:0.75rem;color:var(--text-muted);font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;">Priority</th>
                        <th style="text-align:left;padding:0.75rem;color:var(--text-muted);font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;">Status</th>
                        <th style="text-align:left;padding:0.75rem;color:var(--text-muted);font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;">Opened</th>
                        <th style="padding:0.75rem;"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tickets as $ticket)
                    @php
                        $statusColor = match($ticket->status) {
                            'open'             => '#ef4444',
                            'in_progress'      => '#eab308',
                            'pending_customer' => '#3b82f6',
                            'resolved'         => '#00C896',
                            'closed'           => 'var(--text-muted)',
                            default            => 'var(--text-muted)',
                        };
                        $priorityColor = match($ticket->priority) {
                            'urgent' => '#ef4444',
                            'high'   => '#f97316',
                            'medium' => '#3b82f6',
                            'low'    => 'var(--text-muted)',
                            default  => 'var(--text-muted)',
                        };
                    @endphp
                    <tr style="border-bottom:1px solid #1e293b;">
                        <td style="padding:0.75rem;">
                            <span style="color:#00C896;font-family:monospace;font-size:0.8rem;">{{ $ticket->reference_number }}</span>
                        </td>
                        <td style="padding:0.75rem;">
                            <span style="color:#e2e8f0;font-size:0.875rem;">{{ Str::limit($ticket->subject, 45) }}</span>
                        </td>
                        <td style="padding:0.75rem;">
                            <span style="color:var(--text-muted);font-size:0.8125rem;">{{ \App\Models\Ticket::typeLabel($ticket->type) }}</span>
                        </td>
                        <td style="padding:0.75rem;">
                            <span style="color:{{ $priorityColor }};font-size:0.8125rem;font-weight:600;text-transform:capitalize;">{{ $ticket->priority }}</span>
                        </td>
                        <td style="padding:0.75rem;">
                            <span style="color:{{ $statusColor }};font-size:0.8125rem;font-weight:600;">
                                {{ \App\Models\Ticket::statusOptions()[$ticket->status] ?? $ticket->status }}
                            </span>
                        </td>
                        <td style="padding:0.75rem;color:var(--text-muted);font-size:0.8125rem;">{{ $ticket->created_at->diffForHumans() }}</td>
                        <td style="padding:0.75rem;text-align:right;">
                            <a href="{{ route('customer.tickets.show', ['locale' => app()->getLocale(), 'id' => $ticket->id]) }}"
                               class="cp-btn-outline" style="font-size:0.75rem;padding:0.375rem 0.75rem;">View</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div style="padding:1rem 0.75rem 0;">
                {{ $tickets->links() }}
            </div>
        </div>
    @endif
</x-layouts.customer>

<x-layouts.customer title="My Licenses">
    <div class="cp-page-header">
        <div>
            <h1 class="cp-page-title">My Licenses</h1>
            <p class="cp-page-subtitle">Software licenses issued to your account</p>
        </div>
    </div>

    @if($licenses->isEmpty())
        <div class="cp-section-card" style="text-align:center;padding:3rem;">
            <div class="cp-empty-state">
                <i data-lucide="key" style="width:48px;height:48px;color:#334155"></i>
                <p>No licenses yet.</p>
                <p style="font-size:0.8125rem">Software licenses issued to your account will appear here.</p>
            </div>
        </div>
    @else
        <div class="cp-section-card" style="padding:0;">
            <table style="width:100%;border-collapse:collapse;">
                <thead>
                    <tr style="border-bottom:1px solid #334155;">
                        <th style="text-align:left;padding:0.75rem;color:var(--text-muted);font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;">Product</th>
                        <th style="text-align:left;padding:0.75rem;color:var(--text-muted);font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;">Plan</th>
                        <th style="text-align:center;padding:0.75rem;color:var(--text-muted);font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;">Seats</th>
                        <th style="text-align:left;padding:0.75rem;color:var(--text-muted);font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;">Status</th>
                        <th style="text-align:left;padding:0.75rem;color:var(--text-muted);font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;">Expires</th>
                        <th style="padding:0.75rem;"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($licenses as $license)
                    @php
                        $statusColor = match($license->status) {
                            'active'    => '#00C896',
                            'suspended' => '#eab308',
                            'expired'   => '#ef4444',
                            'cancelled' => 'var(--text-muted)',
                            default     => 'var(--text-muted)',
                        };
                        $expiring = $license->isExpiringSoon();
                    @endphp
                    <tr style="border-bottom:1px solid #1e293b;">
                        <td style="padding:0.75rem;">
                            <div style="color:#e2e8f0;font-size:0.875rem;font-weight:500;">{{ $license->product_name }}</div>
                            <div style="color:var(--text-faint);font-size:0.75rem;font-family:monospace;">{{ $license->license_key }}</div>
                        </td>
                        <td style="padding:0.75rem;">
                            <span style="background:rgba(100,116,139,0.15);color:var(--text-muted);font-size:0.7rem;font-weight:600;padding:0.2rem 0.5rem;border-radius:20px;text-transform:uppercase;letter-spacing:0.04em;">
                                {{ \App\Models\License::planLabel($license->plan) }}
                            </span>
                        </td>
                        <td style="padding:0.75rem;color:var(--text-muted);font-size:0.875rem;text-align:center;">{{ $license->seats }}</td>
                        <td style="padding:0.75rem;">
                            <span style="color:{{ $statusColor }};font-size:0.8125rem;font-weight:600;text-transform:capitalize;">
                                {{ $license->status }}
                            </span>
                        </td>
                        <td style="padding:0.75rem;color:{{ $expiring ? '#eab308' : 'var(--text-muted)' }};font-size:0.8125rem;">
                            {{ $license->end_date?->format('d M Y') }}
                            @if($expiring)
                                <span style="font-size:0.7rem;"> &#9888; Expiring soon</span>
                            @endif
                        </td>
                        <td style="padding:0.75rem;text-align:right;">
                            <a href="{{ route('customer.licenses.show', ['locale' => app()->getLocale(), 'id' => $license->id]) }}"
                               class="cp-btn-outline" style="font-size:0.75rem;padding:0.375rem 0.75rem;">Details</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div style="padding:1rem 0.75rem 0;">
                {{ $licenses->links() }}
            </div>
        </div>
    @endif
</x-layouts.customer>

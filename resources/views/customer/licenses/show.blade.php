<x-layouts.customer title="{{ $license->product_name }} License">
    <div class="cp-page-header">
        <div>
            <h1 class="cp-page-title">{{ $license->product_name }}</h1>
            <p class="cp-page-subtitle">{{ \App\Models\License::planLabel($license->plan) }} Plan &middot; {{ $license->seats }} seat(s)</p>
        </div>
        <a href="{{ route('customer.licenses', ['locale' => app()->getLocale()]) }}" class="cp-btn-outline">
            &larr; Back
        </a>
    </div>

    @php
        $statusColor = match($license->status) {
            'active'    => '#00C896',
            'suspended' => '#eab308',
            'expired'   => '#ef4444',
            'cancelled' => '#64748b',
            default     => '#94a3b8',
        };
    @endphp

    @php
        $expiring = $license->isExpiringSoon();
    @endphp

    @if($expiring)
        <div style="background:rgba(234,179,8,0.08);border:1px solid rgba(234,179,8,0.25);border-radius:10px;padding:1rem 1.25rem;margin-bottom:1.5rem;display:flex;align-items:flex-start;justify-content:space-between;gap:1rem;flex-wrap:wrap">
            <div>
                <p style="color:#eab308;font-weight:600;font-size:0.9rem;margin:0;">&#9888; License expiring soon</p>
                <p style="color:#64748b;font-size:0.8rem;margin:0.25rem 0 0;">
                    Your license expires on <strong style="color:#eab308">{{ $license->end_date?->format('d M Y') }}</strong>
                    @php
                        $daysLeft = now()->diffInDays($license->end_date, false);
                    @endphp
                    — {{ $daysLeft }} day{{ $daysLeft !== 1 ? 's' : '' }} remaining.
                </p>
            </div>
            <a href="{{ route('customer.tickets.create', ['locale' => app()->getLocale()]) }}?subject=License+Renewal+Request&type=billing"
               style="display:inline-flex;align-items:center;gap:6px;background:#eab308;color:#0f172a;font-size:0.8125rem;font-weight:700;padding:0.5rem 1rem;border-radius:6px;text-decoration:none;flex-shrink:0;transition:opacity 0.15s"
               onmouseover="this.style.opacity='0.85'" onmouseout="this.style.opacity='1'">
                <i data-lucide="refresh-cw" style="width:13px;height:13px"></i>
                Request Renewal
            </a>
        </div>
    @endif

    @if($license->status === 'expired')
        <div style="background:rgba(239,68,68,0.06);border:1px solid rgba(239,68,68,0.2);border-radius:10px;padding:1rem 1.25rem;margin-bottom:1.5rem;display:flex;align-items:center;justify-content:space-between;gap:1rem;flex-wrap:wrap">
            <div>
                <p style="color:#ef4444;font-weight:600;font-size:0.9rem;margin:0;">&#9888; License expired</p>
                <p style="color:#64748b;font-size:0.8rem;margin:0.25rem 0 0;">This license expired on {{ $license->end_date?->format('d M Y') }}. Submit a renewal request to restore access.</p>
            </div>
            <a href="{{ route('customer.tickets.create', ['locale' => app()->getLocale()]) }}?subject=License+Renewal+Request&type=billing"
               style="display:inline-flex;align-items:center;gap:6px;background:#ef4444;color:#fff;font-size:0.8125rem;font-weight:700;padding:0.5rem 1rem;border-radius:6px;text-decoration:none;flex-shrink:0">
                <i data-lucide="refresh-cw" style="width:13px;height:13px"></i>
                Request Renewal
            </a>
        </div>
    @endif

    <div class="cp-section-card">
        <h2 style="color:#e2e8f0;font-size:1rem;font-weight:600;margin-bottom:1.5rem;">License Details</h2>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;">
            <div>
                <p style="color:#94a3b8;font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.25rem;">License Key</p>
                <p style="color:#00C896;font-family:monospace;font-size:0.9rem;word-break:break-all;">{{ $license->license_key }}</p>
            </div>
            <div>
                <p style="color:#94a3b8;font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.25rem;">Status</p>
                <p style="color:{{ $statusColor }};font-size:0.9rem;font-weight:600;text-transform:capitalize;">{{ $license->status }}</p>
            </div>
            <div>
                <p style="color:#94a3b8;font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.25rem;">Product</p>
                <p style="color:#e2e8f0;font-size:0.875rem;">{{ $license->product_name }}</p>
            </div>
            <div>
                <p style="color:#94a3b8;font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.25rem;">Plan</p>
                <p style="color:#e2e8f0;font-size:0.875rem;">{{ \App\Models\License::planLabel($license->plan) }}</p>
            </div>
            <div>
                <p style="color:#94a3b8;font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.25rem;">Authorized Seats</p>
                <p style="color:#e2e8f0;font-size:0.875rem;">{{ $license->seats }}</p>
            </div>
            <div>
                <p style="color:#94a3b8;font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.25rem;">Valid From</p>
                <p style="color:#e2e8f0;font-size:0.875rem;">{{ $license->start_date?->format('d M Y') }}</p>
            </div>
            <div>
                <p style="color:#94a3b8;font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.25rem;">Expires</p>
                <p style="color:{{ $expiring ? '#eab308' : '#e2e8f0' }};font-size:0.875rem;">{{ $license->end_date?->format('d M Y') }}</p>
            </div>
            @if($license->price)
            <div>
                <p style="color:#94a3b8;font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.25rem;">License Fee</p>
                <p style="color:#e2e8f0;font-size:0.875rem;">{{ $license->currency }} {{ number_format($license->price) }}</p>
            </div>
            @endif
        </div>

        @if($license->notes)
        <div style="margin-top:1.5rem;padding-top:1.5rem;border-top:1px solid #334155;">
            <p style="color:#94a3b8;font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.5rem;">Notes</p>
            <p style="color:#94a3b8;font-size:0.875rem;line-height:1.6;">{{ $license->notes }}</p>
        </div>
        @endif
    </div>

    <div class="cp-section-card" style="margin-top:1rem;">
        <p style="color:#64748b;font-size:0.8125rem;line-height:1.7;">
            Need to renew, upgrade, or have questions about this license? Contact
            <a href="mailto:support@opeshealthsystems.com" style="color:#00C896;">support@opeshealthsystems.com</a>
            or call {{ config('company.phone') }}.
        </p>
    </div>
</x-layouts.customer>

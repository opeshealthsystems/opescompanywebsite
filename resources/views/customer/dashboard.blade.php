<x-layouts.customer title="Dashboard">
    <div class="cp-page-header">
        <div>
            <h1 class="cp-page-title">Welcome, {{ $user->name }}</h1>
            <p class="cp-page-subtitle">
                {{ $profile?->facility_name ?? 'Your OPES Health Systems account' }}
                @if($profile?->city) · {{ $profile->city }} @endif
                @if($profile?->country) · {{ $profile->country }} @endif
            </p>
        </div>
        <a href="{{ route('customer.profile', ['locale' => app()->getLocale()]) }}" class="cp-btn-outline">
            <i data-lucide="settings" style="width:15px;height:15px"></i> Edit Profile
        </a>
    </div>

    <div class="cp-stats-row">
        <a href="{{ route('customer.licenses', ['locale' => app()->getLocale()]) }}" class="cp-stat-card" style="text-decoration:none">
            <div class="cp-stat-icon" style="background:rgba(0,200,150,0.1)">
                <i data-lucide="key" style="width:20px;height:20px;color:#00C896"></i>
            </div>
            <div>
                <p class="cp-stat-value">{{ $activeLicenses }}</p>
                <p class="cp-stat-label">Active Licenses</p>
            </div>
        </a>
        <a href="{{ route('customer.tickets', ['locale' => app()->getLocale()]) }}" class="cp-stat-card" style="text-decoration:none">
            <div class="cp-stat-icon" style="background:rgba(26,111,232,0.1)">
                <i data-lucide="ticket" style="width:20px;height:20px;color:#1A6FE8"></i>
            </div>
            <div>
                <p class="cp-stat-value">{{ $openTickets }}</p>
                <p class="cp-stat-label">Open Tickets</p>
            </div>
        </a>
        <a href="{{ route('customer.invoices', ['locale' => app()->getLocale()]) }}" class="cp-stat-card" style="text-decoration:none">
            <div class="cp-stat-icon" style="background:rgba(234,179,8,0.1)">
                <i data-lucide="receipt" style="width:20px;height:20px;color:#eab308"></i>
            </div>
            <div>
                <p class="cp-stat-value">{{ $pendingInvoices }}</p>
                <p class="cp-stat-label">Pending Invoices</p>
            </div>
        </a>
    </div>

    <div class="cp-section-grid">
        <div class="cp-section-card">
            <div class="cp-section-header">
                <h2 class="cp-section-title">
                    <i data-lucide="key" style="width:18px;height:18px;color:#00C896"></i> My Licenses
                </h2>
                <a href="{{ route('contact', ['locale' => app()->getLocale()]) }}" class="cp-btn-primary">
                    Request License
                </a>
            </div>
            <div class="cp-empty-state">
                <i data-lucide="package-open" style="width:40px;height:40px;color:#334155"></i>
                <p>No active licenses yet.</p>
                <p style="font-size:0.8125rem">Contact us to purchase software licenses for your facility.</p>
            </div>
        </div>

        <div class="cp-section-card">
            <div class="cp-section-header">
                <h2 class="cp-section-title">
                    <i data-lucide="ticket" style="width:18px;height:18px;color:#1A6FE8"></i> Support Tickets
                </h2>
                <a href="{{ route('customer.tickets.create', ['locale' => app()->getLocale()]) }}" class="cp-btn-primary">
                    New Ticket
                </a>
            </div>
            @if($recentTickets->isEmpty())
            <div class="cp-empty-state">
                <i data-lucide="message-circle" style="width:40px;height:40px;color:#334155"></i>
                <p>No tickets yet.</p>
                <p style="font-size:0.8125rem">Open a ticket any time for technical support or questions.</p>
            </div>
            @else
            <ul style="list-style:none;padding:0;margin:0">
                @foreach($recentTickets as $ticket)
                <li style="padding:10px 0;border-bottom:1px solid #1e293b;display:flex;justify-content:space-between;align-items:center">
                    <a href="{{ route('customer.tickets.show', ['locale' => app()->getLocale(), 'ticket' => $ticket->id]) }}"
                       style="color:#e2e8f0;text-decoration:none;font-size:0.875rem">{{ $ticket->subject }}</a>
                    <span style="font-size:0.75rem;color:{{ $ticket->status === 'open' ? '#00C896' : 'var(--text-muted)' }}">
                        {{ ucfirst($ticket->status) }}
                    </span>
                </li>
                @endforeach
            </ul>
            <div style="margin-top:12px">
                <a href="{{ route('customer.tickets', ['locale' => app()->getLocale()]) }}" class="cp-btn-outline" style="font-size:0.8125rem">
                    View all tickets →
                </a>
            </div>
            @endif
        </div>
    </div>

    {{-- ── PRODUCT BROCHURES ──────────────────────────────────────── --}}
    <div class="cp-section-card" style="margin-top:24px">
        <div class="cp-section-header">
            <h2 class="cp-section-title">
                <i data-lucide="file-text" style="width:18px;height:18px;color:#00C896"></i> Product Brochures
            </h2>
            <span style="font-size:0.8125rem;color:var(--text-muted)">{{ count($allProducts) }} products</span>
        </div>
        <p style="font-size:0.875rem;color:var(--text-muted);margin:0 0 16px">Download a full product brochure (PDF) for any OPES Health Systems product.</p>
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:10px">
            @foreach($allProducts as $slug => $prod)
            <a href="{{ route('product.brochure', ['locale' => app()->getLocale(), 'slug' => $slug]) }}"
               target="_blank"
               style="display:flex;align-items:center;gap:10px;padding:10px 14px;background:#0f172a;border:1px solid #1e293b;border-left:3px solid {{ $prod['color'] }};border-radius:6px;text-decoration:none;transition:border-color .2s"
               onmouseover="this.style.borderColor='{{ $prod['color'] }}'" onmouseout="this.style.borderLeftColor='{{ $prod['color'] }}';this.style.borderColor='#1e293b';this.style.borderLeftColor='{{ $prod['color'] }}'">
                <i data-lucide="file-down" style="width:16px;height:16px;color:{{ $prod['color'] }};flex-shrink:0"></i>
                <div style="min-width:0">
                    <div style="font-size:0.8125rem;font-weight:600;color:#e2e8f0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $prod['name'] }}</div>
                    <div style="font-size:0.75rem;color:var(--text-muted)">{{ $prod['category'] }}</div>
                </div>
                <i data-lucide="download" style="width:14px;height:14px;color:var(--text-faint);margin-left:auto;flex-shrink:0"></i>
            </a>
            @endforeach
        </div>
    </div>

    <div class="cp-help-card">
        <i data-lucide="life-buoy" style="width:24px;height:24px;color:#00C896"></i>
        <div>
            <p class="cp-help-title">Need help?</p>
            <p class="cp-help-text">Our team is available Mon–Fri 8 am – 6 pm (WAT). Email
                <a href="mailto:support@opeshealthsystems.com" class="auth-link">support@opeshealthsystems.com</a>
                or visit our <a href="{{ route('contact', ['locale' => app()->getLocale()]) }}" class="auth-link">contact page</a>.
            </p>
        </div>
    </div>
</x-layouts.customer>

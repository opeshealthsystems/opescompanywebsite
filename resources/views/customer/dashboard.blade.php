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
        <div class="cp-stat-card">
            <div class="cp-stat-icon" style="background:rgba(0,200,150,0.1)">
                <i data-lucide="key" style="width:20px;height:20px;color:#00C896"></i>
            </div>
            <div>
                <p class="cp-stat-value">0</p>
                <p class="cp-stat-label">Active Licenses</p>
            </div>
        </div>
        <div class="cp-stat-card">
            <div class="cp-stat-icon" style="background:rgba(26,111,232,0.1)">
                <i data-lucide="ticket" style="width:20px;height:20px;color:#1A6FE8"></i>
            </div>
            <div>
                <p class="cp-stat-value">0</p>
                <p class="cp-stat-label">Open Tickets</p>
            </div>
        </div>
        <div class="cp-stat-card">
            <div class="cp-stat-icon" style="background:rgba(234,179,8,0.1)">
                <i data-lucide="bug" style="width:20px;height:20px;color:#eab308"></i>
            </div>
            <div>
                <p class="cp-stat-value">0</p>
                <p class="cp-stat-label">Bug Reports</p>
            </div>
        </div>
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
                <span class="cp-badge-coming-soon">Coming soon</span>
            </div>
            <div class="cp-empty-state">
                <i data-lucide="message-circle" style="width:40px;height:40px;color:#334155"></i>
                <p>No open tickets.</p>
                <p style="font-size:0.8125rem">Ticket system launching soon — contact us directly for urgent issues.</p>
            </div>
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

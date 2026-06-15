<div class="opes-login-root">
    <div class="opes-login-frame">

        {{-- LEFT BRAND PANEL --}}
        <div class="opes-left">
            <div class="opes-glow-top"></div>
            <div class="opes-glow-bottom"></div>

            <div class="opes-logo-row">
                <div class="opes-logo-mark">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#0f172a" stroke-width="2.5">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="2" y1="12" x2="22" y2="12"/>
                        <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
                    </svg>
                </div>
                <span class="opes-brand-name">OPES <em>Platform</em></span>
            </div>

            <div class="opes-hero">
                <h1>Operations &amp;<br><em>Support</em> Hub</h1>
                <p>Centralised admin panel for customers, licenses, tickets and team management.</p>

                <div class="opes-features">
                    <div class="opes-feat">
                        <div class="opes-feat-icon">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#00C896" stroke-width="2">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
                                <path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
                            </svg>
                        </div>
                        Role-based access control
                    </div>
                    <div class="opes-feat">
                        <div class="opes-feat-icon">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#00C896" stroke-width="2">
                                <path d="M2 9a3 3 0 0 1 0 6v2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2a3 3 0 0 1 0-6V7a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2Z"/>
                            </svg>
                        </div>
                        Ticket &amp; support management
                    </div>
                    <div class="opes-feat">
                        <div class="opes-feat-icon">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#00C896" stroke-width="2">
                                <line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/>
                                <line x1="6" y1="20" x2="6" y2="14"/><line x1="2" y1="20" x2="22" y2="20"/>
                            </svg>
                        </div>
                        Real-time reports dashboard
                    </div>
                </div>
            </div>

            <div class="opes-version">OPES v1.0 · Admin Panel</div>
        </div>

        {{-- RIGHT FORM PANEL --}}
        <div class="opes-right">
            <div class="opes-form-head">
                <h2>Welcome back</h2>
                <p>Sign in to your admin account to continue</p>
            </div>

            <form wire:submit.prevent="authenticate" class="opes-form">
                {{ $this->form }}

                <button type="submit" class="opes-submit-btn">
                    Sign in to OPES
                </button>
            </form>

            <div class="opes-divider">
                <div class="opes-divider-line"></div>
                <span>Quick demo access</span>
                <div class="opes-divider-line"></div>
            </div>

            <div class="opes-demo-grid">
                <button type="button" class="opes-demo-btn opes-demo-admin"
                    wire:click="loginAsDemo('admin@demo.opes')">
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                        <path d="m9 12 2 2 4-4"/>
                    </svg>
                    Admin
                </button>
                <button type="button" class="opes-demo-btn opes-demo-support"
                    wire:click="loginAsDemo('support@demo.opes')">
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 14h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-7a9 9 0 0 1 18 0v7a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h3"/>
                    </svg>
                    Support
                </button>
                <button type="button" class="opes-demo-btn opes-demo-customer"
                    wire:click="loginAsDemo('customer@demo.opes')">
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
                    </svg>
                    Customer
                </button>
                <button type="button" class="opes-demo-btn opes-demo-tester"
                    wire:click="loginAsDemo('tester@demo.opes')">
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M10 2v7.527a2 2 0 0 1-.211.896L4.72 20.55a1 1 0 0 0 .9 1.45h12.76a1 1 0 0 0 .9-1.45l-5.069-10.127A2 2 0 0 1 14 9.527V2"/>
                        <path d="M8.5 2h7"/>
                    </svg>
                    Tester
                </button>
            </div>
        </div>

    </div>

    <style>
        /* Override Filament's body/layout wrappers */
        html, body, .fi-body, .fi-layout, .fi-simple-layout,
        .fi-simple-main, main {
            background: #0a0f1e !important;
            min-height: 100vh;
        }
        .fi-layout, .fi-simple-layout {
            display: block !important;
        }
        .fi-simple-main {
            padding: 0 !important;
            max-width: none !important;
            width: 100% !important;
        }

        /* Root: take full viewport */
        .opes-login-root {
            position: fixed;
            inset: 0;
            z-index: 50;
            overflow-y: auto;
            background: #0a0f1e;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            font-family: 'Inter', system-ui, sans-serif;
        }

        /* Split frame */
        .opes-login-frame {
            display: flex;
            width: 100%;
            max-width: 920px;
            min-height: 560px;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 40px 80px rgba(0,0,0,.6);
            border: 1px solid #1e293b;
        }

        /* ── LEFT PANEL ── */
        .opes-left {
            width: 42%;
            background: linear-gradient(145deg, #0d1829 0%, #0f172a 60%, #061a12 100%);
            padding: 48px 40px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
            flex-shrink: 0;
        }
        .opes-glow-top {
            position: absolute;
            top: -80px; right: -80px;
            width: 280px; height: 280px;
            background: radial-gradient(circle, rgba(0,200,150,.12) 0%, transparent 70%);
            pointer-events: none;
        }
        .opes-glow-bottom {
            position: absolute;
            bottom: -60px; left: -40px;
            width: 200px; height: 200px;
            background: radial-gradient(circle, rgba(0,200,150,.07) 0%, transparent 70%);
            pointer-events: none;
        }
        .opes-logo-row {
            display: flex;
            align-items: center;
            gap: 10px;
            position: relative;
        }
        .opes-logo-mark {
            width: 36px; height: 36px;
            background: #00C896;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .opes-brand-name {
            font-size: 17px;
            font-weight: 700;
            color: #e2e8f0;
            letter-spacing: -.3px;
        }
        .opes-brand-name em {
            font-style: normal;
            color: #00C896;
        }
        .opes-hero {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding-top: 32px;
            position: relative;
        }
        .opes-hero h1 {
            font-size: 27px;
            font-weight: 800;
            color: #f1f5f9;
            line-height: 1.2;
            letter-spacing: -.5px;
            margin: 0;
        }
        .opes-hero h1 em {
            font-style: normal;
            color: #00C896;
        }
        .opes-hero > p {
            margin: 10px 0 0;
            font-size: 13px;
            color: #64748b;
            line-height: 1.6;
        }
        .opes-features {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-top: 28px;
        }
        .opes-feat {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 12px;
            color: #94a3b8;
        }
        .opes-feat-icon {
            width: 28px; height: 28px;
            border-radius: 6px;
            background: rgba(0,200,150,.1);
            border: 1px solid rgba(0,200,150,.2);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .opes-version {
            font-size: 10px;
            color: #334155;
            position: relative;
        }

        /* ── RIGHT PANEL ── */
        .opes-right {
            flex: 1;
            background: #111827;
            padding: 48px 44px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            overflow-y: auto;
        }
        .opes-form-head {
            margin-bottom: 24px;
        }
        .opes-form-head h2 {
            font-size: 22px;
            font-weight: 700;
            color: #f1f5f9;
            letter-spacing: -.3px;
            margin: 0;
        }
        .opes-form-head p {
            font-size: 13px;
            color: #64748b;
            margin: 4px 0 0;
        }

        /* Form spacing */
        .opes-form {
            display: flex;
            flex-direction: column;
            gap: 0;
        }
        .opes-form > * + * {
            margin-top: 0;
        }

        /* Override Filament form field styles inside our right panel */
        .opes-right .fi-fo-field-wrp {
            margin-bottom: 14px;
        }
        .opes-right .fi-input-wrp {
            background: #1e293b !important;
            border-color: #334155 !important;
            border-radius: 8px !important;
        }
        .opes-right input[type="email"],
        .opes-right input[type="password"],
        .opes-right input[type="text"] {
            background: #1e293b !important;
            color: #e2e8f0 !important;
            border-color: #334155 !important;
            border-radius: 8px !important;
            font-size: 13px !important;
        }
        .opes-right input[type="email"]:focus,
        .opes-right input[type="password"]:focus {
            border-color: #00C896 !important;
            outline: none !important;
            box-shadow: 0 0 0 1px #00C896 !important;
        }
        .opes-right input::placeholder {
            color: #475569 !important;
        }
        .opes-right .fi-input {
            background: #1e293b !important;
            border-color: #334155 !important;
            color: #e2e8f0 !important;
        }
        .opes-right label,
        .opes-right .fi-label {
            color: #94a3b8 !important;
            font-size: 11px !important;
            font-weight: 600 !important;
            letter-spacing: .04em !important;
            text-transform: uppercase !important;
        }
        .opes-right .fi-checkbox-input {
            accent-color: #00C896 !important;
        }
        .opes-right .fi-link,
        .opes-right a {
            color: #00C896 !important;
        }
        /* Remove gap between form fields that Filament adds */
        .opes-right .fi-fo-component-ctn,
        .opes-right [class*="space-y"] {
            --tw-space-y-reverse: 0;
        }
        .opes-right form > div {
            gap: 0 !important;
        }

        /* Submit button */
        .opes-submit-btn {
            width: 100%;
            background: #00C896;
            color: #0f172a;
            font-size: 14px;
            font-weight: 700;
            padding: 11px 16px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            font-family: inherit;
            letter-spacing: .01em;
            margin-top: 16px;
            transition: opacity .15s;
        }
        .opes-submit-btn:hover { opacity: .88; }
        .opes-submit-btn:active { opacity: .75; }

        /* Divider */
        .opes-divider {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 20px 0 14px;
        }
        .opes-divider-line {
            flex: 1;
            height: 1px;
            background: #1e293b;
        }
        .opes-divider span {
            font-size: 11px;
            color: #334155;
            white-space: nowrap;
        }

        /* Demo grid */
        .opes-demo-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
        }
        .opes-demo-btn {
            padding: 8px 10px;
            border-radius: 7px;
            border: 1px solid;
            font-size: 11px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: opacity .15s;
            font-family: inherit;
            background: transparent;
        }
        .opes-demo-btn:hover { opacity: .75; }
        .opes-demo-admin    { background: rgba(0,200,150,.08);   border-color: rgba(0,200,150,.3);   color: #00C896; }
        .opes-demo-support  { background: rgba(96,165,250,.08);  border-color: rgba(96,165,250,.3);  color: #60a5fa; }
        .opes-demo-customer { background: rgba(192,132,252,.08); border-color: rgba(192,132,252,.3); color: #c084fc; }
        .opes-demo-tester   { background: rgba(251,191,36,.08);  border-color: rgba(251,191,36,.3);  color: #fbbf24; }

        /* Mobile */
        @media (max-width: 640px) {
            .opes-login-frame { flex-direction: column; min-height: auto; }
            .opes-left { width: 100%; padding: 28px 24px; }
            .opes-right { padding: 28px 24px; }
            .opes-hero h1 { font-size: 22px; }
            .opes-features { display: none; }
        }
    </style>
</div>

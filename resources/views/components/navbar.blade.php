@php $locale = app()->getLocale(); @endphp

<nav class="site-nav" id="siteNav">
    <a href="{{ url($locale) }}" class="nav-logo">
        <div class="nav-logo-mark"><i data-lucide="circle-dashed" style="width:20px;height:20px"></i></div>
        <div class="nav-logo-text">
            <div class="nav-logo-name">Opes <span>Health</span> Systems</div>
            <div class="nav-logo-tagline">Digital Health Infrastructure for Africa</div>
        </div>
    </a>

    <button class="nav-toggle" type="button" aria-label="Toggle menu" aria-expanded="false">
        <i data-lucide="menu" class="nav-toggle-open" style="width:22px;height:22px"></i>
        <i data-lucide="x" class="nav-toggle-close" style="width:22px;height:22px"></i>
    </button>

    <div class="nav-collapse">
    <div class="nav-links">

        {{-- Products dropdown --}}
        <div class="nav-dropdown-wrap">
            <a href="{{ url($locale.'/products') }}" class="nav-dropdown-trigger">
                {{ __('nav.products') }}
                <i data-lucide="chevron-down" style="width:13px;height:13px;opacity:.5;transition:transform 0.2s"></i>
            </a>
            <div class="nav-dropdown">
                <div class="nav-dd-section">
                    <div class="nav-dd-section-label">Core Platform</div>
                    <a href="{{ url($locale.'/products/opescare') }}" class="nav-dd-item">
                        <i data-lucide="fingerprint" style="width:14px;height:14px;color:#00C896"></i>
                        <div>
                            <div class="nav-dd-name">OPESCare</div>
                            <div class="nav-dd-sub">Universal Health ID</div>
                        </div>
                    </a>
                    <a href="{{ url($locale.'/products/opes-emr') }}" class="nav-dd-item">
                        <i data-lucide="stethoscope" style="width:14px;height:14px;color:#00C896"></i>
                        <div>
                            <div class="nav-dd-name">OPES EMR</div>
                            <div class="nav-dd-sub">Electronic Medical Records</div>
                        </div>
                    </a>
                    <a href="{{ url($locale.'/products/opes-hospital-his') }}" class="nav-dd-item">
                        <i data-lucide="hospital" style="width:14px;height:14px;color:#00C896"></i>
                        <div>
                            <div class="nav-dd-name">OPES Hospital HIS</div>
                            <div class="nav-dd-sub">Hospital Information System</div>
                        </div>
                    </a>
                </div>
                <div class="nav-dd-section">
                    <div class="nav-dd-section-label">Diagnostics</div>
                    <a href="{{ url($locale.'/products/opes-lab') }}" class="nav-dd-item">
                        <i data-lucide="microscope" style="width:14px;height:14px;color:#1A6FE8"></i>
                        <div>
                            <div class="nav-dd-name">OPES Lab</div>
                            <div class="nav-dd-sub">Laboratory Information System</div>
                        </div>
                    </a>
                    <a href="{{ url($locale.'/products/pharmis') }}" class="nav-dd-item">
                        <i data-lucide="pill" style="width:14px;height:14px;color:#1A6FE8"></i>
                        <div>
                            <div class="nav-dd-name">PHARMIS</div>
                            <div class="nav-dd-sub">Pharmacy Management</div>
                        </div>
                    </a>
                    <a href="{{ url($locale.'/products/radis') }}" class="nav-dd-item">
                        <i data-lucide="scan" style="width:14px;height:14px;color:#1A6FE8"></i>
                        <div>
                            <div class="nav-dd-name">RADIS</div>
                            <div class="nav-dd-sub">Radiology Information System</div>
                        </div>
                    </a>
                </div>
                <div class="nav-dd-footer">
                    <a href="{{ url($locale.'/products') }}">
                        View all 22 systems
                        <i data-lucide="arrow-right" style="width:11px;height:11px"></i>
                    </a>
                </div>
            </div>
        </div>

        {{-- Solutions dropdown --}}
        <div class="nav-dropdown-wrap">
            <a href="{{ url($locale.'/solutions') }}" class="nav-dropdown-trigger">
                {{ __('nav.solutions') }}
                <i data-lucide="chevron-down" style="width:13px;height:13px;opacity:.5;transition:transform 0.2s"></i>
            </a>
            <div class="nav-dropdown nav-dropdown--narrow">
                <a href="{{ url($locale.'/solutions') }}" class="nav-dd-item">
                    <i data-lucide="hospital" style="width:14px;height:14px;color:#00C896"></i>
                    <div>
                        <div class="nav-dd-name">Hospitals</div>
                        <div class="nav-dd-sub">Full-suite digital transformation</div>
                    </div>
                </a>
                <a href="{{ url($locale.'/solutions') }}" class="nav-dd-item">
                    <i data-lucide="stethoscope" style="width:14px;height:14px;color:#1A6FE8"></i>
                    <div>
                        <div class="nav-dd-name">Clinics</div>
                        <div class="nav-dd-sub">EMR & triage for smaller facilities</div>
                    </div>
                </a>
                <a href="{{ url($locale.'/solutions') }}" class="nav-dd-item">
                    <i data-lucide="microscope" style="width:14px;height:14px;color:#00C896"></i>
                    <div>
                        <div class="nav-dd-name">Laboratories</div>
                        <div class="nav-dd-sub">OPES Lab + OPESCare</div>
                    </div>
                </a>
                <a href="{{ url($locale.'/solutions') }}" class="nav-dd-item">
                    <i data-lucide="shield-check" style="width:14px;height:14px;color:#1A6FE8"></i>
                    <div>
                        <div class="nav-dd-name">Government & NGOs</div>
                        <div class="nav-dd-sub">UHC IS + population health</div>
                    </div>
                </a>
                <div class="nav-dd-footer">
                    <a href="{{ url($locale.'/solutions') }}">
                        View all solutions
                        <i data-lucide="arrow-right" style="width:11px;height:11px"></i>
                    </a>
                </div>
            </div>
        </div>

        {{-- Platform dropdown --}}
        <div class="nav-dropdown-wrap">
            <a href="{{ url($locale.'/architecture') }}" class="nav-dropdown-trigger">
                Platform
                <i data-lucide="chevron-down" style="width:13px;height:13px;opacity:.5;transition:transform 0.2s"></i>
            </a>
            <div class="nav-dropdown nav-dropdown--narrow">
                <a href="{{ url($locale.'/architecture') }}" class="nav-dd-item">
                    <i data-lucide="cpu" style="width:14px;height:14px;color:#00C896"></i>
                    <div>
                        <div class="nav-dd-name">Architecture</div>
                        <div class="nav-dd-sub">HL7 FHIR, Health ID, security</div>
                    </div>
                </a>
                <a href="{{ url($locale.'/implementation') }}" class="nav-dd-item">
                    <i data-lucide="map" style="width:14px;height:14px;color:#1A6FE8"></i>
                    <div>
                        <div class="nav-dd-name">Implementation</div>
                        <div class="nav-dd-sub">90-day deployment framework</div>
                    </div>
                </a>
                <a href="{{ url($locale.'/support') }}" class="nav-dd-item">
                    <i data-lucide="headphones" style="width:14px;height:14px;color:#00C896"></i>
                    <div>
                        <div class="nav-dd-name">Support & SLA</div>
                        <div class="nav-dd-sub">Bronze to Platinum tiers</div>
                    </div>
                </a>
                <a href="{{ url($locale.'/academy') }}" class="nav-dd-item">
                    <i data-lucide="graduation-cap" style="width:14px;height:14px;color:#1A6FE8"></i>
                    <div>
                        <div class="nav-dd-name">OPES Academy</div>
                        <div class="nav-dd-sub">Certification & training</div>
                    </div>
                </a>
                <a href="{{ url($locale.'/compliance') }}" class="nav-dd-item">
                    <i data-lucide="shield-check" style="width:14px;height:14px;color:#00C896"></i>
                    <div>
                        <div class="nav-dd-name">Compliance & Trust</div>
                        <div class="nav-dd-sub">Security, OHADA, data sovereignty</div>
                    </div>
                </a>
                <a href="{{ url($locale.'/clinical-governance') }}" class="nav-dd-item">
                    <i data-lucide="heart-pulse" style="width:14px;height:14px;color:#1A6FE8"></i>
                    <div>
                        <div class="nav-dd-name">Clinical Governance</div>
                        <div class="nav-dd-sub">Patient safety & CDSS oversight</div>
                    </div>
                </a>
            </div>
        </div>

        <a href="{{ url($locale.'/about') }}">{{ __('nav.about') }}</a>
        <a href="{{ url($locale.'/partnerships') }}">{{ __('nav.partnerships') }}</a>
        <a href="{{ url($locale.'/pricing') }}">{{ __('nav.pricing') }}</a>
        <a href="{{ url($locale.'/blog') }}">{{ __('nav.blog') }}</a>
        <a href="{{ url($locale.'/contact') }}">{{ __('nav.contact') }}</a>
    </div>

    <div class="nav-right">
        <x-language-switcher />

        @auth
        {{-- Logged-in account menu --}}
        <div class="nav-account">
            <button class="nav-account-avatar" aria-label="Account menu" type="button">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </button>
            <div class="nav-account-drop">
                <div class="nav-account-drop-header">
                    <div class="nav-account-drop-name">{{ auth()->user()->name }}</div>
                    <div class="nav-account-drop-email">{{ auth()->user()->email }}</div>
                </div>
                <div class="nav-account-drop-divider"></div>
                <a href="{{ route('customer.dashboard', ['locale' => $locale]) }}" class="nav-account-drop-item">
                    <i data-lucide="layout-dashboard" style="width:14px;height:14px"></i> Dashboard
                </a>
                <a href="{{ route('customer.profile', ['locale' => $locale]) }}" class="nav-account-drop-item">
                    <i data-lucide="user" style="width:14px;height:14px"></i> Profile
                </a>
                <a href="{{ route('customer.licenses', ['locale' => $locale]) }}" class="nav-account-drop-item">
                    <i data-lucide="key" style="width:14px;height:14px"></i> Licenses
                </a>
                <a href="{{ route('customer.tickets', ['locale' => $locale]) }}" class="nav-account-drop-item">
                    <i data-lucide="ticket" style="width:14px;height:14px"></i> Support
                </a>
                <div class="nav-account-drop-divider"></div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="nav-account-drop-item nav-account-drop-logout">
                        <i data-lucide="log-out" style="width:14px;height:14px"></i> Sign out
                    </button>
                </form>
            </div>
        </div>
        @else
        {{-- Guest login link --}}
        <a href="{{ route('login') }}" class="nav-account-login" title="Sign in">
            <i data-lucide="user-circle" style="width:20px;height:20px"></i>
        </a>
        @endauth

        <a href="{{ url($locale.'/contact') }}" class="btn-cta">
            {{ __('nav.book_demo') }}
        </a>
    </div>
    </div>
</nav>

<script>
(function () {
    var nav = document.getElementById('siteNav');
    if (!nav) return;
    var toggle = nav.querySelector('.nav-toggle');
    if (!toggle) return;
    toggle.addEventListener('click', function () {
        var open = nav.classList.toggle('nav-open');
        toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    });
    // Close the drawer after tapping a link.
    nav.querySelectorAll('.nav-collapse a').forEach(function (link) {
        link.addEventListener('click', function () {
            nav.classList.remove('nav-open');
            toggle.setAttribute('aria-expanded', 'false');
        });
    });
})();
</script>

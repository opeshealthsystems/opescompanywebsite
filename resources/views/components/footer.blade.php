@php
$locale = app()->getLocale();
$segments = request()->segments();
if (empty($segments)) { $segments = [$locale]; }
$toEn = $segments; $toEn[0] = 'en';
$toFr = $segments; $toFr[0] = 'fr';
$current = $locale;
@endphp

<footer class="site-footer">
    <div class="footer-inner">
        <div class="footer-brand">
            <a href="{{ url($locale) }}" class="nav-logo" style="text-decoration:none;margin-bottom:10px">
                <div class="nav-logo-mark"><i data-lucide="circle-dashed" style="width:20px;height:20px"></i></div>
                <div class="nav-logo-text">
                    <div class="nav-logo-name">Opes <span>Health</span> Systems</div>
                </div>
            </a>
            <p>Africa's most complete digital health ecosystem. Built in Cameroon for the CEMAC region and all of Africa. Bilingual · Interoperable · Ministry-Aligned.</p>
        </div>

        <div class="footer-col">
            <h5>Products</h5>
            <a href="{{ url($locale.'/products/opescare') }}">
                <i data-lucide="fingerprint" style="width:12px;height:12px"></i>OPESCare · Health ID
            </a>
            <a href="{{ url($locale.'/products/opes-emr') }}">
                <i data-lucide="stethoscope" style="width:12px;height:12px"></i>OPES EMR
            </a>
            <a href="{{ url($locale.'/products/opes-hospital-his') }}">
                <i data-lucide="hospital" style="width:12px;height:12px"></i>OPES Hospital HIS
            </a>
            <a href="{{ url($locale.'/products/opes-triage') }}">
                <i data-lucide="timer" style="width:12px;height:12px"></i>Opes Triage
            </a>
            <a href="{{ url($locale.'/products') }}" class="footer-highlight">
                <i data-lucide="layout-grid" style="width:12px;height:12px"></i>View All 22 →
            </a>
        </div>

        <div class="footer-col">
            <h5>Platform</h5>
            <a href="{{ url($locale.'/architecture') }}">
                <i data-lucide="cpu" style="width:12px;height:12px"></i>Architecture
            </a>
            <a href="{{ url($locale.'/implementation') }}">
                <i data-lucide="map" style="width:12px;height:12px"></i>Implementation
            </a>
            <a href="{{ url($locale.'/support') }}">
                <i data-lucide="headphones" style="width:12px;height:12px"></i>Support & SLA
            </a>
            <a href="{{ url($locale.'/academy') }}">
                <i data-lucide="graduation-cap" style="width:12px;height:12px"></i>OPES Academy
            </a>
            <a href="{{ url($locale.'/clinical-governance') }}">
                <i data-lucide="heart-pulse" style="width:12px;height:12px"></i>Clinical Governance
            </a>
            <a href="{{ url($locale.'/interoperability') }}">
                <i data-lucide="share-2" style="width:12px;height:12px"></i>Interoperability
            </a>
            <a href="{{ url($locale.'/quality') }}">
                <i data-lucide="badge-check" style="width:12px;height:12px"></i>Quality (QMS)
            </a>
            <a href="{{ url($locale.'/risk') }}">
                <i data-lucide="shield-alert" style="width:12px;height:12px"></i>Risk Management
            </a>
            <a href="{{ url($locale.'/national-platform') }}">
                <i data-lucide="building-2" style="width:12px;height:12px"></i>National Platform
            </a>
        </div>

        <div class="footer-col">
            <h5>Company</h5>
            <a href="{{ url($locale.'/about') }}">
                <i data-lucide="info" style="width:12px;height:12px"></i>About OPES
            </a>
            @auth
                @if(auth()->user()->hasAnyRole(['customer','admin','super_admin','support']))
                <a href="{{ url($locale.'/strategy') }}">
                    <i data-lucide="map" style="width:12px;height:12px"></i>Strategy 2026–2031
                </a>
                <a href="{{ url($locale.'/financial-model') }}">
                    <i data-lucide="dollar-sign" style="width:12px;height:12px"></i>Revenue Model
                </a>
                @endif
            @else
                <a href="{{ route('login') }}" title="Sign in to view">
                    <i data-lucide="lock" style="width:12px;height:12px"></i>Strategy 2026–2031
                </a>
            @endauth
            <a href="{{ url($locale.'/partner-program') }}">
                <i data-lucide="handshake" style="width:12px;height:12px"></i>Partner Programme
            </a>
            <a href="{{ url($locale.'/partnerships') }}">
                <i data-lucide="handshake" style="width:12px;height:12px"></i>Partnership
            </a>
            <a href="{{ url($locale.'/pricing') }}">
                <i data-lucide="tag" style="width:12px;height:12px"></i>Pricing
            </a>
            <a href="{{ url($locale.'/blog') }}">
                <i data-lucide="rss" style="width:12px;height:12px"></i>Blog
            </a>
            <a href="{{ url($locale.'/contact') }}">
                <i data-lucide="mail" style="width:12px;height:12px"></i>Contact
            </a>
        </div>

        <div class="footer-col">
            <h5>Legal</h5>
            <a href="{{ url($locale.'/privacy') }}">
                <i data-lucide="shield" style="width:12px;height:12px"></i>Privacy Policy
            </a>
            <a href="{{ url($locale.'/terms') }}">
                <i data-lucide="file-text" style="width:12px;height:12px"></i>Terms of Use
            </a>
            <a href="{{ url($locale.'/compliance') }}">
                <i data-lucide="scale" style="width:12px;height:12px"></i>Compliance & Trust
            </a>
            <a href="mailto:{{ config('company.email') }}">
                <i data-lucide="mail" style="width:12px;height:12px"></i>{{ config('company.email') }}
            </a>
        </div>
    </div>

    <div class="footer-bottom">
        <p>© {{ date('Y') }} OPES Health Systems SARL · {{ config('company.address') }} · OHADA Law</p>
        <div class="footer-lang">
            <a href="{{ url(implode('/', $toEn)) }}" class="{{ $current === 'en' ? 'active' : '' }}">EN</a>
            <a href="{{ url(implode('/', $toFr)) }}" class="{{ $current === 'fr' ? 'active' : '' }}">FR</a>
        </div>
    </div>
</footer>

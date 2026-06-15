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
            <div class="nav-logo" style="text-decoration:none">
                <div class="nav-logo-mark">O</div>
                <div class="nav-logo-text">Opes <span>Health</span> Systems</div>
            </div>
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
            <h5>Company</h5>
            <a href="{{ url($locale.'/about') }}">
                <i data-lucide="info" style="width:12px;height:12px"></i>About OPES
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
                <i data-lucide="scale" style="width:12px;height:12px"></i>OHADA Compliance
            </a>
            <a href="mailto:info@opeshealthsystems.com">
                <i data-lucide="mail" style="width:12px;height:12px"></i>info@opeshealthsystems.com
            </a>
        </div>
    </div>

    <div class="footer-bottom">
        <p>© {{ date('Y') }} OPES Health Systems SARL · Bonamousadi, Douala, Cameroon · OHADA Law</p>
        <div class="footer-lang">
            <a href="{{ url(implode('/', $toEn)) }}" class="{{ $current === 'en' ? 'active' : '' }}">EN</a>
            <a href="{{ url(implode('/', $toFr)) }}" class="{{ $current === 'fr' ? 'active' : '' }}">FR</a>
        </div>
    </div>
</footer>

@php
$locale = app()->getLocale();
$isFr = $locale === 'fr';
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
            <p>{{ $isFr ? 'L\'écosystème de santé numérique le plus complet d\'Afrique. Conçu au Cameroun pour la région CEMAC et toute l\'Afrique. Bilingue · Interopérable · Aligné Ministère de la Santé.' : 'Africa\'s most complete digital health ecosystem. Built in Cameroon for the CEMAC region and all of Africa. Bilingual · Interoperable · Ministry-Aligned.' }}</p>
        </div>

        <div class="footer-col">
            <h5>{{ $isFr ? 'Produits' : 'Products' }}</h5>
            <a href="{{ url($locale.'/health-os') }}" class="footer-highlight">
                <i data-lucide="layers" style="width:12px;height:12px"></i>OPES Health OS →
            </a>
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
                <i data-lucide="layout-grid" style="width:12px;height:12px"></i>{{ $isFr ? 'Voir les 22 →' : 'View All 22 →' }}
            </a>
        </div>

        <div class="footer-col">
            <h5>{{ $isFr ? 'Plateforme' : 'Platform' }}</h5>
            <a href="{{ url($locale.'/architecture') }}">
                <i data-lucide="cpu" style="width:12px;height:12px"></i>{{ $isFr ? 'Architecture' : 'Architecture' }}
            </a>
            <a href="{{ url($locale.'/architecture-diagrams') }}">
                <i data-lucide="git-branch" style="width:12px;height:12px"></i>{{ $isFr ? 'Diagrammes architecture' : 'Architecture diagrams' }}
            </a>
            <a href="{{ url($locale.'/implementation') }}">
                <i data-lucide="map" style="width:12px;height:12px"></i>{{ $isFr ? 'Implémentation' : 'Implementation' }}
            </a>
            <a href="{{ url($locale.'/support') }}">
                <i data-lucide="headphones" style="width:12px;height:12px"></i>Support & SLA
            </a>
            <a href="{{ url($locale.'/academy') }}">
                <i data-lucide="graduation-cap" style="width:12px;height:12px"></i>OPES Academy
            </a>
            <a href="{{ url($locale.'/clinical-governance') }}">
                <i data-lucide="heart-pulse" style="width:12px;height:12px"></i>{{ $isFr ? 'Gouvernance clinique' : 'Clinical Governance' }}
            </a>
            <a href="{{ url($locale.'/interoperability') }}">
                <i data-lucide="share-2" style="width:12px;height:12px"></i>{{ $isFr ? 'Interopérabilité' : 'Interoperability' }}
            </a>
            <a href="{{ url($locale.'/quality') }}">
                <i data-lucide="badge-check" style="width:12px;height:12px"></i>{{ $isFr ? 'Qualité (SGQ)' : 'Quality (QMS)' }}
            </a>
            @auth
            @if(auth()->user()->hasAnyRole(['admin','super_admin']))
            <a href="{{ url($locale.'/risk') }}">
                <i data-lucide="shield-alert" style="width:12px;height:12px"></i>{{ $isFr ? 'Gestion des risques' : 'Risk Management' }}
            </a>
            @endif
            @endauth
            <a href="{{ url($locale.'/national-platform') }}">
                <i data-lucide="building-2" style="width:12px;height:12px"></i>{{ $isFr ? 'Plateforme nationale' : 'National Platform' }}
            </a>
        </div>

        <div class="footer-col">
            <h5>{{ $isFr ? 'Entreprise' : 'Company' }}</h5>
            <a href="{{ url($locale.'/about') }}">
                <i data-lucide="info" style="width:12px;height:12px"></i>{{ $isFr ? 'À propos d\'OPES' : 'About OPES' }}
            </a>
            @auth
                @if(auth()->user()->hasAnyRole(['admin','super_admin']))
                <a href="{{ url($locale.'/strategy') }}">
                    <i data-lucide="map" style="width:12px;height:12px"></i>{{ $isFr ? 'Stratégie 2026–2031' : 'Strategy 2026–2031' }}
                </a>
                <a href="{{ url($locale.'/financial-model') }}">
                    <i data-lucide="dollar-sign" style="width:12px;height:12px"></i>{{ $isFr ? 'Modèle de revenus' : 'Revenue Model' }}
                </a>
                <a href="{{ url($locale.'/sales-playbook') }}">
                    <i data-lucide="target" style="width:12px;height:12px"></i>{{ $isFr ? 'Stratégie commerciale' : 'Sales Playbook' }}
                </a>
                <a href="{{ url($locale.'/government-proposal') }}">
                    <i data-lucide="file-text" style="width:12px;height:12px"></i>{{ $isFr ? 'Modèle proposition gouvernementale' : 'Gov. Proposal Template' }}
                </a>
                <a href="{{ url($locale.'/investor-pitch') }}">
                    <i data-lucide="trending-up" style="width:12px;height:12px"></i>{{ $isFr ? 'Présentation investisseurs' : 'Investor Pitch' }}
                </a>
                @endif
            @endauth
            <a href="{{ url($locale.'/partner-program') }}">
                <i data-lucide="handshake" style="width:12px;height:12px"></i>{{ $isFr ? 'Programme partenaires' : 'Partner Programme' }}
            </a>
            <a href="{{ url($locale.'/partnerships') }}">
                <i data-lucide="handshake" style="width:12px;height:12px"></i>{{ $isFr ? 'Partenariat' : 'Partnership' }}
            </a>
            <a href="{{ url($locale.'/become-a-partner') }}">
                <i data-lucide="user-plus" style="width:12px;height:12px"></i>{{ $isFr ? 'Devenir partenaire' : 'Become a Partner' }}
            </a>
            <a href="{{ url($locale.'/join-testers') }}">
                <i data-lucide="flask-conical" style="width:12px;height:12px"></i>{{ $isFr ? 'Rejoindre les testeurs' : 'Join Beta Testers' }}
            </a>
            <a href="{{ url($locale.'/mobile-clinic') }}">
                <i data-lucide="heart" style="width:12px;height:12px"></i>{{ $isFr ? 'Cliniques mobiles' : 'Mobile Clinics' }}
            </a>
            <a href="{{ url($locale.'/book-demo') }}" style="color:#00C896;font-weight:600">
                <i data-lucide="calendar" style="width:12px;height:12px"></i>{{ $isFr ? 'Réserver une démo' : 'Book a Demo' }}
            </a>
            <a href="{{ url($locale.'/pricing') }}">
                <i data-lucide="tag" style="width:12px;height:12px"></i>{{ $isFr ? 'Tarification' : 'Pricing' }}
            </a>
            <a href="{{ url($locale.'/blog') }}">
                <i data-lucide="rss" style="width:12px;height:12px"></i>Blog
            </a>
            <a href="{{ url($locale.'/faq') }}">
                <i data-lucide="help-circle" style="width:12px;height:12px"></i>{{ $isFr ? 'FAQ' : 'FAQ' }}
            </a>
            <a href="{{ url($locale.'/contact') }}">
                <i data-lucide="mail" style="width:12px;height:12px"></i>Contact
            </a>
        </div>

        <div class="footer-col">
            <h5>{{ $isFr ? 'Juridique' : 'Legal' }}</h5>
            <a href="{{ url($locale.'/privacy') }}">
                <i data-lucide="shield" style="width:12px;height:12px"></i>{{ $isFr ? 'Politique de confidentialité' : 'Privacy Policy' }}
            </a>
            <a href="{{ url($locale.'/terms') }}">
                <i data-lucide="file-text" style="width:12px;height:12px"></i>{{ $isFr ? 'Conditions d\'utilisation' : 'Terms of Use' }}
            </a>
            <a href="{{ url($locale.'/compliance') }}">
                <i data-lucide="scale" style="width:12px;height:12px"></i>{{ $isFr ? 'Conformité & confiance' : 'Compliance & Trust' }}
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

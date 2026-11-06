@php $locale = app()->getLocale(); $isFr = $locale === 'fr'; @endphp

<x-layouts.app>

{{-- ── MINISTRY BANNER ─────────────────────────────────────────── --}}
<div class="ministry-banner">
    <i data-lucide="landmark" style="width:15px;height:15px;color:var(--brand-accent);flex-shrink:0"></i>
    <p>
        <strong>{{ $isFr ? 'Des logiciels de santé conçus au Cameroun' : 'Healthcare software designed in Cameroon' }}</strong>
        &nbsp;·&nbsp; {{ $isFr ? 'Pour les équipes de soins en Afrique' : 'For healthcare teams across Africa' }}
    </p>
</div>

{{-- ── HERO SLIDER ──────────────────────────────────────────────── --}}
<div class="hero-wrap" id="heroWrap">
    <div id="heroSlider">

        {{-- SLIDE 1: Platform Overview --}}
        <div class="hero-slide active">
            <div class="hero">
                <div>
                    <div class="hero-eyebrow">
                        <i data-lucide="map-pin" style="width:11px;height:11px"></i>
                        {{ __('home.hero_eyebrow') }}
                    </div>
                    <h1>
                        {{ __('home.hero_title_1') }}<br>
                        <span class="gradient-text">{{ __('home.hero_title_gradient') }}</span>
                    </h1>
                    <p class="hero-sub">{{ __('home.hero_tagline') }}</p>
                    <div class="hero-ctas">
                        <a href="{{ url($locale.'/products') }}" class="btn-primary">
                            <i data-lucide="layout-grid" style="width:15px;height:15px"></i>
                            {{ __('home.cta_explore') }}
                        </a>
                        <a href="{{ url($locale.'/book-demo') }}" class="btn-secondary">
                            <i data-lucide="play-circle" style="width:15px;height:15px;color:var(--brand-accent)"></i>
                            {{ __('home.cta_watch_demo') }}
                        </a>
                    </div>
                    <div class="hero-trust">
                        <div class="trust-item"><div class="num">{{ $products->count() }}</div><div class="label">{{ __('home.trust_22') }}</div></div>
                        <div class="trust-item"><div class="num">EN/FR</div><div class="label">{{ __('home.trust_bilingual') }}</div></div>
                        <div class="trust-item"><div class="num">{{ $isFr ? 'AFRIQUE' : 'AFRICA' }}</div><div class="label">{{ $isFr ? 'Priorité régionale · partenariats mondiaux' : 'Regional focus · global partnerships' }}</div></div>
                        <div class="trust-item"><div class="num">OPES</div><div class="label">{{ __('home.trust_moh') }}</div></div>
                    </div>
                </div>
                <div class="hero-visual">
                    <div class="hero-visual-title">OPES Software Ecosystem</div>
                    <div class="ecosystem-grid">
                        <div class="eco-item hl-teal">
                            <div class="eco-icon-wrap teal"><i data-lucide="fingerprint" style="width:15px;height:15px;color:var(--brand-accent)"></i></div>
                            <div><div class="eco-name" style="color:var(--brand-accent)">OPESCare</div><div class="eco-desc">{{ $isFr ? 'Identifiant santé · Interopérabilité' : 'Health ID · Interoperability' }}</div></div>
                        </div>
                        <div class="eco-item hl-blue">
                            <div class="eco-icon-wrap blue"><i data-lucide="stethoscope" style="width:15px;height:15px;color:var(--brand-accent)"></i></div>
                            <div><div class="eco-name" style="color:var(--brand-accent)">OPES EMR</div><div class="eco-desc">{{ $isFr ? 'Cliniques & petits hôpitaux' : 'Clinics & Small Hospitals' }}</div></div>
                        </div>
                        <div class="eco-item">
                            <div class="eco-icon-wrap neutral"><i data-lucide="microscope" style="width:15px;height:15px;color:var(--text-muted)"></i></div>
                            <div><div class="eco-name">OPES Lab</div><div class="eco-desc">LABIS</div></div>
                        </div>
                        <div class="eco-item">
                            <div class="eco-icon-wrap neutral"><i data-lucide="pill" style="width:15px;height:15px;color:var(--text-muted)"></i></div>
                            <div><div class="eco-name">PHARMIS</div><div class="eco-desc">{{ $isFr ? 'Système pharmacie' : 'Pharmacy IS' }}</div></div>
                        </div>
                        <div class="eco-item">
                            <div class="eco-icon-wrap neutral"><i data-lucide="hospital" style="width:15px;height:15px;color:var(--text-muted)"></i></div>
                            <div><div class="eco-name">OPES Hospital</div><div class="eco-desc">{{ $isFr ? 'SIH complet' : 'Full HIS' }}</div></div>
                        </div>
                        <div class="eco-item hl-teal">
                            <div class="eco-icon-wrap teal"><i data-lucide="timer" style="width:15px;height:15px;color:var(--brand-accent)"></i></div>
                            <div><div class="eco-name" style="color:var(--brand-accent)">Opes Triage</div><div class="eco-desc">{{ $isFr ? 'Autonome · Tout hôpital' : 'Standalone · Any Hospital' }}</div></div>
                        </div>
                        <div class="eco-more">{{ $isFr ? '+16 systèmes spécialisés — Cardiologie, Dentisterie, Gynécologie, Pédiatrie & plus →' : '+16 specialist systems — Cardiology, Dental, OB/GYN, Paediatrics & more →' }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- SLIDE 2: Company Overview --}}
        <div class="hero-slide">
            <div class="hero">
                <div>
                    <div class="hero-eyebrow">
                        <i data-lucide="heart-pulse" style="width:11px;height:11px"></i>
                        {{ __('home.s2_eyebrow') }}
                    </div>
                    <h1>{{ __('home.s2_title') }}<br><span class="gradient-text">{{ __('home.s2_title_grad') }}</span></h1>
                    <p class="hero-sub">{{ __('home.s2_sub') }}</p>
                    <div class="hero-ctas">
                        <a href="{{ url($locale.'/products') }}" class="btn-primary">
                            <i data-lucide="layout-grid" style="width:15px;height:15px"></i>
                            {{ __('home.s2_cta1') }}
                        </a>
                        <a href="{{ url($locale.'/book-demo') }}" class="btn-secondary">
                            <i data-lucide="play-circle" style="width:15px;height:15px;color:var(--brand-accent)"></i>
                            {{ __('home.s2_cta2') }}
                        </a>
                    </div>
                    <p class="hero-slide-support">{{ __('home.s2_support') }}</p>
                </div>
                <div class="hero-visual">
                    <div class="hero-visual-title">{{ $isFr ? 'Familles de plateformes' : 'Platform Families' }}</div>
                    <div class="slide-pillars-grid">
                        <div class="slide-pillar">
                            <i data-lucide="heart-pulse" style="width:18px;height:18px;color:var(--brand-accent)"></i>
                            <div class="slide-pillar-name">{{ $isFr ? 'Clinique' : 'Clinical' }}</div>
                            <div class="slide-pillar-desc">{{ $isFr ? 'DME, SIH, soins spécialisés' : 'EMR, HIS, Specialty Care' }}</div>
                        </div>
                        <div class="slide-pillar">
                            <i data-lucide="microscope" style="width:18px;height:18px;color:var(--brand-accent)"></i>
                            <div class="slide-pillar-name">Diagnostics</div>
                            <div class="slide-pillar-desc">{{ $isFr ? 'Laboratoire, Radiologie, Imagerie' : 'Lab, Radiology, Imaging' }}</div>
                        </div>
                        <div class="slide-pillar">
                            <i data-lucide="share-2" style="width:18px;height:18px;color:var(--brand-accent)"></i>
                            <div class="slide-pillar-name">{{ $isFr ? 'Interopérabilité' : 'Interoperability' }}</div>
                            <div class="slide-pillar-desc">Health ID, HIE, MPI</div>
                        </div>
                        <div class="slide-pillar">
                            <i data-lucide="brain-circuit" style="width:18px;height:18px;color:var(--brand-accent)"></i>
                            <div class="slide-pillar-name">Intelligence</div>
                            <div class="slide-pillar-desc">{{ $isFr ? 'CDSS, Analytique, Surveillance' : 'CDSS, Analytics, Surveillance' }}</div>
                        </div>
                    </div>
                    <div class="eco-more" style="margin-top:10px">{{ $isFr ? '+ Administration & Finance — Facturation, RH, Paie, Inventaire →' : '+ Administration & Finance — Billing, HR, Payroll, Inventory →' }}</div>
                </div>
            </div>
        </div>

        {{-- SLIDE 3: OPES Hospital Platform --}}
        <div class="hero-slide">
            <div class="hero">
                <div>
                    <div class="hero-eyebrow">
                        <i data-lucide="building-2" style="width:11px;height:11px"></i>
                        {{ __('home.s3_eyebrow') }}
                    </div>
                    <h1>{{ __('home.s3_title') }}<br><span class="gradient-text">{{ __('home.s3_title_grad') }}</span></h1>
                    <p class="hero-sub">{{ __('home.s3_sub') }}</p>
                    <div class="hero-ctas">
                        <a href="{{ url($locale.'/products/opes-hospital-his') }}" class="btn-primary">
                            <i data-lucide="building-2" style="width:15px;height:15px"></i>
                            {{ __('home.s3_cta') }}
                        </a>
                    </div>
                    <p class="hero-slide-support">{{ __('home.s3_support') }}</p>
                </div>
                <div class="hero-visual">
                    <div class="hero-visual-title">{{ $isFr ? 'OPES Hospital · Modules clés' : 'OPES Hospital · Key Modules' }}</div>
                    <div class="slide-feature-list">
                        @foreach($isFr ? ['Dossiers médicaux électroniques','Système d\'information hospitalier','Système d\'information de laboratoire','Système d\'information de pharmacie','Système d\'information de radiologie','Facturation & gestion du cycle de revenus','Inventaire & approvisionnement','Modules de soins spécialisés'] : ['Electronic Medical Records','Hospital Information System','Laboratory Information System','Pharmacy Information System','Radiology Information System','Billing & Revenue Cycle Management','Inventory & Procurement','Specialty Care Modules'] as $f)
                        <div class="slide-feature-item">
                            <i data-lucide="check-circle" style="width:13px;height:13px;color:var(--brand-accent);flex-shrink:0"></i>
                            <span>{{ $f }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- SLIDE 4: OPES Care --}}
        <div class="hero-slide">
            <div class="hero">
                <div>
                    <div class="hero-eyebrow">
                        <i data-lucide="network" style="width:11px;height:11px"></i>
                        {{ __('home.s4_eyebrow') }}
                    </div>
                    <h1>{{ __('home.s4_title') }}<br><span class="gradient-text">{{ __('home.s4_title_grad') }}</span></h1>
                    <p class="hero-sub">{{ __('home.s4_sub') }}</p>
                    <div class="hero-ctas">
                        <a href="{{ url($locale.'/products/opescare') }}" class="btn-primary">
                            <i data-lucide="share-2" style="width:15px;height:15px"></i>
                            {{ __('home.s4_cta') }}
                        </a>
                    </div>
                    <p class="hero-slide-support">{{ __('home.s4_support') }}</p>
                </div>
                <div class="hero-visual">
                    <div class="hero-visual-title">OPES Care · Services</div>
                    <div class="slide-feature-list">
                        @foreach($isFr ? ['OPES Health ID','Index principal des patients (MPI)','Échange d\'informations de santé (HIE)','Échange de références','Portail patient','Accès aux dossiers inter-établissements','Services d\'interopérabilité'] : ['OPES Health ID','Master Patient Index (MPI)','Health Information Exchange (HIE)','Referral Exchange','Patient Portal','Cross-Facility Record Access','Interoperability Services'] as $f)
                        <div class="slide-feature-item">
                            <i data-lucide="check-circle" style="width:13px;height:13px;color:var(--brand-accent);flex-shrink:0"></i>
                            <span>{{ $f }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- SLIDE 5: OPES Intelligence --}}
        <div class="hero-slide">
            <div class="hero">
                <div>
                    <div class="hero-eyebrow">
                        <i data-lucide="brain-circuit" style="width:11px;height:11px"></i>
                        {{ __('home.s5_eyebrow') }}
                    </div>
                    <h1>{{ __('home.s5_title') }}<br><span class="gradient-text">{{ __('home.s5_title_grad') }}</span></h1>
                    <p class="hero-sub">{{ __('home.s5_sub') }}</p>
                    <div class="hero-ctas">
                        <a href="{{ url($locale.'/products') }}" class="btn-primary">
                            <i data-lucide="brain-circuit" style="width:15px;height:15px"></i>
                            {{ __('home.s5_cta') }}
                        </a>
                    </div>
                    <p class="hero-slide-support">{{ __('home.s5_support') }}</p>
                </div>
                <div class="hero-visual">
                    <div class="hero-visual-title">{{ $isFr ? 'OPES Intelligence · Capacités' : 'OPES Intelligence · Capabilities' }}</div>
                    <div class="slide-feature-list">
                        @foreach($isFr ? ['Aide à la décision clinique (CDSS)','Triage numérique','Analytique de santé populationnelle','Surveillance des maladies','Tableaux de bord exécutifs','Suivi de la qualité','Perspectives de santé prédictives'] : ['Clinical Decision Support (CDSS)','Digital Triage','Population Health Analytics','Disease Surveillance','Executive Dashboards','Quality Monitoring','Predictive Healthcare Insights'] as $f)
                        <div class="slide-feature-item">
                            <i data-lucide="check-circle" style="width:13px;height:13px;color:var(--brand-accent);flex-shrink:0"></i>
                            <span>{{ $f }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

    </div>{{-- #heroSlider --}}

    <div class="hero-progress-wrap"><div id="heroProgressBar" class="hero-progress-bar"></div></div>
    <div class="hero-slider-nav">
        <button class="hero-dot active" data-target="0" aria-label="Slide 1"></button>
        <button class="hero-dot" data-target="1" aria-label="Slide 2"></button>
        <button class="hero-dot" data-target="2" aria-label="Slide 3"></button>
        <button class="hero-dot" data-target="3" aria-label="Slide 4"></button>
        <button class="hero-dot" data-target="4" aria-label="Slide 5"></button>
    </div>
</div>

{{-- ── CATEGORY TABS ───────────────────────────────────────────── --}}
<div class="category-strip">
    <div class="cat-inner">
        <a href="#software-catalogue" class="cat-tab active">{{ $isFr ? 'Tous les systèmes' : 'All Systems' }}</a>
        <a href="{{ url($locale.'/products') }}#core-platform" class="cat-tab inactive">{{ $isFr ? 'Plateforme principale' : 'Core Platform' }}</a>
        <a href="{{ url($locale.'/products') }}#diagnostics" class="cat-tab inactive">Diagnostics</a>
        <a href="{{ url($locale.'/products') }}#specialist-systems" class="cat-tab inactive">{{ $isFr ? 'Systèmes spécialisés' : 'Specialist Systems' }}</a>
        <a href="{{ url($locale.'/products') }}" class="cat-tab inactive">Administration &amp; Finance</a>
    </div>
</div>

{{-- ── PRODUCT CATALOGUE ──────────────────────────────── --}}
<div class="section" id="software-catalogue">
    <div class="section-label">
        <i data-lucide="grid-3x3" style="width:12px;height:12px"></i> {{ $isFr ? 'Nos logiciels' : 'Our Software' }}
    </div>
    <h2 class="section-title">{{ $isFr ? $products->count().' systèmes. Un seul écosystème.' : $products->count().' Systems. One Ecosystem.' }}</h2>
    <p class="section-sub">{{ $isFr ? 'Chaque système s\'intègre avec OPESCare — l\'identifiant de santé du patient le suit dans chaque établissement.' : 'Every system integrates with OPESCare — your patient\'s Health ID follows them across every facility.' }}</p>

    <div class="product-grid">
        @foreach($products as $p)
        <a href="{{ url($locale.'/products/'.$p->slug) }}" class="product-card {{ $p->category === 'core' ? 'core' : ($p->category === 'diagnostics' ? 'diag' : '') }}">
            <div class="p-icon"><i data-lucide="{{ $p->icon }}" style="width:24px;height:24px;color:{{ $p->color }}"></i></div>
            <div class="p-name">{{ $p->name }}</div><div class="p-cat">{{ $isFr ? (config('product_copy_fr.'.$p->slug)[0] ?? $p->subtitle_fr ?? $p->subtitle) : $p->subtitle }}</div>
        </a>
        @endforeach
    </div>
</div>

<div class="divider"></div>

{{-- ── WHY OPES ─────────────────────────────────────────────────── --}}
<div class="why-section">
    <div class="pillars">
        <div class="pillar">
            <div class="pillar-icon-wrap" style="background:rgba(0,200,150,0.1)">
                <i data-lucide="globe-2" style="width:26px;height:26px;color:var(--brand-accent)"></i>
            </div>
            <h3>{{ $isFr ? 'Conçu pour l\'Afrique' : 'Built for Africa' }}</h3>
            <p>{{ $isFr ? 'Développé au Cameroun pour accompagner les systèmes de santé africains et les collaborations internationales. Les parcours, langues, normes et règles locales sont étudiés avec chaque partenaire.' : 'Developed in Cameroon to support African health systems and international collaboration. Workflows, languages, standards and local requirements are considered with each partner.' }}</p>
        </div>
        <div class="pillar">
            <div class="pillar-icon-wrap" style="background:rgba(26,111,232,0.1)">
                <i data-lucide="network" style="width:26px;height:26px;color:var(--brand-accent)"></i>
            </div>
            <h3>{{ $isFr ? 'Entièrement interopérable' : 'Fully Interoperable' }}</h3>
            <p>{{ $isFr ? 'Définissez les échanges de données utiles à votre établissement. Confirmez les systèmes participants, les interfaces, les autorisations et les tests nécessaires dans le périmètre du projet.' : 'Define the data exchanges your facility needs. Confirm participating systems, interfaces, permissions and acceptance tests in your project scope.' }}</p>
        </div>
        <div class="pillar">
            <div class="pillar-icon-wrap" style="background:rgba(0,200,150,0.1)">
                <i data-lucide="layout-grid" style="width:26px;height:26px;color:var(--brand-accent)"></i>
            </div>
            <h3>{{ $isFr ? 'Chaque établissement, chaque spécialité' : 'Every Facility, Every Specialty' }}</h3>
            <p>{{ $isFr ? 'D\'une petite clinique à un hôpital multi-départements. De la médecine générale à la cardiologie, dentisterie, santé mentale, pédiatrie et 12 autres spécialités. Un seul fournisseur, une couverture complète.' : 'From a single-room clinic to a multi-department hospital. From general practice to cardiology, dentistry, mental health, paediatrics and 12 more specialties. One vendor, complete coverage.' }}</p>
        </div>
    </div>
</div>

<div class="divider"></div>

{{-- ── ECOSYSTEM DIAGRAM ───────────────────────────────────────── --}}
<div class="section" style="text-align:center">
    <div class="section-label" style="justify-content:center">
        <i data-lucide="share-2" style="width:12px;height:12px"></i> Architecture
    </div>
    <h2 class="section-title">{{ $isFr ? 'Un identifiant santé. Tous les systèmes connectés.' : 'One Health ID. Every System Connected.' }}</h2>
    <p class="section-sub" style="max-width:580px;margin:0 auto">{{ $isFr ? 'OPESCare est au cœur de l\'écosystème OPES — une couche d\'identité de santé universelle et d\'interopérabilité qui connecte chaque système que vous déployez.' : 'OPESCare sits at the center of the OPES ecosystem — a universal Health ID and interoperability layer that connects every system you deploy.' }}</p>

    <div class="eco-diagram">
        <div class="eco-center-box">
            <i data-lucide="fingerprint" style="width:28px;height:28px;color:var(--brand-accent)"></i>
            <div>
                <div class="eco-center-title">OPESCare · Health ID Layer</div>
                <div class="eco-center-sub">{{ $isFr ? 'Interopérabilité · Identité patient · Échange de données' : 'Interoperability · Patient Identity · Data Exchange' }}</div>
            </div>
        </div>
        <div class="eco-spokes">
            <div class="eco-spoke"><i data-lucide="stethoscope" style="width:14px;height:14px;color:var(--text-muted);flex-shrink:0"></i><span class="eco-spoke-name">OPES EMR</span></div>
            <div class="eco-spoke"><i data-lucide="hospital" style="width:14px;height:14px;color:var(--text-muted);flex-shrink:0"></i><span class="eco-spoke-name">Hospital HIS</span></div>
            <div class="eco-spoke"><i data-lucide="microscope" style="width:14px;height:14px;color:var(--text-muted);flex-shrink:0"></i><span class="eco-spoke-name">OPES Lab</span></div>
            <div class="eco-spoke"><i data-lucide="pill" style="width:14px;height:14px;color:var(--text-muted);flex-shrink:0"></i><span class="eco-spoke-name">PHARMIS</span></div>
            <div class="eco-spoke"><i data-lucide="image-up" style="width:14px;height:14px;color:var(--text-muted);flex-shrink:0"></i><span class="eco-spoke-name">RADIS</span></div>
            <div class="eco-spoke"><i data-lucide="heart-pulse" style="width:14px;height:14px;color:var(--text-muted);flex-shrink:0"></i><span class="eco-spoke-name">CARDIS</span></div>
            <div class="eco-spoke"><i data-lucide="brain" style="width:14px;height:14px;color:var(--text-muted);flex-shrink:0"></i><span class="eco-spoke-name">MHIS</span></div>
            <div class="eco-spoke"><i data-lucide="baby" style="width:14px;height:14px;color:var(--text-muted);flex-shrink:0"></i><span class="eco-spoke-name">GYNOBSIS</span></div>
            <div class="eco-spoke"><i data-lucide="eye" style="width:14px;height:14px;color:var(--text-muted);flex-shrink:0"></i><span class="eco-spoke-name">OPHIS</span></div>
            <div class="eco-spoke"><i data-lucide="more-horizontal" style="width:14px;height:14px;color:var(--text-muted);flex-shrink:0"></i><span class="eco-spoke-name">+13 {{ $isFr ? 'autres' : 'more' }}</span></div>
        </div>
    </div>
</div>

<div class="divider"></div>

{{-- ── STATS ────────────────────────────────────────────────────── --}}
<div class="stats-section">
    <div class="stats-inner">
        <div class="stat-item">
            <div class="stat-icon-wrap"><i data-lucide="layout-grid" style="width:22px;height:22px;color:var(--brand-accent)"></i></div>
            <div class="stat-num">{{ $products->count() }}</div>
            <div class="stat-label">{{ $isFr ? 'Systèmes logiciels intégrés' : 'Integrated Software Systems' }}</div>
        </div>
        <div class="stat-item">
            <div class="stat-icon-wrap"><i data-lucide="languages" style="width:22px;height:22px;color:var(--brand-accent)"></i></div>
            <div class="stat-num">EN/FR</div>
            <div class="stat-label">{{ $isFr ? 'Plateforme entièrement bilingue' : 'Fully Bilingual Platform' }}</div>
        </div>
        <div class="stat-item">
            <div class="stat-icon-wrap"><i data-lucide="map" style="width:22px;height:22px;color:var(--brand-accent)"></i></div>
            <div class="stat-num">{{ $isFr ? 'AFRIQUE' : 'AFRICA' }}</div>
            <div class="stat-label">{{ $isFr ? 'Depuis le Cameroun, pour l’Afrique et le monde' : 'From Cameroon to Africa and the world' }}</div>
        </div>
        <div class="stat-item">
            <div class="stat-icon-wrap"><i data-lucide="landmark" style="width:22px;height:22px;color:var(--brand-accent)"></i></div>
            <div class="stat-num">OPES</div>
            <div class="stat-label">{{ $isFr ? 'Conçu au Cameroun' : 'Designed in Cameroon' }}</div>
        </div>
    </div>
</div>

<div class="divider"></div>

{{-- ── TRIAGE CALLOUT ──────────────────────────────────────────── --}}
<div class="triage-section">
    <div class="triage-card">
        <div>
            <div class="triage-badge">
                <i data-lucide="zap" style="width:11px;height:11px"></i>
                {{ $isFr ? 'Autonome · Fonctionne avec tout logiciel' : 'Standalone · Works With Any Software' }}
            </div>
            <h3>{{ $isFr ? 'Réduire les temps d\'attente — Dès demain' : 'Reduce Patient Wait Times — Starting Tomorrow' }}</h3>
            <p>{{ $isFr ? 'Opes Triage est notre système de triage autonome qui fonctionne même si votre hôpital utilise déjà d\'autres logiciels. Aucun remplacement nécessaire. Déployez-le indépendamment et commencez à réduire les temps d\'attente en quelques jours, pas des mois.' : 'Opes Triage is our standalone triage system that works even if your hospital already has existing software. No replacement required. Deploy it independently and start reducing wait times in days, not months.' }}</p>
            <a href="{{ url($locale.'/products/opes-triage') }}" class="btn-primary" style="margin-top:24px;display:inline-flex">
                {{ $isFr ? 'En savoir plus sur Opes Triage' : 'Learn About Opes Triage' }}
                <i data-lucide="arrow-right" style="width:15px;height:15px"></i>
            </a>
        </div>
        <div class="triage-stat">
            <i data-lucide="timer" style="width:32px;height:32px;color:var(--brand-accent)"></i>
            <div class="triage-stat-num">{{ $isFr ? '↓ Temps d\'attente' : '↓ Wait Times' }}</div>
            <div class="triage-stat-label">{{ $isFr ? 'Tout hôpital · Toute taille' : 'Any hospital · Any size' }}</div>
        </div>
    </div>
</div>

<div class="divider"></div>

{{-- ── FEATURED PRODUCTS ───────────────────────────────────────── --}}
<div class="section">
    <div class="section-label"><i data-lucide="star" style="width:12px;height:12px"></i> {{ $isFr ? 'Sélection' : 'Spotlight' }}</div>
    <h2 class="section-title">{{ $isFr ? 'Commencez par l\'essentiel' : 'Start with the Essentials' }}</h2>
    <p class="section-sub">{{ $isFr ? 'Découvrez une sélection de produits et confirmez le périmètre adapté à votre établissement.' : 'Explore selected products and confirm the right scope for your facility.' }}</p>
    <div class="featured-grid">
        <a href="{{ url($locale.'/products/opescare') }}" class="featured-card" style="border-color:rgba(0,200,150,0.2)">
            <div class="featured-icon" style="background:rgba(0,200,150,0.1)">
                <i data-lucide="fingerprint" style="width:22px;height:22px;color:var(--brand-accent)"></i>
            </div>
            <h3>OPESCare — Health ID</h3>
            <p>{{ $isFr ? 'L\'épine dorsale de l\'écosystème OPES. Attribue à chaque patient un identifiant de santé universel et permet un échange de données fluide entre tous vos systèmes et établissements partenaires.' : 'The backbone of the OPES ecosystem. Assigns every patient a universal Health ID and enables seamless data exchange across all your systems and partner facilities.' }}</p>
            <div class="featured-link">{{ $isFr ? 'Explorer OPESCare' : 'Explore OPESCare' }} <i data-lucide="arrow-right" style="width:13px;height:13px"></i></div>
        </a>
        <a href="{{ url($locale.'/products/opes-emr') }}" class="featured-card" style="border-color:rgba(26,111,232,0.2)">
            <div class="featured-icon" style="background:rgba(26,111,232,0.1)">
                <i data-lucide="stethoscope" style="width:22px;height:22px;color:var(--brand-accent)"></i>
            </div>
            <h3>OPES EMR</h3>
            <p>{{ $isFr ? 'Dossiers médicaux électroniques conçus pour les cliniques et les petits hôpitaux au Cameroun. Bilingue, intuitif et conçu pour le rythme de la pratique clinique africaine.' : 'Electronic Medical Records designed for clinics and small hospitals in Cameroon. Bilingual, intuitive, and built to handle the pace of African clinical practice.' }}</p>
            <div class="featured-link">{{ $isFr ? 'Explorer OPES EMR' : 'Explore OPES EMR' }} <i data-lucide="arrow-right" style="width:13px;height:13px"></i></div>
        </a>
        <a href="{{ url($locale.'/products/opes-hospital-his') }}" class="featured-card">
            <div class="featured-icon" style="background:rgba(255,255,255,0.06)">
                <i data-lucide="hospital" style="width:22px;height:22px;color:var(--text-muted)"></i>
            </div>
            <h3>OPES Hospital HIS</h3>
            <p>{{ $isFr ? 'Un système d\'information hospitalier complet pour les grands établissements — admissions, services, sorties, facturation, rapports et intégration complète avec tous les modules spécialisés OPES.' : 'A complete Hospital Information System for larger facilities — admissions, wards, discharge, billing, reporting, and full integration with all OPES specialist modules.' }}</p>
            <div class="featured-link">{{ $isFr ? 'Explorer OPES Hospital' : 'Explore OPES Hospital' }} <i data-lucide="arrow-right" style="width:13px;height:13px"></i></div>
        </a>
    </div>
</div>

<div class="divider"></div>

{{-- ── DATA AND CLINICAL DECISION SUPPORT SERVICES ──────────────── --}}
<section class="section" aria-labelledby="health-services-title">
    <div class="section-label"><i data-lucide="briefcase-medical" style="width:12px;height:12px"></i> {{ $isFr ? 'Nos services' : 'Our Services' }}</div>
    <h2 class="section-title" id="health-services-title">{{ $isFr ? 'Des services de données et d’aide clinique' : 'Data and clinical decision support services' }}</h2>
    <p class="section-sub">{{ $isFr ? 'Des services conçus pour aider les organisations de santé à utiliser leurs données et à soutenir les équipes de soins.' : 'Services designed to help health organisations make practical use of their data and support care teams.' }}</p>
    <div class="featured-grid">
        @foreach($healthServiceOfferings as $offering)
        <a href="{{ url($offering['contact_url']) }}" class="featured-card" style="border-color:rgba(18,58,99,0.2)">
            <div class="featured-icon" style="background:rgba(18,58,99,0.08)">
                <i data-lucide="{{ $offering['icon'] }}" style="width:22px;height:22px;color:#123a63"></i>
            </div>
            <h3>{{ $offering['name'] }}</h3>
            <p>{{ $offering['summary'] }}</p>
            <div class="featured-link">{{ $offering['cta'] }} <i data-lucide="arrow-right" style="width:13px;height:13px"></i></div>
        </a>
        @endforeach
    </div>
</section>

<div class="divider"></div>

{{-- ── DEMO CTA ─────────────────────────────────────────────────── --}}
<div class="demo-section">
    <div class="section-label" style="justify-content:center;color:var(--brand-accent);margin-bottom:12px">
        <i data-lucide="calendar-check" style="width:13px;height:13px"></i>
        {{ $isFr ? 'Commencer' : 'Get Started' }}
    </div>
    <h2>{{ __('home.demo_title') }}</h2>
    <p>{{ __('home.demo_text') }}</p>
    <form action="{{ url($locale.'/contact') }}" method="POST" class="demo-form">
        @csrf
        @if(session('success'))<p role="status">{{ session('success') }}</p>@endif
        @if($errors->any())<div role="alert">@foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>@endif
        <input aria-label="{{ __('home.demo_name') }}" autocomplete="name" class="demo-input" type="text" name="name" required maxlength="100" placeholder="{{ __('home.demo_name') }}">
        <input aria-label="{{ __('home.demo_email') }}" autocomplete="email" class="demo-input" type="email" name="email" required maxlength="150" placeholder="{{ __('home.demo_email') }}">
        <select aria-label="{{ __('home.demo_facility') }}" class="demo-select" name="facility_type">
            <option value="">{{ __('home.demo_facility') }}</option>
            @foreach(config('facility_types') as $value => $labels)
            <option value="{{ $value }}">{{ $labels[app()->getLocale()] ?? $labels['en'] }}</option>
            @endforeach
        </select>
        <button type="submit" class="demo-btn">
            {{ __('home.demo_btn') }}
            <i data-lucide="send" style="width:15px;height:15px"></i>
        </button>
    </form>
</div>

<div class="divider"></div>

{{-- ── PARTNERSHIPS ─────────────────────────────────────────────── --}}
<div class="section">
    <div class="section-label"><i data-lucide="handshake" style="width:12px;height:12px"></i> {{ $isFr ? 'Collaborer' : 'Collaborate' }}</div>
    <h2 class="section-title">{{ $isFr ? 'Devenez partenaire OPES' : 'Partner With OPES' }}</h2>
    <p class="section-sub">{{ $isFr ? 'OPES fournit des services de données de santé et des systèmes d’aide à la décision clinique. Nous sommes prêts à collaborer avec des entreprises développant des solutions d’IA pour la santé.' : 'OPES provides health data services and clinical decision support systems. We are ready to partner with companies developing healthcare AI.' }}</p>
    <div class="partner-grid">
        <div class="partner-card" style="border-color:rgba(0,200,150,0.18)">
            <div class="partner-icon" style="background:rgba(0,200,150,0.08)">
                <i data-lucide="hospital" style="width:20px;height:20px;color:var(--brand-accent)"></i>
            </div>
            <h3>{{ $isFr ? 'Hôpitaux & cliniques' : 'Hospitals & Clinics' }}</h3>
            <p>{{ $isFr ? 'Pilotez nos systèmes, fournissez une validation clinique et façonnez l\'avenir de la santé numérique africaine.' : 'Pilot our systems, provide clinical validation, and shape the future of African digital health.' }}</p>
        </div>
        <div class="partner-card" style="border-color:rgba(26,111,232,0.18)">
            <div class="partner-icon" style="background:rgba(26,111,232,0.08)">
                <i data-lucide="trending-up" style="width:20px;height:20px;color:var(--brand-accent)"></i>
            </div>
            <h3>{{ $isFr ? 'Investisseurs' : 'Investors' }}</h3>
            <p>{{ $isFr ? 'Échangeons sur les opportunités d’investissement et de déploiement adaptées aux besoins des systèmes de santé de la région.' : 'Discuss investment and deployment opportunities shaped around the needs of health systems in the region.' }}</p>
        </div>
        <div class="partner-card">
            <div class="partner-icon" style="background:rgba(255,255,255,0.05)">
                <i data-lucide="landmark" style="width:20px;height:20px;color:var(--text-muted)"></i>
            </div>
            <h3>{{ $isFr ? 'Gouvernement & ONG' : 'Government & NGOs' }}</h3>
            <p>{{ $isFr ? 'Définissons ensemble les priorités, les exigences de gouvernance et les intégrations pertinentes pour votre programme.' : 'Scope programme priorities, governance requirements and relevant integrations together.' }}</p>
        </div>
        <div class="partner-card">
            <div class="partner-icon" style="background:rgba(255,255,255,0.05)">
                <i data-lucide="flask-conical" style="width:20px;height:20px;color:var(--text-muted)"></i>
            </div>
            <h3>{{ $isFr ? 'Pharma & Assurance' : 'Pharma & Insurance' }}</h3>
            <p>{{ $isFr ? 'Étudions les besoins en données, les autorisations et les intégrations de financement ou d’assurance adaptés à chaque projet.' : 'Explore project-specific data needs, permissions and insurance or financing integrations.' }}</p>
        </div>
        <div class="partner-card">
            <div class="partner-icon" style="background:rgba(255,255,255,0.05)">
                <i data-lucide="graduation-cap" style="width:20px;height:20px;color:var(--text-muted)"></i>
            </div>
            <h3>{{ $isFr ? 'Académique & Recherche' : 'Academic & Research' }}</h3>
            <p>{{ $isFr ? 'Évaluons les possibilités de recherche et de formation après accord sur le protocole, l’éthique, les données et les rôles.' : 'Explore research and training opportunities after agreeing the protocol, ethics, data access and partner roles.' }}</p>
        </div>
        <div class="partner-card">
            <div class="partner-icon" style="background:rgba(255,255,255,0.05)">
                <i data-lucide="plug-zap" style="width:20px;height:20px;color:var(--text-muted)"></i>
            </div>
            <h3>{{ $isFr ? 'Entreprises d’IA en santé' : 'Healthcare AI Companies' }}</h3>
            <p>{{ $isFr ? 'OPES apporte ses services de données de santé et ses flux CDSS ; nos partenaires apportent leurs solutions d’IA en santé. Nous sommes prêts à étudier ensemble des intégrations et évaluations. Les accès aux données, la gouvernance et la validation sont définis pour chaque projet.' : 'OPES brings health data services and CDSS workflows; our partners bring healthcare AI solutions. We are ready to explore integrations and evaluations together. Data access, governance and validation are scoped for each project.' }}</p>
        </div>
    </div>
    <a href="{{ url($locale.'/partnerships') }}" class="btn-secondary" style="display:inline-flex;margin-top:24px">
        {{ $isFr ? 'Discuter d’un partenariat en IA santé' : 'Discuss a healthcare AI partnership' }}
        <i data-lucide="arrow-right" style="width:15px;height:15px"></i>
    </a>
</div>

<div class="divider"></div>

{{-- Published editorial resources only; each card opens the article it describes. --}}
@if($articles->isNotEmpty())
<section class="section">
    <div class="section-label">{{ $isFr ? 'Ressources' : 'Resources' }}</div>
    <h2 class="section-title">{{ $isFr ? 'Préparer votre projet de santé numérique' : 'Plan your digital health project' }}</h2>
    <div class="blog-grid">@foreach($articles as $article)
        <a href="{{ url($locale.'/blog/'.$article->slug) }}" class="blog-card"><div class="blog-body"><h3>{{ $article->getLocalizedTitle($locale) }}</h3><p>{{ $article->getLocalizedExcerpt($locale) }}</p><div class="blog-meta">{{ $article->published_at?->format('d M Y') }} · {{ $article->reading_time }} min</div></div></a>
    @endforeach</div>
</section>
@endif
<section class="section" style="padding-top:16px"><div class="section-label">{{ $isFr ? 'Sécurité du médicament' : 'Medicine safety' }}</div><h2 class="section-title">OPES DrugTrace</h2><p class="section-sub">{{ $isFr ? 'Disponible pour la sécurité du médicament et la traçabilité pharmaceutique. Demandez une démonstration adaptée à vos processus.' : 'Released and available for medicine safety and pharmaceutical traceability. Request a demonstration scoped to your workflows.' }}</p><a class="btn-primary" href="{{ url($locale.'/products/opes-drugtrace') }}">{{ $isFr ? 'Découvrir DrugTrace' : 'Explore DrugTrace' }}</a></section>

{{-- ── TESTIMONIALS — only shown when real practitioner reviews exist ── --}}
@if($testimonials->isNotEmpty())
<div class="divider"></div>
<div class="section">
    <div class="section-label"><i data-lucide="message-square-quote" style="width:12px;height:12px"></i> {{ $isFr ? 'Avis vérifiés' : 'Verified Reviews' }}</div>
    <h2 class="section-title">{{ $isFr ? 'Ce que disent les professionnels de santé' : 'What Healthcare Professionals Say' }}</h2>
    <p class="section-sub">{{ $isFr ? 'Avis réels de médecins, infirmiers et praticiens de santé ayant testé nos systèmes.' : 'Real reviews from doctors, nurses, and health practitioners who tested our systems.' }}</p>
    <div class="testimonial-grid">
        @foreach($testimonials as $t)
        @php
            $profile   = $t->practitioner?->practitionerProfile;
            $name      = $t->practitioner?->name ?? 'Healthcare Professional';
            $profession = $profile ? \App\Models\PractitionerProfile::professionOptions()[$profile->profession] ?? $profile->profession : null;
            $specialty  = $profile?->specialty;
            $workplace  = $profile?->workplace_name;
            $program    = $t->application?->program?->product_name ?? $t->application?->program?->title;
            $avg        = round($t->averageRating());
        @endphp
        <div class="testimonial">
            <div style="display:flex;gap:3px;margin-bottom:10px">
                @for($s = 1; $s <= 5; $s++)
                <i data-lucide="star" style="width:13px;height:13px;{{ $s <= $avg ? 'color:#f59e0b;fill:#f59e0b' : 'color:#334155' }}"></i>
                @endfor
            </div>
            <i data-lucide="quote" style="width:20px;height:20px;color:var(--brand-accent);opacity:.5"></i>
            <blockquote>"{{ $t->findings_text }}"</blockquote>
            <div class="testimonial-author">
                <i data-lucide="user-circle" style="width:14px;height:14px;color:var(--brand-accent)"></i>
                {{ $name }}
                @if($profession || $specialty || $workplace)
                · <span>{{ implode(', ', array_filter([$profession, $specialty, $workplace])) }}</span>
                @endif
                @if($program)
                <span style="margin-left:6px;font-size:var(--fs-xs);color:var(--text-faint);background:#0f172a;padding:2px 7px;border-radius:8px;border:1px solid #1e293b">{{ $program }}</span>
                @endif
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif

<script>
document.addEventListener('DOMContentLoaded', function () {
    var slider = document.getElementById('heroSlider');
    if (!slider) return;
    var slides = Array.from(slider.querySelectorAll('.hero-slide'));
    var dots   = Array.from(document.querySelectorAll('.hero-dot'));
    var bar    = document.getElementById('heroProgressBar');
    if (!slides.length) return;
    var current = 0, timer, barAnim;
    var INTERVAL = 5000;
    var paused = true; // Slides change only on request, avoiding moving content during reading.

    function goTo(n) {
        slides[current].classList.remove('active');
        if (dots[current]) dots[current].classList.remove('active');
        current = ((n % slides.length) + slides.length) % slides.length;
        slides[current].classList.add('active');
        if (dots[current]) dots[current].classList.add('active');
        startBar();
        resetTimer();
    }

    function startBar() {
        if (!bar || paused) return;
        bar.style.transition = 'none';
        bar.style.width = '0%';
        requestAnimationFrame(function () {
            requestAnimationFrame(function () {
                bar.style.transition = 'width ' + INTERVAL + 'ms linear';
                bar.style.width = '100%';
            });
        });
    }

    function resetTimer() {
        clearInterval(timer);
        if (paused) return;
        timer = setInterval(function () { goTo(current + 1); }, INTERVAL);
    }

    dots.forEach(function (dot, i) {
        dot.addEventListener('click', function () { goTo(i); });
    });

    var wrap = document.getElementById('heroWrap');
    if (wrap) {
        wrap.addEventListener('mouseenter', function () { clearInterval(timer); if (bar) { bar.style.transition = 'none'; } });
        wrap.addEventListener('mouseleave', function () { startBar(); resetTimer(); });
    }

    // Swipe support
    var touchX = null;
    slider.addEventListener('touchstart', function (e) { touchX = e.touches[0].clientX; }, { passive: true });
    slider.addEventListener('touchend', function (e) {
        if (touchX === null) return;
        var diff = touchX - e.changedTouches[0].clientX;
        if (Math.abs(diff) > 40) goTo(current + (diff > 0 ? 1 : -1));
        touchX = null;
    });

    startBar();
    resetTimer();
});
</script>

</x-layouts.app>

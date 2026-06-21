@php $locale = app()->getLocale(); $isFr = $locale === 'fr'; @endphp

<x-layouts.app>

{{-- ── MINISTRY BANNER ─────────────────────────────────────────── --}}
<div class="ministry-banner">
    <i data-lucide="landmark" style="width:15px;height:15px;color:#00C896;flex-shrink:0"></i>
    <p>
        <strong>{{ $isFr ? 'Aligné avec la Stratégie nationale de santé numérique 2026–2030 du Ministère de la Santé du Cameroun' : 'Aligned with the Cameroon Ministry of Health Digital Health Strategy 2026–2030' }}</strong>
        &nbsp;·&nbsp; Conçu pour le Cameroun, la CEMAC et toute l'Afrique
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
                        <a href="{{ url($locale.'/contact') }}" class="btn-secondary">
                            <i data-lucide="play-circle" style="width:15px;height:15px;color:#00C896"></i>
                            {{ __('home.cta_watch_demo') }}
                        </a>
                    </div>
                    <div class="hero-trust">
                        <div class="trust-item"><div class="num">22</div><div class="label">{{ __('home.trust_22') }}</div></div>
                        <div class="trust-item"><div class="num">EN/FR</div><div class="label">{{ __('home.trust_bilingual') }}</div></div>
                        <div class="trust-item"><div class="num">CEMAC</div><div class="label">{{ __('home.trust_cemac') }}</div></div>
                        <div class="trust-item"><div class="num">MoH</div><div class="label">{{ __('home.trust_moh') }}</div></div>
                    </div>
                </div>
                <div class="hero-visual">
                    <div class="hero-visual-title">OPES Software Ecosystem</div>
                    <div class="ecosystem-grid">
                        <div class="eco-item hl-teal">
                            <div class="eco-icon-wrap teal"><i data-lucide="fingerprint" style="width:15px;height:15px;color:#00C896"></i></div>
                            <div><div class="eco-name" style="color:#00C896">OPESCare</div><div class="eco-desc">{{ $isFr ? 'Identifiant santé · Interopérabilité' : 'Health ID · Interoperability' }}</div></div>
                        </div>
                        <div class="eco-item hl-blue">
                            <div class="eco-icon-wrap blue"><i data-lucide="stethoscope" style="width:15px;height:15px;color:#1A6FE8"></i></div>
                            <div><div class="eco-name" style="color:#1A6FE8">OPES EMR</div><div class="eco-desc">{{ $isFr ? 'Cliniques & petits hôpitaux' : 'Clinics & Small Hospitals' }}</div></div>
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
                            <div class="eco-icon-wrap teal"><i data-lucide="timer" style="width:15px;height:15px;color:#00C896"></i></div>
                            <div><div class="eco-name" style="color:#00C896">Opes Triage</div><div class="eco-desc">{{ $isFr ? 'Autonome · Tout hôpital' : 'Standalone · Any Hospital' }}</div></div>
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
                        <a href="{{ url($locale.'/contact') }}" class="btn-secondary">
                            <i data-lucide="play-circle" style="width:15px;height:15px;color:#00C896"></i>
                            {{ __('home.s2_cta2') }}
                        </a>
                    </div>
                    <p class="hero-slide-support">{{ __('home.s2_support') }}</p>
                </div>
                <div class="hero-visual">
                    <div class="hero-visual-title">{{ $isFr ? 'Familles de plateformes' : 'Platform Families' }}</div>
                    <div class="slide-pillars-grid">
                        <div class="slide-pillar">
                            <i data-lucide="heart-pulse" style="width:18px;height:18px;color:#00C896"></i>
                            <div class="slide-pillar-name">{{ $isFr ? 'Clinique' : 'Clinical' }}</div>
                            <div class="slide-pillar-desc">{{ $isFr ? 'DME, SIH, soins spécialisés' : 'EMR, HIS, Specialty Care' }}</div>
                        </div>
                        <div class="slide-pillar">
                            <i data-lucide="microscope" style="width:18px;height:18px;color:#1A6FE8"></i>
                            <div class="slide-pillar-name">Diagnostics</div>
                            <div class="slide-pillar-desc">{{ $isFr ? 'Laboratoire, Radiologie, Imagerie' : 'Lab, Radiology, Imaging' }}</div>
                        </div>
                        <div class="slide-pillar">
                            <i data-lucide="share-2" style="width:18px;height:18px;color:#00C896"></i>
                            <div class="slide-pillar-name">{{ $isFr ? 'Interopérabilité' : 'Interoperability' }}</div>
                            <div class="slide-pillar-desc">Health ID, HIE, MPI</div>
                        </div>
                        <div class="slide-pillar">
                            <i data-lucide="brain-circuit" style="width:18px;height:18px;color:#1A6FE8"></i>
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
                            <i data-lucide="check-circle" style="width:13px;height:13px;color:#00C896;flex-shrink:0"></i>
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
                            <i data-lucide="check-circle" style="width:13px;height:13px;color:#1A6FE8;flex-shrink:0"></i>
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
                            <i data-lucide="check-circle" style="width:13px;height:13px;color:#00C896;flex-shrink:0"></i>
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
        <div class="cat-tab active">{{ $isFr ? 'Tous les systèmes' : 'All Systems' }}</div>
        <div class="cat-tab inactive">{{ $isFr ? 'Plateforme principale' : 'Core Platform' }}</div>
        <div class="cat-tab inactive">Diagnostics</div>
        <div class="cat-tab inactive">{{ $isFr ? 'Systèmes spécialisés' : 'Specialist Systems' }}</div>
        <div class="cat-tab inactive">Administration &amp; Finance</div>
    </div>
</div>

{{-- ── PRODUCT GRID (22 systems) ──────────────────────────────── --}}
<div class="section">
    <div class="section-label">
        <i data-lucide="grid-3x3" style="width:12px;height:12px"></i> {{ $isFr ? 'Nos logiciels' : 'Our Software' }}
    </div>
    <h2 class="section-title">{{ $isFr ? $products->count().' systèmes. Un seul écosystème.' : $products->count().' Systems. One Ecosystem.' }}</h2>
    <p class="section-sub">{{ $isFr ? 'Chaque système s\'intègre avec OPESCare — l\'identifiant de santé du patient le suit dans chaque établissement.' : 'Every system integrates with OPESCare — your patient\'s Health ID follows them across every facility.' }}</p>

    <div class="product-grid">
        @foreach($products as $p)
        <a href="{{ url($locale.'/products/'.$p->slug) }}" class="product-card {{ $p->category === 'core' ? 'core' : ($p->category === 'diagnostics' ? 'diag' : '') }}">
            <div class="p-icon"><i data-lucide="{{ $p->icon }}" style="width:24px;height:24px;color:{{ $p->color }}"></i></div>
            <div class="p-name">{{ $p->name }}</div><div class="p-cat">{{ $p->subtitle }}</div>
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
                <i data-lucide="globe-2" style="width:26px;height:26px;color:#00C896"></i>
            </div>
            <h3>{{ $isFr ? 'Conçu pour l\'Afrique' : 'Built for Africa' }}</h3>
            <p>{{ $isFr ? 'Conçu depuis le début pour le Cameroun, la région CEMAC et toute l\'Afrique. Entièrement bilingue en anglais et en français. Comprend les flux de travail locaux, les réglementations et les réalités des soins de santé.' : 'Designed from the ground up for Cameroon, the CEMAC region and all of Africa. Fully bilingual in English and French. Understands local workflows, regulations, and healthcare realities.' }}</p>
        </div>
        <div class="pillar">
            <div class="pillar-icon-wrap" style="background:rgba(26,111,232,0.1)">
                <i data-lucide="network" style="width:26px;height:26px;color:#1A6FE8"></i>
            </div>
            <h3>{{ $isFr ? 'Entièrement interopérable' : 'Fully Interoperable' }}</h3>
            <p>{{ $isFr ? 'OPESCare attribue à chaque patient un identifiant de santé unique qui fonctionne sur les 22 systèmes. Votre laboratoire, pharmacie, DME et hôpital parlent tous le même langage — automatiquement.' : 'OPESCare gives every patient a single Health ID that works across all 22 systems. Your lab, pharmacy, EMR, and hospital all speak the same language — automatically.' }}</p>
        </div>
        <div class="pillar">
            <div class="pillar-icon-wrap" style="background:rgba(0,200,150,0.1)">
                <i data-lucide="layout-grid" style="width:26px;height:26px;color:#00C896"></i>
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
            <i data-lucide="fingerprint" style="width:28px;height:28px;color:#00C896"></i>
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
            <div class="stat-icon-wrap"><i data-lucide="layout-grid" style="width:22px;height:22px;color:#00C896"></i></div>
            <div class="stat-num">22</div>
            <div class="stat-label">{{ $isFr ? 'Systèmes logiciels intégrés' : 'Integrated Software Systems' }}</div>
        </div>
        <div class="stat-item">
            <div class="stat-icon-wrap"><i data-lucide="languages" style="width:22px;height:22px;color:#1A6FE8"></i></div>
            <div class="stat-num">EN/FR</div>
            <div class="stat-label">{{ $isFr ? 'Plateforme entièrement bilingue' : 'Fully Bilingual Platform' }}</div>
        </div>
        <div class="stat-item">
            <div class="stat-icon-wrap"><i data-lucide="map" style="width:22px;height:22px;color:#00C896"></i></div>
            <div class="stat-num">CEMAC</div>
            <div class="stat-label">{{ $isFr ? 'Couverture régionale 6 pays' : '6-Country Regional Coverage' }}</div>
        </div>
        <div class="stat-item">
            <div class="stat-icon-wrap"><i data-lucide="landmark" style="width:22px;height:22px;color:#1A6FE8"></i></div>
            <div class="stat-num">MoH</div>
            <div class="stat-label">{{ $isFr ? 'Aligné Ministère de la Santé' : 'Ministry of Health Aligned' }}</div>
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
            <i data-lucide="timer" style="width:32px;height:32px;color:#00C896"></i>
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
    <p class="section-sub">{{ $isFr ? 'Nos systèmes les plus adoptés — déployez-en un ou déployez l\'écosystème complet.' : 'Our most adopted systems — deploy one or deploy the full ecosystem.' }}</p>
    <div class="featured-grid">
        <a href="{{ url($locale.'/products/opescare') }}" class="featured-card" style="border-color:rgba(0,200,150,0.2)">
            <div class="featured-icon" style="background:rgba(0,200,150,0.1)">
                <i data-lucide="fingerprint" style="width:22px;height:22px;color:#00C896"></i>
            </div>
            <h3>OPESCare — Health ID</h3>
            <p>{{ $isFr ? 'L\'épine dorsale de l\'écosystème OPES. Attribue à chaque patient un identifiant de santé universel et permet un échange de données fluide entre tous vos systèmes et établissements partenaires.' : 'The backbone of the OPES ecosystem. Assigns every patient a universal Health ID and enables seamless data exchange across all your systems and partner facilities.' }}</p>
            <div class="featured-link">{{ $isFr ? 'Explorer OPESCare' : 'Explore OPESCare' }} <i data-lucide="arrow-right" style="width:13px;height:13px"></i></div>
        </a>
        <a href="{{ url($locale.'/products/opes-emr') }}" class="featured-card" style="border-color:rgba(26,111,232,0.2)">
            <div class="featured-icon" style="background:rgba(26,111,232,0.1)">
                <i data-lucide="stethoscope" style="width:22px;height:22px;color:#1A6FE8"></i>
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

{{-- ── DEMO CTA ─────────────────────────────────────────────────── --}}
<div class="demo-section">
    <div class="section-label" style="justify-content:center;color:#1A6FE8;margin-bottom:12px">
        <i data-lucide="calendar-check" style="width:13px;height:13px"></i>
        {{ $isFr ? 'Commencer' : 'Get Started' }}
    </div>
    <h2>{{ __('home.demo_title') }}</h2>
    <p>{{ __('home.demo_text') }}</p>
    <form action="{{ url($locale.'/contact') }}" method="POST" class="demo-form">
        @csrf
        <input class="demo-input" type="text" name="name" placeholder="{{ __('home.demo_name') }}">
        <input class="demo-input" type="email" name="email" placeholder="{{ __('home.demo_email') }}">
        <select class="demo-select" name="facility_type">
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
    <p class="section-sub">{{ $isFr ? 'Nous construisons l\'infrastructure de santé numérique de l\'Afrique. Rejoignez-nous.' : 'We\'re building Africa\'s digital health infrastructure. Join us.' }}</p>
    <div class="partner-grid">
        <div class="partner-card" style="border-color:rgba(0,200,150,0.18)">
            <div class="partner-icon" style="background:rgba(0,200,150,0.08)">
                <i data-lucide="hospital" style="width:20px;height:20px;color:#00C896"></i>
            </div>
            <h4>{{ $isFr ? 'Hôpitaux & cliniques' : 'Hospitals & Clinics' }}</h4>
            <p>{{ $isFr ? 'Pilotez nos systèmes, fournissez une validation clinique et façonnez l\'avenir de la santé numérique africaine.' : 'Pilot our systems, provide clinical validation, and shape the future of African digital health.' }}</p>
        </div>
        <div class="partner-card" style="border-color:rgba(26,111,232,0.18)">
            <div class="partner-icon" style="background:rgba(26,111,232,0.08)">
                <i data-lucide="trending-up" style="width:20px;height:20px;color:#1A6FE8"></i>
            </div>
            <h4>{{ $isFr ? 'Investisseurs' : 'Investors' }}</h4>
            <p>{{ $isFr ? 'Rejoignez une entreprise qui construit une infrastructure de santé évolutive pour plus de 500 millions de personnes en CEMAC et en Afrique.' : 'Join a venture building scalable health infrastructure for 500M+ people across CEMAC and Africa.' }}</p>
        </div>
        <div class="partner-card">
            <div class="partner-icon" style="background:rgba(255,255,255,0.05)">
                <i data-lucide="landmark" style="width:20px;height:20px;color:var(--text-muted)"></i>
            </div>
            <h4>{{ $isFr ? 'Gouvernement & ONG' : 'Government & NGOs' }}</h4>
            <p>{{ $isFr ? 'Alignez-vous sur les objectifs nationaux de CSU. Intégrez OPES aux rapports de santé publique et aux systèmes de surveillance des maladies.' : 'Align with national UHC goals. Integrate OPES with public health reporting and disease surveillance systems.' }}</p>
        </div>
        <div class="partner-card">
            <div class="partner-icon" style="background:rgba(255,255,255,0.05)">
                <i data-lucide="flask-conical" style="width:20px;height:20px;color:var(--text-muted)"></i>
            </div>
            <h4>{{ $isFr ? 'Pharma & Assurance' : 'Pharma & Insurance' }}</h4>
            <p>{{ $isFr ? 'Accédez à des analyses de santé populationnelle anonymisées et intégrez le financement assurantiel dans les plateformes OPES.' : 'Access anonymised population health analytics and integrate insurance financing into OPES platforms.' }}</p>
        </div>
        <div class="partner-card">
            <div class="partner-icon" style="background:rgba(255,255,255,0.05)">
                <i data-lucide="graduation-cap" style="width:20px;height:20px;color:var(--text-muted)"></i>
            </div>
            <h4>{{ $isFr ? 'Académique & Recherche' : 'Academic & Research' }}</h4>
            <p>{{ $isFr ? 'Co-publiez des recherches en technologie de la santé et formez la prochaine génération de professionnels africains de l\'informatique de santé.' : 'Co-publish health technology research and train the next generation of African health informatics professionals.' }}</p>
        </div>
        <div class="partner-card">
            <div class="partner-icon" style="background:rgba(255,255,255,0.05)">
                <i data-lucide="plug-zap" style="width:20px;height:20px;color:var(--text-muted)"></i>
            </div>
            <h4>{{ $isFr ? 'Partenaires technologiques' : 'Technology Partners' }}</h4>
            <p>{{ $isFr ? 'Intégrez vos solutions à l\'écosystème OPES via notre couche d\'interopérabilité et les API développeurs.' : 'Integrate your solutions with the OPES ecosystem through our interoperability layer and developer APIs.' }}</p>
        </div>
    </div>
</div>

<div class="divider"></div>

{{-- ── BLOG ─────────────────────────────────────────────────────── --}}
<div class="section">
    <div class="section-label"><i data-lucide="rss" style="width:12px;height:12px"></i> {{ $isFr ? 'Analyses' : 'Insights' }}</div>
    <h2 class="section-title">{{ $isFr ? 'Santé numérique en Afrique' : 'Digital Health in Africa' }}</h2>
    <p class="section-sub">{{ $isFr ? 'Actualités, analyses produits et perspectives en technologie de la santé pour la région CEMAC.' : 'News, product deep-dives, and health technology insights for the CEMAC region.' }}</p>
    <div class="blog-grid">
        <a href="{{ url($locale.'/blog') }}" class="blog-card">
            <div class="blog-thumb" style="background:linear-gradient(135deg,rgba(0,200,150,0.12),rgba(26,111,232,0.12))">
                <i data-lucide="fingerprint" style="width:36px;height:36px;color:#00C896;opacity:.7"></i>
            </div>
            <div class="blog-body">
                <div class="blog-cat"><i data-lucide="tag" style="width:10px;height:10px"></i> {{ $isFr ? 'Produit' : 'Product' }}</div>
                <h4>{{ $isFr ? 'Comment l\'identifiant santé OPESCare transforme les dossiers patients au Cameroun' : 'How OPESCare\'s Health ID is Transforming Patient Records in Cameroon' }}</h4>
                <div class="blog-meta"><i data-lucide="clock" style="width:10px;height:10px"></i> {{ $isFr ? 'Juin 2026 · 5 min de lecture' : 'June 2026 · 5 min read' }}</div>
            </div>
        </a>
        <a href="{{ url($locale.'/blog') }}" class="blog-card">
            <div class="blog-thumb" style="background:linear-gradient(135deg,rgba(26,111,232,0.12),rgba(0,200,150,0.08))">
                <i data-lucide="landmark" style="width:36px;height:36px;color:#1A6FE8;opacity:.7"></i>
            </div>
            <div class="blog-body">
                <div class="blog-cat"><i data-lucide="tag" style="width:10px;height:10px"></i> {{ $isFr ? 'Politique' : 'Policy' }}</div>
                <h4>{{ $isFr ? 'Stratégie nationale de santé numérique 2026–2030 du Cameroun : ce que cela signifie pour les hôpitaux' : 'Cameroon MoH 2026–2030 Digital Health Strategy: What It Means for Hospitals' }}</h4>
                <div class="blog-meta"><i data-lucide="clock" style="width:10px;height:10px"></i> {{ $isFr ? 'Juin 2026 · 8 min de lecture' : 'June 2026 · 8 min read' }}</div>
            </div>
        </a>
        <a href="{{ url($locale.'/blog') }}" class="blog-card">
            <div class="blog-thumb" style="background:linear-gradient(135deg,rgba(0,200,150,0.08),rgba(26,111,232,0.12))">
                <i data-lucide="network" style="width:36px;height:36px;color:var(--text-muted);opacity:.7"></i>
            </div>
            <div class="blog-body">
                <div class="blog-cat"><i data-lucide="tag" style="width:10px;height:10px"></i> {{ $isFr ? 'Technologie' : 'Technology' }}</div>
                <h4>{{ $isFr ? 'Pourquoi l\'interopérabilité est le mot le plus important dans la santé africaine aujourd\'hui' : 'Why Interoperability is the Most Important Word in African Healthcare Today' }}</h4>
                <div class="blog-meta"><i data-lucide="clock" style="width:10px;height:10px"></i> {{ $isFr ? 'Mai 2026 · 6 min de lecture' : 'May 2026 · 6 min read' }}</div>
            </div>
        </a>
    </div>
</div>

<div class="divider"></div>

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
            <i data-lucide="quote" style="width:20px;height:20px;color:#00C896;opacity:.5"></i>
            <blockquote>"{{ $t->findings_text }}"</blockquote>
            <div class="testimonial-author">
                <i data-lucide="user-circle" style="width:14px;height:14px;color:#00C896"></i>
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
        if (!bar) return;
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

@php $locale = app()->getLocale(); $isFr = $locale === 'fr'; @endphp

<x-layouts.app
    title="{{ $isFr ? 'Plan stratégique 2026–2031 — OPES Health Systems' : 'Strategic Plan 2026–2031 — OPES Health Systems' }}"
    description="{{ $isFr ? 'Feuille de route 2026–2031 d\'OPES : 5 thèmes stratégiques, 500+ établissements, interopérabilité nationale, leadership régional.' : 'OPES 2026–2031 roadmap: 5 strategic themes, 500+ facilities, national interoperability, and regional digital health leadership.' }}">

{{-- ── HERO ─────────────────────────────────────────────────────── --}}
<div class="about-hero">
    <div class="section-label" style="justify-content:center;margin-bottom:16px">
        <i data-lucide="map" style="width:12px;height:12px"></i>
        {{ $isFr ? 'Plan stratégique 2026–2031 v1.0' : 'Strategic Plan 2026–2031 v1.0' }}
    </div>
    <h1 class="about-title">
        {{ $isFr ? 'Devenir l\'infrastructure' : 'Becoming the infrastructure' }}
        <span class="gradient-text">{{ $isFr ? 'de santé numérique de l\'Afrique' : 'of Africa\'s digital health' }}</span>
    </h1>
    <p class="about-sub" style="max-width:740px">
        {{ $isFr
            ? 'Feuille de route sur cinq ans pour faire d\'OPES Health Systems la plateforme d\'infrastructure de santé numérique interopérable la plus fiable d\'Afrique — au service des établissements de soins, des gouvernements, des assureurs et des institutions de santé publique.'
            : 'Five-year roadmap to transform OPES Health Systems into Africa\'s most trusted interoperable digital health infrastructure platform — serving care facilities, governments, insurers, and public health institutions.' }}
    </p>
</div>

{{-- ── VISION / MISSION ─────────────────────────────────────────── --}}
<div class="section" style="max-width:960px;margin:0 auto">
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px">
        <div style="background:linear-gradient(135deg,#0f1f2e,#0d1a14);border:1px solid rgba(0,200,150,0.2);border-radius:16px;padding:28px">
            <div style="font-size:10px;font-weight:800;color:#00C896;text-transform:uppercase;letter-spacing:0.1em;margin-bottom:12px">
                {{ $isFr ? 'Vision 2031' : 'Vision 2031' }}
            </div>
            <p style="font-size:15px;font-weight:700;color:#e2e8f0;line-height:1.65;margin:0">
                {{ $isFr
                    ? 'Devenir le fournisseur d\'infrastructure de santé numérique interopérable le plus fiable d\'Afrique.'
                    : 'Become Africa\'s most trusted provider of interoperable digital health infrastructure.' }}
            </p>
        </div>
        <div style="background:linear-gradient(135deg,#0f152e,#0f1a2e);border:1px solid rgba(26,111,232,0.2);border-radius:16px;padding:28px">
            <div style="font-size:10px;font-weight:800;color:#1A6FE8;text-transform:uppercase;letter-spacing:0.1em;margin-bottom:12px">
                {{ $isFr ? 'Mission' : 'Mission' }}
            </div>
            <p style="font-size:15px;font-weight:700;color:#e2e8f0;line-height:1.65;margin:0">
                {{ $isFr
                    ? 'Connecter les prestataires de soins, les patients, les institutions de santé publique et les gouvernements par des technologies de santé sécurisées, intelligentes et interopérables.'
                    : 'Connect healthcare providers, patients, public health institutions, and governments through secure, intelligent, and interoperable healthcare technologies.' }}
            </p>
        </div>
    </div>
</div>

{{-- ── STATS ────────────────────────────────────────────────────── --}}
<div class="section" style="text-align:center">
    <div class="stats-bar" style="max-width:960px;margin:0 auto">
        @foreach($isFr
            ? [['5','Thèmes stratégiques'],['500+','Établissements cibles'],['10 000+','Utilisateurs certifiés'],['5','Ans de feuille de route']]
            : [['5','Strategic themes'],['500+','Target facilities'],['10,000+','Certified users'],['5','Year roadmap']]
        as $s)
        <div class="stat-item">
            <div class="stat-value">{{ $s[0] }}</div>
            <div class="stat-label">{{ $s[1] }}</div>
        </div>
        @endforeach
    </div>
</div>

<div class="divider"></div>

{{-- ── 5 STRATEGIC THEMES ───────────────────────────────────────── --}}
<div class="section" style="max-width:960px;margin:0 auto">
    <div class="section-label" style="margin-bottom:16px">
        <i data-lucide="target" style="width:12px;height:12px"></i>
        {{ $isFr ? 'Thèmes stratégiques' : 'Strategic themes' }}
    </div>
    <h2 class="section-title">{{ $isFr ? 'Cinq axes pour transformer la santé en Afrique' : 'Five axes to transform healthcare in Africa' }}</h2>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:16px;margin-top:28px">
        @php $themes = $isFr ? [
            ['1','hospital','#00C896','Digitalisation des établissements','Accélérer la numérisation des soins à travers toute l\'Afrique.',['500+ cliniques','100+ hôpitaux','25+ réseaux hospitaliers']],
            ['2','share-2','#1A6FE8','Interopérabilité de la santé','Construire le plus grand écosystème d\'interopérabilité de santé en Afrique centrale.',['Identifiant national de santé','Infrastructure HIE','Réseau de référencement','Connectivité assurance']],
            ['3','brain','#00C896','Intelligence clinique','Développer des capacités avancées d\'intelligence clinique.',['CDSS mature','Plateforme de triage nationale','Plateforme d\'analytique clinique']],
            ['4','building-2','#1A6FE8','Programmes nationaux de santé numérique','Soutenir les initiatives gouvernementales de santé numérique.',['Registres nationaux','Plateformes de santé publique','Programmes CSU','Surveillance des maladies']],
            ['5','graduation-cap','#00C896','Développement des capacités humaines','Développer une main-d\'œuvre en technologie de la santé.',['10 000 utilisateurs certifiés','1 000 administrateurs certifiés','500 implémenteurs certifiés','100 formateurs certifiés']],
        ] : [
            ['1','hospital','#00C896','Healthcare facility digitization','Accelerate healthcare digitization across the continent.',['500+ clinics','100+ hospitals','25+ hospital networks']],
            ['2','share-2','#1A6FE8','Healthcare interoperability','Build the largest healthcare interoperability ecosystem in Central Africa.',['National Health ID','HIE infrastructure','Referral exchange network','Insurance connectivity']],
            ['3','brain','#00C896','Clinical intelligence','Develop advanced clinical intelligence capabilities.',['Mature CDSS','National triage platform','Clinical analytics platform']],
            ['4','building-2','#1A6FE8','National digital health programs','Support government digital health initiatives.',['National registries','Public health platforms','UHC programs','Disease surveillance']],
            ['5','graduation-cap','#00C896','Human capacity development','Build a healthcare technology workforce through OPES Academy.',['10,000 certified users','1,000 certified administrators','500 certified implementers','100 certified trainers']],
        ]; @endphp
        @foreach($themes as $t)
        <div style="background:#0F172A;border:1px solid #1e293b;border-radius:14px;padding:20px">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:14px">
                <div style="width:36px;height:36px;border-radius:9px;background:{{ $t[2] }}15;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:13px;font-weight:800;color:{{ $t[2] }}">{{ $t[0] }}</div>
                <div>
                    <div style="font-weight:700;color:#e2e8f0;font-size:13px">{{ $t[3] }}</div>
                    <div style="font-size:11px;color:{{ $t[2] }};font-weight:600">{{ $isFr ? 'Thème '.$t[0] : 'Theme '.$t[0] }}</div>
                </div>
            </div>
            <p style="font-size:11px;color:#64748b;line-height:1.6;margin:0 0 12px">{{ $t[4] }}</p>
            @foreach($t[5] as $target)
            <div style="display:flex;align-items:center;gap:6px;font-size:11px;color:#94a3b8;padding:4px 0">
                <i data-lucide="check" style="width:10px;height:10px;color:{{ $t[2] }};flex-shrink:0"></i>{{ $target }}
            </div>
            @endforeach
        </div>
        @endforeach
    </div>
</div>

<div class="divider"></div>

{{-- ── 5-YEAR ROADMAP ───────────────────────────────────────────── --}}
<div class="section" style="max-width:960px;margin:0 auto">
    <div class="section-label" style="margin-bottom:16px">
        <i data-lucide="milestone" style="width:12px;height:12px"></i>
        {{ $isFr ? 'Feuille de route sur 5 ans' : '5-year growth roadmap' }}
    </div>
    <h2 class="section-title">{{ $isFr ? 'De la fondation au leadership régional' : 'From foundation to regional leadership' }}</h2>
    <div style="margin-top:28px">
        @php $years = $isFr ? [
            ['2026','#475569','Fondation','Stabilisation produit, déploiements pilotes, mise en place de la gouvernance.',['10 établissements','2 hôpitaux phares'],'pen-line'],
            ['2027','#1A6FE8','Expansion','Croissance commerciale, visibilité régionale, montée en échelle.',['50 établissements','10 hôpitaux'],'trending-up'],
            ['2028','#00A87B','Interopérabilité','Déploiement Health ID, mise en service de l\'HIE, réseau connecté.',['Réseau d\'établissements connectés'],'share-2'],
            ['2029','#00C896','Programmes nationaux','Engagement gouvernemental, registres nationaux, santé publique.',['Projets de santé publique'],'building-2'],
            ['2030–31','#FFB020','Leadership régional','Expansion multi-pays, infrastructure de santé numérique régionale.',['Infrastructure régionale déployée'],'globe'],
        ] : [
            ['2026','#475569','Foundation','Product stabilization, pilot deployments, governance implementation.',['10 facilities','2 flagship hospitals'],'pen-line'],
            ['2027','#1A6FE8','Expansion','Commercial growth, regional visibility, scale-up.',['50 facilities','10 hospitals'],'trending-up'],
            ['2028','#00A87B','Interoperability','Health ID rollout, HIE deployment, connected facility network.',['Connected facility network'],'share-2'],
            ['2029','#00C896','National programs','Government engagement, national registries, public health projects.',['Public health projects launched'],'building-2'],
            ['2030–31','#FFB020','Regional leadership','Multi-country expansion, regional digital health infrastructure.',['Regional infrastructure live'],'globe'],
        ]; @endphp
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(170px,1fr));gap:0;position:relative">
            {{-- connecting line --}}
            <div style="position:absolute;top:40px;left:10%;right:10%;height:2px;background:linear-gradient(90deg,#1e293b,#00C896,#1e293b);z-index:0;border-radius:2px"></div>
            @foreach($years as $idx => $y)
            <div style="position:relative;z-index:1;padding:0 8px;text-align:center">
                <div style="width:56px;height:56px;border-radius:50%;background:{{ $y[1] }}20;border:2px solid {{ $y[1] }};display:flex;align-items:center;justify-content:center;margin:12px auto 14px;flex-shrink:0">
                    <i data-lucide="{{ $y[5] }}" style="width:20px;height:20px;color:{{ $y[1] }}"></i>
                </div>
                <div style="font-size:10px;font-weight:800;color:{{ $y[1] }};text-transform:uppercase;letter-spacing:0.08em;margin-bottom:4px">{{ $y[0] }}</div>
                <div style="font-weight:700;color:#e2e8f0;font-size:13px;margin-bottom:6px">{{ $y[2] }}</div>
                <p style="font-size:11px;color:#64748b;line-height:1.55;margin:0 0 8px">{{ $y[3] }}</p>
                @foreach($y[4] as $goal)
                <div style="background:{{ $y[1] }}15;border:1px solid {{ $y[1] }}30;border-radius:20px;padding:3px 10px;font-size:10px;font-weight:600;color:{{ $y[1] }};margin-bottom:4px;display:inline-block">{{ $goal }}</div>
                @endforeach
            </div>
            @endforeach
        </div>
    </div>
</div>

<div class="divider"></div>

{{-- ── STRATEGIC KPIs ───────────────────────────────────────────── --}}
<div class="section" style="max-width:960px;margin:0 auto">
    <div class="section-label" style="margin-bottom:16px">
        <i data-lucide="bar-chart-2" style="width:12px;height:12px"></i>
        {{ $isFr ? 'KPIs stratégiques' : 'Strategic KPIs' }}
    </div>
    <h2 class="section-title">{{ $isFr ? 'Ce que nous mesurons pour piloter la stratégie' : 'What we measure to navigate the strategy' }}</h2>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:14px;margin-top:28px">
        @foreach($isFr
            ? [
                ['trending-up','#00C896','Croissance',['Revenus récurrents annuels','Nombre d\'établissements','Nombre d\'utilisateurs']],
                ['settings','#1A6FE8','Opérations',['Rétention client','Taux de succès des déploiements','Performance du support']],
                ['heart-pulse','#00C896','Clinique',['Complétude de la documentation','Indicateurs de sécurité clinique']],
                ['share-2','#1A6FE8','Interopérabilité',['Établissements connectés','Transactions d\'échange','Taux de correspondance d\'identité']],
            ] : [
                ['trending-up','#00C896','Growth',['Annual recurring revenue','Number of facilities','Number of users']],
                ['settings','#1A6FE8','Operations',['Customer retention','Deployment success rate','Support performance']],
                ['heart-pulse','#00C896','Clinical',['Documentation completeness','Clinical safety indicators']],
                ['share-2','#1A6FE8','Interoperability',['Connected facilities','Exchange transactions','Identity match rates']],
            ]
        as $cat)
        <div style="background:#0F172A;border:1px solid #1e293b;border-radius:12px;padding:18px">
            <div style="display:flex;align-items:center;gap:8px;margin-bottom:14px">
                <i data-lucide="{{ $cat[0] }}" style="width:14px;height:14px;color:{{ $cat[1] }}"></i>
                <div style="font-weight:700;color:#e2e8f0;font-size:13px">{{ $cat[2] }}</div>
            </div>
            @foreach($cat[3] as $kpi)
            <div style="display:flex;align-items:center;gap:6px;font-size:12px;color:#94a3b8;padding:5px 0;border-bottom:1px solid #1e293b40">
                <i data-lucide="chevron-right" style="width:10px;height:10px;color:{{ $cat[1] }};flex-shrink:0"></i>{{ $kpi }}
            </div>
            @endforeach
        </div>
        @endforeach
    </div>
</div>

<div class="divider"></div>

{{-- ── STRATEGIC OUTCOMES 2031 ─────────────────────────────────── --}}
<div class="section" style="max-width:820px;margin:0 auto;text-align:center">
    <div class="section-label" style="justify-content:center;margin-bottom:16px">
        <i data-lucide="flag" style="width:12px;height:12px"></i>
        {{ $isFr ? 'Résultats stratégiques 2031' : 'Strategic outcomes 2031' }}
    </div>
    <h2 class="section-title">{{ $isFr ? 'Ce qu\'OPES sera en 2031' : 'What OPES will be by 2031' }}</h2>
    <div style="display:flex;flex-direction:column;gap:10px;margin-top:28px;max-width:640px;margin-left:auto;margin-right:auto">
        @foreach($isFr
            ? [['#00C896','star','Un leader reconnu de la technologie de santé en Afrique'],['#1A6FE8','share-2','Un fournisseur d\'interopérabilité reconnu à l\'échelle nationale'],['#00C896','building-2','Un partenaire de confiance des gouvernements africains'],['#1A6FE8','graduation-cap','Le principal développeur de la main-d\'œuvre de santé numérique'],['#00C896','globe','Une plateforme d\'infrastructure de santé numérique régionale']]
            : [['#00C896','star','A leading healthcare technology company across Africa'],['#1A6FE8','share-2','A recognised national interoperability provider'],['#00C896','building-2','A trusted partner of African governments'],['#1A6FE8','graduation-cap','A major digital health workforce developer'],['#00C896','globe','A regional digital health infrastructure platform']]
        as $outcome)
        <div style="display:flex;align-items:center;gap:14px;background:#0F172A;border:1px solid #1e293b;border-left:3px solid {{ $outcome[0] }};border-radius:10px;padding:14px 18px;text-align:left">
            <div style="width:34px;height:34px;border-radius:8px;background:{{ $outcome[0] }}15;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <i data-lucide="{{ $outcome[1] }}" style="width:15px;height:15px;color:{{ $outcome[0] }}"></i>
            </div>
            <div style="font-size:13px;font-weight:600;color:#e2e8f0">{{ $outcome[2] }}</div>
        </div>
        @endforeach
    </div>
</div>

{{-- ── CTA ──────────────────────────────────────────────────────── --}}
<div class="demo-section">
    <h2>{{ $isFr ? 'Rejoindre la vision OPES 2031 ?' : 'Join the OPES 2031 vision?' }}</h2>
    <p>{{ $isFr
        ? 'Établissements, gouvernements, assureurs ou partenaires — découvrez comment OPES peut faire partie de votre propre feuille de route de santé numérique.'
        : 'Facilities, governments, insurers, or partners — discover how OPES can be part of your own digital health roadmap.' }}</p>
    <div style="display:flex;gap:14px;justify-content:center;flex-wrap:wrap;margin-top:12px">
        <a href="{{ url($locale.'/contact') }}" class="btn-primary">
            {{ $isFr ? 'Nous contacter' : 'Contact us' }}
            <i data-lucide="arrow-right" style="width:15px;height:15px"></i>
        </a>
        <a href="{{ url($locale.'/about') }}" class="btn-secondary">
            {{ $isFr ? 'À propos d\'OPES' : 'About OPES' }}
            <i data-lucide="info" style="width:15px;height:15px;color:#94a3b8"></i>
        </a>
    </div>
</div>

</x-layouts.app>

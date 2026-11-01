@php $locale = app()->getLocale(); $isFr = $locale === 'fr'; @endphp

<x-layouts.app
    title="{{ $isFr ? 'Pitch investisseurs — OPES Health Systems' : 'Investor Pitch Deck — OPES Health Systems' }}"
    description="{{ $isFr ? 'Présentation investisseurs OPES Health Systems : opportunité marché Afrique, plateforme OPES Health OS, modèle commercial, avantages concurrentiels et feuille de route 2026–2031.' : 'OPES Health Systems investor presentation: Africa market opportunity, OPES Health OS platform, business model, competitive advantages, and 2026–2031 roadmap.' }}">

{{-- ── HERO ──────────────────────────────────────────────────────── --}}
<div class="about-hero">
    <div class="section-label" style="justify-content:center;margin-bottom:16px">
        <i data-lucide="lock" style="width:12px;height:12px;color:#F59E0B"></i>
        {{ $isFr ? 'Présentation investisseurs confidentielle v1.0' : 'Confidential investor presentation v1.0' }}
    </div>
    <h1 class="about-title">
        OPES Health Systems
        <span class="gradient-text">{{ $isFr ? 'Pitch Investisseurs' : 'Investor Pitch Deck' }}</span>
    </h1>
    <p class="about-sub" style="max-width:680px">
        {{ $isFr
            ? 'Construire l\'infrastructure de santé numérique de l\'Afrique — une plateforme interopérable connectant prestataires de soins, assureurs, institutions de santé publique et gouvernements.'
            : 'Building the Digital Health Infrastructure of Africa — an interoperable platform connecting healthcare providers, insurers, public health institutions, and governments.' }}
    </p>
    <div style="margin-top:20px;display:inline-flex;align-items:center;gap:14px;background:#0F172A;border:1px solid #1e293b;border-radius:12px;padding:14px 22px">
        <div style="text-align:left">
            <div style="font-size:var(--fs-xs);color:var(--text-faint);margin-bottom:2px">{{ $isFr ? 'Fondateur & CEO' : 'Founder & CEO' }}</div>
            <div style="font-size:14px;font-weight:700;color:#e2e8f0">Jude Nshome</div>
        </div>
        <div style="width:1px;height:32px;background:#1e293b"></div>
        <div style="text-align:left">
            <div style="font-size:var(--fs-xs);color:var(--text-faint);margin-bottom:2px">{{ $isFr ? 'Présentation' : 'Deck' }}</div>
            <div style="font-size:14px;font-weight:700;color:#e2e8f0">20 {{ $isFr ? 'diapositives' : 'slides' }}</div>
        </div>
    </div>
</div>

{{-- ── STATS ─────────────────────────────────────────────────────── --}}
<div class="section" style="text-align:center">
    <div class="stats-bar" style="max-width:960px;margin:0 auto">
        @foreach($isFr
            ? [['20','Diapositives'],['8','Sources de revenus'],['10','Avantages concurrentiels'],['5','Phases de croissance']]
            : [['20','Slides'],['8','Revenue streams'],['10','Competitive advantages'],['5','Growth phases']]
        as $s)
        <div class="stat-item">
            <div class="stat-value">{{ $s[0] }}</div>
            <div class="stat-label">{{ $s[1] }}</div>
        </div>
        @endforeach
    </div>
</div>

<div class="divider"></div>

{{-- ── VISION + PROBLEM (2-COL) ────────────────────────────────── --}}
<div class="section" style="max-width:960px;margin:0 auto">
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:40px">
        {{-- Vision & Mission --}}
        <div>
            <div class="section-label" style="margin-bottom:16px">
                <i data-lucide="compass" style="width:12px;height:12px"></i>
                {{ $isFr ? 'Diapositive 2 — Vision & mission' : 'Slide 2 — Vision & mission' }}
            </div>
            <div style="background:#69D5F508;border:1px solid #69D5F520;border-radius:14px;padding:20px;margin-bottom:12px">
                <div style="font-size:var(--fs-2xs);font-weight:700;color:var(--brand-accent);text-transform:uppercase;letter-spacing:0.08em;margin-bottom:8px">{{ $isFr ? 'Notre vision' : 'Our vision' }}</div>
                <p style="font-size:13px;color:#e2e8f0;line-height:1.65;margin:0">{{ $isFr ? 'Devenir le fournisseur d\'infrastructure de santé numérique interopérable le plus fiable d\'Afrique.' : 'To become Africa\'s most trusted provider of interoperable digital health infrastructure.' }}</p>
            </div>
            <div style="background:#8AC5ED08;border:1px solid #8AC5ED20;border-radius:14px;padding:20px">
                <div style="font-size:var(--fs-2xs);font-weight:700;color:var(--brand-accent);text-transform:uppercase;letter-spacing:0.08em;margin-bottom:8px">{{ $isFr ? 'Notre mission' : 'Our mission' }}</div>
                <p style="font-size:13px;color:#e2e8f0;line-height:1.65;margin:0">{{ $isFr ? 'Connecter les prestataires de soins, patients, assureurs, institutions de santé publique et gouvernements via des technologies de santé sécurisées, interopérables et intelligentes.' : 'To connect healthcare providers, patients, insurers, public health institutions, and governments through secure, interoperable, and intelligent healthcare technologies.' }}</p>
            </div>
        </div>
        {{-- The Problem --}}
        <div>
            <div class="section-label" style="margin-bottom:16px">
                <i data-lucide="alert-triangle" style="width:12px;height:12px;color:#EF4444"></i>
                {{ $isFr ? 'Diapositive 3 — Le problème' : 'Slide 3 — The problem' }}
            </div>
            <h3 style="font-size:15px;font-weight:700;color:#e2e8f0;margin-bottom:14px">{{ $isFr ? '8 défis systémiques en Afrique' : '8 systemic challenges across Africa' }}</h3>
            @foreach($isFr
                ? ['Dossiers patients fragmentés','Processus papier','Interopérabilité limitée','Faible visibilité en santé publique','Mauvaise continuité des soins','Aide à la décision clinique insuffisante','Fuites de revenus dans les établissements','Absence d\'infrastructure nationale de santé numérique']
                : ['Fragmented patient records','Paper-based processes','Limited interoperability','Weak public health visibility','Poor continuity of care','Limited clinical decision support','Revenue leakage in healthcare facilities','Lack of national digital health infrastructure']
            as $prob)
            <div style="display:flex;align-items:center;gap:9px;padding:8px 10px;background:#EF444408;border-radius:7px;border-left:2px solid #EF444440;margin-bottom:5px">
                <i data-lucide="x-circle" style="width:11px;height:11px;color:#EF4444;flex-shrink:0"></i>
                <span style="font-size:var(--fs-xs);color:var(--text-muted)">{{ $prob }}</span>
            </div>
            @endforeach
        </div>
    </div>
</div>

<div class="divider"></div>

{{-- ── MARKET OPPORTUNITY ────────────────────────────────────────── --}}
<div class="section" style="max-width:960px;margin:0 auto">
    <div class="section-label" style="margin-bottom:16px">
        <i data-lucide="globe" style="width:12px;height:12px"></i>
        {{ $isFr ? 'Diapositive 4 — Opportunité de marché' : 'Slide 4 — Market opportunity' }}
    </div>
    <h2 class="section-title">{{ $isFr ? 'L\'Afrique — une des plus grandes opportunités mondiales de numérisation de santé' : 'Africa — one of the largest healthcare digitisation opportunities globally' }}</h2>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:12px;margin-top:28px">
        @foreach($isFr
            ? [['users','#69D5F5','Croissance démographique','Population en forte croissance avec des besoins de santé croissants.'],
               ['building','#8AC5ED','Urbanisation','Expansion rapide des villes nécessitant des systèmes de santé structurés.'],
               ['shield-check','#69D5F5','Couverture Santé Universelle','Mandats gouvernementaux CSU créant une demande massive de systèmes.'],
               ['building-2','#8AC5ED','Transformation numérique','Priorités nationales de transformation digitale dans toute l\'Afrique.'],
               ['trending-up','#69D5F5','Investissements santé numérique','Croissance des financements internationaux dans la e-santé africaine.'],
               ['graduation-cap','#8AC5ED','Expansion des effectifs de santé','Besoin croissant d\'outils numériques pour les professionnels de santé.']]
            : [['users','#69D5F5','Population growth','Fast-growing population with increasing healthcare needs.'],
               ['building','#8AC5ED','Urbanisation','Rapid city expansion requiring structured health systems.'],
               ['shield-check','#69D5F5','Universal Health Coverage','Government UHC mandates creating massive system demand.'],
               ['building-2','#8AC5ED','Government digital transformation','National digitalisation priorities across Africa.'],
               ['trending-up','#69D5F5','Digital health investment growth','Growing international funding in African e-health.'],
               ['graduation-cap','#8AC5ED','Healthcare workforce expansion','Growing need for digital tools for healthcare professionals.']]
        as $opp)
        <div style="background:#0F172A;border:1px solid #1e293b;border-radius:12px;padding:16px">
            <div style="width:32px;height:32px;border-radius:8px;background:{{ $opp[1] }}15;display:flex;align-items:center;justify-content:center;margin-bottom:10px">
                <i data-lucide="{{ $opp[0] }}" style="width:14px;height:14px;color:{{ $opp[1] }}"></i>
            </div>
            <div style="font-size:var(--fs-xs);font-weight:700;color:#e2e8f0;margin-bottom:5px">{{ $opp[2] }}</div>
            <div style="font-size:var(--fs-xs);color:var(--text-muted);line-height:1.5">{{ $opp[3] }}</div>
        </div>
        @endforeach
    </div>
</div>

<div class="divider"></div>

{{-- ── SOLUTION + PLATFORM ARCHITECTURE (2-COL) ───────────────── --}}
<div class="section" style="max-width:960px;margin:0 auto">
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:40px">
        {{-- Solution --}}
        <div>
            <div class="section-label" style="margin-bottom:16px">
                <i data-lucide="layers" style="width:12px;height:12px"></i>
                {{ $isFr ? 'Diapositives 5–8 — La solution' : 'Slides 5–8 — The solution' }}
            </div>
            <h3 style="font-size:15px;font-weight:700;color:#e2e8f0;margin-bottom:14px">OPES Health OS</h3>
            <p style="font-size:var(--fs-xs);color:var(--text-muted);margin-bottom:14px;line-height:1.6">{{ $isFr ? 'Système d\'exploitation de santé intégré pour prestataires, réseaux de santé, institutions de santé publique, assureurs et gouvernements.' : 'Integrated healthcare operating system for providers, health networks, public health institutions, insurers, and governments.' }}</p>
            @foreach($isFr
                ? [['activity','#69D5F5','OPES Clinic'],['hospital','#8AC5ED','OPES Hospital'],['layout-grid','#69D5F5','OPES Specialty Suite'],['share-2','#8AC5ED','OPES Care'],['cpu','#69D5F5','OPES Clinical Intelligence'],['shield-check','#8AC5ED','OPES Insurance'],['bar-chart-2','#69D5F5','OPES Public Health'],['truck','#8AC5ED','OPES Supply Chain'],['wrench','#69D5F5','OPES Biomedical'],['graduation-cap','#8AC5ED','OPES Academy']]
                : [['activity','#69D5F5','OPES Clinic'],['hospital','#8AC5ED','OPES Hospital'],['layout-grid','#69D5F5','OPES Specialty Suite'],['share-2','#8AC5ED','OPES Care'],['cpu','#69D5F5','OPES Clinical Intelligence'],['shield-check','#8AC5ED','OPES Insurance'],['bar-chart-2','#69D5F5','OPES Public Health'],['truck','#8AC5ED','OPES Supply Chain'],['wrench','#69D5F5','OPES Biomedical'],['graduation-cap','#8AC5ED','OPES Academy']]
            as $comp)
            <div style="display:flex;align-items:center;gap:8px;padding:7px 10px;background:#0F172A;border-radius:7px;border:1px solid #1e293b;margin-bottom:5px">
                <i data-lucide="{{ $comp[0] }}" style="width:12px;height:12px;color:{{ $comp[1] }};flex-shrink:0"></i>
                <span style="font-size:var(--fs-xs);color:var(--text-muted);font-weight:500">{{ $comp[2] }}</span>
            </div>
            @endforeach
        </div>
        {{-- Platform Architecture --}}
        <div>
            <div class="section-label" style="margin-bottom:16px">
                <i data-lucide="git-branch" style="width:12px;height:12px"></i>
                {{ $isFr ? 'Diapositive 6 — Architecture plateforme' : 'Slide 6 — Platform architecture' }}
            </div>
            <h3 style="font-size:15px;font-weight:700;color:#e2e8f0;margin-bottom:14px">{{ $isFr ? '7 couches interconnectées' : '7 interconnected layers' }}</h3>
            @foreach($isFr
                ? [['#69D5F5','Systèmes cliniques','EMR, HIS, systèmes de spécialités'],
                   ['#8AC5ED','Systèmes administratifs','RH, Finance, Approvisionnement'],
                   ['#69D5F5','Couche d\'interopérabilité','APIs FHIR, HL7, connecteurs HIE'],
                   ['#8AC5ED','Couche Health ID','Identifiant patient unique & MPI'],
                   ['#69D5F5','Couche intelligence clinique','CDSS, Triage, Analytique'],
                   ['#8AC5ED','Couche santé publique','Surveillance, Registres, CSU'],
                   ['#69D5F5','Couche académie','Formation, Certification, Transfert']]
                : [['#69D5F5','Clinical systems','EMR, HIS, specialty systems'],
                   ['#8AC5ED','Administrative systems','HR, Finance, Procurement'],
                   ['#69D5F5','Interoperability layer','FHIR APIs, HL7, HIE connectors'],
                   ['#8AC5ED','Health ID layer','Unique patient identifier & MPI'],
                   ['#69D5F5','Clinical intelligence layer','CDSS, Triage, Analytics'],
                   ['#8AC5ED','Public health layer','Surveillance, Registries, UHC'],
                   ['#69D5F5','Academy layer','Training, Certification, Transfer']]
            as $layer)
            <div style="display:flex;gap:10px;padding:9px 12px;background:#0F172A;border-radius:7px;border-left:2px solid {{ $layer[0] }};margin-bottom:5px">
                <div>
                    <div style="font-size:var(--fs-xs);font-weight:700;color:#e2e8f0">{{ $layer[1] }}</div>
                    <div style="font-size:var(--fs-2xs);color:var(--text-faint);margin-top:1px">{{ $layer[2] }}</div>
                </div>
            </div>
            @endforeach
            <div style="margin-top:14px;text-align:center;padding:12px;background:#0F172A;border-radius:10px;border:1px solid #1e293b">
                <div style="font-size:var(--fs-xs);font-weight:700;color:var(--brand-accent)">{{ $isFr ? 'Une plateforme · Une identité santé · Un écosystème connecté' : 'One Platform · One Health Identity · One Connected Ecosystem' }}</div>
            </div>
        </div>
    </div>
</div>

<div class="divider"></div>

{{-- ── WHY WE WIN + COMPETITIVE ADVANTAGES ─────────────────────── --}}
<div class="section" style="max-width:960px;margin:0 auto">
    <div class="section-label" style="margin-bottom:16px">
        <i data-lucide="trophy" style="width:12px;height:12px"></i>
        {{ $isFr ? 'Diapositives 9–10 — Avantages concurrentiels' : 'Slides 9–10 — Competitive advantages' }}
    </div>
    <h2 class="section-title">{{ $isFr ? 'Pourquoi OPES gagne — 10 avantages' : 'Why OPES wins — 10 advantages' }}</h2>
    <div style="background:#0F172A;border:1px solid #1e293b;border-radius:14px;padding:20px 24px;margin-top:20px;margin-bottom:20px">
        <p style="font-size:13px;color:var(--text-muted);line-height:1.7;margin:0">
            {{ $isFr
                ? 'La plupart des concurrents fournissent DME · Systèmes labo · Systèmes pharmacie — séparément. OPES fournit une infrastructure de santé, l\'interopérabilité, l\'identité santé, l\'intelligence clinique, l\'intelligence santé publique et le développement des compétences dans un seul écosystème.'
                : 'Most competitors provide EMR · Laboratory systems · Pharmacy systems — separately. OPES provides healthcare infrastructure, interoperability, health identity, clinical intelligence, public health intelligence, and workforce development within a single ecosystem.' }}
        </p>
    </div>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:10px">
        @foreach($isFr
            ? [['1','#69D5F5','share-2','Interopérabilité en premier','FHIR & HL7 natifs, pas des add-ons.'],
               ['2','#8AC5ED','map','Contexte africain','Conçu pour les réalités locales de santé.'],
               ['3','#69D5F5','wifi-off','Architecture offline-capable','Fonctionne dans les zones à faible connectivité.'],
               ['4','#8AC5ED','building-2','Prêt au niveau national','Capable de supporter des programmes nationaux de santé.'],
               ['5','#69D5F5','fingerprint','Infrastructure Health ID','Identité patient unique à travers l\'écosystème.'],
               ['6','#8AC5ED','cpu','Intelligence clinique','CDSS, triage, analytique intégrés nativement.'],
               ['7','#69D5F5','layers','Couverture plateforme E2E','Du cabinet à l\'infrastructure de santé nationale.'],
               ['8','#8AC5ED','map-pin','Capacité d\'implémentation locale','Équipes terrain locales formées.'],
               ['9','#69D5F5','graduation-cap','Développement des capacités','OPES Academy — compétences durables.'],
               ['10','#8AC5ED','handshake','Partenariat à long terme','Engagement de transformation, pas de transaction.']]
            : [['1','#69D5F5','share-2','Interoperability first','FHIR & HL7 native, not add-ons.'],
               ['2','#8AC5ED','map','African context','Designed for local healthcare realities.'],
               ['3','#69D5F5','wifi-off','Offline-capable design','Works in low-connectivity environments.'],
               ['4','#8AC5ED','building-2','National health readiness','Capable of supporting national health programmes.'],
               ['5','#69D5F5','fingerprint','Health ID infrastructure','Unique patient identity across the ecosystem.'],
               ['6','#8AC5ED','cpu','Clinical intelligence','CDSS, triage, analytics built natively.'],
               ['7','#69D5F5','layers','End-to-end platform coverage','From clinic to national health infrastructure.'],
               ['8','#8AC5ED','map-pin','Local implementation capacity','Trained local field teams.'],
               ['9','#69D5F5','graduation-cap','Capacity building','OPES Academy — sustainable skills.'],
               ['10','#8AC5ED','handshake','Long-term partnership','Transformation commitment, not a transaction.']]
        as $adv)
        <div style="display:flex;gap:10px;padding:12px 14px;background:#0F172A;border-radius:9px;border:1px solid #1e293b">
            <div style="width:22px;height:22px;border-radius:6px;background:{{ $adv[1] }}15;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:var(--fs-2xs);font-weight:800;color:{{ $adv[1] }}">{{ $adv[0] }}</div>
            <div>
                <div style="font-size:var(--fs-xs);font-weight:700;color:#e2e8f0;margin-bottom:2px">{{ $adv[3] }}</div>
                <div style="font-size:var(--fs-2xs);color:var(--text-faint)">{{ $adv[4] }}</div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<div class="divider"></div>

{{-- ── BUSINESS MODEL + MARKET ENTRY (2-COL) ──────────────────── --}}
<div class="section" style="max-width:960px;margin:0 auto">
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:40px">
        {{-- Business Model --}}
        <div>
            <div class="section-label" style="margin-bottom:16px">
                <i data-lucide="dollar-sign" style="width:12px;height:12px"></i>
                {{ $isFr ? 'Diapositive 11 — Modèle commercial' : 'Slide 11 — Business model' }}
            </div>
            <h3 style="font-size:15px;font-weight:700;color:#e2e8f0;margin-bottom:14px">{{ $isFr ? '8 sources de revenus diversifiées' : '8 diversified revenue streams' }}</h3>
            @foreach($isFr
                ? [['#69D5F5','Licences logicielles'],['#8AC5ED','Maintenance annuelle'],['#69D5F5','Services d\'implémentation'],['#8AC5ED','Services d\'interopérabilité'],['#69D5F5','Services Health ID'],['#8AC5ED','Formation & Certification'],['#69D5F5','Services gérés'],['#8AC5ED','Programmes gouvernementaux']]
                : [['#69D5F5','Software licensing'],['#8AC5ED','Annual maintenance'],['#69D5F5','Implementation services'],['#8AC5ED','Interoperability services'],['#69D5F5','Health ID services'],['#8AC5ED','Training & certification'],['#69D5F5','Managed services'],['#8AC5ED','Government programmes']]
            as $rev)
            <div style="display:flex;align-items:center;gap:10px;padding:9px 12px;background:#0F172A;border-radius:7px;border:1px solid #1e293b;margin-bottom:5px">
                <div style="width:8px;height:8px;border-radius:50%;background:{{ $rev[0] }};flex-shrink:0"></div>
                <span style="font-size:var(--fs-xs);color:var(--text-muted);font-weight:500">{{ $rev[1] }}</span>
            </div>
            @endforeach
            <div style="margin-top:14px">
                <a href="{{ url($locale.'/financial-model') }}" style="display:inline-flex;align-items:center;gap:6px;font-size:var(--fs-xs);color:var(--brand-accent);font-weight:600;text-decoration:none">
                    {{ $isFr ? 'Voir le modèle financier détaillé' : 'View detailed financial model' }}
                    <i data-lucide="arrow-right" style="width:11px;height:11px"></i>
                </a>
            </div>
        </div>
        {{-- Market Entry Strategy --}}
        <div>
            <div class="section-label" style="margin-bottom:16px">
                <i data-lucide="target" style="width:12px;height:12px"></i>
                {{ $isFr ? 'Diapositive 12 — Stratégie d\'entrée marché' : 'Slide 12 — Market entry strategy' }}
            </div>
            <h3 style="font-size:15px;font-weight:700;color:#e2e8f0;margin-bottom:14px">{{ $isFr ? '5 phases de pénétration' : '5 penetration phases' }}</h3>
            @foreach($isFr
                ? [['var(--text-faint)','Phase 1','Cliniques','Premier segment — établissements de soins primaires.'],
                   ['#8AC5ED','Phase 2','Hôpitaux','Expansion vers les hôpitaux de district et régionaux.'],
                   ['#00A87B','Phase 3','Réseaux hospitaliers','Connecter les groupes multi-sites.'],
                   ['#69D5F5','Phase 4','Organisations d\'assurance','Integrer les acteurs de la couverture santé.'],
                   ['#FFB020','Phase 5','Gouvernements','Programmes nationaux CSU et plateformes de santé publique.']]
                : [['var(--text-faint)','Phase 1','Clinics','First segment — primary care facilities.'],
                   ['#8AC5ED','Phase 2','Hospitals','Expansion to district and regional hospitals.'],
                   ['#00A87B','Phase 3','Hospital networks','Connect multi-site groups.'],
                   ['#69D5F5','Phase 4','Insurance organisations','Integrate health coverage actors.'],
                   ['#FFB020','Phase 5','Governments','National UHC programmes and public health platforms.']]
            as $idx => $phase)
            <div style="display:flex;gap:12px;margin-bottom:8px">
                <div style="display:flex;flex-direction:column;align-items:center">
                    <div style="width:30px;height:30px;border-radius:50%;background:{{ $phase[0] }}20;border:1px solid {{ $phase[0] }}60;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:var(--fs-2xs);font-weight:800;color:{{ $phase[0] }}">{{ $idx + 1 }}</div>
                    @if($idx < 4)<div style="width:1px;height:10px;background:#1e293b;margin:2px 0"></div>@endif
                </div>
                <div style="padding-top:5px;margin-bottom:{{ $idx < 4 ? '0' : '0' }}">
                    <div style="font-size:var(--fs-xs);font-weight:700;color:#e2e8f0">{{ $phase[2] }}</div>
                    <div style="font-size:var(--fs-xs);color:var(--text-faint);margin-top:1px">{{ $phase[3] }}</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<div class="divider"></div>

{{-- ── STRATEGIC ROADMAP ─────────────────────────────────────────── --}}
<div class="section" style="max-width:960px;margin:0 auto">
    <div class="section-label" style="margin-bottom:16px">
        <i data-lucide="map" style="width:12px;height:12px"></i>
        {{ $isFr ? 'Diapositive 14 — Feuille de route stratégique' : 'Slide 14 — Strategic roadmap' }}
    </div>
    <h2 class="section-title">{{ $isFr ? 'Croissance 2026–2031' : 'Growth 2026–2031' }}</h2>
    <div style="position:relative;margin-top:36px">
        <div style="position:absolute;top:20px;left:20px;right:20px;height:1px;background:var(--surface);z-index:0"></div>
        <div style="display:grid;grid-template-columns:repeat(5,1fr);gap:12px;position:relative;z-index:1">
            @foreach($isFr
                ? [['2026','var(--text-faint)','Fondation','Premières cliniques et hôpitaux déployés'],
                   ['2027','#8AC5ED','Expansion','Croissance du réseau de prestataires'],
                   ['2028','#00A87B','Réseau inter.','Connexions HIE actives entre établissements'],
                   ['2029','#69D5F5','Prog. nationaux','Premiers contrats gouvernementaux'],
                   ['2030–31','#FFB020','Expansion rég.','Déploiement dans la région CEMAC et au-delà']]
                : [['2026','var(--text-faint)','Foundation','First clinics and hospitals deployed'],
                   ['2027','#8AC5ED','Expansion','Growing provider network'],
                   ['2028','#00A87B','Interoperability network','Active HIE connections between facilities'],
                   ['2029','#69D5F5','National programmes','First government contracts'],
                   ['2030–31','#FFB020','Regional expansion','Deployment across CEMAC region and beyond']]
            as $ms)
            <div style="background:#0F172A;border:1px solid {{ $ms[1] }}30;border-radius:12px;padding:16px 12px;text-align:center">
                <div style="width:36px;height:36px;border-radius:50%;background:{{ $ms[1] }}15;border:2px solid {{ $ms[1] }}40;margin:0 auto 10px;display:flex;align-items:center;justify-content:center">
                    <div style="width:10px;height:10px;border-radius:50%;background:{{ $ms[1] }}"></div>
                </div>
                <div style="font-size:var(--fs-xs);font-weight:800;color:{{ $ms[1] }};margin-bottom:4px">{{ $ms[0] }}</div>
                <div style="font-size:var(--fs-xs);font-weight:700;color:#e2e8f0;margin-bottom:6px">{{ $ms[2] }}</div>
                <div style="font-size:var(--fs-2xs);color:var(--text-faint);line-height:1.5">{{ $ms[3] }}</div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<div class="divider"></div>

{{-- ── IMPACT + GROWTH METRICS (2-COL) ────────────────────────── --}}
<div class="section" style="max-width:960px;margin:0 auto">
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:40px">
        {{-- Impact --}}
        <div>
            <div class="section-label" style="margin-bottom:16px">
                <i data-lucide="heart-pulse" style="width:12px;height:12px"></i>
                {{ $isFr ? 'Diapositive 15 — Impact attendu' : 'Slide 15 — Expected impact' }}
            </div>
            @foreach($isFr
                ? [['#69D5F5','check-circle','Meilleurs soins patients'],['#8AC5ED','check-circle','Meilleures données de santé'],['#69D5F5','check-circle','Meilleure visibilité en santé publique'],['#8AC5ED','check-circle','Meilleure utilisation des ressources'],['#69D5F5','check-circle','Systèmes de santé plus robustes'],['#8AC5ED','check-circle','Meilleur accès aux soins']]
                : [['#69D5F5','check-circle','Better patient care'],['#8AC5ED','check-circle','Better health data'],['#69D5F5','check-circle','Better public health visibility'],['#8AC5ED','check-circle','Better resource utilisation'],['#69D5F5','check-circle','Stronger health systems'],['#8AC5ED','check-circle','Better healthcare access']]
            as $impact)
            <div style="display:flex;align-items:center;gap:10px;padding:10px 12px;background:#0F172A;border-radius:8px;border:1px solid #1e293b;margin-bottom:6px">
                <i data-lucide="{{ $impact[1] }}" style="width:13px;height:13px;color:{{ $impact[0] }};flex-shrink:0"></i>
                <span style="font-size:var(--fs-xs);color:var(--text-muted)">{{ $impact[2] }}</span>
            </div>
            @endforeach
        </div>
        {{-- Growth Metrics --}}
        <div>
            <div class="section-label" style="margin-bottom:16px">
                <i data-lucide="bar-chart-2" style="width:12px;height:12px"></i>
                {{ $isFr ? 'Diapositive 17 — Métriques de croissance' : 'Slide 17 — Growth metrics' }}
            </div>
            <h3 style="font-size:15px;font-weight:700;color:#e2e8f0;margin-bottom:14px">{{ $isFr ? '6 indicateurs de suivi' : '6 tracking indicators' }}</h3>
            @foreach($isFr
                ? [['#69D5F5','Établissements connectés'],['#8AC5ED','Utilisateurs actifs'],['#69D5F5','Health IDs émis'],['#8AC5ED','Transactions d\'interopérabilité'],['#69D5F5','Croissance des revenus'],['#8AC5ED','Croissance des certifications']]
                : [['#69D5F5','Facilities connected'],['#8AC5ED','Active users'],['#69D5F5','Health IDs issued'],['#8AC5ED','Interoperability transactions'],['#69D5F5','Revenue growth'],['#8AC5ED','Certification growth']]
            as $metric)
            <div style="display:flex;align-items:center;gap:10px;padding:10px 14px;background:#0F172A;border-radius:8px;border-left:2px solid {{ $metric[0] }};margin-bottom:6px">
                <span style="font-size:var(--fs-xs);color:var(--text-muted);font-weight:500">{{ $metric[1] }}</span>
            </div>
            @endforeach
        </div>
    </div>
</div>

<div class="divider"></div>

{{-- ── PARTNERSHIP + INVESTMENT (2-COL) ───────────────────────── --}}
<div class="section" style="max-width:960px;margin:0 auto">
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:40px">
        {{-- Partnership --}}
        <div>
            <div class="section-label" style="margin-bottom:16px">
                <i data-lucide="handshake" style="width:12px;height:12px"></i>
                {{ $isFr ? 'Diapositive 18 — Opportunités de partenariat' : 'Slide 18 — Partnership opportunities' }}
            </div>
            <h3 style="font-size:15px;font-weight:700;color:#e2e8f0;margin-bottom:14px">{{ $isFr ? 'Partenaires cibles' : 'Target partners' }}</h3>
            @foreach($isFr
                ? [['building-2','#69D5F5','Ministères de la Santé'],['globe','#8AC5ED','Partenaires de développement'],['hospital','#69D5F5','Réseaux hospitaliers'],['shield-check','#8AC5ED','Organisations d\'assurance'],['graduation-cap','#69D5F5','Universités'],['cpu','#8AC5ED','Partenaires technologiques']]
                : [['building-2','#69D5F5','Ministries of Health'],['globe','#8AC5ED','Development partners'],['hospital','#69D5F5','Hospital networks'],['shield-check','#8AC5ED','Insurance organisations'],['graduation-cap','#69D5F5','Universities'],['cpu','#8AC5ED','Technology partners']]
            as $partner)
            <div style="display:flex;align-items:center;gap:10px;padding:9px 12px;background:#0F172A;border-radius:7px;border:1px solid #1e293b;margin-bottom:5px">
                <i data-lucide="{{ $partner[0] }}" style="width:12px;height:12px;color:{{ $partner[1] }};flex-shrink:0"></i>
                <span style="font-size:var(--fs-xs);color:var(--text-muted)">{{ $partner[2] }}</span>
            </div>
            @endforeach
        </div>
        {{-- Investment --}}
        <div>
            <div class="section-label" style="margin-bottom:16px">
                <i data-lucide="trending-up" style="width:12px;height:12px"></i>
                {{ $isFr ? 'Diapositive 19 — Opportunité d\'investissement' : 'Slide 19 — Investment opportunity' }}
            </div>
            <h3 style="font-size:15px;font-weight:700;color:#e2e8f0;margin-bottom:14px">{{ $isFr ? 'L\'investissement supporte' : 'Investment supports' }}</h3>
            @foreach($isFr
                ? [['#69D5F5','code','Développement produit','Accélérer le développement de la plateforme OPES Health OS.'],
                   ['#8AC5ED','map','Expansion marché','Déploiements dans de nouveaux marchés africains.'],
                   ['#69D5F5','server','Infrastructure','Renforcement de l\'infrastructure cloud et technique.'],
                   ['#8AC5ED','graduation-cap','Expansion de l\'académie','Programmes de formation et certifications supplémentaires.'],
                   ['#69D5F5','building-2','Programmes gouvernementaux','Financer les déploiements au niveau national.'],
                   ['#8AC5ED','globe','Croissance régionale','Extension dans la région CEMAC et toute l\'Afrique.']]
                : [['#69D5F5','code','Product development','Accelerate OPES Health OS platform development.'],
                   ['#8AC5ED','map','Market expansion','Deployments across new African markets.'],
                   ['#69D5F5','server','Infrastructure','Cloud and technical infrastructure strengthening.'],
                   ['#8AC5ED','graduation-cap','Academy expansion','Additional training programmes and certifications.'],
                   ['#69D5F5','building-2','Government programmes','Fund national-level deployments.'],
                   ['#8AC5ED','globe','Regional growth','Expansion across the CEMAC region and all of Africa.']]
            as $inv)
            <div style="display:flex;gap:10px;padding:10px 12px;background:#0F172A;border-radius:8px;border:1px solid #1e293b;margin-bottom:6px">
                <i data-lucide="{{ $inv[1] }}" style="width:12px;height:12px;color:{{ $inv[0] }};flex-shrink:0;margin-top:1px"></i>
                <div>
                    <div style="font-size:var(--fs-xs);font-weight:700;color:#e2e8f0">{{ $inv[2] }}</div>
                    <div style="font-size:var(--fs-2xs);color:var(--text-faint);margin-top:1px">{{ $inv[3] }}</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- ── CTA ──────────────────────────────────────────────────────── --}}
<div class="demo-section">
    <h2>{{ $isFr ? 'Construisons l\'infrastructure de santé numérique de l\'Afrique.' : 'Building the Digital Health Infrastructure of Africa.' }}</h2>
    <p>{{ $isFr
        ? 'OPES Health Systems crée un écosystème de santé connecté permettant aux prestataires, institutions de santé publique, assureurs et gouvernements de délivrer de meilleurs soins grâce à une infrastructure numérique sécurisée, interopérable et intelligente.'
        : 'OPES Health Systems is creating a connected healthcare ecosystem enabling providers, public health institutions, insurers, and governments to deliver better healthcare through secure, interoperable, and intelligent digital infrastructure.' }}</p>
    <div style="margin-top:16px;font-size:var(--fs-xs);color:var(--text-faint)">
        Jude Nshome · Founder & CEO · <a href="mailto:{{ config('company.email') }}" style="color:var(--brand-accent);text-decoration:none">{{ config('company.email') }}</a> · {{ config('company.phone') }}
    </div>
    <div style="display:flex;gap:14px;justify-content:center;flex-wrap:wrap;margin-top:16px">
        <a href="{{ url($locale.'/health-os') }}" class="btn-primary">
            {{ $isFr ? 'Catalogue produits' : 'Product catalog' }}
            <i data-lucide="arrow-right" style="width:15px;height:15px"></i>
        </a>
        <a href="{{ url($locale.'/contact') }}" class="btn-secondary">
            {{ $isFr ? 'Contacter OPES' : 'Contact OPES' }}
            <i data-lucide="mail" style="width:15px;height:15px;color:var(--text-muted)"></i>
        </a>
    </div>
</div>

</x-layouts.app>

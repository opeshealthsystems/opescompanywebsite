@php $locale = app()->getLocale(); $isFr = $locale === 'fr'; @endphp

<x-layouts.app
    title="{{ $isFr ? 'Plateforme nationale de santé numérique — OPES Health Systems' : 'National Digital Health Platform — OPES Health Systems' }}"
    description="{{ $isFr ? 'Blueprint gouvernemental OPES : Health ID national, HIE, UHC, surveillance épidémiologique, registres nationaux et tableaux de bord gouvernementaux.' : 'OPES government blueprint: National Health ID, HIE, UHC layer, disease surveillance, national registries, and government dashboards.' }}">

{{-- ── HERO ─────────────────────────────────────────────────────── --}}
<div class="about-hero">
    <div class="section-label" style="justify-content:center;margin-bottom:16px">
        <i data-lucide="building-2" style="width:12px;height:12px"></i>
        {{ $isFr ? 'Édition Gouvernement v1.0' : 'Government Edition v1.0' }}
    </div>
    <h1 class="about-title">
        {{ $isFr ? 'Plateforme nationale' : 'National digital health' }}
        <span class="gradient-text">{{ $isFr ? 'de santé numérique' : 'platform' }}</span>
    </h1>
    <p class="about-sub" style="max-width:740px">
        {{ $isFr
            ? 'Un cadre complet pour la digitalisation nationale de la santé, l\'interopérabilité, la couverture universelle, l\'intelligence de santé publique et le renforcement du système de santé — conçu pour les gouvernements et institutions de santé publique.'
            : 'A comprehensive framework for national healthcare digitization, interoperability, universal health coverage support, public health intelligence, and health system strengthening — designed for governments and public health institutions.' }}
    </p>
    {{-- National Vision --}}
    <div style="margin-top:28px;background:linear-gradient(135deg,#0f1f2e,#0d1a14);border:1px solid rgba(0,200,150,0.2);border-radius:16px;padding:24px 32px;display:inline-block;text-align:center">
        @foreach($isFr
            ? ['Un citoyen.','Une identité de santé.','Un écosystème de santé connecté.']
            : ['One Citizen.','One Health Identity.','One Connected Health Ecosystem.']
        as $line)
        <div style="font-size:15px;font-weight:700;color:#00C896;line-height:2">{{ $line }}</div>
        @endforeach
    </div>
</div>

{{-- ── STATS ────────────────────────────────────────────────────── --}}
<div class="section" style="text-align:center">
    <div class="stats-bar" style="max-width:960px;margin:0 auto">
        @foreach($isFr
            ? [['6','Composants nationaux'],['5','Phases de déploiement'],['5','Comités de gouvernance'],['7','Résultats attendus']]
            : [['6','National components'],['5','Deployment phases'],['5','Governance boards'],['7','Expected outcomes']]
        as $s)
        <div class="stat-item">
            <div class="stat-value">{{ $s[0] }}</div>
            <div class="stat-label">{{ $s[1] }}</div>
        </div>
        @endforeach
    </div>
</div>

<div class="divider"></div>

{{-- ── CORE COMPONENTS ──────────────────────────────────────────── --}}
<div class="section" style="max-width:960px;margin:0 auto">
    <div class="section-label" style="margin-bottom:16px">
        <i data-lucide="layers" style="width:12px;height:12px"></i>
        {{ $isFr ? 'Composants nationaux' : 'Core national components' }}
    </div>
    <h2 class="section-title">{{ $isFr ? 'Six piliers de l\'infrastructure nationale' : 'Six pillars of national infrastructure' }}</h2>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:14px;margin-top:28px">
        @php $comps = $isFr ? [
            ['fingerprint','#00C896','Identifiant national de santé',['Identification unique du patient','Dossiers de santé longitudinaux','Reconnaissance inter-établissements','Profils de santé citoyens']],
            ['git-merge','#1A6FE8','Index national des patients (MPI)',['Rapprochement de patients','Résolution des doublons','Gouvernance de l\'identité']],
            ['arrow-left-right','#00C896','Échange national d\'informations (HIE)',['Échange d\'informations cliniques','Échange de références','Échange de résultats de labo','Échange d\'imagerie']],
            ['stethoscope','#1A6FE8','Registre national des prestataires',['Médecins','Infirmiers','Pharmaciens','Spécialistes']],
            ['hospital','#00C896','Registre national des établissements',['Cliniques','Hôpitaux','Laboratoires','Pharmacies']],
            ['activity','#1A6FE8','Plateforme nationale d\'intelligence de santé publique',['Surveillance des maladies','Analytique de santé publique','Tableaux de bord nationaux']],
        ] : [
            ['fingerprint','#00C896','National Health ID',['Unique patient identification','Longitudinal health records','Cross-facility recognition','Citizen health profiles']],
            ['git-merge','#1A6FE8','National Master Patient Index',['Patient matching','Duplicate resolution','Identity governance']],
            ['arrow-left-right','#00C896','National Health Information Exchange',['Clinical information exchange','Referral exchange','Laboratory exchange','Imaging exchange']],
            ['stethoscope','#1A6FE8','National Provider Registry',['Physicians','Nurses','Pharmacists','Specialists']],
            ['hospital','#00C896','National Facility Registry',['Clinics','Hospitals','Laboratories','Pharmacies']],
            ['activity','#1A6FE8','National Public Health Intelligence Platform',['Disease surveillance','Public health analytics','National dashboards']],
        ]; @endphp
        @foreach($comps as $c)
        <div style="background:#0F172A;border:1px solid #1e293b;border-radius:12px;padding:18px">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:12px">
                <div style="width:36px;height:36px;border-radius:9px;background:{{ $c[1] }}15;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    <i data-lucide="{{ $c[0] }}" style="width:16px;height:16px;color:{{ $c[1] }}"></i>
                </div>
                <div style="font-weight:700;color:#e2e8f0;font-size:13px">{{ $c[2] }}</div>
            </div>
            @foreach($c[3] as $item)
            <div style="display:flex;align-items:center;gap:6px;font-size:11px;color:#64748b;padding:3px 0">
                <i data-lucide="chevron-right" style="width:10px;height:10px;color:{{ $c[1] }};flex-shrink:0"></i>{{ $item }}
            </div>
            @endforeach
        </div>
        @endforeach
    </div>
</div>

<div class="divider"></div>

{{-- ── UHC + PUBLIC HEALTH + REGISTRIES ───────────────────────── --}}
<div class="section" style="max-width:960px;margin:0 auto">
    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:20px">
        {{-- UHC --}}
        <div>
            <div style="font-size:10px;font-weight:800;color:#00C896;text-transform:uppercase;letter-spacing:0.08em;margin-bottom:14px">
                {{ $isFr ? 'Couche CSU / UHC' : 'UHC layer' }}
            </div>
            @foreach($isFr
                ? ['Enregistrement des bénéficiaires','Traitement des demandes','Remboursement des prestataires','Administration des prestations']
                : ['Beneficiary registration','Claims processing','Provider reimbursement','Benefits administration']
            as $item)
            <div style="display:flex;align-items:center;gap:8px;padding:8px 12px;background:#0F172A;border-radius:8px;margin-bottom:6px;font-size:12px;color:#94a3b8;border-left:2px solid #00C896">
                <i data-lucide="check-circle" style="width:11px;height:11px;color:#00C896;flex-shrink:0"></i>{{ $item }}
            </div>
            @endforeach
        </div>
        {{-- Public health programs --}}
        <div>
            <div style="font-size:10px;font-weight:800;color:#1A6FE8;text-transform:uppercase;letter-spacing:0.08em;margin-bottom:14px">
                {{ $isFr ? 'Programmes de santé publique' : 'Public health programs' }}
            </div>
            @foreach($isFr
                ? ['Programme VIH','Programme Tuberculose','Programme Paludisme','Programme de vaccination','Santé maternelle','Programme MNT']
                : ['HIV programme','TB programme','Malaria programme','Immunization programme','Maternal health programme','NCD programme']
            as $prog)
            <div style="display:flex;align-items:center;gap:8px;padding:8px 12px;background:#0F172A;border-radius:8px;margin-bottom:6px;font-size:12px;color:#94a3b8;border-left:2px solid #1A6FE8">
                <i data-lucide="check-circle" style="width:11px;height:11px;color:#1A6FE8;flex-shrink:0"></i>{{ $prog }}
            </div>
            @endforeach
        </div>
        {{-- National registries --}}
        <div>
            <div style="font-size:10px;font-weight:800;color:#00C896;text-transform:uppercase;letter-spacing:0.08em;margin-bottom:14px">
                {{ $isFr ? 'Registres nationaux' : 'National registries' }}
            </div>
            @foreach($isFr
                ? ['Registre de vaccination','Registre maternel','Registre des cancers','Registre des maladies chroniques','Registre des maladies rares']
                : ['Immunization registry','Maternal registry','Cancer registry','Chronic disease registry','Rare disease registry']
            as $reg)
            <div style="display:flex;align-items:center;gap:8px;padding:8px 12px;background:#0F172A;border-radius:8px;margin-bottom:6px;font-size:12px;color:#94a3b8;border-left:2px solid #00C896">
                <i data-lucide="database" style="width:11px;height:11px;color:#00C896;flex-shrink:0"></i>{{ $reg }}
            </div>
            @endforeach
        </div>
    </div>
</div>

<div class="divider"></div>

{{-- ── GOVERNMENT DASHBOARDS + GOVERNANCE ──────────────────────── --}}
<div class="section" style="max-width:960px;margin:0 auto">
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:32px">
        {{-- Dashboards --}}
        <div>
            <div class="section-label" style="margin-bottom:16px">
                <i data-lucide="layout-dashboard" style="width:12px;height:12px"></i>
                {{ $isFr ? 'Tableaux de bord gouvernementaux' : 'Government dashboards' }}
            </div>
            @foreach($isFr
                ? [['bar-chart-2','#00C896','Tableau de bord exécutif','Pour les ministres — vision nationale de haut niveau.'],['map-pin','#1A6FE8','Tableau de bord régional','Pour les autorités régionales — suivi des zones.'],['hospital','#00C896','Tableau de bord établissement','Pour la direction des établissements de soins.']]
                : [['bar-chart-2','#00C896','Executive dashboard','For ministers — national high-level view.'],['map-pin','#1A6FE8','Regional dashboard','For regional authorities — zone-level monitoring.'],['hospital','#00C896','Facility dashboard','For healthcare facility management.']]
            as $db)
            <div style="display:flex;gap:12px;align-items:flex-start;padding:14px;background:#0F172A;border-radius:10px;margin-bottom:8px;border-left:3px solid {{ $db[1] }}">
                <i data-lucide="{{ $db[0] }}" style="width:15px;height:15px;color:{{ $db[1] }};flex-shrink:0;margin-top:2px"></i>
                <div>
                    <div style="font-weight:700;color:#e2e8f0;font-size:12px;margin-bottom:3px">{{ $db[2] }}</div>
                    <div style="font-size:11px;color:#64748b">{{ $db[3] }}</div>
                </div>
            </div>
            @endforeach
        </div>
        {{-- Governance --}}
        <div>
            <div class="section-label" style="margin-bottom:16px">
                <i data-lucide="network" style="width:12px;height:12px"></i>
                {{ $isFr ? 'Modèle de gouvernance nationale' : 'National governance model' }}
            </div>
            @foreach($isFr
                ? [['#00C896','Comité de pilotage national','Supervision stratégique'],['#1A6FE8','Conseil de gouvernance technique','Supervision technologique'],['#00C896','Conseil de gouvernance clinique','Supervision clinique'],['#1A6FE8','Conseil de gouvernance des données','Gouvernance de l\'information'],['#00C896','Conseil consultatif de santé publique','Supervision santé publique']]
                : [['#00C896','National Steering Committee','Strategic oversight'],['#1A6FE8','Technical Governance Board','Technology oversight'],['#00C896','Clinical Governance Board','Clinical oversight'],['#1A6FE8','Data Governance Board','Information governance'],['#00C896','Public Health Advisory Board','Public health oversight']]
            as $gov)
            <div style="display:flex;align-items:center;gap:10px;padding:10px 12px;background:#0F172A;border-radius:8px;margin-bottom:6px;border-left:2px solid {{ $gov[0] }}">
                <div>
                    <div style="font-weight:700;font-size:11px;color:#e2e8f0">{{ $gov[1] }}</div>
                    <div style="font-size:10px;color:#64748b">{{ $gov[2] }}</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<div class="divider"></div>

{{-- ── 5-PHASE DEPLOYMENT ───────────────────────────────────────── --}}
<div class="section" style="max-width:960px;margin:0 auto">
    <div class="section-label" style="margin-bottom:16px">
        <i data-lucide="milestone" style="width:12px;height:12px"></i>
        {{ $isFr ? 'Stratégie de déploiement national' : 'National deployment strategy' }}
    </div>
    <h2 class="section-title">{{ $isFr ? 'Cinq phases vers une santé numérique nationale' : 'Five phases toward national digital health' }}</h2>
    <div style="display:grid;grid-template-columns:repeat(5,1fr);gap:0;margin-top:24px">
        @foreach($isFr
            ? [['1','#475569','Fondation','Registres & Health ID'],['2','#1A6FE8','Digitalisation','EMR & HIS'],['3','#00A87B','Interopérabilité','HIE & Référencement'],['4','#00C896','Intelligence','Surveillance & Analytique'],['5','#FFB020','Optimisation','Population & IA']]
            : [['1','#475569','Foundation','Registries & Health ID'],['2','#1A6FE8','Facility digitization','EMR & HIS'],['3','#00A87B','Interoperability','HIE & Referral exchange'],['4','#00C896','Intelligence','Surveillance & Analytics'],['5','#FFB020','Optimisation','Population health & AI']]
        as $idx => $ph)
        <div style="text-align:center;padding:0 6px;position:relative">
            @if($idx < 4)
            <div style="position:absolute;top:22px;right:-1px;width:50%;height:2px;background:{{ $ph[1] }}40;z-index:0"></div>
            @endif
            <div style="width:44px;height:44px;border-radius:50%;background:{{ $ph[1] }}20;border:2px solid {{ $ph[1] }};display:flex;align-items:center;justify-content:center;margin:0 auto 10px;font-size:14px;font-weight:800;color:{{ $ph[1] }};position:relative;z-index:1">{{ $ph[0] }}</div>
            <div style="font-weight:700;color:#e2e8f0;font-size:11px;margin-bottom:4px">{{ $ph[2] }}</div>
            <div style="font-size:10px;color:#64748b;line-height:1.4">{{ $ph[3] }}</div>
        </div>
        @endforeach
    </div>
</div>

<div class="divider"></div>

{{-- ── EXPECTED OUTCOMES ────────────────────────────────────────── --}}
<div class="section" style="max-width:720px;margin:0 auto;text-align:center">
    <div class="section-label" style="justify-content:center;margin-bottom:16px">
        <i data-lucide="flag" style="width:12px;height:12px"></i>
        {{ $isFr ? 'Résultats attendus' : 'Expected national outcomes' }}
    </div>
    <h2 class="section-title">{{ $isFr ? 'Ce que la plateforme rend possible' : 'What the platform makes possible' }}</h2>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:10px;margin-top:20px">
        @foreach($isFr
            ? [['#00C896','Meilleur accès aux soins'],['#1A6FE8','Continuité améliorée des soins'],['#00C896','Intelligence de santé publique renforcée'],['#1A6FE8','Gouvernance du système de santé plus forte'],['#00C896','Meilleure planification sanitaire'],['#1A6FE8','Gestion UHC améliorée'],['#00C896','Meilleurs résultats de santé']]
            : [['#00C896','Improved access to care'],['#1A6FE8','Improved continuity of care'],['#00C896','Better public health intelligence'],['#1A6FE8','Stronger health system governance'],['#00C896','Improved healthcare planning'],['#1A6FE8','Improved UHC management'],['#00C896','Improved health outcomes']]
        as $out)
        <div style="display:flex;align-items:center;gap:8px;background:#0F172A;border:1px solid #1e293b;border-radius:8px;padding:10px 14px;text-align:left">
            <i data-lucide="check-circle" style="width:12px;height:12px;color:{{ $out[0] }};flex-shrink:0"></i>
            <span style="font-size:12px;color:#94a3b8;font-weight:600">{{ $out[1] }}</span>
        </div>
        @endforeach
    </div>
</div>

{{-- ── CTA ──────────────────────────────────────────────────────── --}}
<div class="demo-section">
    <h2>{{ $isFr ? 'Partenariat gouvernemental ?' : 'Government partnership?' }}</h2>
    <p>{{ $isFr
        ? 'Notre équipe spécialisée peut présenter le Blueprint gouvernemental et discuter de la feuille de route pour votre contexte national.'
        : 'Our specialist team can present the Government Blueprint and discuss the roadmap for your national context.' }}</p>
    <div style="display:flex;gap:14px;justify-content:center;flex-wrap:wrap;margin-top:12px">
        <a href="{{ url($locale.'/contact') }}" class="btn-primary">
            {{ $isFr ? 'Contacter notre équipe gouvernementale' : 'Contact our government team' }}
            <i data-lucide="arrow-right" style="width:15px;height:15px"></i>
        </a>
        <a href="{{ url($locale.'/interoperability') }}" class="btn-secondary">
            {{ $isFr ? 'Interopérabilité & HIE' : 'Interoperability & HIE' }}
            <i data-lucide="share-2" style="width:15px;height:15px;color:#94a3b8"></i>
        </a>
    </div>
</div>

</x-layouts.app>

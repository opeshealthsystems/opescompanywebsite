@php $locale = app()->getLocale(); $isFr = $locale === 'fr'; @endphp

<x-layouts.app
    title="{{ $isFr ? 'Modèle de proposition gouvernementale — OPES Health Systems' : 'Government Proposal Template & Donor Engagement — OPES Health Systems' }}"
    description="{{ $isFr ? 'Cadre officiel de proposition OPES pour gouvernements et bailleurs : 17 sections structurées, 6 phases d\'implémentation, gouvernance et plan de durabilité.' : 'OPES official proposal framework for governments and donors: 17 structured sections, 6 implementation phases, governance, and sustainability plan.' }}">

{{-- ── HERO ──────────────────────────────────────────────────────── --}}
<div class="about-hero">
    <div class="section-label" style="justify-content:center;margin-bottom:16px">
        <i data-lucide="lock" style="width:12px;height:12px;color:#F59E0B"></i>
        {{ $isFr ? 'Cadre officiel de proposition v1.0' : 'Official proposal framework v1.0' }}
    </div>
    <h1 class="about-title">
        {{ $isFr ? 'Modèle de proposition' : 'Government Proposal Template' }}
        <span class="gradient-text">{{ $isFr ? 'gouvernementale & bailleurs' : '& Donor Engagement Framework' }}</span>
    </h1>
    <p class="about-sub" style="max-width:760px">
        {{ $isFr
            ? 'Un cadre complet de 17 sections pour préparer des propositions techniques et financières destinées aux gouvernements, ministères de la santé, bailleurs de fonds et organisations de développement.'
            : 'A complete 17-section framework for preparing technical and financial proposals for governments, Ministries of Health, development partners, and donor organisations.' }}
    </p>
    <div style="background:#F59E0B0D;border:1px solid #F59E0B20;border-radius:10px;padding:12px 20px;margin-top:20px;display:inline-block;max-width:620px">
        <p style="font-size:11px;color:#F59E0B;text-align:center;margin:0">
            {{ $isFr
                ? 'Ce document contient des informations propriétaires et confidentielles appartenant à OPES Health Systems et est destiné exclusivement à l\'organisation destinataire.'
                : 'This document contains proprietary and confidential information belonging to OPES Health Systems and is intended solely for the recipient organisation.' }}
        </p>
    </div>
</div>

{{-- ── STATS ─────────────────────────────────────────────────────── --}}
<div class="section" style="text-align:center">
    <div class="stats-bar" style="max-width:960px;margin:0 auto">
        @foreach($isFr
            ? [['17','Sections structurées'],['6','Phases d\'implémentation'],['4','Comités de gouvernance'],['4','Piliers de durabilité']]
            : [['17','Structured sections'],['6','Implementation phases'],['4','Governance committees'],['4','Sustainability pillars']]
        as $s)
        <div class="stat-item">
            <div class="stat-value">{{ $s[0] }}</div>
            <div class="stat-label">{{ $s[1] }}</div>
        </div>
        @endforeach
    </div>
</div>

<div class="divider"></div>

{{-- ── PROPOSAL COVER STRUCTURE ─────────────────────────────────── --}}
<div class="section" style="max-width:960px;margin:0 auto">
    <div class="section-label" style="margin-bottom:16px">
        <i data-lucide="file-text" style="width:12px;height:12px"></i>
        {{ $isFr ? 'Structure de la page de couverture' : 'Cover page structure' }}
    </div>
    <h2 class="section-title">{{ $isFr ? 'En-tête de chaque proposition' : 'Header for every proposal' }}</h2>
    <div style="background:#0F172A;border:1px solid #1e293b;border-radius:14px;padding:28px 32px;margin-top:24px;max-width:720px">
        <div style="font-size:10px;font-weight:700;color:#00C896;text-transform:uppercase;letter-spacing:0.1em;margin-bottom:16px">{{ $isFr ? 'PAGE DE COUVERTURE' : 'COVER PAGE' }}</div>
        @foreach($isFr
            ? [['file-text','#00C896','Titre de la proposition','Ex: Plateforme nationale d\'échange d\'informations de santé pour la République du Cameroun'],
               ['building-2','#1A6FE8','Préparé par','OPES Health Systems — Building the Digital Health Infrastructure of Africa'],
               ['hash','#00C896','Numéro de référence','[Insérer la référence]'],
               ['calendar','#1A6FE8','Date de soumission','[Insérer la date]'],
               ['lock','#F59E0B','Déclaration de confidentialité','Ce document est propriétaire et confidentiel.']]
            : [['file-text','#00C896','Proposal title','e.g., National Health Information Exchange Platform for the Republic of Cameroon'],
               ['building-2','#1A6FE8','Prepared by','OPES Health Systems — Building the Digital Health Infrastructure of Africa'],
               ['hash','#00C896','Reference number','[Insert reference]'],
               ['calendar','#1A6FE8','Submission date','[Insert date]'],
               ['lock','#F59E0B','Confidentiality statement','This document is proprietary and confidential.']]
        as $field)
        <div style="display:flex;gap:12px;padding:10px 0;border-bottom:1px solid #0f172a">
            <i data-lucide="{{ $field[0] }}" style="width:13px;height:13px;color:{{ $field[1] }};flex-shrink:0;margin-top:2px"></i>
            <div>
                <div style="font-size:11px;font-weight:700;color:#e2e8f0">{{ $field[2] }}</div>
                <div style="font-size:11px;color:#475569;margin-top:2px">{{ $field[3] }}</div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<div class="divider"></div>

{{-- ── 17-SECTION TABLE OF CONTENTS ────────────────────────────── --}}
<div class="section" style="max-width:960px;margin:0 auto">
    <div class="section-label" style="margin-bottom:16px">
        <i data-lucide="list" style="width:12px;height:12px"></i>
        {{ $isFr ? 'Table des matières' : 'Table of contents' }}
    </div>
    <h2 class="section-title">{{ $isFr ? '17 sections de la proposition complète' : '17 sections in every complete proposal' }}</h2>
    @php $sections = $isFr ? [
        ['1','Résumé exécutif','#00C896','Aperçu concis : problème, solution proposée, résultats attendus'],
        ['2','Profil organisationnel','#1A6FE8','Présentation d\'OPES, mission, vision, solutions principales'],
        ['3','Compréhension des exigences','#00C896','Défis : cliniques, opérationnels, santé publique, interopérabilité, CSU'],
        ['4','Évaluation de la situation actuelle','#1A6FE8','Systèmes, processus, infrastructure, données et gouvernance existants'],
        ['5','Solution proposée','#00C896','Vue d\'ensemble de la plateforme, composants, bénéfices stratégiques'],
        ['6','Architecture technique','#1A6FE8','Application, infrastructure, sécurité, interopérabilité, reprise après sinistre'],
        ['7','Méthodologie d\'implémentation','#00C896','6 phases : évaluation, conception, déploiement, formation, go-live, optimisation'],
        ['8','Cadre de gouvernance','#1A6FE8','Comités de pilotage, technique, clinique et de gouvernance des données'],
        ['9','Stratégie de renforcement des capacités','#00C896','Formation utilisateurs, administrateurs, certifications, transfert de connaissances'],
        ['10','Plan de durabilité','#1A6FE8','Durabilité opérationnelle, technique, financière et institutionnelle'],
        ['11','Équipe projet','#00C896','Sponsor exécutif, directeur, architecte, lead clinique, lead sécurité, formateur'],
        ['12','Gestion des risques','#1A6FE8','Résistance utilisateurs, limites d\'infrastructure, qualité des données, sécurité, délais'],
        ['13','Proposition financière','#00C896','Licences, implémentation, formation, support, maintenance, services optionnels'],
        ['14','Cadre de suivi & évaluation','#1A6FE8','Taux d\'adoption, disponibilité, qualité des données, satisfaction, performance'],
        ['15','Résultats attendus','#00C896','Meilleurs soins, continuité, rapports, planification, utilisation des ressources'],
        ['16','Pourquoi OPES Health Systems','#1A6FE8','Interopérabilité, contexte africain, présence locale, architecture évolutive'],
        ['17','Conclusion','#00C896','Engagement partenarial pour une infrastructure de santé numérique durable'],
    ] : [
        ['1','Executive Summary','#00C896','Concise overview: problem, proposed solution, expected outcomes'],
        ['2','Organisational Profile','#1A6FE8','OPES overview, mission, vision, core solutions'],
        ['3','Understanding of Requirements','#00C896','Challenges: clinical, operational, public health, interoperability, UHC'],
        ['4','Current Situation Assessment','#1A6FE8','Existing systems, processes, infrastructure, data and governance structures'],
        ['5','Proposed Solution','#00C896','Platform overview, components, strategic benefits'],
        ['6','Technical Architecture','#1A6FE8','Application, infrastructure, security, interoperability, disaster recovery'],
        ['7','Implementation Methodology','#00C896','6 phases: assessment, design, deployment, training, go-live, optimisation'],
        ['8','Governance Framework','#1A6FE8','Steering, technical, clinical, and data governance committees'],
        ['9','Capacity Building Strategy','#00C896','User training, administrator training, certifications, knowledge transfer'],
        ['10','Sustainability Plan','#1A6FE8','Operational, technical, financial, and institutional sustainability'],
        ['11','Project Team','#00C896','Executive sponsor, director, architect, clinical lead, security lead, trainer'],
        ['12','Risk Management','#1A6FE8','User resistance, infrastructure limits, data quality, security, timeline risks'],
        ['13','Financial Proposal','#00C896','Licensing, implementation, training, support, maintenance, optional services'],
        ['14','Monitoring & Evaluation Framework','#1A6FE8','Adoption rate, availability, data quality, user satisfaction, reporting performance'],
        ['15','Expected Outcomes','#00C896','Better care, continuity, public health reporting, planning, resource utilisation'],
        ['16','Why OPES Health Systems','#1A6FE8','Interoperability, African context, local presence, scalable architecture'],
        ['17','Conclusion','#00C896','Partnership commitment for sustainable digital health infrastructure'],
    ]; @endphp
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-top:28px">
        @foreach($sections as $sec)
        <div style="display:flex;gap:10px;padding:12px 14px;background:#0F172A;border-radius:9px;border:1px solid #1e293b">
            <div style="width:24px;height:24px;border-radius:6px;background:{{ $sec[2] }}15;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:10px;font-weight:800;color:{{ $sec[2] }}">{{ $sec[0] }}</div>
            <div>
                <div style="font-size:12px;font-weight:700;color:#e2e8f0;margin-bottom:2px">{{ $sec[1] }}</div>
                <div style="font-size:10px;color:#475569;line-height:1.4">{{ $sec[2] }}</div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<div class="divider"></div>

{{-- ── SECTIONS 1 & 5: EXEC SUMMARY + PROPOSED SOLUTION (2-COL) ── --}}
<div class="section" style="max-width:960px;margin:0 auto">
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:40px">
        {{-- Executive Summary Structure --}}
        <div>
            <div class="section-label" style="margin-bottom:16px">
                <i data-lucide="file-text" style="width:12px;height:12px"></i>
                {{ $isFr ? 'Section 1 — Résumé exécutif' : 'Section 1 — Executive summary' }}
            </div>
            <h3 style="font-size:15px;font-weight:700;color:#e2e8f0;margin-bottom:14px">{{ $isFr ? 'Structure en 3 parties' : '3-part structure' }}</h3>
            @foreach($isFr
                ? [['alert-triangle','#EF4444','Problème','Défis actuels de santé auxquels le client est confronté.'],
                   ['cpu','#00C896','Solution proposée','Plateforme OPES Health OS et composants recommandés.'],
                   ['trending-up','#1A6FE8','Résultats attendus','Meilleurs soins, rapports, interopérabilité, résultats patients.']]
                : [['alert-triangle','#EF4444','Problem','Current healthcare challenges the client faces.'],
                   ['cpu','#00C896','Proposed solution','OPES Health OS platform and recommended components.'],
                   ['trending-up','#1A6FE8','Expected outcomes','Better care, reporting, interoperability, patient outcomes.']]
            as $es)
            <div style="display:flex;gap:12px;padding:14px;background:#0F172A;border-radius:10px;border-left:2px solid {{ $es[1] }};margin-bottom:10px">
                <i data-lucide="{{ $es[0] }}" style="width:14px;height:14px;color:{{ $es[1] }};flex-shrink:0;margin-top:1px"></i>
                <div>
                    <div style="font-size:12px;font-weight:700;color:#e2e8f0;margin-bottom:4px">{{ $es[2] }}</div>
                    <div style="font-size:11px;color:#64748b;line-height:1.5">{{ $es[3] }}</div>
                </div>
            </div>
            @endforeach
        </div>
        {{-- Proposed Solution Components --}}
        <div>
            <div class="section-label" style="margin-bottom:16px">
                <i data-lucide="layers" style="width:12px;height:12px"></i>
                {{ $isFr ? 'Section 5 — Solution proposée' : 'Section 5 — Proposed solution' }}
            </div>
            <h3 style="font-size:15px;font-weight:700;color:#e2e8f0;margin-bottom:14px">{{ $isFr ? 'Composants à inclure' : 'Components to include' }}</h3>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:5px;margin-bottom:16px">
                @foreach($isFr
                    ? ['Health ID','DME','HIS','Laboratoire','Pharmacie','HIE','Registres','Analytique','Surveillance']
                    : ['Health ID','EMR','HIS','Laboratory','Pharmacy','HIE','Registries','Analytics','Surveillance']
                as $comp)
                <div style="display:flex;align-items:center;gap:6px;padding:7px 9px;background:#0F172A;border-radius:7px;border:1px solid #1e293b">
                    <i data-lucide="check" style="width:10px;height:10px;color:#00C896;flex-shrink:0"></i>
                    <span style="font-size:11px;color:#94a3b8">{{ $comp }}</span>
                </div>
                @endforeach
            </div>
            <div style="font-size:11px;font-weight:600;color:#64748b;text-transform:uppercase;letter-spacing:0.06em;margin-bottom:8px">{{ $isFr ? 'Bénéfices stratégiques' : 'Strategic benefits' }}</div>
            @foreach($isFr
                ? ['Meilleurs soins patients','Meilleur reporting','Meilleure planification','Meilleure gouvernance']
                : ['Better care','Better reporting','Better planning','Better governance']
            as $ben)
            <div style="display:flex;align-items:center;gap:8px;padding:7px 0;border-bottom:1px solid #0f172a40">
                <i data-lucide="arrow-right" style="width:10px;height:10px;color:#1A6FE8;flex-shrink:0"></i>
                <span style="font-size:12px;color:#94a3b8">{{ $ben }}</span>
            </div>
            @endforeach
        </div>
    </div>
</div>

<div class="divider"></div>

{{-- ── SECTION 7: IMPLEMENTATION METHODOLOGY (6-PHASE FLOW) ────── --}}
<div class="section" style="max-width:960px;margin:0 auto">
    <div class="section-label" style="margin-bottom:16px">
        <i data-lucide="map" style="width:12px;height:12px"></i>
        {{ $isFr ? 'Section 7 — Méthodologie d\'implémentation' : 'Section 7 — Implementation methodology' }}
    </div>
    <h2 class="section-title">{{ $isFr ? 'Six phases d\'implémentation structurées' : 'Six structured implementation phases' }}</h2>
    <div style="position:relative;margin-top:36px;overflow-x:auto;padding-bottom:8px">
        <div style="display:flex;gap:0;min-width:700px;align-items:flex-start">
            @php $phases = $isFr ? [
                ['search','#00C896','1','Évaluation','Engagement parties prenantes · Évaluation infrastructure · Évaluation workflows'],
                ['cpu','#1A6FE8','2','Conception','Configuration · Conception de l\'architecture'],
                ['server','#00C896','3','Déploiement','Installation · Tests · Migration des données'],
                ['graduation-cap','#1A6FE8','4','Formation','Formation utilisateurs finaux · Formation administrateurs'],
                ['zap','#00C896','5','Go-Live','Activation en production · Support Hypercare'],
                ['trending-up','#1A6FE8','6','Optimisation','Suivi des KPIs · Amélioration continue'],
            ] : [
                ['search','#00C896','1','Assessment','Stakeholder engagement · Infrastructure assessment · Workflow assessment'],
                ['cpu','#1A6FE8','2','Design','Configuration · Architecture design'],
                ['server','#00C896','3','Deployment','Installation · Testing · Data migration'],
                ['graduation-cap','#1A6FE8','4','Training','End-user training · Administrator training'],
                ['zap','#00C896','5','Go-Live','Production activation · Hypercare support'],
                ['trending-up','#1A6FE8','6','Optimisation','KPI monitoring · Continuous improvement'],
            ]; @endphp
            @foreach($phases as $idx => $ph)
            <div style="display:flex;align-items:flex-start;flex:1;min-width:0">
                <div style="flex:1;min-width:0">
                    <div style="background:#0F172A;border:1px solid {{ $ph[1] }}30;border-top:2px solid {{ $ph[1] }};border-radius:10px;padding:14px 10px;text-align:center">
                        <div style="width:30px;height:30px;border-radius:50%;background:{{ $ph[1] }}15;display:flex;align-items:center;justify-content:center;margin:0 auto 8px">
                            <i data-lucide="{{ $ph[0] }}" style="width:13px;height:13px;color:{{ $ph[1] }}"></i>
                        </div>
                        <div style="font-size:9px;font-weight:800;color:{{ $ph[1] }};text-transform:uppercase;letter-spacing:0.08em;margin-bottom:3px">{{ $isFr ? 'Phase' : 'Phase' }} {{ $ph[2] }}</div>
                        <div style="font-size:11px;font-weight:700;color:#e2e8f0;margin-bottom:6px">{{ $ph[3] }}</div>
                        <div style="font-size:10px;color:#475569;line-height:1.5">{{ $ph[4] }}</div>
                    </div>
                </div>
                @if($idx < count($phases) - 1)
                <div style="flex-shrink:0;display:flex;align-items:center;padding:0 2px;margin-top:38px">
                    <i data-lucide="chevron-right" style="width:12px;height:12px;color:#1e293b"></i>
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
</div>

<div class="divider"></div>

{{-- ── SECTION 6 PRINCIPLES + SECTION 8 GOVERNANCE (2-COL) ─────── --}}
<div class="section" style="max-width:960px;margin:0 auto">
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:40px">
        {{-- Architecture Principles --}}
        <div>
            <div class="section-label" style="margin-bottom:16px">
                <i data-lucide="cpu" style="width:12px;height:12px"></i>
                {{ $isFr ? 'Section 6 — Principes d\'architecture' : 'Section 6 — Architecture principles' }}
            </div>
            <h3 style="font-size:15px;font-weight:700;color:#e2e8f0;margin-bottom:16px">{{ $isFr ? 'Fondations techniques non-négociables' : 'Non-negotiable technical foundations' }}</h3>
            @foreach($isFr
                ? [['share-2','#00C896','Interopérabilité en premier','FHIR R4/R5, HL7, APIs ouvertes par défaut'],
                   ['shield','#1A6FE8','Sécurité by design','Authentification, chiffrement, audit intégré dès la conception'],
                   ['lock','#00C896','Confidentialité by design','Consentement, gouvernance des données, contrôles réglementaires'],
                   ['trending-up','#1A6FE8','Évolutivité','Conçu pour évoluer du cabinet clinique à la plateforme nationale'],
                   ['activity','#00C896','Résilience','Reprise après sinistre, haute disponibilité, mode hors ligne']]
                : [['share-2','#00C896','Interoperability first','FHIR R4/R5, HL7, open APIs by default'],
                   ['shield','#1A6FE8','Security by design','Authentication, encryption, audit built in from the start'],
                   ['lock','#00C896','Privacy by design','Consent, data governance, regulatory controls'],
                   ['trending-up','#1A6FE8','Scalability','Built to scale from clinic to national platform'],
                   ['activity','#00C896','Resilience','Disaster recovery, high availability, offline mode']]
            as $prin)
            <div style="display:flex;gap:10px;padding:10px 12px;background:#0F172A;border-radius:8px;border:1px solid #1e293b;margin-bottom:7px">
                <i data-lucide="{{ $prin[0] }}" style="width:13px;height:13px;color:{{ $prin[1] }};flex-shrink:0;margin-top:1px"></i>
                <div>
                    <div style="font-size:12px;font-weight:700;color:#e2e8f0">{{ $prin[2] }}</div>
                    <div style="font-size:11px;color:#475569;margin-top:1px">{{ $prin[3] }}</div>
                </div>
            </div>
            @endforeach
        </div>
        {{-- Governance Framework --}}
        <div>
            <div class="section-label" style="margin-bottom:16px">
                <i data-lucide="users" style="width:12px;height:12px"></i>
                {{ $isFr ? 'Section 8 — Cadre de gouvernance' : 'Section 8 — Governance framework' }}
            </div>
            <h3 style="font-size:15px;font-weight:700;color:#e2e8f0;margin-bottom:16px">{{ $isFr ? '4 comités de supervision' : '4 oversight committees' }}</h3>
            @foreach($isFr
                ? [['#00C896','Comité de pilotage','Supervision stratégique — direction générale, ministère, partenaires'],
                   ['#1A6FE8','Comité technique','Supervision technique — architecture, sécurité, infrastructure'],
                   ['#00C896','Comité clinique','Supervision clinique — workflows, sécurité patient, qualité'],
                   ['#1A6FE8','Comité de gouvernance des données','Gouvernance de l\'information — confidentialité, qualité, conformité']]
                : [['#00C896','Steering Committee','Strategic oversight — executive leadership, ministry, partners'],
                   ['#1A6FE8','Technical Committee','Technical oversight — architecture, security, infrastructure'],
                   ['#00C896','Clinical Committee','Clinical oversight — workflows, patient safety, quality'],
                   ['#1A6FE8','Data Governance Committee','Information governance — privacy, data quality, compliance']]
            as $idx => $com)
            <div style="padding:16px;background:#0F172A;border-radius:10px;border-left:3px solid {{ $com[0] }};margin-bottom:10px">
                <div style="font-size:12px;font-weight:700;color:#e2e8f0;margin-bottom:4px">{{ $com[1] }}</div>
                <div style="font-size:11px;color:#64748b;line-height:1.5">{{ $com[2] }}</div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<div class="divider"></div>

{{-- ── SECTION 10 SUSTAINABILITY + SECTION 11 PROJECT TEAM (2-COL) --}}
<div class="section" style="max-width:960px;margin:0 auto">
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:40px">
        {{-- Sustainability --}}
        <div>
            <div class="section-label" style="margin-bottom:16px">
                <i data-lucide="leaf" style="width:12px;height:12px"></i>
                {{ $isFr ? 'Section 10 — Plan de durabilité' : 'Section 10 — Sustainability plan' }}
            </div>
            <h3 style="font-size:15px;font-weight:700;color:#e2e8f0;margin-bottom:16px">{{ $isFr ? '4 piliers de durabilité' : '4 sustainability pillars' }}</h3>
            @foreach($isFr
                ? [['#00C896','activity','Durabilité opérationnelle','Garantir la continuité des opérations après le déploiement initial.'],
                   ['#1A6FE8','cpu','Durabilité technique','Développer la capacité technique locale pour la maintenance et l\'évolution.'],
                   ['#00C896','dollar-sign','Durabilité financière','Soutenir la viabilité à long terme avec un modèle de financement pérenne.'],
                   ['#1A6FE8','building','Durabilité institutionnelle','Ancrer la propriété au sein des institutions locales et du gouvernement.']]
                : [['#00C896','activity','Operational sustainability','Ensure continued operation after initial deployment.'],
                   ['#1A6FE8','cpu','Technical sustainability','Develop local technical capacity for maintenance and evolution.'],
                   ['#00C896','dollar-sign','Financial sustainability','Support long-term viability with a sustainable financing model.'],
                   ['#1A6FE8','building','Institutional sustainability','Embed ownership within local institutions and government.']]
            as $sus)
            <div style="padding:14px 16px;background:#0F172A;border-radius:10px;border-left:2px solid {{ $sus[0] }};margin-bottom:8px">
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px">
                    <i data-lucide="{{ $sus[1] }}" style="width:12px;height:12px;color:{{ $sus[0] }}"></i>
                    <div style="font-size:12px;font-weight:700;color:#e2e8f0">{{ $sus[2] }}</div>
                </div>
                <div style="font-size:11px;color:#64748b;line-height:1.5">{{ $sus[3] }}</div>
            </div>
            @endforeach
        </div>
        {{-- Project Team --}}
        <div>
            <div class="section-label" style="margin-bottom:16px">
                <i data-lucide="user-check" style="width:12px;height:12px"></i>
                {{ $isFr ? 'Section 11 — Équipe projet' : 'Section 11 — Project team' }}
            </div>
            <h3 style="font-size:15px;font-weight:700;color:#e2e8f0;margin-bottom:16px">{{ $isFr ? '7 rôles clés' : '7 key roles' }}</h3>
            @foreach($isFr
                ? [['#00C896','Sponsor exécutif'],['#1A6FE8','Directeur de projet'],['#00C896','Architecte de solution'],['#1A6FE8','Lead clinique'],['#00C896','Lead implémentation'],['#1A6FE8','Lead sécurité'],['#00C896','Lead formation']]
                : [['#00C896','Executive Sponsor'],['#1A6FE8','Project Director'],['#00C896','Solution Architect'],['#1A6FE8','Clinical Lead'],['#00C896','Implementation Lead'],['#1A6FE8','Security Lead'],['#00C896','Training Lead']]
            as $role)
            <div style="display:flex;align-items:center;gap:10px;padding:9px 12px;background:#0F172A;border-radius:8px;border:1px solid #1e293b;margin-bottom:6px">
                <div style="width:8px;height:8px;border-radius:50%;background:{{ $role[0] }};flex-shrink:0"></div>
                <span style="font-size:12px;color:#94a3b8;font-weight:500">{{ $role[1] }}</span>
            </div>
            @endforeach
            <div style="margin-top:14px;padding:12px 14px;background:#00C89608;border:1px solid #00C89620;border-radius:9px">
                <div style="font-size:11px;color:#00C896;font-weight:600;margin-bottom:4px">{{ $isFr ? 'Formation via' : 'Training delivered via' }}</div>
                <div style="font-size:11px;color:#64748b">{{ $isFr ? 'OPES Academy — Utilisateurs, administrateurs, certifications et transfert de connaissances' : 'OPES Academy — Users, administrators, certifications, and knowledge transfer' }}</div>
            </div>
        </div>
    </div>
</div>

<div class="divider"></div>

{{-- ── SECTION 16: WHY OPES ─────────────────────────────────────── --}}
<div class="section" style="max-width:960px;margin:0 auto">
    <div class="section-label" style="margin-bottom:16px">
        <i data-lucide="star" style="width:12px;height:12px"></i>
        {{ $isFr ? 'Section 16 — Pourquoi OPES Health Systems' : 'Section 16 — Why OPES Health Systems' }}
    </div>
    <h2 class="section-title">{{ $isFr ? '8 raisons pour les gouvernements de choisir OPES' : '8 reasons governments choose OPES' }}</h2>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:10px;margin-top:28px">
        @foreach($isFr
            ? [['share-2','#00C896','Interopérabilité en premier','Standards HL7 FHIR par défaut, pas en option.'],
               ['map','#1A6FE8','Contexte africain','Conçu pour les défis et réalités de santé africains.'],
               ['map-pin','#00C896','Présence locale','Support terrain, maintenance et équipes d\'implémentation locales.'],
               ['trending-up','#1A6FE8','Architecture évolutive','De la clinique au système de santé national.'],
               ['fingerprint','#00C896','Infrastructure Health ID','Identité patient unique à travers tous les établissements.'],
               ['cpu','#1A6FE8','Intelligence clinique','CDSS, triage, analytique population, surveillance.'],
               ['graduation-cap','#00C896','Renforcement des capacités','Développement des compétences locales via OPES Academy.'],
               ['handshake','#1A6FE8','Partenariat à long terme','Engagement au-delà du déploiement initial.']]
            : [['share-2','#00C896','Interoperability first','HL7 FHIR standards by default, not optional.'],
               ['map','#1A6FE8','African context','Designed for African healthcare challenges and realities.'],
               ['map-pin','#00C896','Local presence','On-the-ground support, maintenance, and local implementation teams.'],
               ['trending-up','#1A6FE8','Scalable architecture','From clinic to national health system.'],
               ['fingerprint','#00C896','Health ID infrastructure','Unique patient identity across all facilities.'],
               ['cpu','#1A6FE8','Clinical intelligence','CDSS, triage, population analytics, surveillance.'],
               ['graduation-cap','#00C896','Capacity building','Local skill development through OPES Academy.'],
               ['handshake','#1A6FE8','Long-term partnership','Commitment beyond initial deployment.']]
        as $why)
        <div style="background:#0F172A;border:1px solid #1e293b;border-radius:12px;padding:16px">
            <div style="width:32px;height:32px;border-radius:8px;background:{{ $why[1] }}15;display:flex;align-items:center;justify-content:center;margin-bottom:10px">
                <i data-lucide="{{ $why[0] }}" style="width:14px;height:14px;color:{{ $why[1] }}"></i>
            </div>
            <div style="font-size:12px;font-weight:700;color:#e2e8f0;margin-bottom:5px">{{ $why[2] }}</div>
            <div style="font-size:11px;color:#64748b;line-height:1.5">{{ $why[3] }}</div>
        </div>
        @endforeach
    </div>
</div>

{{-- ── CTA ──────────────────────────────────────────────────────── --}}
<div class="demo-section">
    <h2>{{ $isFr ? 'Prêt à préparer une proposition ?' : 'Ready to prepare a proposal?' }}</h2>
    <p>{{ $isFr
        ? 'Utilisez ce cadre pour préparer des propositions complètes, techniques et financières pour les gouvernements, ministères et bailleurs de fonds qui cherchent à transformer leur infrastructure de santé numérique.'
        : 'Use this framework to prepare complete technical and financial proposals for governments, ministries, and donors seeking to transform their digital health infrastructure.' }}</p>
    <div style="display:flex;gap:14px;justify-content:center;flex-wrap:wrap;margin-top:12px">
        <a href="{{ url($locale.'/national-platform') }}" class="btn-primary">
            {{ $isFr ? 'Plateforme nationale' : 'National platform' }}
            <i data-lucide="arrow-right" style="width:15px;height:15px"></i>
        </a>
        <a href="{{ url($locale.'/contact') }}" class="btn-secondary">
            {{ $isFr ? 'Contacter notre équipe' : 'Contact our team' }}
            <i data-lucide="mail" style="width:15px;height:15px;color:#94a3b8"></i>
        </a>
    </div>
</div>

</x-layouts.app>

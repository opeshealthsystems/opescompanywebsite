@php $locale = app()->getLocale(); $isFr = $locale === 'fr'; @endphp

<x-layouts.app
    title="{{ $isFr ? 'Interopérabilité & HIE — OPES Health Systems' : 'Interoperability & HIE — OPES Health Systems' }}"
    description="{{ $isFr ? 'Cadre de gouvernance de l\'interopérabilité et de l\'échange d\'informations de santé de la plateforme OPES.' : 'OPES interoperability governance and health information exchange framework — HL7 FHIR, Health ID, MPI, HIE, API governance.' }}">

{{-- ── HERO ─────────────────────────────────────────────────────── --}}
<div class="about-hero">
    <div class="section-label" style="justify-content:center;margin-bottom:16px">
        <i data-lucide="share-2" style="width:12px;height:12px"></i>
        {{ $isFr ? 'Interopérabilité & HIE v1.0' : 'Interoperability & HIE v1.0' }}
    </div>
    <h1 class="about-title">
        {{ $isFr ? 'Échange d\'informations de santé' : 'Health Information Exchange' }}
        <span class="gradient-text">{{ $isFr ? 'gouverné & sécurisé' : 'governed & secure' }}</span>
    </h1>
    <p class="about-sub" style="max-width:720px">
        {{ $isFr
            ? 'Gouvernance, politiques, standards et contrôles opérationnels qui permettent un échange d\'informations de santé sécurisé et interopérable entre établissements, réseaux, assureurs, institutions de santé publique et programmes nationaux.'
            : 'Governance, policies, standards, and operational controls enabling secure, standards-based healthcare information exchange across organizations, networks, insurers, public health institutions, and national digital health programs.' }}
    </p>
</div>

{{-- ── STATS ────────────────────────────────────────────────────── --}}
<div class="section" style="text-align:center">
    <div class="stats-bar" style="max-width:960px;margin:0 auto">
        @foreach($isFr
            ? [['HL7 FHIR R4','Standard d\'échange principal'],['5','Comités de gouvernance'],['6','Composants d\'architecture'],['6','KPIs d\'interopérabilité']]
            : [['HL7 FHIR R4','Primary exchange standard'],['5','Governance committees'],['6','Architecture components'],['6','Interoperability KPIs']]
        as $s)
        <div class="stat-item">
            <div class="stat-value" style="font-size:clamp(16px,2.2vw,26px)">{{ $s[0] }}</div>
            <div class="stat-label">{{ $s[1] }}</div>
        </div>
        @endforeach
    </div>
</div>

<div class="divider"></div>

{{-- ── PRINCIPLES ───────────────────────────────────────────────── --}}
<div class="section" style="max-width:960px;margin:0 auto">
    <div class="section-label" style="margin-bottom:16px">
        <i data-lucide="compass" style="width:12px;height:12px"></i>
        {{ $isFr ? 'Principes d\'interopérabilité' : 'Interoperability principles' }}
    </div>
    <h2 class="section-title">{{ $isFr ? 'Six principes qui gouvernent tout échange' : 'Six principles governing every exchange' }}</h2>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:14px;margin-top:32px">
        @foreach($isFr
            ? [
                ['user','#00C896','Centré sur le patient','L\'échange d\'informations existe pour améliorer les soins. Le patient est le bénéficiaire final de chaque flux de données.'],
                ['lock','#1A6FE8','Accès autorisé uniquement','Les informations ne sont échangées qu\'entre participants autorisés, selon des politiques de consentement claires.'],
                ['minimize-2','#00C896','Minimisation des données','Seules les informations nécessaires sont échangées — aucune donnée superflue ne transite.'],
                ['file-code','#1A6FE8','Standards reconnus','L\'échange suit les standards internationaux reconnus : HL7 FHIR, ICD-11, SNOMED CT, LOINC.'],
                ['shield','#00C896','Sécurité & vie privée','Confidentialité, intégrité et disponibilité sont garanties à chaque étape de l\'échange.'],
                ['activity','#1A6FE8','Traçabilité totale','Toutes les activités d\'échange sont tracées et auditables à tout moment.'],
            ] : [
                ['user','#00C896','Patient-centred exchange','Information exchange exists to improve patient care. The patient is the ultimate beneficiary of every data flow.'],
                ['lock','#1A6FE8','Authorized access only','Information is only exchanged among authorized participants, subject to clear consent policies.'],
                ['minimize-2','#00C896','Data minimization','Only necessary information is exchanged — no superfluous data in transit.'],
                ['file-code','#1A6FE8','Standards-based','Exchange follows recognized international standards: HL7 FHIR, ICD-11, SNOMED CT, LOINC.'],
                ['shield','#00C896','Security & privacy','Confidentiality, integrity, and availability are guaranteed at every stage of exchange.'],
                ['activity','#1A6FE8','Full accountability','All exchange activities are traceable and auditable at any time.'],
            ]
        as $p)
        <div style="background:#0F172A;border:1px solid #1e293b;border-radius:12px;padding:18px 16px">
            <div style="width:36px;height:36px;border-radius:9px;background:{{ $p[1] }}15;display:flex;align-items:center;justify-content:center;margin-bottom:12px">
                <i data-lucide="{{ $p[0] }}" style="width:16px;height:16px;color:{{ $p[1] }}"></i>
            </div>
            <div style="font-weight:700;color:#e2e8f0;font-size:13px;margin-bottom:6px">{{ $p[2] }}</div>
            <div style="font-size:12px;color:var(--text-muted);line-height:1.6">{{ $p[3] }}</div>
        </div>
        @endforeach
    </div>
</div>

<div class="divider"></div>

{{-- ── GOVERNANCE STRUCTURE ─────────────────────────────────────── --}}
<div class="section" style="max-width:960px;margin:0 auto">
    <div class="section-label" style="margin-bottom:16px">
        <i data-lucide="network" style="width:12px;height:12px"></i>
        {{ $isFr ? 'Structure de gouvernance' : 'Governance structure' }}
    </div>
    <h2 class="section-title">{{ $isFr ? 'Cinq comités pour un écosystème de confiance' : 'Five committees for a trusted ecosystem' }}</h2>
    <div class="pi-grid" style="max-width:960px;margin-top:32px">
        @php $govComm = $isFr ? [
            ['globe-2','#00C896','Conseil national de gouvernance de l\'interopérabilité','Stratégie nationale, approbation des politiques, gouvernance de l\'écosystème HIE à l\'échelle nationale.'],
            ['code','#1A6FE8','Comité des standards d\'interopérabilité','Adoption et mise à jour des standards techniques, gouvernance des versions FHIR et des mappings terminologiques.'],
            ['fingerprint','#00C896','Comité de gouvernance Health ID','Politiques d\'identité, qualité des identités, résolution des doublons et cycle de vie de l\'ID de santé.'],
            ['arrow-left-right','#1A6FE8','Comité d\'échange d\'informations','Politiques d\'échange, participation des partenaires, gouvernance du partage de données et gestion du consentement.'],
            ['server','#00C896','Comité d\'architecture technique','APIs, infrastructure d\'intégration, services de registres et gouvernance des services d\'interopérabilité.'],
        ] : [
            ['globe-2','#00C896','National Interoperability Governance Board','National interoperability strategy, policy approval, ecosystem governance at national scale.'],
            ['code','#1A6FE8','Interoperability Standards Committee','Standards adoption and updates, FHIR version governance, and terminology mapping management.'],
            ['fingerprint','#00C896','Health ID Governance Committee','Identity policies, identity quality, duplicate resolution, and Health ID lifecycle management.'],
            ['arrow-left-right','#1A6FE8','Information Exchange Committee','Exchange policies, partner participation, data sharing governance, and consent management.'],
            ['server','#00C896','Technical Architecture Committee','APIs, integration infrastructure, registry services, and interoperability service governance.'],
        ]; @endphp
        @foreach($govComm as $c)
        <div class="pi-card" style="flex-direction:column;align-items:flex-start">
            <div style="width:40px;height:40px;border-radius:10px;background:{{ $c[1] }}15;display:flex;align-items:center;justify-content:center;margin-bottom:12px">
                <i data-lucide="{{ $c[0] }}" style="width:18px;height:18px;color:{{ $c[1] }}"></i>
            </div>
            <div style="font-weight:700;color:#e2e8f0;font-size:14px;margin-bottom:6px">{{ $c[2] }}</div>
            <div style="font-size:12px;color:var(--text-muted);line-height:1.6">{{ $c[3] }}</div>
        </div>
        @endforeach
    </div>
</div>

<div class="divider"></div>

{{-- ── ARCHITECTURE COMPONENTS ──────────────────────────────────── --}}
<div class="section" style="max-width:960px;margin:0 auto">
    <div class="section-label" style="margin-bottom:16px">
        <i data-lucide="layers" style="width:12px;height:12px"></i>
        {{ $isFr ? 'Architecture d\'interopérabilité' : 'Interoperability architecture' }}
    </div>
    <h2 class="section-title">{{ $isFr ? 'Six composants, un écosystème connecté' : 'Six components, one connected ecosystem' }}</h2>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:16px;margin-top:32px">
        @php $arch = $isFr ? [
            ['fingerprint','#00C896','OPES Health ID','Identification unique du patient',['Attribution de l\'identifiant','Vérification de l\'identité','Reconnaissance inter-établissements']],
            ['git-merge','#1A6FE8','Master Patient Index (MPI)','Référentiel d\'identité centralisé',['Rapprochement de patients','Détection des doublons','Résolution d\'identité']],
            ['arrow-left-right','#00C896','Health Information Exchange','Échange clinique inter-établissements',['Échange de données cliniques','Échange de résultats','Transferts et références']],
            ['shield-check','#1A6FE8','API Gateway','Portail sécurisé d\'intégration',['Sécurité API','Surveillance API','Gouvernance des versions']],
            ['book','#00C896','Terminology Services','Mapping terminologique standardisé',['Gestion ICD-11','Mapping SNOMED CT','Mapping LOINC']],
            ['database','#1A6FE8','Registres d\'interopérabilité','Annuaires de référence',['Registre des établissements','Registre des professionnels','Registre des organisations']],
        ] : [
            ['fingerprint','#00C896','OPES Health ID','Unique patient identification',['Identity assignment','Identity verification','Cross-facility recognition']],
            ['git-merge','#1A6FE8','Master Patient Index (MPI)','Centralised identity repository',['Patient matching','Duplicate detection','Identity resolution']],
            ['arrow-left-right','#00C896','Health Information Exchange','Cross-facility clinical exchange',['Clinical data exchange','Results exchange','Referrals & transfers']],
            ['shield-check','#1A6FE8','API Gateway','Secure integration portal',['API security','API monitoring','Version governance']],
            ['book','#00C896','Terminology Services','Standardised terminology mapping',['ICD-11 management','SNOMED CT mapping','LOINC mapping']],
            ['database','#1A6FE8','Interoperability Registries','Reference directories',['Facility registry','Provider registry','Organisation registry']],
        ]; @endphp
        @foreach($arch as $a)
        <div style="background:#0F172A;border:1px solid #1e293b;border-radius:12px;padding:18px 16px">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:14px">
                <div style="width:38px;height:38px;border-radius:10px;background:{{ $a[1] }}15;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    <i data-lucide="{{ $a[0] }}" style="width:17px;height:17px;color:{{ $a[1] }}"></i>
                </div>
                <div>
                    <div style="font-weight:700;color:#e2e8f0;font-size:13px">{{ $a[2] }}</div>
                    <div style="font-size:10px;color:{{ $a[1] }};font-weight:600;text-transform:uppercase;letter-spacing:0.06em">{{ $a[3] }}</div>
                </div>
            </div>
            <ul style="list-style:none;padding:0;margin:0;display:flex;flex-direction:column;gap:6px">
                @foreach($a[4] as $fn)
                <li style="display:flex;align-items:center;gap:6px;font-size:12px;color:var(--text-muted)">
                    <i data-lucide="chevron-right" style="width:11px;height:11px;color:{{ $a[1] }};flex-shrink:0"></i>{{ $fn }}
                </li>
                @endforeach
            </ul>
        </div>
        @endforeach
    </div>
</div>

<div class="divider"></div>

{{-- ── DATA EXCHANGE CATEGORIES ─────────────────────────────────── --}}
<div class="section" style="max-width:960px;margin:0 auto">
    <div class="section-label" style="margin-bottom:16px">
        <i data-lucide="folder-open" style="width:12px;height:12px"></i>
        {{ $isFr ? 'Catégories de données échangées' : 'Data exchange categories' }}
    </div>
    <h2 class="section-title">{{ $isFr ? 'Cinq domaines d\'information couverts' : 'Five information domains covered' }}</h2>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:14px;margin-top:32px">
        @php $cats = $isFr ? [
            ['stethoscope','#00C896','Informations cliniques',['Diagnostics','Médicaments','Allergies','Consultations']],
            ['microscope','#1A6FE8','Informations de laboratoire',['Demandes d\'analyses','Résultats','Informations de qualité']],
            ['scan','#00C896','Imagerie médicale',['Demandes d\'imagerie','Rapports radiologiques','Métadonnées DICOM']],
            ['arrow-right-left','#1A6FE8','Références & transferts',['Références','Contre-références','Notes de transfert']],
            ['bar-chart-2','#00C896','Santé publique',['Notifications de maladie','Rapports de surveillance','Reporting des programmes']],
        ] : [
            ['stethoscope','#00C896','Clinical information',['Diagnoses','Medications','Allergies','Encounters']],
            ['microscope','#1A6FE8','Laboratory information',['Orders','Results','Quality information']],
            ['scan','#00C896','Imaging information',['Imaging requests','Radiology reports','DICOM metadata']],
            ['arrow-right-left','#1A6FE8','Referrals & transfers',['Referrals','Counter-referrals','Transfer notes']],
            ['bar-chart-2','#00C896','Public health information',['Disease notifications','Surveillance reports','Programme reporting']],
        ]; @endphp
        @foreach($cats as $cat)
        <div style="background:#0F172A;border:1px solid #1e293b;border-radius:12px;padding:18px 16px">
            <div style="width:36px;height:36px;border-radius:9px;background:{{ $cat[1] }}15;display:flex;align-items:center;justify-content:center;margin-bottom:12px">
                <i data-lucide="{{ $cat[0] }}" style="width:16px;height:16px;color:{{ $cat[1] }}"></i>
            </div>
            <div style="font-weight:700;color:#e2e8f0;font-size:13px;margin-bottom:10px">{{ $cat[2] }}</div>
            @foreach($cat[3] as $item)
            <div style="font-size:11px;color:var(--text-muted);padding:4px 0;border-bottom:1px solid #1e293b20">{{ $item }}</div>
            @endforeach
        </div>
        @endforeach
    </div>
</div>

<div class="divider"></div>

{{-- ── API & PARTNER ONBOARDING ─────────────────────────────────── --}}
<div class="section" style="max-width:960px;margin:0 auto">
    <div class="section-label" style="margin-bottom:16px">
        <i data-lucide="plug" style="width:12px;height:12px"></i>
        {{ $isFr ? 'API & intégration partenaires' : 'API & partner integration' }}
    </div>
    <h2 class="section-title">{{ $isFr ? 'Un cadre clair pour rejoindre l\'écosystème' : 'A clear framework for joining the ecosystem' }}</h2>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:40px;margin-top:32px;align-items:start">

        {{-- API lifecycle --}}
        <div>
            <div style="font-weight:700;color:#e2e8f0;font-size:13px;margin-bottom:20px">
                {{ $isFr ? 'Cycle de vie des APIs' : 'API lifecycle' }}
            </div>
            @foreach($isFr
                ? [['pen-line','var(--text-faint)','Conception','Design de l\'API selon les standards FHIR et REST.'],['search','#00C896','Revue','Revue par le Comité d\'architecture technique.'],['badge-check','#1A6FE8','Approbation','Validation sécurité, documentation et versioning.'],['rocket','#00C896','Déploiement','Mise en production via l\'API Gateway sécurisé.'],['activity','#1A6FE8','Surveillance','Monitoring de disponibilité, performance et sécurité.'],['archive','var(--text-faint)','Retrait','Procédure formelle de dépréciation et retrait.']]
                : [['pen-line','var(--text-faint)','Design','API designed to FHIR and REST standards.'],['search','#00C896','Review','Technical Architecture Committee review.'],['badge-check','#1A6FE8','Approval','Security, documentation and versioning validation.'],['rocket','#00C896','Deployment','Production via the secured API Gateway.'],['activity','#1A6FE8','Monitoring','Availability, performance and security monitoring.'],['archive','var(--text-faint)','Retirement','Formal deprecation and retirement procedure.']]
            as $idx => $step)
            <div style="display:flex;gap:12px">
                <div style="display:flex;flex-direction:column;align-items:center">
                    <div style="width:32px;height:32px;border-radius:50%;background:{{ $step[1] }}20;border:1px solid {{ $step[1] }}40;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                        <i data-lucide="{{ $step[0] }}" style="width:13px;height:13px;color:{{ $step[1] }}"></i>
                    </div>
                    @if($idx < 5)<div style="width:1px;height:18px;background:#1e293b;margin:2px 0"></div>@endif
                </div>
                <div style="padding-top:5px;margin-bottom:{{ $idx < 5 ? '8px' : '0' }}">
                    <div style="font-weight:700;color:#e2e8f0;font-size:12px">{{ $step[2] }}</div>
                    <div style="font-size:11px;color:var(--text-muted);line-height:1.5">{{ $step[3] }}</div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Partner onboarding --}}
        <div>
            <div style="font-weight:700;color:#e2e8f0;font-size:13px;margin-bottom:14px">
                {{ $isFr ? 'Processus d\'intégration partenaire' : 'Partner onboarding process' }}
            </div>
            <p style="font-size:12px;color:var(--text-muted);line-height:1.65;margin-bottom:16px">
                {{ $isFr
                    ? 'Tout établissement ou organisation souhaitant participer à l\'échange d\'informations doit compléter le processus d\'homologation suivant.'
                    : 'Every facility or organisation wishing to participate in information exchange must complete the following certification process.' }}
            </p>
            @foreach($isFr
                ? [['#00C896','1','Enregistrement','Identification et enregistrement de l\'organisation.'],['#1A6FE8','2','Vérification','Validation légale et organisationnelle.'],['#00C896','3','Évaluation technique','Revue de la capacité d\'intégration et des systèmes.'],['#1A6FE8','4','Évaluation sécurité','Audit de sécurité et conformité aux contrôles OPES.'],['#00C896','5','Certification','Homologation formelle et signature de la charte d\'échange.']]
                : [['#00C896','1','Registration','Organisation identified and formally registered.'],['#1A6FE8','2','Verification','Legal and organisational validation.'],['#00C896','3','Technical assessment','Integration capability and systems review.'],['#1A6FE8','4','Security assessment','Security audit and OPES controls compliance check.'],['#00C896','5','Certification','Formal certification and exchange charter signature.']]
            as $step)
            <div style="display:flex;align-items:center;gap:10px;padding:10px 12px;background:#0F172A;border-radius:8px;margin-bottom:6px;border-left:3px solid {{ $step[0] }}">
                <div style="width:20px;height:20px;border-radius:50%;background:{{ $step[0] }}20;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:10px;font-weight:800;color:{{ $step[0] }}">{{ $step[1] }}</div>
                <div>
                    <div style="font-weight:700;font-size:12px;color:#e2e8f0">{{ $step[2] }}</div>
                    <div style="font-size:11px;color:var(--text-muted)">{{ $step[3] }}</div>
                </div>
            </div>
            @endforeach

            <div style="margin-top:14px;background:#0f1a2e;border:1px solid rgba(0,200,150,0.15);border-radius:10px;padding:14px 16px">
                <div style="font-size:11px;font-weight:700;color:#00C896;margin-bottom:8px">{{ $isFr ? 'Participants éligibles' : 'Eligible participants' }}</div>
                <div style="display:flex;flex-wrap:wrap;gap:6px">
                    @foreach($isFr
                        ? ['Hôpitaux','Cliniques','Laboratoires','Pharmacies','Assureurs','Programmes de santé publique','Ministères','Instituts de recherche']
                        : ['Hospitals','Clinics','Laboratories','Pharmacies','Insurers','Public health programmes','Government agencies','Research institutions']
                    as $participant)
                    <span style="background:#1e293b;color:var(--text-muted);font-size:10px;padding:3px 8px;border-radius:12px">{{ $participant }}</span>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<div class="divider"></div>

{{-- ── CONSENT & DATA SHARING ───────────────────────────────────── --}}
<div class="section" style="max-width:960px;margin:0 auto">
    <div class="section-label" style="margin-bottom:16px">
        <i data-lucide="user-check" style="width:12px;height:12px"></i>
        {{ $isFr ? 'Consentement & partage de données' : 'Consent & data sharing' }}
    </div>
    <h2 class="section-title">{{ $isFr ? 'Le patient contrôle son information' : 'The patient controls their information' }}</h2>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:16px;margin-top:32px">
        @foreach($isFr
            ? [
                ['lock','#00C896','Autorisation','Tout échange requiert une autorisation explicite — consentement du patient ou base légale documentée.'],
                ['target','#1A6FE8','Validation de la finalité','La finalité de chaque échange est validée avant transmission. Aucune réutilisation non autorisée.'],
                ['file-search','#00C896','Journalisation des audits','Chaque échange est enregistré : qui, quoi, quand, pourquoi. Piste d\'audit complète et immuable.'],
                ['clipboard-check','#1A6FE8','Gestion du consentement','Enregistrement, gestion, révocation et audit du consentement du patient via un registre dédié.'],
            ] : [
                ['lock','#00C896','Authorization','Every exchange requires explicit authorization — patient consent or a documented legal basis.'],
                ['target','#1A6FE8','Purpose validation','The purpose of each exchange is validated before transmission. No unauthorized secondary use.'],
                ['file-search','#00C896','Audit logging','Every exchange is recorded: who, what, when, why. Complete, immutable audit trail.'],
                ['clipboard-check','#1A6FE8','Consent management','Patient consent recording, management, revocation, and auditing via a dedicated consent registry.'],
            ]
        as $item)
        <div style="background:#0F172A;border:1px solid #1e293b;border-radius:12px;padding:18px 16px">
            <div style="width:36px;height:36px;border-radius:9px;background:{{ $item[1] }}15;display:flex;align-items:center;justify-content:center;margin-bottom:12px">
                <i data-lucide="{{ $item[0] }}" style="width:16px;height:16px;color:{{ $item[1] }}"></i>
            </div>
            <div style="font-weight:700;color:#e2e8f0;font-size:13px;margin-bottom:6px">{{ $item[2] }}</div>
            <div style="font-size:12px;color:var(--text-muted);line-height:1.6">{{ $item[3] }}</div>
        </div>
        @endforeach
    </div>
</div>

<div class="divider"></div>

{{-- ── KPIs ─────────────────────────────────────────────────────── --}}
<div class="section" style="max-width:960px;margin:0 auto">
    <div class="section-label" style="margin-bottom:16px">
        <i data-lucide="bar-chart-2" style="width:12px;height:12px"></i>
        {{ $isFr ? 'KPIs d\'interopérabilité' : 'Interoperability KPIs' }}
    </div>
    <h2 class="section-title">{{ $isFr ? 'Six métriques pour mesurer la santé de l\'écosystème' : 'Six metrics for a healthy exchange ecosystem' }}</h2>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:14px;margin-top:32px">
        @foreach($isFr
            ? [['arrow-left-right','#00C896','Taux de succès des échanges','% d\'échanges complétés sans erreur'],['git-merge','#1A6FE8','Taux de doublons patients','% de doublons détectés / résolus dans le MPI'],['fingerprint','#00C896','Taux de correspondance d\'identité','% d\'identités correctement réconciliées'],['send','#1A6FE8','Taux de complétion des références','% de références médicales suivies d\'un retour'],['server','#00C896','Disponibilité API','% de disponibilité des services d\'échange'],['check-circle','#1A6FE8','Score de qualité des données','Conformité aux règles de validation et de complétude']]
            : [['arrow-left-right','#00C896','Exchange success rate','% of exchanges completed without error'],['git-merge','#1A6FE8','Duplicate patient rate','% of duplicates detected / resolved in MPI'],['fingerprint','#00C896','Identity match rate','% of identities correctly reconciled'],['send','#1A6FE8','Referral completion rate','% of referrals with a documented follow-up'],['server','#00C896','API availability','% uptime of exchange services'],['check-circle','#1A6FE8','Data quality score','Conformance to validation and completeness rules']]
        as $kpi)
        <div style="background:#0F172A;border:1px solid #1e293b;border-radius:12px;padding:16px">
            <div style="display:flex;align-items:center;gap:8px;margin-bottom:8px">
                <i data-lucide="{{ $kpi[0] }}" style="width:14px;height:14px;color:{{ $kpi[1] }}"></i>
                <div style="font-weight:700;color:#e2e8f0;font-size:12px">{{ $kpi[2] }}</div>
            </div>
            <div style="font-size:11px;color:var(--text-muted);line-height:1.55">{{ $kpi[3] }}</div>
        </div>
        @endforeach
    </div>
</div>

<div class="divider"></div>

{{-- ── STANDARDS ────────────────────────────────────────────────── --}}
<div class="section" style="max-width:960px;margin:0 auto;text-align:center">
    <div class="section-label" style="justify-content:center;margin-bottom:16px">
        <i data-lucide="award" style="width:12px;height:12px"></i>
        {{ $isFr ? 'Standards supportés' : 'Supported standards' }}
    </div>
    <h2 class="section-title">{{ $isFr ? 'Construit sur les standards internationaux de santé numérique' : 'Built on international digital health standards' }}</h2>
    <div style="display:flex;flex-wrap:wrap;justify-content:center;gap:12px;margin-top:28px;max-width:700px;margin-left:auto;margin-right:auto">
        @foreach([
            ['HL7 FHIR R4','#00C896','Clinical data exchange protocol'],
            ['ICD-11','#1A6FE8','International disease classification'],
            ['SNOMED CT','#00C896','Clinical terminology system'],
            ['LOINC','#1A6FE8','Lab & clinical observations'],
            ['DICOM','#00C896','Medical imaging standard'],
            ['REST API','#1A6FE8','Web integration architecture'],
        ] as $std)
        <div style="background:#0F172A;border:1px solid {{ $std[1] }}30;border-radius:10px;padding:12px 18px;text-align:center">
            <div style="font-weight:800;color:{{ $std[1] }};font-size:13px">{{ $std[0] }}</div>
            <div style="font-size:10px;color:var(--text-faint);margin-top:3px">{{ $std[2] }}</div>
        </div>
        @endforeach
    </div>
</div>

{{-- ── CTA ──────────────────────────────────────────────────────── --}}
<div class="demo-section">
    <h2>{{ $isFr ? 'Intégrer votre système à l\'écosystème OPES ?' : 'Integrate your system into the OPES ecosystem?' }}</h2>
    <p>{{ $isFr
        ? 'Notre équipe technique peut évaluer votre capacité d\'intégration et vous guider à travers le processus d\'homologation.'
        : 'Our technical team can assess your integration capability and guide you through the partner certification process.' }}</p>
    <div style="display:flex;gap:14px;justify-content:center;flex-wrap:wrap;margin-top:12px">
        <a href="{{ url($locale.'/contact') }}" class="btn-primary">
            {{ $isFr ? 'Démarrer l\'intégration' : 'Start the integration' }}
            <i data-lucide="arrow-right" style="width:15px;height:15px"></i>
        </a>
        <a href="{{ url($locale.'/architecture') }}" class="btn-secondary">
            {{ $isFr ? 'Architecture technique' : 'Technical architecture' }}
            <i data-lucide="cpu" style="width:15px;height:15px;color:var(--text-muted)"></i>
        </a>
    </div>
</div>

</x-layouts.app>

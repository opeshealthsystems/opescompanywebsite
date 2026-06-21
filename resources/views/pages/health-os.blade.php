@php $locale = app()->getLocale(); $isFr = $locale === 'fr'; @endphp

<x-layouts.app
    title="{{ $isFr ? 'OPES Health OS — Catalogue produits & matrice de capacités' : 'OPES Health OS — Product Catalog & Capability Matrix' }}"
    description="{{ $isFr ? 'Écosystème complet OPES Health OS : 6 familles de produits, 22+ applications interopérables pour cliniques, hôpitaux, gouvernements et programmes de santé publique en Afrique.' : 'Complete OPES Health OS ecosystem: 6 product families, 22+ interoperable applications for clinics, hospitals, governments, and public health programmes across Africa.' }}">

{{-- ── HERO ──────────────────────────────────────────────────────── --}}
<div class="about-hero">
    <div class="section-label" style="justify-content:center;margin-bottom:16px">
        <i data-lucide="layers" style="width:12px;height:12px"></i>
        {{ $isFr ? 'Catalogue produits v1.0' : 'Product Catalog v1.0' }}
    </div>
    <h1 class="about-title">
        {{ $isFr ? 'OPES Health OS —' : 'OPES Health OS —' }}
        <span class="gradient-text">{{ $isFr ? 'Le système d\'exploitation santé intégré' : 'The integrated healthcare operating system' }}</span>
    </h1>
    <p class="about-sub" style="max-width:760px">
        {{ $isFr
            ? 'Un écosystème complet de 6 familles de produits et 22+ applications interopérables — conçu pour connecter cliniques, hôpitaux, laboratoires, pharmacies, assureurs, gouvernements et programmes de santé publique à travers l\'Afrique.'
            : 'A complete ecosystem of 6 product families and 22+ interoperable applications — built to connect clinics, hospitals, laboratories, pharmacies, insurers, governments, and public health programmes across Africa.' }}
    </p>
    <div style="display:flex;gap:10px;justify-content:center;flex-wrap:wrap;margin-top:24px">
        @foreach($isFr
            ? ['Cliniques','Hôpitaux','Réseaux hospitaliers','Laboratoires','Assureurs','Gouvernements','Santé publique']
            : ['Clinics','Hospitals','Hospital Networks','Laboratories','Insurers','Governments','Public Health']
        as $chip)
        <span style="background:#0F172A;border:1px solid #1e293b;border-radius:20px;padding:5px 12px;font-size:var(--fs-xs);color:var(--text-muted)">{{ $chip }}</span>
        @endforeach
    </div>
</div>

{{-- ── STATS ─────────────────────────────────────────────────────── --}}
<div class="section" style="text-align:center">
    <div class="stats-bar" style="max-width:960px;margin:0 auto">
        @foreach($isFr
            ? [['6','Familles de produits'],['22+','Applications intégrées'],['10','Types de clients'],['3','Modèles de déploiement']]
            : [['6','Product families'],['22+','Integrated applications'],['10','Customer types'],['3','Deployment models']]
        as $s)
        <div class="stat-item">
            <div class="stat-value">{{ $s[0] }}</div>
            <div class="stat-label">{{ $s[1] }}</div>
        </div>
        @endforeach
    </div>
</div>

<div class="divider"></div>

{{-- ── PLATFORM STRUCTURE — 6 FAMILY CARDS ─────────────────────── --}}
<div class="section" style="max-width:960px;margin:0 auto">
    <div class="section-label" style="margin-bottom:16px">
        <i data-lucide="git-branch" style="width:12px;height:12px"></i>
        {{ $isFr ? 'Structure de la plateforme' : 'Platform structure' }}
    </div>
    <h2 class="section-title">{{ $isFr ? 'Six familles de produits, un écosystème unifié' : 'Six product families, one unified ecosystem' }}</h2>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:14px;margin-top:28px">
        @php $families = [
            ['activity','#00C896','1','OPES Clinic',
                $isFr ? 'Cliniques & Centres ambulatoires' : 'Clinics & Outpatient Centers',
                $isFr ? ['DME','Rendez-vous','Prescriptions','Facturation','Inventaire'] : ['EMR','Appointments','Prescriptions','Billing','Inventory']],
            ['hospital','#1A6FE8','2','OPES Hospital',
                $isFr ? 'Hôpitaux de district, régionaux & de référence' : 'District, Regional & Referral Hospitals',
                $isFr ? ['Admissions','Gestion des lits','Bloc opératoire','Urgences','Tableaux de bord'] : ['Admissions','Bed management','Theatre','Emergency dept','Dashboards']],
            ['layout-grid','#00C896','3','OPES Specialty Suite',
                $isFr ? '12 systèmes d\'information départementaux' : '12 departmental information systems',
                $isFr ? ['Labo','Pharmacie','Radiologie','Cardio','Pédiatrie','Oncologie…'] : ['Lab','Pharmacy','Radiology','Cardio','Pediatrics','Oncology…']],
            ['share-2','#1A6FE8','4','OPES Care',
                $isFr ? 'Fondation d\'interopérabilité' : 'Interoperability Foundation',
                $isFr ? ['Health ID','Index Patient Maître','HIE','Registres','Portail patient'] : ['Health ID','Master Patient Index','HIE','Registries','Patient portal']],
            ['cpu','#00C896','5','OPES Clinical Intelligence',
                $isFr ? 'IA clinique & analytique de santé publique' : 'Clinical AI & population health analytics',
                $isFr ? ['CDSS','Triage','Analytique population','Surveillance maladies'] : ['CDSS','Triage','Population analytics','Disease surveillance']],
            ['graduation-cap','#1A6FE8','6','OPES Academy',
                $isFr ? 'Formation & développement des capacités' : 'Training & capacity development',
                $isFr ? ['5 certifications','Développement RH','Formation numérique'] : ['5 certifications','Workforce development','Digital health training']],
        ]; @endphp
        @foreach($families as $f)
        <div style="background:#0F172A;border:1px solid #1e293b;border-radius:14px;padding:20px;position:relative;overflow:hidden">
            <div style="position:absolute;top:10px;right:14px;font-size:32px;font-weight:900;color:{{ $f[2] === '1' || $f[2] === '3' || $f[2] === '5' ? '#00C896' : '#1A6FE8' }}06;line-height:1;pointer-events:none">{{ $f[2] }}</div>
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:12px">
                <div style="width:36px;height:36px;border-radius:9px;background:{{ $f[3] }}15;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    <i data-lucide="{{ $f[0] }}" style="width:16px;height:16px;color:{{ $f[3] }}"></i>
                </div>
                <div>
                    <div style="font-weight:700;color:#e2e8f0;font-size:13px">{{ $f[1] }}</div>
                    <div style="font-size:var(--fs-2xs);color:var(--text-faint);margin-top:1px">{{ $f[4] }}</div>
                </div>
            </div>
            <div style="display:flex;flex-wrap:wrap;gap:5px">
                @foreach($f[5] as $cap)
                <span style="background:{{ $f[3] }}10;border:1px solid {{ $f[3] }}20;border-radius:20px;padding:3px 9px;font-size:var(--fs-2xs);color:{{ $f[3] }}">{{ $cap }}</span>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>
</div>

<div class="divider"></div>

{{-- ── FAMILY 1 + 2 DETAILS (2-COL) ────────────────────────────── --}}
<div class="section" style="max-width:960px;margin:0 auto">
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:40px">
        {{-- Clinic --}}
        <div>
            <div class="section-label" style="margin-bottom:16px">
                <i data-lucide="activity" style="width:12px;height:12px"></i>
                {{ $isFr ? 'Famille 1' : 'Family 1' }}
            </div>
            <h3 style="font-size:16px;font-weight:700;color:#e2e8f0;margin-bottom:6px">OPES Clinic</h3>
            <p style="font-size:var(--fs-xs);color:var(--text-faint);margin-bottom:14px">{{ $isFr ? 'Cliniques · Cabinets médicaux · Centres ambulatoires' : 'Clinics · Medical practices · Outpatient centers' }}</p>
            @foreach($isFr
                ? ['Enregistrement des patients','Gestion des rendez-vous','Dossier médical électronique','Prescriptions','Facturation','Gestion des stocks','Rapports','Historique patient','Documentation clinique']
                : ['Patient registration','Appointment management','Electronic medical records','Prescriptions','Billing','Inventory management','Reporting','Patient history','Clinical documentation']
            as $cap)
            <div style="display:flex;align-items:center;gap:8px;padding:8px 10px;background:#0F172A;border-radius:7px;border:1px solid #1e293b;margin-bottom:5px">
                <i data-lucide="check" style="width:11px;height:11px;color:#00C896;flex-shrink:0"></i>
                <span style="font-size:var(--fs-xs);color:var(--text-muted)">{{ $cap }}</span>
            </div>
            @endforeach
        </div>
        {{-- Hospital --}}
        <div>
            <div class="section-label" style="margin-bottom:16px">
                <i data-lucide="hospital" style="width:12px;height:12px"></i>
                {{ $isFr ? 'Famille 2' : 'Family 2' }}
            </div>
            <h3 style="font-size:16px;font-weight:700;color:#e2e8f0;margin-bottom:6px">OPES Hospital</h3>
            <p style="font-size:var(--fs-xs);color:var(--text-faint);margin-bottom:14px">{{ $isFr ? 'Hôpitaux de district · Régionaux · De référence' : 'District · Regional · Referral hospitals' }}</p>
            @foreach($isFr
                ? ['Enregistrement','Admissions','Gestion des lits','Gestion des soins infirmiers','Gestion du bloc opératoire','Département des urgences','Facturation','Assurance maladie','Inventaire','Achats & approvisionnement','Rapports opérationnels','Tableaux de bord exécutifs']
                : ['Registration','Admissions','Bed management','Nursing management','Theatre management','Emergency department','Billing','Insurance','Inventory','Procurement','Operational reporting','Executive dashboards']
            as $cap)
            <div style="display:flex;align-items:center;gap:8px;padding:8px 10px;background:#0F172A;border-radius:7px;border:1px solid #1e293b;margin-bottom:5px">
                <i data-lucide="check" style="width:11px;height:11px;color:#1A6FE8;flex-shrink:0"></i>
                <span style="font-size:var(--fs-xs);color:var(--text-muted)">{{ $cap }}</span>
            </div>
            @endforeach
        </div>
    </div>
</div>

<div class="divider"></div>

{{-- ── FAMILY 3: SPECIALTY SUITE — 12 IS ───────────────────────── --}}
<div class="section" style="max-width:960px;margin:0 auto">
    <div class="section-label" style="margin-bottom:16px">
        <i data-lucide="layout-grid" style="width:12px;height:12px"></i>
        {{ $isFr ? 'Famille 3' : 'Family 3' }}
    </div>
    <h2 class="section-title">{{ $isFr ? 'OPES Specialty Suite — 12 systèmes départementaux' : 'OPES Specialty Suite — 12 departmental systems' }}</h2>
    <p style="color:var(--text-muted);font-size:14px;margin-bottom:28px">{{ $isFr ? 'Systèmes d\'information spécialisés pour chaque département clinique.' : 'Specialised information systems for every clinical department.' }}</p>
    @php $specialties = $isFr ? [
        ['microscope','#00C896','OPES Lab IS','Opérations laboratoire',['Commandes d\'analyses','Suivi des échantillons','Résultats','Contrôle qualité','Intégration instruments']],
        ['pill','#1A6FE8','OPES Pharmacy IS','Opérations pharmacie',['Traitement ordonnances','Dispensation','Inventaire médicaments','Suivi lots','Surveillance péremption']],
        ['scan','#00C896','OPES Radiology IS','Opérations imagerie',['Demandes d\'imagerie','Planification','Rapports','Workflow radiologue']],
        ['heart','#1A6FE8','OPES Cardiology IS','Services cardiologie',['Gestion ECG','Échocardiographie','Rapports cardiologie']],
        ['smile','#00C896','OPES Dental IS','Gestion cabinet dentaire',['Bilan dentaire','Procédures','Imagerie','Plan de traitement']],
        ['zap','#1A6FE8','OPES Dermatology IS','Gestion dermatologie',['Imagerie clinique','Suivi traitement','Gestion de cas']],
        ['droplet','#00C896','OPES Endocrinology IS','Diabète & soins endocriniens',['Suivi diabète','Maladies chroniques','Suivi de contrôle']],
        ['heart-pulse','#1A6FE8','OPES Obstetrics & Gynecology IS','Services santé féminine',['CPN','Gestion accouchements','Soins postnataux','Dossiers femmes']],
        ['users','#00C896','OPES Pediatrics IS','Soins pédiatriques',['Suivi croissance','Suivi vaccinations','Documentation pédiatrique']],
        ['shield','#1A6FE8','OPES Oncology IS','Prise en charge oncologique',['Plan de traitement','Suivi chimiothérapie','Dossiers oncologie']],
        ['eye','#00C896','OPES Ophthalmology IS','Services ophtalmologie',['Évaluation visuelle','Documentation clinique','Suivi procédures']],
        ['activity','#1A6FE8','OPES Orthopedics IS','Soins orthopédiques',['Gestion des fractures','Documentation procédures','Suivi postopératoire']],
    ] : [
        ['microscope','#00C896','OPES Lab IS','Laboratory operations',['Test orders','Sample tracking','Result reporting','Quality control','Instrument integration']],
        ['pill','#1A6FE8','OPES Pharmacy IS','Pharmacy operations',['Prescription processing','Dispensing','Drug inventory','Batch tracking','Expiry monitoring']],
        ['scan','#00C896','OPES Radiology IS','Imaging operations',['Imaging requests','Scheduling','Reporting','Radiologist workflow']],
        ['heart','#1A6FE8','OPES Cardiology IS','Cardiology services',['ECG management','Echocardiography','Cardiology reporting']],
        ['smile','#00C896','OPES Dental IS','Dental practice management',['Dental charting','Procedures','Imaging','Treatment planning']],
        ['zap','#1A6FE8','OPES Dermatology IS','Dermatology practice',['Clinical imaging','Treatment tracking','Case management']],
        ['droplet','#00C896','OPES Endocrinology IS','Diabetes & endocrine care',['Diabetes monitoring','Chronic disease management','Follow-up tracking']],
        ['heart-pulse','#1A6FE8','OPES Obstetrics & Gynecology IS','Women\'s health services',['ANC','Delivery management','Postnatal care','Women\'s health records']],
        ['users','#00C896','OPES Pediatrics IS','Pediatric care',['Growth monitoring','Vaccination tracking','Pediatric documentation']],
        ['shield','#1A6FE8','OPES Oncology IS','Cancer care management',['Treatment planning','Chemotherapy tracking','Oncology records']],
        ['eye','#00C896','OPES Ophthalmology IS','Eye care services',['Vision assessment','Clinical documentation','Procedure tracking']],
        ['activity','#1A6FE8','OPES Orthopedics IS','Orthopedic care',['Fracture management','Procedure documentation','Follow-up care']],
    ]; @endphp
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:12px">
        @foreach($specialties as $sp)
        <div style="background:#0F172A;border:1px solid #1e293b;border-radius:12px;padding:16px">
            <div style="display:flex;align-items:center;gap:9px;margin-bottom:10px">
                <div style="width:30px;height:30px;border-radius:8px;background:{{ $sp[1] }}15;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    <i data-lucide="{{ $sp[0] }}" style="width:13px;height:13px;color:{{ $sp[1] }}"></i>
                </div>
                <div>
                    <div style="font-weight:700;color:#e2e8f0;font-size:var(--fs-xs)">{{ $sp[2] }}</div>
                    <div style="font-size:var(--fs-2xs);color:var(--text-faint)">{{ $sp[3] }}</div>
                </div>
            </div>
            @foreach($sp[4] as $cap)
            <div style="display:flex;align-items:center;gap:6px;font-size:var(--fs-xs);color:var(--text-muted);padding:2px 0">
                <i data-lucide="chevron-right" style="width:9px;height:9px;color:{{ $sp[1] }};flex-shrink:0"></i>{{ $cap }}
            </div>
            @endforeach
        </div>
        @endforeach
    </div>
</div>

<div class="divider"></div>

{{-- ── FAMILY 4: OPES CARE ────────────────────────────────────────── --}}
<div class="section" style="max-width:960px;margin:0 auto">
    <div class="section-label" style="margin-bottom:16px">
        <i data-lucide="share-2" style="width:12px;height:12px"></i>
        {{ $isFr ? 'Famille 4' : 'Family 4' }}
    </div>
    <h2 class="section-title">{{ $isFr ? 'OPES Care — Fondation d\'interopérabilité' : 'OPES Care — Interoperability Foundation' }}</h2>
    <p style="color:var(--text-muted);font-size:14px;margin-bottom:28px">{{ $isFr ? 'L\'infrastructure qui connecte tous les produits OPES et les systèmes externes via l\'identifiant patient unique.' : 'The infrastructure that connects all OPES products and external systems through the unique patient identifier.' }}</p>
    @php $careComps = $isFr ? [
        ['fingerprint','#00C896','OPES Health ID','Identification unique du patient, reconnaissance QR, inter-établissements, dossiers longitudinaux'],
        ['users','#1A6FE8','Index Patient Maître','Détection de doublons, résolution d\'identité, correspondance de dossiers'],
        ['git-merge','#00C896','Échange d\'informations de santé','Échanges cliniques, référentiels, résultats de laboratoire, imagerie'],
        ['user-check','#1A6FE8','Registre des professionnels','Registre des professionnels de santé, accréditations, suivi des licences'],
        ['building','#00C896','Registre des établissements','Enregistrement, classification et gestion du réseau des établissements'],
        ['monitor','#1A6FE8','Portail patient','Prise de rendez-vous, accès aux dossiers et résultats, communication'],
        ['arrow-right-left','#00C896','Plateforme de référence','Création, suivi et contre-référencement'],
    ] : [
        ['fingerprint','#00C896','OPES Health ID','Unique patient identification, QR recognition, cross-facility, longitudinal records'],
        ['users','#1A6FE8','Master Patient Index','Duplicate detection, identity resolution, record matching'],
        ['git-merge','#00C896','Health Information Exchange','Clinical, referral, laboratory, and imaging exchange'],
        ['user-check','#1A6FE8','Provider Registry','Healthcare professional registry, credential management, licence tracking'],
        ['building','#00C896','Facility Registry','Facility registration, classification, and network management'],
        ['monitor','#1A6FE8','Patient Portal','Appointment booking, record & results access, patient communication'],
        ['arrow-right-left','#00C896','Referral Exchange Platform','Referral creation, tracking, and counter-referrals'],
    ]; @endphp
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:10px">
        @foreach($careComps as $c)
        <div style="display:flex;gap:12px;padding:14px 16px;background:#0F172A;border-radius:10px;border-left:2px solid {{ $c[1] }}">
            <div style="width:30px;height:30px;border-radius:8px;background:{{ $c[1] }}15;display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:1px">
                <i data-lucide="{{ $c[0] }}" style="width:13px;height:13px;color:{{ $c[1] }}"></i>
            </div>
            <div>
                <div style="font-weight:700;color:#e2e8f0;font-size:var(--fs-xs);margin-bottom:3px">{{ $c[2] }}</div>
                <div style="font-size:var(--fs-xs);color:var(--text-muted);line-height:1.5">{{ $c[3] }}</div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<div class="divider"></div>

{{-- ── FAMILY 5: CLINICAL INTELLIGENCE ─────────────────────────── --}}
<div class="section" style="max-width:960px;margin:0 auto">
    <div class="section-label" style="margin-bottom:16px">
        <i data-lucide="cpu" style="width:12px;height:12px"></i>
        {{ $isFr ? 'Famille 5' : 'Family 5' }}
    </div>
    <h2 class="section-title">{{ $isFr ? 'OPES Clinical Intelligence' : 'OPES Clinical Intelligence' }}</h2>
    <p style="color:var(--text-muted);font-size:14px;margin-bottom:28px">{{ $isFr ? 'La couche d\'intelligence clinique et d\'analytique de santé publique de la plateforme OPES.' : 'The clinical intelligence and population health analytics layer of the OPES platform.' }}</p>
    @php $ciComps = $isFr ? [
        ['cpu','#00C896','OPES CDSS','Vérification des interactions médicamenteuses, directives cliniques, aide au diagnostic, recommandations thérapeutiques'],
        ['zap','#1A6FE8','OPES Triage','Évaluation des risques, priorisation des patients, classification des urgences'],
        ['bar-chart-2','#00C896','Analytique de santé populationnelle','Tendances des maladies, stratification des risques, analyse des résultats'],
        ['activity','#1A6FE8','Surveillance des maladies','Détection des épidémies, surveillance santé publique, rapports épidémiologiques'],
        ['layout-dashboard','#00C896','Tableaux de bord Intelligence Executive','KPIs cliniques, financiers, opérationnels et de santé publique'],
        ['shield-check','#1A6FE8','Analytique qualité des soins','Indicateurs qualité clinique, conformité, benchmarking de performance'],
    ] : [
        ['cpu','#00C896','OPES CDSS','Drug interaction checking, clinical guidelines, diagnostic assistance, treatment recommendations'],
        ['zap','#1A6FE8','OPES Triage','Risk assessment, patient prioritisation, emergency classification'],
        ['bar-chart-2','#00C896','Population Health Analytics','Disease trends, risk stratification, outcome analysis'],
        ['activity','#1A6FE8','Disease Surveillance','Outbreak detection, public health monitoring, epidemiological reporting'],
        ['layout-dashboard','#00C896','Executive Intelligence Dashboards','Clinical, financial, operational, and population health KPIs'],
        ['shield-check','#1A6FE8','Healthcare Quality Analytics','Clinical quality indicators, compliance monitoring, performance benchmarking'],
    ]; @endphp
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:10px">
        @foreach($ciComps as $c)
        <div style="display:flex;gap:12px;padding:14px 16px;background:#0F172A;border-radius:10px;border-left:2px solid {{ $c[1] }}">
            <div style="width:30px;height:30px;border-radius:8px;background:{{ $c[1] }}15;display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:1px">
                <i data-lucide="{{ $c[0] }}" style="width:13px;height:13px;color:{{ $c[1] }}"></i>
            </div>
            <div>
                <div style="font-weight:700;color:#e2e8f0;font-size:var(--fs-xs);margin-bottom:3px">{{ $c[2] }}</div>
                <div style="font-size:var(--fs-xs);color:var(--text-muted);line-height:1.5">{{ $c[3] }}</div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<div class="divider"></div>

{{-- ── FAMILY 6: ACADEMY + SHARED SERVICES (2-COL) ─────────────── --}}
<div class="section" style="max-width:960px;margin:0 auto">
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:40px">
        {{-- Academy --}}
        <div>
            <div class="section-label" style="margin-bottom:16px">
                <i data-lucide="graduation-cap" style="width:12px;height:12px"></i>
                {{ $isFr ? 'Famille 6' : 'Family 6' }}
            </div>
            <h3 style="font-size:16px;font-weight:700;color:#e2e8f0;margin-bottom:16px">OPES Academy</h3>
            <div style="margin-bottom:20px">
                <div style="font-size:var(--fs-xs);font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.06em;margin-bottom:10px">{{ $isFr ? 'Programmes de certification' : 'Certification programmes' }}</div>
                @foreach($isFr
                    ? ['Utilisateur certifié','Professionnel certifié','Administrateur certifié','Implémenteur certifié','Formateur certifié']
                    : ['Certified User','Certified Professional','Certified Administrator','Certified Implementer','Certified Trainer']
                as $cert)
                <div style="display:flex;align-items:center;gap:8px;padding:8px 10px;background:#0F172A;border-radius:7px;border:1px solid #1e293b;margin-bottom:5px">
                    <i data-lucide="award" style="width:11px;height:11px;color:#1A6FE8;flex-shrink:0"></i>
                    <span style="font-size:var(--fs-xs);color:var(--text-muted)">{{ $cert }}</span>
                </div>
                @endforeach
            </div>
            <div style="font-size:var(--fs-xs);font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.06em;margin-bottom:10px">{{ $isFr ? 'Développement des capacités' : 'Workforce development' }}</div>
            @foreach($isFr
                ? ['Formation santé numérique','Renforcement des capacités','Programmes de certification']
                : ['Digital health training','Capacity building','Certification programmes']
            as $cap)
            <div style="display:flex;align-items:center;gap:8px;padding:8px 10px;background:#0F172A;border-radius:7px;border:1px solid #1e293b;margin-bottom:5px">
                <i data-lucide="check" style="width:11px;height:11px;color:#1A6FE8;flex-shrink:0"></i>
                <span style="font-size:var(--fs-xs);color:var(--text-muted)">{{ $cap }}</span>
            </div>
            @endforeach
            <div style="margin-top:16px">
                <a href="{{ url($locale.'/academy') }}" style="display:inline-flex;align-items:center;gap:6px;font-size:var(--fs-xs);color:#1A6FE8;font-weight:600;text-decoration:none">
                    {{ $isFr ? 'Explorer l\'académie' : 'Explore the Academy' }}
                    <i data-lucide="arrow-right" style="width:11px;height:11px"></i>
                </a>
            </div>
        </div>
        {{-- Shared Services --}}
        <div>
            <div class="section-label" style="margin-bottom:16px">
                <i data-lucide="layers" style="width:12px;height:12px"></i>
                {{ $isFr ? 'Services partagés enterprise' : 'Enterprise shared services' }}
            </div>
            <h3 style="font-size:16px;font-weight:700;color:#e2e8f0;margin-bottom:6px">{{ $isFr ? 'Inclus dans tous les produits OPES' : 'Included across all OPES products' }}</h3>
            <p style="font-size:var(--fs-xs);color:var(--text-faint);margin-bottom:16px">{{ $isFr ? 'Services communs disponibles pour toute la suite.' : 'Common services available across the entire suite.' }}</p>
            @foreach($isFr
                ? [['shield','#00C896','Sécurité','Authentification, MFA, journaux d\'audit, chiffrement'],
                   ['scale','#1A6FE8','Conformité','Gestion du consentement, gouvernance des données, contrôles réglementaires'],
                   ['share-2','#00C896','Interopérabilité','APIs, FHIR R4/R5, HL7, services de terminologie'],
                   ['file-text','#1A6FE8','Rapports','Rapports opérationnels, cliniques et exécutifs']]
                : [['shield','#00C896','Security','Authentication, MFA, audit logging, encryption'],
                   ['scale','#1A6FE8','Compliance','Consent management, data governance, regulatory controls'],
                   ['share-2','#00C896','Interoperability','APIs, FHIR R4/R5, HL7, terminology services'],
                   ['file-text','#1A6FE8','Reporting','Operational, clinical & executive reporting']]
            as $svc)
            <div style="display:flex;gap:12px;padding:12px 14px;background:#0F172A;border-radius:9px;border:1px solid #1e293b;margin-bottom:8px">
                <i data-lucide="{{ $svc[0] }}" style="width:14px;height:14px;color:{{ $svc[1] }};margin-top:1px;flex-shrink:0"></i>
                <div>
                    <div style="font-size:var(--fs-xs);font-weight:700;color:#e2e8f0">{{ $svc[2] }}</div>
                    <div style="font-size:var(--fs-xs);color:var(--text-faint);margin-top:2px">{{ $svc[3] }}</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<div class="divider"></div>

{{-- ── DEPLOYMENT OPTIONS ────────────────────────────────────────── --}}
<div class="section" style="max-width:960px;margin:0 auto">
    <div class="section-label" style="margin-bottom:16px">
        <i data-lucide="server" style="width:12px;height:12px"></i>
        {{ $isFr ? 'Options de déploiement' : 'Deployment options' }}
    </div>
    <h2 class="section-title">{{ $isFr ? 'S\'adapte à chaque organisation' : 'Adapts to every organisation' }}</h2>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:14px;margin-top:28px">
        @foreach($isFr
            ? [['server','#00C896','On-Premise','Hôpitaux & Institutions gouvernementales','Contrôle total des données sur site — idéal pour les hôpitaux publics et ministères exigeant la souveraineté des données.'],
               ['cloud','#1A6FE8','Cloud privé','Réseaux hospitaliers & Grandes organisations','Scalabilité et haute disponibilité pour les groupes multi-sites nécessitant une infrastructure gérée.'],
               ['git-branch','#00C896','Hybride','Déploiements nationaux','Combine infrastructure locale et services cloud pour les programmes nationaux de santé nécessitant flexibilité et résilience.']]
            : [['server','#00C896','On-Premise','Hospitals & Government institutions','Full local data control — ideal for public hospitals and ministries requiring data sovereignty.'],
               ['cloud','#1A6FE8','Private Cloud','Hospital networks & Large organisations','Scalability and high availability for multi-site groups needing managed infrastructure.'],
               ['git-branch','#00C896','Hybrid','National deployments','Combines local infrastructure with cloud services for national health programmes requiring flexibility and resilience.']]
        as $dep)
        <div style="background:#0F172A;border:1px solid #1e293b;border-radius:14px;padding:22px">
            <div style="width:40px;height:40px;border-radius:10px;background:{{ $dep[1] }}15;display:flex;align-items:center;justify-content:center;margin-bottom:14px">
                <i data-lucide="{{ $dep[0] }}" style="width:18px;height:18px;color:{{ $dep[1] }}"></i>
            </div>
            <div style="font-size:15px;font-weight:700;color:#e2e8f0;margin-bottom:4px">{{ $dep[2] }}</div>
            <div style="font-size:var(--fs-2xs);font-weight:600;color:{{ $dep[1] }};text-transform:uppercase;letter-spacing:0.06em;margin-bottom:10px">{{ $dep[3] }}</div>
            <div style="font-size:var(--fs-xs);color:var(--text-muted);line-height:1.6">{{ $dep[4] }}</div>
        </div>
        @endforeach
    </div>
</div>

<div class="divider"></div>

{{-- ── CUSTOMER MATRIX ──────────────────────────────────────────── --}}
<div class="section" style="max-width:960px;margin:0 auto">
    <div class="section-label" style="margin-bottom:16px">
        <i data-lucide="users" style="width:12px;height:12px"></i>
        {{ $isFr ? 'Matrice clients' : 'Customer matrix' }}
    </div>
    <h2 class="section-title">{{ $isFr ? 'La solution adaptée à chaque organisation' : 'The right solution for every organisation' }}</h2>
    <div style="margin-top:28px;overflow-x:auto">
        <table style="width:100%;border-collapse:collapse;font-size:var(--fs-xs)">
            <thead>
                <tr style="border-bottom:1px solid #1e293b">
                    <th style="text-align:left;padding:10px 14px;color:var(--text-muted);font-weight:600;font-size:var(--fs-xs);text-transform:uppercase;letter-spacing:0.05em">{{ $isFr ? 'Type de client' : 'Customer type' }}</th>
                    <th style="text-align:left;padding:10px 14px;color:var(--text-muted);font-weight:600;font-size:var(--fs-xs);text-transform:uppercase;letter-spacing:0.05em">{{ $isFr ? 'Produits recommandés' : 'Recommended products' }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($isFr
                    ? [['Clinique','OPES Clinic','#00C896'],
                       ['Centre médical','OPES Clinic + Lab IS + Pharmacy IS','#1A6FE8'],
                       ['Hôpital de district','OPES Hospital','#00C896'],
                       ['Hôpital régional','OPES Hospital + OPES Care + Clinical Intelligence','#1A6FE8'],
                       ['Groupe hospitalier','OPES Health OS complet','#00C896'],
                       ['Organisation d\'assurance','OPES Care + Analytics','#1A6FE8'],
                       ['Ministère de la Santé','Plateforme nationale complète','#00C896'],
                       ['Programmes ONG','OPES Care + Registres + Analytics','#1A6FE8'],
                       ['Programmes de santé publique','Clinical Intelligence + Registres','#00C896']]
                    : [['Clinic','OPES Clinic','#00C896'],
                       ['Medical Center','OPES Clinic + Lab IS + Pharmacy IS','#1A6FE8'],
                       ['District Hospital','OPES Hospital','#00C896'],
                       ['Regional Hospital','OPES Hospital + OPES Care + Clinical Intelligence','#1A6FE8'],
                       ['Hospital Group','Full OPES Health OS','#00C896'],
                       ['Insurance Organisation','OPES Care + Analytics','#1A6FE8'],
                       ['Ministry of Health','Full National Platform','#00C896'],
                       ['NGO Programmes','OPES Care + Registries + Analytics','#1A6FE8'],
                       ['Public Health Programmes','Clinical Intelligence + Registries','#00C896']]
                as $idx => $row)
                <tr style="border-bottom:1px solid #0f172a40;{{ $idx % 2 === 0 ? 'background:#0F172A40' : '' }}">
                    <td style="padding:10px 14px;color:var(--text-muted);font-weight:600">{{ $row[0] }}</td>
                    <td style="padding:10px 14px">
                        <span style="background:{{ $row[2] }}10;border:1px solid {{ $row[2] }}25;color:{{ $row[2] }};border-radius:20px;padding:3px 10px;font-size:var(--fs-xs);font-weight:600">{{ $row[1] }}</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="divider"></div>

{{-- ── DIFFERENTIATORS + NATIONAL HEALTH (2-COL) ───────────────── --}}
<div class="section" style="max-width:960px;margin:0 auto">
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:40px">
        {{-- Strategic Differentiators --}}
        <div>
            <div class="section-label" style="margin-bottom:16px">
                <i data-lucide="star" style="width:12px;height:12px"></i>
                {{ $isFr ? 'Différenciateurs stratégiques' : 'Strategic differentiators' }}
            </div>
            <h3 style="font-size:16px;font-weight:700;color:#e2e8f0;margin-bottom:16px">{{ $isFr ? '10 raisons de choisir OPES' : '10 reasons to choose OPES' }}</h3>
            @foreach($isFr
                ? ['Architecture de plateforme unifiée','Design interopérabilité-first','Infrastructure Health ID','Couche d\'intelligence clinique','Systèmes d\'information spécialisés','Prêt pour la santé nationale','Focus sur la santé africaine','Architecture capable en mode hors-ligne','Modèles de déploiement évolutifs','Écosystème de développement des compétences']
                : ['Unified platform architecture','Interoperability-first design','Health ID infrastructure','Clinical intelligence layer','Specialty information systems','National health readiness','African healthcare focus','Offline-capable architecture','Scalable deployment models','Workforce development ecosystem']
            as $i => $diff)
            <div style="display:flex;align-items:flex-start;gap:10px;padding:8px 0;border-bottom:1px solid #0f172a">
                <div style="width:20px;height:20px;border-radius:50%;background:{{ $i % 2 === 0 ? '#00C89615' : '#1A6FE815' }};border:1px solid {{ $i % 2 === 0 ? '#00C89630' : '#1A6FE830' }};display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:var(--fs-2xs);font-weight:800;color:{{ $i % 2 === 0 ? '#00C896' : '#1A6FE8' }}">{{ $i + 1 }}</div>
                <span style="font-size:var(--fs-xs);color:var(--text-muted);padding-top:2px;line-height:1.5">{{ $diff }}</span>
            </div>
            @endforeach
        </div>
        {{-- National Digital Health --}}
        <div>
            <div class="section-label" style="margin-bottom:16px">
                <i data-lucide="building-2" style="width:12px;height:12px"></i>
                {{ $isFr ? 'Capacités numériques nationales' : 'National digital health capabilities' }}
            </div>
            <h3 style="font-size:16px;font-weight:700;color:#e2e8f0;margin-bottom:6px">{{ $isFr ? 'Prêt pour les Ministères de la Santé' : 'Ready for Ministries of Health' }}</h3>
            <p style="font-size:var(--fs-xs);color:var(--text-faint);margin-bottom:16px">{{ $isFr ? 'OPES Health OS supporte les programmes de santé nationaux à grande échelle.' : 'OPES Health OS supports large-scale national health programmes.' }}</p>
            @foreach($isFr
                ? ['Identifiant national de santé','HIE national','Registre national des professionnels','Registre national des établissements','Surveillance des maladies','Programmes de vaccination','Programmes CSU (Couverture Santé Universelle)','Tableaux de bord de santé nationaux','Analytique de santé populationnelle']
                : ['National Health ID','National HIE','National Provider Registry','National Facility Registry','Disease surveillance','Immunisation programmes','UHC programmes','National health dashboards','Population health analytics']
            as $cap)
            <div style="display:flex;align-items:center;gap:8px;padding:8px 0;border-bottom:1px solid #0f172a40">
                <i data-lucide="check-circle" style="width:12px;height:12px;color:#00C896;flex-shrink:0"></i>
                <span style="font-size:var(--fs-xs);color:var(--text-muted)">{{ $cap }}</span>
            </div>
            @endforeach
            <div style="margin-top:16px">
                <a href="{{ url($locale.'/national-platform') }}" style="display:inline-flex;align-items:center;gap:6px;font-size:var(--fs-xs);color:#00C896;font-weight:600;text-decoration:none">
                    {{ $isFr ? 'Voir la Plateforme Nationale' : 'View National Platform' }}
                    <i data-lucide="arrow-right" style="width:11px;height:11px"></i>
                </a>
            </div>
        </div>
    </div>
</div>

{{-- ── CTA ──────────────────────────────────────────────────────── --}}
<div class="demo-section">
    <h2>{{ $isFr ? 'Prêt à déployer OPES Health OS ?' : 'Ready to deploy OPES Health OS?' }}</h2>
    <p>{{ $isFr
        ? 'Que vous soyez une clinique, un hôpital, un gouvernement ou un programme de santé publique — notre équipe vous guidera vers la configuration optimale pour votre organisation.'
        : 'Whether you\'re a clinic, a hospital, a government, or a public health programme — our team will guide you to the optimal configuration for your organisation.' }}</p>
    <div style="display:flex;gap:14px;justify-content:center;flex-wrap:wrap;margin-top:12px">
        <a href="{{ url($locale.'/products') }}" class="btn-primary">
            {{ $isFr ? 'Explorer tous les produits' : 'Explore all products' }}
            <i data-lucide="arrow-right" style="width:15px;height:15px"></i>
        </a>
        <a href="{{ url($locale.'/contact') }}" class="btn-secondary">
            {{ $isFr ? 'Parler à notre équipe' : 'Talk to our team' }}
            <i data-lucide="mail" style="width:15px;height:15px;color:var(--text-muted)"></i>
        </a>
    </div>
</div>

</x-layouts.app>

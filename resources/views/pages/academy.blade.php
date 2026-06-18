@php $locale = app()->getLocale(); $isFr = $locale === 'fr'; @endphp

<x-layouts.app
    title="{{ $isFr ? 'OPES Academy — Formation & Certification' : 'OPES Academy — Training & Certification' }}"
    description="{{ $isFr
        ? 'OPES Academy : programmes de formation certifiante, développement des compétences numériques en santé pour les professionnels africains.'
        : 'OPES Academy: certified training programmes and digital health skills development for African health professionals.' }}">

{{-- HERO --}}
<div class="pricing-hero">
    <div class="section-label" style="justify-content:center;margin-bottom:16px">
        <i data-lucide="graduation-cap" style="width:12px;height:12px"></i>
        OPES Academy
    </div>
    <h1>
        {{ $isFr ? 'Former les professionnels de santé' : 'Building the digital health' }}
        <span class="gradient-text">{{ $isFr ? 'numériques de demain' : 'workforce of tomorrow' }}</span>
    </h1>
    <p>
        {{ $isFr
            ? 'OPES Academy est le programme de formation et de certification conçu pour renforcer les compétences numériques des professionnels de santé africains — de l\'utilisation quotidienne du système à la gestion avancée des données de santé.'
            : 'OPES Academy is the training and certification programme designed to build digital health competencies across African health professionals — from day-to-day system use to advanced health data management.' }}
    </p>
    <div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap;margin-top:28px">
        <a href="{{ url($locale.'/courses') }}" class="btn-primary">
            {{ $isFr ? 'Voir les cours disponibles' : 'Browse available courses' }} <i data-lucide="arrow-right" style="width:15px;height:15px"></i>
        </a>
        <a href="{{ url($locale.'/contact') }}" class="btn-secondary">
            {{ $isFr ? 'Formations pour établissements' : 'Facility training packages' }} <i data-lucide="building-2" style="width:15px;height:15px;color:#94a3b8"></i>
        </a>
    </div>
</div>

{{-- STATS --}}
<div class="section" style="text-align:center;max-width:800px;margin:0 auto">
    <div class="stats-bar">
        @foreach($isFr
            ? [['6','Parcours certifiants'],['3','Niveaux de maîtrise'],['EN/FR','Tous les cours bilingues'],['CPD','Crédits de développement professionnel']]
            : [['6','Certification tracks'],['3','Mastery levels'],['EN/FR','All courses bilingual'],['CPD','Professional development credits']]
        as $s)
        <div class="stat-item">
            <div class="stat-value">{{ $s[0] }}</div>
            <div class="stat-label">{{ $s[1] }}</div>
        </div>
        @endforeach
    </div>
</div>

<div class="divider"></div>

{{-- CERTIFICATION TRACKS --}}
<div class="section" style="max-width:960px;margin:0 auto">
    <div class="section-label" style="margin-bottom:16px">
        <i data-lucide="award" style="width:12px;height:12px"></i>
        {{ $isFr ? 'Parcours de certification' : 'Certification tracks' }}
    </div>
    <h2 class="section-title">{{ $isFr ? 'Six parcours, un seul objectif : l\'excellence numérique' : 'Six tracks, one goal: digital excellence' }}</h2>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:20px;margin-top:32px">
        @php
        $tracks = $isFr ? [
            [
                'icon'=>'stethoscope','color'=>'#00C896',
                'title'=>'Clinicien Numérique',
                'cert'=>'OPES Certified Clinical User',
                'audience'=>'Médecins, infirmiers, sages-femmes',
                'desc'=>'Maîtrise du dossier patient électronique, des prescriptions numériques, du triage, et de la consultation dans OPES EMR.',
                'modules'=>['EMR & dossier patient','Prescriptions & ordonnances','Triage & admissions','Gestion des consultations'],
            ],
            [
                'icon'=>'flask-conical','color'=>'#1A6FE8',
                'title'=>'Technicien de Laboratoire',
                'cert'=>'OPES Certified Lab Technician',
                'audience'=>'Biologistes, techniciens, laborantins',
                'desc'=>'Gestion des demandes d\'analyses, validation des résultats, contrôle qualité et interface avec le dossier patient via OPES Lab.',
                'modules'=>['Gestion des demandes','Validation des résultats','Contrôle qualité','Intégration EMR'],
            ],
            [
                'icon'=>'pill','color'=>'#00C896',
                'title'=>'Gestionnaire de Pharmacie',
                'cert'=>'OPES Certified Pharmacy Manager',
                'audience'=>'Pharmaciens, préparateurs, gestionnaires',
                'desc'=>'Gestion des stocks pharmaceutiques, dispensation, alertes de péremption et interaction médicamenteuse dans PHARMIS.',
                'modules'=>['Gestion des stocks','Dispensation','Alertes médicamenteuses','Rapports de pharmacie'],
            ],
            [
                'icon'=>'bar-chart-2','color'=>'#1A6FE8',
                'title'=>'Administrateur de données de santé',
                'cert'=>'OPES Certified Health Data Administrator',
                'audience'=>'Informaticiens, administrateurs DRH, DSI',
                'desc'=>'Administration système, gestion des utilisateurs, sauvegardes, reporting HMIS, et export DHIS2 pour les partenaires de santé.',
                'modules'=>['Administration système','Reporting HMIS','Export DHIS2','Gestion des utilisateurs'],
            ],
            [
                'icon'=>'brain','color'=>'#00C896',
                'title'=>'Analyste Intelligence Clinique',
                'cert'=>'OPES Certified Clinical Intelligence Analyst',
                'audience'=>'Épidémiologistes, DSP, chercheurs',
                'desc'=>'Utilisation avancée des tableaux de bord analytiques, surveillance épidémiologique, et interprétation des indicateurs de performance clinique.',
                'modules'=>['Tableaux de bord avancés','Indicateurs épidémiologiques','Rapport de performance','IA décision clinique'],
            ],
            [
                'icon'=>'server','color'=>'#1A6FE8',
                'title'=>'Ingénieur Système OPES',
                'cert'=>'OPES Certified System Engineer',
                'audience'=>'Ingénieurs IT, intégrateurs, DSI',
                'desc'=>'Déploiement, configuration avancée, intégrations HL7 FHIR, administration de l\'API ouverte, et maintenance de l\'infrastructure OPES.',
                'modules'=>['Déploiement & infrastructure','Intégrations FHIR','Administration API','Sécurité & hardening'],
            ],
        ] : [
            [
                'icon'=>'stethoscope','color'=>'#00C896',
                'title'=>'Digital Clinician',
                'cert'=>'OPES Certified Clinical User',
                'audience'=>'Doctors, nurses, midwives',
                'desc'=>'Mastery of electronic patient records, digital prescriptions, triage, and consultation workflows in OPES EMR.',
                'modules'=>['EMR & patient records','Prescriptions & orders','Triage & admissions','Consultation management'],
            ],
            [
                'icon'=>'flask-conical','color'=>'#1A6FE8',
                'title'=>'Lab Technician',
                'cert'=>'OPES Certified Lab Technician',
                'audience'=>'Biologists, technicians, lab staff',
                'desc'=>'Managing test requests, validating results, quality control, and EMR integration via OPES Lab.',
                'modules'=>['Request management','Result validation','Quality control','EMR integration'],
            ],
            [
                'icon'=>'pill','color'=>'#00C896',
                'title'=>'Pharmacy Manager',
                'cert'=>'OPES Certified Pharmacy Manager',
                'audience'=>'Pharmacists, dispensers, store managers',
                'desc'=>'Pharmaceutical stock management, dispensing, expiry alerts, and drug interaction management in PHARMIS.',
                'modules'=>['Stock management','Dispensing','Drug interactions','Pharmacy reports'],
            ],
            [
                'icon'=>'bar-chart-2','color'=>'#1A6FE8',
                'title'=>'Health Data Administrator',
                'cert'=>'OPES Certified Health Data Administrator',
                'audience'=>'IT officers, data managers, CIOs',
                'desc'=>'System administration, user management, backups, HMIS reporting, and DHIS2 exports for health partners.',
                'modules'=>['System administration','HMIS reporting','DHIS2 export','User management'],
            ],
            [
                'icon'=>'brain','color'=>'#00C896',
                'title'=>'Clinical Intelligence Analyst',
                'cert'=>'OPES Certified Clinical Intelligence Analyst',
                'audience'=>'Epidemiologists, public health officers, researchers',
                'desc'=>'Advanced use of analytics dashboards, epidemiological surveillance, and interpretation of clinical performance indicators.',
                'modules'=>['Advanced dashboards','Epidemiological indicators','Performance reporting','AI decision support'],
            ],
            [
                'icon'=>'server','color'=>'#1A6FE8',
                'title'=>'OPES System Engineer',
                'cert'=>'OPES Certified System Engineer',
                'audience'=>'IT engineers, integrators, CIOs',
                'desc'=>'Deployment, advanced configuration, HL7 FHIR integrations, open API administration, and OPES infrastructure maintenance.',
                'modules'=>['Deployment & infrastructure','FHIR integrations','API administration','Security & hardening'],
            ],
        ];
        @endphp
        @foreach($tracks as $track)
        <div class="pi-card" style="flex-direction:column;align-items:flex-start">
            <div style="width:44px;height:44px;border-radius:12px;background:{{ $track['color'] }}15;display:flex;align-items:center;justify-content:center;margin-bottom:14px">
                <i data-lucide="{{ $track['icon'] }}" style="width:20px;height:20px;color:{{ $track['color'] }}"></i>
            </div>
            <div style="font-weight:800;color:#e2e8f0;font-size:15px;margin-bottom:2px">{{ $track['title'] }}</div>
            <div style="font-size:11px;color:{{ $track['color'] }};font-weight:700;text-transform:uppercase;letter-spacing:0.07em;margin-bottom:8px">{{ $track['cert'] }}</div>
            <div style="font-size:11px;color:#475569;margin-bottom:10px;font-style:italic">{{ $track['audience'] }}</div>
            <div style="font-size:12px;color:#64748b;line-height:1.6;margin-bottom:14px">{{ $track['desc'] }}</div>
            <div style="display:flex;flex-wrap:wrap;gap:6px">
                @foreach($track['modules'] as $m)
                <span style="font-size:10px;color:#64748b;background:#0F172A;border:1px solid #1e293b;border-radius:4px;padding:2px 7px">{{ $m }}</span>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>
</div>

<div class="divider"></div>

{{-- HOW IT WORKS --}}
<div class="section" style="max-width:900px;margin:0 auto;text-align:center">
    <div class="section-label" style="justify-content:center;margin-bottom:16px">
        <i data-lucide="route" style="width:12px;height:12px"></i>
        {{ $isFr ? 'Comment ça marche' : 'How it works' }}
    </div>
    <h2 class="section-title">{{ $isFr ? 'Votre parcours de certification' : 'Your certification journey' }}</h2>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:16px;margin-top:36px">
        @php
        $steps = $isFr ? [
            ['icon'=>'user-plus',      'color'=>'#1A6FE8','step'=>'01','title'=>'S\'inscrire',        'desc'=>'Créez votre compte praticien OPES et choisissez votre parcours de certification.'],
            ['icon'=>'book-open',      'color'=>'#00C896','step'=>'02','title'=>'Suivre les cours',   'desc'=>'Accédez aux modules e-learning bilingues à votre rythme, depuis n\'importe quel appareil.'],
            ['icon'=>'check-square',   'color'=>'#1A6FE8','step'=>'03','title'=>'Valider les acquis', 'desc'=>'Évaluations pratiques et quiz à la fin de chaque module pour mesurer votre progression.'],
            ['icon'=>'award',          'color'=>'#00C896','step'=>'04','title'=>'Obtenir la certification','desc'=>'Certificat numérique OPES téléchargeable, avec code de vérification unique.'],
        ] : [
            ['icon'=>'user-plus',      'color'=>'#1A6FE8','step'=>'01','title'=>'Register',           'desc'=>'Create your OPES practitioner account and choose your certification track.'],
            ['icon'=>'book-open',      'color'=>'#00C896','step'=>'02','title'=>'Take courses',       'desc'=>'Access bilingual e-learning modules at your own pace from any device.'],
            ['icon'=>'check-square',   'color'=>'#1A6FE8','step'=>'03','title'=>'Validate knowledge', 'desc'=>'Practical assessments and quizzes at the end of each module to measure progress.'],
            ['icon'=>'award',          'color'=>'#00C896','step'=>'04','title'=>'Get certified',      'desc'=>'Downloadable OPES digital certificate with a unique verification code.'],
        ];
        @endphp
        @foreach($steps as $step)
        <div style="background:#0f1a2e;border:1px solid #1e293b;border-radius:12px;padding:20px 16px;text-align:center">
            <div style="font-size:11px;color:#475569;font-weight:700;margin-bottom:10px">{{ $step['step'] }}</div>
            <div style="width:44px;height:44px;border-radius:50%;background:{{ $step['color'] }}15;display:flex;align-items:center;justify-content:center;margin:0 auto 12px">
                <i data-lucide="{{ $step['icon'] }}" style="width:20px;height:20px;color:{{ $step['color'] }}"></i>
            </div>
            <div style="font-weight:700;color:#e2e8f0;font-size:13px;margin-bottom:6px">{{ $step['title'] }}</div>
            <div style="font-size:12px;color:#64748b;line-height:1.55">{{ $step['desc'] }}</div>
        </div>
        @endforeach
    </div>
</div>

<div class="divider"></div>

{{-- FOR FACILITIES --}}
<div class="section" style="max-width:960px;margin:0 auto">
    <div class="opescare-spotlight">
        <div class="section-label" style="margin-bottom:14px">
            <i data-lucide="building-2" style="width:12px;height:12px"></i>
            {{ $isFr ? 'Formations institutionnelles' : 'Institutional training' }}
        </div>
        <h2 style="font-size:clamp(18px,2.5vw,26px);font-weight:700;color:#e2e8f0;margin-bottom:14px;line-height:1.3">
            {{ $isFr ? 'Formez tout votre établissement d\'un coup' : 'Train your entire facility at once' }}
        </h2>
        <p style="color:#94a3b8;max-width:680px;line-height:1.75;font-size:14px;margin-bottom:24px">
            {{ $isFr
                ? 'OPES Academy propose des programmes de formation sur site pour les établissements qui souhaitent former l\'ensemble de leur personnel en même temps — avec un formateur certifié OPES déployé sur place, des supports adaptés à votre configuration système, et un suivi des certifications via le tableau de bord administrateur.'
                : 'OPES Academy offers on-site training programmes for facilities that want to train all staff at once — with a certified OPES trainer deployed on-site, materials adapted to your system configuration, and certification tracking via the admin dashboard.' }}
        </p>
        <div style="display:flex;gap:12px;flex-wrap:wrap">
            <a href="{{ url($locale.'/contact') }}" class="btn-primary">
                {{ $isFr ? 'Demander une formation sur site' : 'Request on-site training' }} <i data-lucide="arrow-right" style="width:15px;height:15px"></i>
            </a>
        </div>
    </div>
</div>

{{-- CTA --}}
<div class="demo-section">
    <h2>{{ $isFr ? 'Commencez votre parcours de certification' : 'Start your certification journey' }}</h2>
    <p>{{ $isFr
        ? 'Inscrivez-vous gratuitement et accédez aux premiers modules de votre parcours dès aujourd\'hui.'
        : 'Sign up for free and access the first modules of your track today.' }}</p>
    <div style="display:flex;gap:14px;justify-content:center;flex-wrap:wrap;margin-top:12px">
        <a href="{{ url($locale.'/practitioners/register') }}" class="btn-primary">
            {{ $isFr ? 'Créer mon compte' : 'Create my account' }} <i data-lucide="arrow-right" style="width:15px;height:15px"></i>
        </a>
        <a href="{{ url($locale.'/courses') }}" class="btn-secondary">
            {{ $isFr ? 'Explorer les cours' : 'Explore courses' }} <i data-lucide="book-open" style="width:15px;height:15px;color:#94a3b8"></i>
        </a>
    </div>
</div>

</x-layouts.app>

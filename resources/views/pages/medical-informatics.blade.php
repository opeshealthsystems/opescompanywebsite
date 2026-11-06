@php $locale = app()->getLocale(); $isFr = $locale === 'fr'; @endphp

<x-layouts.app
    :title="$isFr ? 'Informatique médicale — Formation, accès institutionnel et certification | OPES' : 'Medical Informatics — Institutional Learning & Certification | OPES'"
    :description="$isFr ? 'Programme OPES d’informatique médicale : accès gratuit aux plateformes OPES pour les établissements de santé, écoles de soins infirmiers et universités, apprentissage par les parcours réels et certification.' : 'OPES Medical Informatics programme: free OPES platform access for health institutions, nursing schools and universities, real-workflow learning and completion certificates.'">

<style>
.mi-hero { background:var(--surface); border-bottom:1px solid #1e293b; padding:clamp(48px,8vw,96px) 24px 64px; text-align:center; }
.mi-hero h1 { max-width:940px; margin:0 auto 18px; color:#f1f5f9; font-size:clamp(2rem,5vw,3.5rem); line-height:1.12; font-weight:800; letter-spacing:-.03em; }
.mi-hero p { max-width:800px; margin:0 auto; color:var(--text-muted); font-size:clamp(1rem,2vw,1.15rem); line-height:1.75; }
.mi-actions { display:flex; justify-content:center; gap:12px; flex-wrap:wrap; margin-top:28px; }
.mi-section { max-width:1120px; margin:0 auto; padding:clamp(36px,6vw,72px) 24px; scroll-margin-top:90px; }
.mi-section h2 { color:#f1f5f9; font-size:clamp(1.45rem,3vw,2rem); line-height:1.25; margin:0 0 12px; }
.mi-lead { color:var(--text-muted); max-width:790px; line-height:1.75; margin:0 0 28px; }
.mi-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(min(100%,245px),1fr)); gap:16px; }
.mi-card { height:100%; background:#0b1d31; border:1px solid #29445e; border-radius:12px; padding:22px; }
.mi-card h3 { color:#eaf2f8; font-size:1rem; line-height:1.4; margin:10px 0 8px; }
.mi-card p, .mi-card li { color:var(--text-muted); font-size:.9rem; line-height:1.65; }
.mi-card ul { margin:10px 0 0; padding-left:18px; }
.mi-icon { width:40px; height:40px; display:grid; place-items:center; background:#173651; color:#acd5ec; border-radius:10px; }
.mi-access { display:grid; grid-template-columns:1.25fr .75fr; gap:24px; align-items:center; background:#0b1d31; border:1px solid #315f7d; border-radius:16px; padding:clamp(22px,4vw,40px); }
.mi-access p { color:var(--text-muted); line-height:1.75; }
.mi-access-callout { border-left:3px solid #8ac5ed; padding:8px 0 8px 18px; color:#eaf2f8; line-height:1.65; }
.mi-steps { counter-reset:mi-step; display:grid; grid-template-columns:repeat(auto-fit,minmax(min(100%,190px),1fr)); gap:12px; }
.mi-step { position:relative; background:#0b1d31; border:1px solid #29445e; border-radius:12px; padding:20px; }
.mi-step::before { counter-increment:mi-step; content:counter(mi-step,decimal-leading-zero); display:block; color:#acd5ec; font-size:.78rem; letter-spacing:.12em; font-weight:800; margin-bottom:12px; }
.mi-step h3 { color:#eaf2f8; font-size:.96rem; margin:0 0 8px; }
.mi-step p { color:var(--text-muted); font-size:.88rem; line-height:1.6; margin:0; }
.mi-note { border:1px solid #29445e; border-radius:10px; padding:16px 18px; color:#cbd5e1; line-height:1.7; font-size:.9rem; }
.mi-faq { border-top:1px solid #29445e; }
.mi-faq details { border-bottom:1px solid #29445e; padding:17px 0; }
.mi-faq summary { color:#eaf2f8; font-weight:700; cursor:pointer; line-height:1.5; }
.mi-faq p { color:var(--text-muted); line-height:1.7; max-width:850px; }
.mi-table-wrap { overflow-x:auto; border:1px solid #29445e; border-radius:12px; }
.mi-table { width:100%; border-collapse:collapse; min-width:650px; }
.mi-table th,.mi-table td { padding:13px 15px; text-align:left; vertical-align:top; border-bottom:1px solid #29445e; color:#cbd5e1; line-height:1.55; font-size:.88rem; }
.mi-table th { color:#eaf2f8; background:#102943; font-size:.8rem; letter-spacing:.03em; }
.mi-table tr:last-child td { border-bottom:0; }
.mi-status { margin:18px auto 0; max-width:850px; border:1px solid #607d94; background:#0b1d31; border-radius:12px; padding:15px 18px; color:#d9e5ee; line-height:1.65; font-size:.9rem; }
.mi-level { border-top:1px solid #29445e; padding:20px 0; }
.mi-level h3 { color:#eaf2f8; margin:0 0 12px; font-size:1.05rem; }
.mi-cert-list { display:grid; grid-template-columns:repeat(auto-fit,minmax(min(100%,270px),1fr)); gap:12px; }
.mi-cert { background:#0b1d31; border:1px solid #29445e; border-radius:10px; padding:15px; }
.mi-cert strong { display:block; color:#eaf2f8; font-size:.92rem; margin-bottom:5px; }
.mi-cert span { display:block; color:var(--text-muted); font-size:.85rem; line-height:1.55; }
@media(max-width:720px) { .mi-access { grid-template-columns:1fr; } .mi-hero { padding-left:18px; padding-right:18px; } .mi-section { padding-left:18px; padding-right:18px; } }
</style>

<header class="mi-hero">
    <div class="section-label" style="justify-content:center;margin-bottom:16px"><i data-lucide="book-open-check" style="width:14px;height:14px"></i>{{ $isFr ? 'Formation · Partenariat académique · Transformation numérique' : 'Learning · Academic partnership · Digital transformation' }}</div>
    <h1>{{ $isFr ? 'L’informatique médicale s’apprend en pratiquant les parcours de santé numériques.' : 'Learn Medical Informatics by working through real digital health workflows.' }}</h1>
    <p>{{ $isFr ? 'Depuis le Cameroun, OPES Health Systems invite les écoles de santé et de soins infirmiers, les universités, les hôpitaux d’enseignement et les organisations de santé en Afrique et dans le monde à former la prochaine génération aux systèmes, données et pratiques numériques de santé.' : 'From Cameroon, OPES Health Systems invites health and nursing schools, universities, teaching hospitals and health organizations across Africa and worldwide to prepare the next generation to work with digital health systems, data and practice.' }}</p>
    <div class="mi-actions">
        <a class="btn-primary" href="{{ url($locale.'/become-a-partner?partner_type=academic') }}">{{ $isFr ? 'Devenir institution partenaire' : 'Partner as an institution' }} <i data-lucide="arrow-right" style="width:15px;height:15px"></i></a>
        <a class="btn-secondary" href="{{ url($locale.'/courses') }}">{{ $isFr ? 'Voir les cours' : 'Explore courses' }} <i data-lucide="graduation-cap" style="width:15px;height:15px"></i></a>
        <a class="btn-secondary" href="{{ url($locale.'/hands-on-learning') }}">{{ $isFr ? 'Projet Hands-On Learning' : 'Project Hands-On Learning' }} <i data-lucide="book-open-check" style="width:15px;height:15px"></i></a>
    </div>
    <p class="mi-status">{{ $isFr ? 'Projet Hands-On Learning · Parcours santé : cadre de certification en préparation pour revue avec les partenaires cliniques et académiques. Le projet de programme vise la prochaine année académique, sous réserve de cette revue. Le démarrage, les titres définitifs, les évaluations, les co-émetteurs et les frais seront confirmés avant l’ouverture officielle des cohortes.' : 'Project Hands-On Learning · Health pathway: the certification framework is being prepared for clinical and academic partner review. The draft targets the next academic year, subject to that review. Programme launch, final credential titles, assessments, co-issuers and any fees will be confirmed before cohorts formally open.' }}</p>
</header>

<section class="mi-section" id="institution-access">
    <div class="mi-access">
        <div>
            <div class="section-label" style="margin-bottom:12px"><i data-lucide="building-2" style="width:13px;height:13px"></i>{{ $isFr ? 'Accès institutionnel' : 'Institutional access' }}</div>
            <h2>{{ $isFr ? 'Accès gratuit aux plateformes OPES pour les établissements de santé et les universités' : 'Free OPES platform access for health institutions and universities' }}</h2>
            <p>{{ $isFr ? 'Nous invitons chaque établissement de santé, école de soins infirmiers et université à rejoindre le programme et à demander un accès gratuit aux plateformes OPES pour l’apprentissage des étudiants et des enseignants. L’accès institutionnel aux plateformes est gratuit ; OPES et chaque établissement organisent ensemble l’intégration, les cohortes et le périmètre pédagogique.' : 'We invite every health institution, nursing school and university to join the programme and request free access to OPES platforms for students and faculty to learn. Institutional platform access is free; OPES and each institution coordinate onboarding, cohorts and the learning scope together.' }}</p>
        </div>
        <div class="mi-access-callout">{{ $isFr ? 'Les responsables de faculté, de formation clinique et de transformation numérique peuvent proposer un partenariat institutionnel dès maintenant.' : 'Faculty leaders, clinical educators and digital transformation teams can propose an institutional partnership now.' }}</div>
    </div>
</section>

<section class="mi-section" id="learning-workflows">
    <div class="section-label">{{ $isFr ? 'Apprentissage appliqué' : 'Applied learning' }}</div>
    <h2>{{ $isFr ? 'Apprendre les systèmes en suivant un parcours de soins complet' : 'Learn the systems by following a complete care workflow' }}</h2>
    <p class="mi-lead">{{ $isFr ? 'Les apprenants suivent le parcours complet, de l’arrivée et l’identification du patient jusqu’à la documentation, aux examens, aux médicaments, à la continuité des soins et aux rapports. Ils travaillent avec les systèmes OPES concernés et des données de formation ou autrement autorisées.' : 'Learners follow a complete care journey from patient arrival and identity through documentation, diagnostics, medication, continuity of care and reporting. They practise with relevant OPES systems using training data or other specifically authorized data.' }}</p>
    <div class="mi-grid">
        @foreach($isFr ? [
            ['users-round','Identité, inscription et consentement','Comprendre l’identité de santé, la qualité des données et les règles d’accès aux dossiers.'],
            ['stethoscope','Triage et documentation clinique','Suivre les parcours de consultation, la saisie structurée et l’aide à la décision clinique.'],
            ['microscope','Laboratoire, pharmacie et références','Observer comment les commandes, résultats, prescriptions et références circulent entre équipes.'],
            ['chart-no-axes-combined','Données, interopérabilité et rapports','Apprendre les bases des données de santé, des échanges interopérables et des indicateurs de programme.'],
        ] : [
            ['users-round','Identity, registration and consent','Use health identity and patient matching concepts, record quality, consent and role-based access.'],
            ['stethoscope','Triage and clinical documentation','Practise pre-consultation, triage, structured encounters, histories, orders and clinical decision support.'],
            ['microscope','Diagnostics, pharmacy and referrals','Follow laboratory and imaging requests, results, prescribing, medicine verification and referrals.'],
            ['chart-no-axes-combined','Data, interoperability and reporting','Explore HL7 and FHIR concepts, ICD-10 and LOINC coding, APIs, dashboards and health data analysis.'],
        ] as $card)
        <article class="mi-card"><div class="mi-icon"><i data-lucide="{{ $card[0] }}" style="width:20px;height:20px"></i></div><h3>{{ $card[1] }}</h3><p>{{ $card[2] }}</p></article>
        @endforeach
    </div>
    <p class="mi-note" style="margin-top:18px">{{ $isFr ? 'Les étudiants apprennent sur des parcours et fonctions de la plateforme réels, dans un cadre pédagogique. Les exercices utilisent des données de formation ou des données autorisées et protégées. Aucune donnée patient identifiable n’est utilisée sans base légale, autorisation et gouvernance appropriées.' : 'Students learn with real platform functions and workflows in an educational setting. Exercises use training data or specifically authorized and protected data. Identifiable patient information is not used without a lawful basis, authorization and appropriate governance.' }}</p>
    <div class="mi-grid" style="margin-top:16px">
        <article class="mi-card"><h3>{{ $isFr ? 'À qui s’adresse le parcours ?' : 'Who is the pathway for?' }}</h3><p>{{ $isFr ? 'Étudiants en médecine, soins infirmiers, maïeutique, pharmacie, laboratoire, imagerie, information sanitaire et santé publique ; professionnels en exercice ; équipes informatiques de santé, responsables et enseignants.' : 'Medical, nursing, midwifery, pharmacy, laboratory, imaging, health information and public health students; practising health workers; health IT teams, managers and educators.' }}</p></article>
        <article class="mi-card"><h3>{{ $isFr ? 'Des systèmes professionnels, pas un exercice fictif' : 'Professional systems, not a paper-only simulation' }}</h3><p>{{ $isFr ? 'Le projet vise l’apprentissage sur les vrais logiciels professionnels utilisés dans les hôpitaux, laboratoires, pharmacies et réseaux de santé, avec des comptes et données limités au cadre de formation.' : 'The project is designed around the professional systems used in hospitals, laboratories, pharmacies and health networks, with accounts and data restricted to the learning context.' }}</p></article>
    </div>
</section>

<section class="mi-section" id="curriculum">
    <div class="section-label">{{ $isFr ? 'Parcours de formation' : 'Learning pathway' }}</div>
    <h2>{{ $isFr ? 'Une discipline à la croisée des soins, des données et des systèmes' : 'A discipline at the intersection of care, data and systems' }}</h2>
    <p class="mi-lead">{{ $isFr ? 'Le programme associe cours structurés, démonstrations, exercices guidés, études de cas et évaluations. Les thèmes incluent la qualité des données, la gouvernance, la confidentialité, la sécurité et les responsabilités professionnelles. La loi camerounaise n° 2024/017 est étudiée dans son contexte ; HIPAA et RGPD servent de références comparatives, et non de déclaration de conformité.' : 'The programme combines structured lessons, demonstrations, guided exercises, case studies and assessments. Topics include data quality, governance, privacy, security and professional responsibility. Cameroon Law No. 2024/017 is considered in context; HIPAA and GDPR are comparative references, not a claim of compliance.' }}</p>
    <div class="mi-grid">
        @foreach($isFr ? [
            ['01','Fondamentaux de l’informatique médicale','Systèmes d’information de santé, dossiers numériques, terminologies, rôles et flux de travail.'],
            ['02','Pratique numérique par métier','Parcours cliniques, infirmiers, laboratoire, pharmacie, santé publique et administration.'],
            ['03','Données et interopérabilité','Qualité, gouvernance, échanges de données de santé, API et notions de standards.'],
            ['04','Sécurité, confidentialité et éthique','Accès selon le rôle, consentement, minimisation des données, sécurité et responsabilités.'],
            ['05','Conception et transformation','Analyse des besoins, cartographie des parcours, mise en œuvre et gestion du changement.'],
            ['06','Évaluation et recherche','Mesurer l’utilisabilité, la qualité des données et les résultats d’un projet numérique en santé.'],
        ] : [
            ['01','Electronic health and medical records','Record encounters, histories, orders and results; understand record structure and data quality.'],
            ['02','Clinical decision support','Study medication safety alerts, diagnostic suggestions and preventive-care reminders.'],
            ['03','Health information exchange and identity','Learn patient identity, matching, secure information exchange and continuity across facilities.'],
            ['04','Interoperability and terminology','Introduction to HL7, FHIR, APIs, ICD-10 and LOINC, with standards-based exchange concepts.'],
            ['05','Medical imaging informatics','Learn imaging requests, reports, exchange workflows and DICOM concepts.'],
            ['06','Telehealth and remote care','Explore virtual consultations, remote monitoring and follow-up workflows.'],
            ['07','Patient flow and hospital operations','Practise triage, queues, consultation, laboratory and pharmacy workflows.'],
            ['08','Health data, analytics and responsible AI','Use SQL/Python and BI concepts, secure database principles and privacy-aware analysis with training or authorized data.'],
        ] as $item)
        <article class="mi-card"><div class="section-label">{{ $item[0] }}</div><h3>{{ $item[1] }}</h3><p>{{ $item[2] }}</p></article>
        @endforeach
    </div>
</section>

<section class="mi-section" id="certification-pathways">
    <div class="section-label">{{ $isFr ? 'Certification professionnelle proposée' : 'Proposed professional certification' }}</div>
    <h2>{{ $isFr ? 'Parcours Digital Health Professional (DHP)' : 'Digital Health Professional (DHP) pathway' }}</h2>
    <p class="mi-lead">{{ $isFr ? 'Le cadre décrit quatre niveaux de progression. Les intitulés ci-dessous sont proposés et restent soumis à la revue des partenaires, aux validations du programme et à une vérification des marques avant toute délivrance.' : 'The draft framework has four progression levels. The credential names below are proposed and remain subject to partner review, programme approval and trademark checks before any certificates are issued.' }}</p>

    <div class="mi-level">
        <h3>{{ $isFr ? 'Niveau 1 · Fondamentaux' : 'Level 1 · Foundation' }}</h3>
        <div class="mi-cert-list"><article class="mi-cert"><strong>Certified Digital Health Associate (CDHA)</strong><span>{{ $isFr ? 'Étudiants en santé et personnel d’accueil, d’admission et de facturation.' : 'Health students and reception, admissions and billing staff.' }}</span></article></div>
    </div>
    <div class="mi-level">
        <h3>{{ $isFr ? 'Niveau 2 · Pratique professionnelle' : 'Level 2 · Practitioner' }}</h3>
        <div class="mi-cert-list">
            <article class="mi-cert"><strong>Certified Clinical Informatics Practitioner (CCIP)</strong><span>{{ $isFr ? 'Infirmiers, sages-femmes et médecins : dossiers numériques, aide à la décision, prescriptions et triage.' : 'Nurses, midwives and doctors: digital records, decision support, prescribing and triage.' }}</span></article>
            <article class="mi-cert"><strong>Certified Health Records &amp; Information Specialist (CHRIS)</strong><span>{{ $isFr ? 'Personnel des archives médicales et de l’information sanitaire.' : 'Medical records and health information staff.' }}</span></article>
            <article class="mi-cert"><strong>Certified Pharmacy Informatics Specialist (CPhIS)</strong><span>{{ $isFr ? 'Pharmaciens et techniciens en pharmacie.' : 'Pharmacists and pharmacy technicians.' }}</span></article>
            <article class="mi-cert"><strong>Certified Laboratory Informatics Specialist (CLIS)</strong><span>{{ $isFr ? 'Scientifiques et techniciens de laboratoire.' : 'Laboratory scientists and technicians.' }}</span></article>
            <article class="mi-cert"><strong>Certified Imaging Informatics Technologist (CIIT)</strong><span>{{ $isFr ? 'Radiologues et techniciens d’imagerie : PACS et DICOM.' : 'Radiographers and imaging technicians: PACS and DICOM.' }}</span></article>
            <article class="mi-cert"><strong>Certified Telehealth Practitioner (CTHP)</strong><span>{{ $isFr ? 'Médecins, infirmiers et agents de santé communautaire : soins virtuels et suivi à distance.' : 'Doctors, nurses and community health workers: virtual care and remote follow-up.' }}</span></article>
        </div>
    </div>
    <div class="mi-level">
        <h3>{{ $isFr ? 'Niveau 3 · Spécialisation' : 'Level 3 · Specialist' }}</h3>
        <div class="mi-cert-list">
            <article class="mi-cert"><strong>Certified Health Interoperability Engineer (CHIE)</strong><span>{{ $isFr ? 'Personnel informatique et développeurs : HL7, FHIR, API, identité santé et index maître des patients.' : 'Health IT teams and developers: HL7, FHIR, APIs, Health ID and master patient index concepts.' }}</span></article>
            <article class="mi-cert"><strong>Certified Health Data &amp; AI Analyst (CHDAA)</strong><span>{{ $isFr ? 'Responsables, chercheurs et spécialistes du suivi-évaluation : SQL, Python, tableaux de bord et analyse responsable.' : 'Managers, researchers and monitoring teams: SQL, Python, dashboards and responsible analytics.' }}</span></article>
            <article class="mi-cert"><strong>Certified Health Systems Administrator (CHSA)</strong><span>{{ $isFr ? 'Équipes informatiques hospitalières : exploitation, sécurité et maintenance des systèmes de santé.' : 'Hospital IT teams: operating, securing and maintaining health systems.' }}</span></article>
        </div>
    </div>
    <div class="mi-level">
        <h3>{{ $isFr ? 'Niveau 4 · Leadership et formation' : 'Level 4 · Leadership and faculty' }}</h3>
        <div class="mi-cert-list">
            <article class="mi-cert"><strong>Certified Digital Health Leader (CDHL)</strong><span>{{ $isFr ? 'Directions et responsables : stratégie numérique, gouvernance et protection des données.' : 'Directors and administrators: digital strategy, data governance and data protection.' }}</span></article>
            <article class="mi-cert"><strong>Certified Digital Health Trainer (CDHT)</strong><span>{{ $isFr ? 'Enseignants, formateurs et tuteurs hospitaliers : formation des formateurs.' : 'Lecturers, tutors and hospital educators: train-the-trainer.' }}</span></article>
        </div>
    </div>
</section>

<section class="mi-section" id="assessment-and-fees">
    <div class="section-label">{{ $isFr ? 'Évaluation et accès' : 'Assessment and access' }}</div>
    <h2>{{ $isFr ? 'Apprendre gratuitement ; la certification reste facultative' : 'Learning is free; certification is optional' }}</h2>
    <div class="mi-grid">
        <article class="mi-card"><div class="mi-icon"><i data-lucide="book-open-check" style="width:20px;height:20px"></i></div><h3>{{ $isFr ? 'Apprentissage' : 'Learning' }}</h3><p>{{ $isFr ? 'L’apprentissage et l’accès pédagogique aux plateformes sont gratuits pour les cohortes des établissements partenaires, selon le périmètre convenu.' : 'Learning and educational platform access are free for cohorts at participating partner institutions, within the agreed learning scope.' }}</p></article>
        <article class="mi-card"><div class="mi-icon"><i data-lucide="clipboard-check" style="width:20px;height:20px"></i></div><h3>{{ $isFr ? 'Évaluation proposée' : 'Proposed assessment' }}</h3><p>{{ $isFr ? 'Le projet prévoit des tâches pratiques sur les systèmes et une évaluation écrite. Les épreuves, critères de réussite, reprises et modalités de vérification seront publiés après validation du programme.' : 'The draft proposes practical tasks on the systems and a written assessment. Exam format, pass criteria, retakes and certificate verification will be published after programme approval.' }}</p></article>
        <article class="mi-card"><div class="mi-icon"><i data-lucide="badge-check" style="width:20px;height:20px"></i></div><h3>{{ $isFr ? 'Certificat professionnel' : 'Professional certificate' }}</h3><p>{{ $isFr ? 'Un certificat OPES professionnel ne remplace pas un diplôme universitaire, une accréditation académique ni une autorisation d’exercer.' : 'An OPES professional certificate does not replace an academic degree, academic accreditation or a licence to practise.' }}</p></article>
    </div>
    <h3 style="color:#eaf2f8;margin:28px 0 12px">{{ $isFr ? 'Structure indicative des niveaux — à valider' : 'Indicative level structure — subject to approval' }}</h3>
    <div class="mi-table-wrap"><table class="mi-table">
        <thead><tr><th>{{ $isFr ? 'Niveau' : 'Level' }}</th><th>{{ $isFr ? 'Durée indicative' : 'Indicative duration' }}</th><th>{{ $isFr ? 'Évaluation proposée' : 'Proposed assessment' }}</th></tr></thead>
        <tbody>
            @foreach($isFr ? [
                ['Fondamentaux','30–40 heures','Travaux pratiques (60 %) et QCM (40 %)'],
                ['Pratique professionnelle','60–80 heures','Travaux pratiques (60 %) et examen écrit (40 %)'],
                ['Spécialisation','80–120 heures','Projet, examen pratique et soutenance orale'],
                ['Leadership et formation','40–60 heures','Portfolio et présentation de cas ; observation pédagogique pour les formateurs'],
            ] : [
                ['Foundation','30–40 hours','Practical tasks (60%) and multiple-choice exam (40%)'],
                ['Practitioner','60–80 hours','Practical tasks (60%) and written exam (40%)'],
                ['Specialist','80–120 hours','Project, practical exam and oral defence'],
                ['Leadership and faculty','40–60 hours','Portfolio and case presentation; teaching observation for trainers'],
            ] as $row)
            <tr><td><strong>{{ $row[0] }}</strong></td><td>{{ $row[1] }}</td><td>{{ $row[2] }}</td></tr>
            @endforeach
        </tbody>
    </table></div>
    <h3 style="color:#eaf2f8;margin:28px 0 12px">{{ $isFr ? 'Exemple de parcours pratique CCIP — 72 heures proposées' : 'Illustrative CCIP practical pathway — proposed 72 hours' }}</h3>
    <div class="mi-table-wrap"><table class="mi-table">
        <thead><tr><th>{{ $isFr ? 'Module' : 'Module' }}</th><th>{{ $isFr ? 'Pratique' : 'Practice' }}</th><th>{{ $isFr ? 'Heures' : 'Hours' }}</th></tr></thead>
        <tbody>
            @foreach($isFr ? [
                ['Identité patient et dossiers','Inscription, Health ID, index maître des patients, structure du dossier','8'],
                ['Triage et flux patients','Préconsultation, files par priorité, points de consultation','8'],
                ['Documentation clinique','Rencontres, notes, codage ICD-10, dossiers infirmiers','12'],
                ['Demandes et résultats','Demandes de laboratoire et d’imagerie, résultats, notions LOINC et DICOM','10'],
                ['Prescription et sécurité des médicaments','Prescriptions électroniques, aide à la décision, délivrance et vérification DrugTrace','10'],
                ['Télésanté et continuité','Télémédecine, références et suivi prénatal/pédiatrique via les services concernés','8'],
                ['Confidentialité, consentement et sécurité','Accès selon le consentement, pistes d’audit et responsabilités de protection','6'],
                ['Cas pratique intégré','Parcours complet du patient, de l’arrivée à la sortie, sur les systèmes pédagogiques','10'],
            ] : [
                ['Patient identity and records','Registration, Health ID, master patient index and record structure','8'],
                ['Triage and patient flow','Pre-consultation, priority queues and consultation points','8'],
                ['Clinical documentation','Encounters, notes, ICD-10 coding and nursing records','12'],
                ['Orders and results','Lab and imaging orders, results, LOINC and DICOM concepts','10'],
                ['E-prescribing and medicine safety','E-prescriptions, decision support, dispensing and DrugTrace verification','10'],
                ['Telehealth and continuity of care','Telemedicine, referrals and antenatal/paediatric follow-up workflows','8'],
                ['Privacy, consent and security','Consent-based access, audit trails and protection responsibilities','6'],
                ['Integrated practical case','A complete patient journey from arrival to discharge on learning systems','10'],
            ] as $row)
            <tr><td><strong>{{ $row[0] }}</strong></td><td>{{ $row[1] }}</td><td>{{ $row[2] }}</td></tr>
            @endforeach
        </tbody>
    </table></div>
    <p class="mi-note" style="margin-top:16px">{{ $isFr ? 'Le document de programme propose actuellement des frais optionnels de certification de 25 000 à 75 000 FCFA selon le niveau. Ces montants, réductions éventuelles et modalités ne sont pas des tarifs définitifs : ils restent à confirmer avec les partenaires avant toute inscription payante. Aucun frais n’est prévu pour apprendre ou utiliser les plateformes dans le cadre institutionnel convenu.' : 'The programme draft currently proposes optional certification fees of FCFA 25,000–75,000 depending on level. These amounts, any discounts and payment terms are not final and must be confirmed with programme partners before paid enrolment. There is no fee to learn or use the platforms within the agreed institutional programme.' }}</p>
    <p class="mi-note" style="margin-top:12px">{{ $isFr ? 'Dans le cadre général Hands-On Learning, des parcours distincts sont aussi envisagés pour les systèmes financiers, comptables, commerciaux, ERP et éducatifs. Cette page porte sur le parcours santé et ses compétences en informatique médicale.' : 'The wider Hands-On Learning initiative also proposes separate learning tracks for finance, accounting, commerce, enterprise and education systems. This page focuses on the health pathway and its Medical Informatics competencies.' }}</p>
    <p class="mi-note" style="margin-top:12px">{{ $isFr ? 'Chaque certificat pourrait inclure une référence et un QR code de vérification si cette fonction et le processus d’émission sont approuvés et opérationnels au lancement.' : 'Each certificate is proposed to carry a reference and verification QR code if that function and issuing process are approved and operational at launch.' }}</p>
</section>

<section class="mi-section" id="partnership-model">
    <div class="section-label">{{ $isFr ? 'Modèle de partenariat' : 'Partnership model' }}</div>
    <h2>{{ $isFr ? 'Un programme co-construit avec les institutions' : 'A programme built with participating institutions' }}</h2>
    <div class="mi-table-wrap"><table class="mi-table">
        <thead><tr><th>{{ $isFr ? 'Étape' : 'Step' }}</th><th>{{ $isFr ? 'Contribution d’OPES' : 'OPES contribution' }}</th><th>{{ $isFr ? 'Contribution de l’institution' : 'Institution contribution' }}</th></tr></thead>
        <tbody>
            @foreach($isFr ? [
                ['1. Accord','Définir le périmètre pédagogique, les cohortes, les rôles, l’accès et le dispositif de gouvernance.','Nommer un coordinateur et identifier les programmes concernés.'],
                ['2. Accès','Configurer l’accès pédagogique aux systèmes et des comptes adaptés aux rôles.','Fournir des appareils ou une salle, une connexion et les horaires de pratique.'],
                ['3. Formation des enseignants','Préparer les enseignants et soutenir la formation des formateurs.','Désigner les enseignants et référents académiques.'],
                ['4. Pratique et évaluation','Fournir les exercices et proposer les évaluations convenues.','Intégrer les activités aux cours et participer à la revue académique.'],
                ['5. Revue annuelle','Examiner les retours, résultats et améliorations avec les partenaires.','Partager les retours des étudiants, enseignants et employeurs.'],
            ] : [
                ['1. Agreement','Agree the learning scope, cohorts, roles, access and governance.','Name a coordinator and identify the programmes involved.'],
                ['2. Access','Configure learning access and role-appropriate system accounts.','Provide devices or a lab, connectivity and practice schedules.'],
                ['3. Faculty preparation','Prepare lecturers and support train-the-trainer activities.','Nominate lecturers and academic coordinators.'],
                ['4. Practice and assessment','Provide workflow exercises and agreed assessments.','Integrate activities into courses and join academic review.'],
                ['5. Annual review','Review feedback, outcomes and improvements with partners.','Share feedback from learners, faculty and employers.'],
            ] as $row)
            <tr><td><strong>{{ $row[0] }}</strong></td><td>{{ $row[1] }}</td><td>{{ $row[2] }}</td></tr>
            @endforeach
        </tbody>
    </table></div>
    <h3 style="color:#eaf2f8;margin:26px 0 12px">{{ $isFr ? 'Préparation indicative de l’établissement' : 'Indicative institution readiness' }}</h3>
    <div class="mi-grid">
        <article class="mi-card"><h3>{{ $isFr ? 'Équipe académique' : 'Academic team' }}</h3><p>{{ $isFr ? 'Désigner un coordinateur et les enseignants qui intégreront les exercices aux cours. Le projet propose de former les enseignants référents.' : 'Nominate a programme coordinator and lecturers who will embed the exercises in courses. The draft proposes training faculty leads.' }}</p></article>
        <article class="mi-card"><h3>{{ $isFr ? 'Appareils et connexion' : 'Devices and connectivity' }}</h3><p>{{ $isFr ? 'Prévoir une salle informatique ou les appareils des étudiants, ainsi qu’une connexion pendant les séances pratiques. Les exigences réelles sont fixées selon les plateformes et la cohorte.' : 'Plan for a computer room or students’ own devices and connectivity during practical sessions. Actual requirements depend on the platforms and cohort.' }}</p></article>
        <article class="mi-card"><h3>{{ $isFr ? 'Cohortes et périmètre' : 'Cohorts and scope' }}</h3><p>{{ $isFr ? 'Choisir les filières, le nombre d’apprenants, les objectifs et les parcours de rôle à couvrir dans l’accord de partenariat.' : 'Agree which programmes, learner numbers, learning outcomes and role-based workflows are included in the partnership.' }}</p></article>
    </div>
    <p class="mi-note" style="margin-top:16px">{{ $isFr ? 'Un partenaire peut proposer une co-émission de certificats et une revue clinique ou académique. Cela n’est possible qu’après accord écrit et approbation du programme. Le programme n’altère pas les diplômes délivrés par les établissements.' : 'A partner may propose certificate co-issuance and clinical or academic review. These arrangements require written agreement and programme approval. The programme does not alter degrees awarded by participating institutions.' }}</p>
</section>

<section class="mi-section" id="student-journey">
    <div class="section-label">{{ $isFr ? 'Parcours étudiant' : 'Student pathway' }}</div>
    <h2>{{ $isFr ? 'De l’accès à la certification' : 'From platform access to certification' }}</h2>
    <div class="mi-steps">
        @foreach($isFr ? [
            ['Inscription institutionnelle','L’établissement partenaire inscrit une cohorte et définit les rôles et objectifs.'],
            ['Accès aux plateformes','Les étudiants et enseignants reçoivent un accès adapté à leur parcours de formation.'],
            ['Cours et pratique guidée','Les étudiants suivent les leçons, exercices et études de cas par rôle.'],
            ['Progression et évaluation','La progression et les compétences sont évaluées selon le programme convenu.'],
            ['Certificat de réussite','Les étudiants qui satisfont aux exigences reçoivent un certificat OPES et partenaire, selon l’accord du programme.'],
        ] : [
            ['Institutional enrolment','The partner institution registers a cohort and agrees learning goals and roles.'],
            ['Platform access','Students and faculty receive access matched to the learning pathway.'],
            ['Lessons and guided practice','Students complete lessons, workflow exercises and role-based case studies.'],
            ['Progress and assessment','Progress and demonstrated skills are reviewed against the agreed curriculum.'],
            ['Completion certificate','Students who meet the requirements receive an OPES and partner certificate under the programme agreement.'],
        ] as $step)
        <article class="mi-step"><h3>{{ $step[0] }}</h3><p>{{ $step[1] }}</p></article>
        @endforeach
    </div>
    <p class="mi-note" style="margin-top:18px">{{ $isFr ? 'Les critères d’évaluation, les compétences certifiées, les organismes co-émetteurs et les modalités de chaque cohorte sont définis avec l’institution. Le certificat de programme OPES ne remplace pas un diplôme universitaire, une autorisation professionnelle ou une accréditation externe.' : 'Assessment criteria, certified competencies, co-issuing partners and cohort arrangements are agreed with the institution. An OPES programme certificate does not replace a university degree, professional licence or external accreditation.' }}</p>
</section>

<section class="mi-section" id="institution-partners">
    <div class="section-label">{{ $isFr ? 'Projet de transformation numérique' : 'Digital transformation project' }}</div>
    <h2>{{ $isFr ? 'Appel aux universités, hôpitaux d’enseignement et organisations de santé' : 'An invitation to universities, teaching hospitals and health organizations' }}</h2>
    <p class="mi-lead">{{ $isFr ? 'La formation en informatique médicale progresse lorsque les apprenants, les équipes de soins, les enseignants et les responsables des systèmes travaillent ensemble. OPES invite les institutions à contribuer à la transformation numérique des soins par la formation, les projets pratiques et l’amélioration des parcours.' : 'Medical Informatics grows when learners, care teams, educators and system leaders work together. OPES invites institutions to contribute to digital health transformation through education, practical projects and better care workflows.' }}</p>
    <div class="mi-grid">
        @foreach($isFr ? [
            ['school','Universités et écoles de santé','Co-définir les cours, intégrer les cohortes étudiantes et désigner les enseignants référents.'],
            ['hospital','Hôpitaux d’enseignement','Accueillir des parcours d’apprentissage, partager l’expertise des équipes et encadrer des projets pratiques.'],
            ['landmark','Organisations de santé et pouvoirs publics','Proposer des besoins prioritaires, des projets de transformation et des compétences à développer.'],
            ['microscope','Partenaires de recherche et technologie','Contribuer aux études, à l’évaluation des systèmes et aux outils utiles à la formation.'],
        ] : [
            ['school','Universities and health schools','Shape the curriculum, enrol student cohorts and appoint faculty coordinators.'],
            ['hospital','Teaching hospitals','Host learning workflows, contribute practitioner expertise and mentor applied projects.'],
            ['landmark','Health organizations and public institutions','Propose priority needs, transformation projects and competencies to develop.'],
            ['microscope','Research and technology partners','Contribute studies, system evaluation and tools that support learning.'],
        ] as $partner)
        <article class="mi-card"><div class="mi-icon"><i data-lucide="{{ $partner[0] }}" style="width:20px;height:20px"></i></div><h3>{{ $partner[1] }}</h3><p>{{ $partner[2] }}</p></article>
        @endforeach
    </div>
</section>

<section class="mi-section" id="mi-faq">
    <div class="section-label">{{ $isFr ? 'Questions fréquentes' : 'Frequently asked' }}</div>
    <h2>{{ $isFr ? 'Pour commencer un partenariat' : 'Starting an institutional partnership' }}</h2>
    <div class="mi-faq">
        <details><summary>{{ $isFr ? 'L’accès aux plateformes est-il réellement gratuit pour les institutions ?' : 'Is platform access really free for institutions?' }}</summary><p>{{ $isFr ? 'Oui. OPES propose gratuitement l’accès institutionnel aux plateformes pour l’apprentissage des étudiants et des enseignants des établissements de santé, écoles de soins infirmiers et universités qui rejoignent le programme. L’intégration et le parcours pédagogique se planifient avec l’établissement.' : 'Yes. OPES offers free institutional platform access for student and faculty learning to health institutions, nursing schools and universities that join the programme. Onboarding and learning pathways are planned with each institution.' }}</p></details>
        <details><summary>{{ $isFr ? 'Les étudiants utilisent-ils des dossiers de vrais patients ?' : 'Will students use real patient records?' }}</summary><p>{{ $isFr ? 'Les étudiants pratiquent les vrais parcours de travail numérique avec des données de formation ou des données spécialement autorisées et protégées. Les dossiers identifiables ne sont pas utilisés sans base légale, autorisations et gouvernance appropriées.' : 'Students practise real digital workflows using training data or specifically authorized and protected data. Identifiable records are not used without a lawful basis, approvals and appropriate governance.' }}</p></details>
        <details><summary>{{ $isFr ? 'Comment le certificat est-il délivré ?' : 'How is the certificate issued?' }}</summary><p>{{ $isFr ? 'Les critères et évaluations sont convenus avec l’établissement. Les étudiants qui terminent le parcours et répondent aux critères reçoivent un certificat délivré par OPES Health Systems et, lorsque le programme est co-organisé, par le partenaire indiqué dans l’accord.' : 'Assessment criteria are agreed with the institution. Students who complete the pathway and meet its requirements receive a certificate issued by OPES Health Systems and, for co-delivered programmes, the partner named in the programme agreement.' }}</p></details>
        <details><summary>{{ $isFr ? 'Comment une institution peut-elle rejoindre le programme ?' : 'How can an institution join?' }}</summary><p>{{ $isFr ? 'Un responsable académique ou institutionnel peut envoyer une proposition. Nous échangeons ensuite sur les cohortes, les cours, l’accès, les données de formation, l’encadrement et les modalités de certification.' : 'An academic or institutional lead can submit a partnership proposal. We then discuss cohorts, courses, platform access, training data, faculty support and certification arrangements.' }}</p></details>
    </div>
</section>

<div class="demo-section">
    <h2>{{ $isFr ? 'Construisons ensemble les compétences numériques de santé' : 'Build digital health skills with us' }}</h2>
    <p>{{ $isFr ? 'Établissements de santé, écoles de soins infirmiers, universités et hôpitaux d’enseignement : proposez un partenariat et demandez l’accès gratuit aux plateformes OPES pour vos apprenants.' : 'Health organizations, nursing schools, universities and teaching hospitals: propose a partnership and request free OPES platform access for your learners.' }}</p>
    <a href="{{ url($locale.'/become-a-partner?partner_type=academic') }}" class="btn-primary" style="display:inline-flex;margin-top:8px">{{ $isFr ? 'Proposer un partenariat institutionnel' : 'Propose an institutional partnership' }} <i data-lucide="arrow-right" style="width:15px;height:15px"></i></a>
</div>

</x-layouts.app>

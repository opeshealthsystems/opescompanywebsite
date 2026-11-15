@php
    $locale = app()->getLocale();
    $isFr = $locale === 'fr';
    $researchAreas = $isFr ? [
        ['cpu', 'Dispositifs et matériel médical', 'Dispositifs connectés, équipements de diagnostic et de suivi, exigences d’installation, maintenance et utilisation dans les établissements.'],
        ['workflow', 'Logiciels et flux de soins', 'Dossiers patients, systèmes de laboratoire et de pharmacie, télémédecine, triage et outils d’aide à la décision clinique.'],
        ['network', 'Données et interopérabilité', 'Qualité des données, échange sécurisé, identifiants de santé et intégration des systèmes dans des environnements de soins réels.'],
        ['scan-search', 'Évaluation des technologies', 'Utilisabilité, adéquation au contexte, performance opérationnelle, sécurité et besoins de mise en œuvre.'],
    ] : [
        ['cpu', 'Medical devices and hardware', 'Connected devices, diagnostic and monitoring equipment, installation needs, maintenance and use in care settings.'],
        ['workflow', 'Health software and care workflows', 'Health records, laboratory and pharmacy systems, telemedicine, triage and clinical decision support tools.'],
        ['network', 'Health data and interoperability', 'Data quality, secure exchange, health identifiers and system integration in real care environments.'],
        ['scan-search', 'Technology evaluation', 'Usability, contextual fit, operational performance, safety and implementation needs.'],
    ];
    $method = $isFr ? [
        ['01', 'Définir la question', 'Co-concevoir une question de recherche, les résultats attendus et les responsabilités avec les partenaires.'],
        ['02', 'Établir le cadre', 'Préciser le protocole, les autorisations, la gouvernance des données, la revue éthique et les mesures de sécurité.'],
        ['03', 'Étudier en contexte', 'Évaluer la technologie avec les utilisateurs et les processus concernés, dans un périmètre convenu.'],
        ['04', 'Partager les résultats', 'Documenter les limites, les enseignements et les prochaines étapes convenues avec les institutions partenaires.'],
    ] : [
        ['01', 'Frame the question', 'Co-design a research question, expected outcomes and responsibilities with participating partners.'],
        ['02', 'Agree the governance', 'Set the protocol, permissions, data governance, ethics review and security controls before work begins.'],
        ['03', 'Evaluate in context', 'Assess the technology with relevant users and workflows within an agreed project scope.'],
        ['04', 'Share the findings', 'Document limitations, lessons and next steps agreed with participating institutions.'],
    ];
@endphp

<x-layouts.app
    :title="$isFr ? 'Recherche sur les technologies de santé : matériel et logiciels | OPES' : 'Health Technology Research: Hardware and Software | OPES'"
    :description="$isFr ? 'OPES Health Systems étudie les technologies de santé, du matériel médical aux logiciels, aux données et à l’interopérabilité. Nous invitons établissements, universités, laboratoires et partenaires à collaborer.' : 'OPES Health Systems researches healthcare technologies, from medical hardware to software, data and interoperability. We welcome collaboration with care providers, universities, research labs and technology partners.'">

<style>
    .ohs-research { --research-line: #385671; --research-panel: #102A43; }
    .ohs-research-hero { max-width: 1080px; margin: 0 auto; padding: clamp(54px,8vw,94px) 24px 58px; text-align: center; }
    .ohs-research-kicker { display: inline-flex; align-items: center; gap: 8px; margin-bottom: 18px; color: #9ACBFF; font-size: 12px; font-weight: 700; letter-spacing: .11em; text-transform: uppercase; }
    .ohs-research-hero h1 { max-width: 900px; margin: 0 auto 18px; color: #F1F5F9; font-size: clamp(2rem,5vw,3.6rem); line-height: 1.12; letter-spacing: -.035em; }
    .ohs-research-hero p { max-width: 820px; margin: 0 auto; color: #A7B8CC; font-size: clamp(1rem,2vw,1.15rem); line-height: 1.8; }
    .ohs-research-actions { display:flex; justify-content:center; flex-wrap:wrap; gap:12px; margin-top:28px; }
    .ohs-research-button { min-height:46px; display:inline-flex; align-items:center; justify-content:center; gap:9px; border:1px solid #7890AA; border-radius:9px; padding:11px 17px; color:#F1F5F9; text-decoration:none; font-weight:700; background:#315F7D; transition:background-color .15s,border-color .15s; }
    .ohs-research-button:hover { background:#244A63; border-color:#9DB1C7; }
    .ohs-research-button:focus-visible { outline:3px solid #A9D8FF; outline-offset:3px; }
    .ohs-research-button--secondary { background:transparent; }
    .ohs-research-button--secondary:hover { background:#16354F; }
    .ohs-research-section { max-width:1120px; margin:0 auto; padding:clamp(34px,5vw,64px) 24px; scroll-margin-top:86px; }
    .ohs-research-section h2 { margin:0 0 12px; color:#F1F5F9; font-size:clamp(1.5rem,3vw,2.1rem); line-height:1.25; }
    .ohs-research-lead { max-width:820px; margin:0 0 26px; color:#A7B8CC; line-height:1.75; }
    .ohs-research-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:16px; }
    .ohs-research-card { height:100%; border:1px solid var(--research-line); border-radius:13px; background:var(--research-panel); padding:22px; }
    .ohs-research-card h3 { margin:12px 0 8px; color:#F1F5F9; font-size:1.02rem; line-height:1.4; }
    .ohs-research-card p { margin:0; color:#A7B8CC; line-height:1.7; font-size:.94rem; }
    .ohs-research-icon { width:40px; height:40px; display:grid; place-items:center; border:1px solid #385671; border-radius:10px; color:#9ACBFF; background:#16354F; }
    .ohs-research-steps { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:12px; }
    .ohs-research-step { border-top:2px solid #315F7D; border-radius:0 0 11px 11px; background:#102A43; padding:18px; }
    .ohs-research-step b { display:block; margin-bottom:12px; color:#9ACBFF; font-size:.8rem; letter-spacing:.08em; }
    .ohs-research-step h3 { margin:0 0 8px; color:#F1F5F9; font-size:.98rem; }
    .ohs-research-step p { margin:0; color:#A7B8CC; font-size:.9rem; line-height:1.65; }
    .ohs-research-callout { display:grid; grid-template-columns:minmax(0,1fr) auto; align-items:center; gap:22px; border:1px solid #385671; border-radius:14px; background:#102A43; padding:clamp(20px,4vw,34px); }
    .ohs-research-callout h2 { margin:0 0 8px; font-size:clamp(1.35rem,2.5vw,1.8rem); }
    .ohs-research-callout p { margin:0; color:#A7B8CC; line-height:1.7; }
    .ohs-research-note { margin-top:20px; border-left:3px solid #7890AA; padding:5px 0 5px 16px; color:#CBD5E1; line-height:1.7; font-size:.9rem; }
    @media(max-width:780px) { .ohs-research-steps { grid-template-columns:repeat(2,minmax(0,1fr)); } .ohs-research-callout { grid-template-columns:1fr; align-items:start; } }
    @media(max-width:560px) { .ohs-research-hero,.ohs-research-section { padding-left:18px; padding-right:18px; } .ohs-research-grid,.ohs-research-steps { grid-template-columns:1fr; } .ohs-research-button { width:100%; } }
</style>

<div class="ohs-research">
    <header class="ohs-research-hero">
        <div class="ohs-research-kicker"><i data-lucide="microscope" aria-hidden="true"></i>{{ $isFr ? 'Recherche et évaluation des technologies' : 'Research and technology evaluation' }}</div>
        <h1>{{ $isFr ? 'Étudier les technologies qui peuvent améliorer les soins' : 'Researching technologies that can improve healthcare' }}</h1>
        <p>{{ $isFr ? 'OPES Health Systems mène des recherches sur les technologies de santé matérielles et logicielles. Nous nous intéressons à leur fonctionnement dans les contextes de soins, à leur intégration dans les pratiques existantes et aux conditions nécessaires pour qu’elles soient utiles, sûres et adaptées.' : 'OPES Health Systems researches both hardware and software used in healthcare. We examine how technologies work in care settings, how they fit existing practice and what is needed for them to be useful, safe and appropriate to their context.' }}</p>
        <div class="ohs-research-actions">
            <a class="ohs-research-button" href="{{ url($locale.'/contact') }}"><i data-lucide="messages-square" aria-hidden="true"></i>{{ $isFr ? 'Proposer une collaboration' : 'Discuss a research collaboration' }}</a>
            <a class="ohs-research-button ohs-research-button--secondary" href="{{ url($locale.'/deidentified-health-data') }}">{{ $isFr ? 'Partenariat de données' : 'Health data partnerships' }}<i data-lucide="arrow-right" aria-hidden="true"></i></a>
        </div>
    </header>

    <section class="ohs-research-section" aria-label="{{ $isFr ? 'Participer à un projet de recherche' : 'Participate in research' }}">
        <x-participation-form kind="research" :locale="$locale" />
    </section>

    <section class="ohs-research-section" aria-labelledby="research-areas-title">
        <div class="section-label">{{ $isFr ? 'DOMAINES D’ÉTUDE' : 'AREAS OF STUDY' }}</div>
        <h2 id="research-areas-title">{{ $isFr ? 'Du dispositif au système de santé' : 'From individual devices to health systems' }}</h2>
        <p class="ohs-research-lead">{{ $isFr ? 'Nos travaux peuvent être définis avec les établissements de santé, les professionnels, les chercheurs, les universités et les partenaires technologiques, selon une question et un périmètre convenus.' : 'Research activities can be shaped with healthcare institutions, practitioners, researchers, universities and technology partners around a defined question and agreed scope.' }}</p>
        <div class="ohs-research-grid">
            @foreach($researchAreas as [$icon, $title, $description])
                <article class="ohs-research-card">
                    <div class="ohs-research-icon"><i data-lucide="{{ $icon }}" aria-hidden="true"></i></div>
                    <h3>{{ $title }}</h3>
                    <p>{{ $description }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="ohs-research-section" aria-labelledby="research-method-title">
        <div class="section-label">{{ $isFr ? 'APPROCHE' : 'APPROACH' }}</div>
        <h2 id="research-method-title">{{ $isFr ? 'Des recherches définies avec les partenaires' : 'Research designed with participating institutions' }}</h2>
        <p class="ohs-research-lead">{{ $isFr ? 'Chaque projet commence par une question précise. Le protocole, l’accès aux données et aux sites, les rôles, les critères d’évaluation et les livrables sont arrêtés avant toute activité de recherche.' : 'Each project starts with a specific question. The protocol, access to sites or data, responsibilities, evaluation criteria and outputs are agreed before research activity starts.' }}</p>
        <div class="ohs-research-steps">
            @foreach($method as [$number, $title, $description])
                <article class="ohs-research-step"><b>{{ $number }}</b><h3>{{ $title }}</h3><p>{{ $description }}</p></article>
            @endforeach
        </div>
        <p class="ohs-research-note">{{ $isFr ? 'OPES invite les organisations à proposer des études, des évaluations de technologies, des projets pilotes et des travaux de recherche appliquée. La faisabilité, les autorisations, les responsabilités et les ressources sont examinées au cas par cas.' : 'OPES welcomes proposals for studies, technology evaluations, pilots and applied research. Feasibility, permissions, responsibilities and resources are assessed for each proposal.' }}</p>
    </section>

    <section class="ohs-research-section" id="research-partnership">
        <div class="ohs-research-callout">
            <div>
                <h2>{{ $isFr ? 'Construisons une recherche utile aux soins' : 'Let’s work on research that serves healthcare' }}</h2>
                <p>{{ $isFr ? 'Nous invitons les hôpitaux, cliniques, pharmacies, universités, laboratoires de recherche, organismes publics et entreprises technologiques à discuter de questions et de projets communs au Cameroun, en Afrique et avec des partenaires internationaux.' : 'We invite hospitals, clinics, pharmacies, universities, research labs, public organisations and technology companies to discuss shared questions and projects in Cameroon, across Africa and with international partners.' }}</p>
            </div>
            <a class="ohs-research-button" href="{{ url($locale.'/contact') }}">{{ $isFr ? 'Contacter OPES' : 'Contact OPES' }}<i data-lucide="arrow-right" aria-hidden="true"></i></a>
        </div>
    </section>
</div>

</x-layouts.app>

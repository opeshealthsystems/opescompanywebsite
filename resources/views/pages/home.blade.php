@php
    $locale = app()->getLocale();
    $isFr = $locale === 'fr';
    $homeProducts = [
        ['name' => 'OPES EMR', 'icon' => 'briefcase-medical', 'description' => $isFr ? 'Dossier médical électronique et flux de travail pour les établissements.' : 'Electronic medical records and care workflows for health facilities.', 'href' => url($locale.'/products/opes-emr')],
        ['name' => $isFr ? 'Triage santé' : 'Health Triage', 'icon' => 'heart-pulse', 'description' => $isFr ? 'Accueil numérique et priorisation des patients selon le protocole du site.' : 'Digital intake and patient prioritisation using the facility’s protocol.', 'href' => url($locale.'/products/opes-triage')],
        ['name' => 'OPES DrugTrace', 'icon' => 'pill', 'description' => $isFr ? 'Traçabilité des médicaments et visibilité sur les mouvements.' : 'Medicine traceability and visibility across supply movements.', 'href' => url($locale.'/products/opes-drugtrace')],
        ['name' => 'OPES Care Health Network', 'icon' => 'network', 'description' => $isFr ? 'Identité patient, échanges et coordination entre les structures.' : 'Patient identity, exchange and coordination across care settings.', 'href' => url($locale.'/health-id')],
        ['name' => $isFr ? 'Recherche de sang' : 'Blood Finder', 'icon' => 'droplet', 'description' => $isFr ? 'Aider les équipes à repérer les disponibilités de sang dans le réseau.' : 'Help care teams find blood availability across a connected network.', 'href' => url($locale.'/products')],
        ['name' => $isFr ? 'Recherche de médicaments' : 'Medicine Finder', 'icon' => 'search', 'description' => $isFr ? 'Rechercher les médicaments et leur disponibilité dans le réseau.' : 'Find medicines and availability information across a network.', 'href' => url($locale.'/products')],
        ['name' => $isFr ? 'Application mobile patient' : 'Patient Mobile App', 'icon' => 'smartphone', 'description' => $isFr ? 'Une application sécurisée pour les services et le suivi des soins.' : 'A secure mobile companion for health services and care follow-up.', 'href' => url($locale.'/mobile-app')],
        ['name' => $isFr ? 'Télémédecine' : 'Telemedicine', 'icon' => 'video', 'description' => $isFr ? 'Des consultations à distance dans des parcours de soins connectés.' : 'Remote consultations within connected care workflows.', 'href' => url($locale.'/products')],
        ['name' => $isFr ? 'Aide à la décision clinique' : 'Clinical Decision Support', 'icon' => 'layers-3', 'description' => $isFr ? 'Des flux de travail CDSS conçus avec les équipes et partenaires concernés.' : 'CDSS workflows scoped with the clinical teams and partners involved.', 'href' => '#health-services'],
    ];
    $pillars = $isFr ? [
        ['user-round-check', 'Identité et consentement', 'Une base de confiance pour l’identité patient, la liaison des dossiers et l’accès autorisé.'],
        ['clipboard-plus', 'Flux cliniques', 'Des parcours numériques pour l’accueil, les soins, le suivi et la continuité entre structures.'],
        ['pill', 'Sécurité des médicaments', 'Suivre et vérifier les médicaments pour soutenir la disponibilité et la qualité des soins.'],
        ['graduation-cap', 'Personnes et apprentissage', 'Partager les connaissances et apprendre sur des flux de travail de santé concrets.'],
    ] : [
        ['user-round-check', 'Identity and consent', 'A trusted foundation for patient identity, record linkage and authorised access.'],
        ['clipboard-plus', 'Clinical workflows', 'Digital pathways for intake, care, follow-up and continuity across facilities.'],
        ['pill', 'Medicine safety', 'Track and verify medicines to support availability and quality of care.'],
        ['graduation-cap', 'People and learning', 'Share knowledge and learn through practical health workflows.'],
    ];
@endphp

<x-layouts.app
    bodyClass="public-home"
    :title="$isFr ? 'OPES Health Systems | Infrastructure numérique de santé pour l’Afrique' : 'OPES Health Systems | Connected Digital Health Infrastructure for Africa'"
    :description="$isFr ? 'OPES Health Systems relie les données de santé, les flux de soins, les médicaments et les personnes. Partenariats ouverts aux systèmes de santé, institutions, chercheurs et entreprises d’IA en santé.' : 'OPES Health Systems connects health data, care workflows, medicines and people. We welcome health systems, institutions, researchers and healthcare AI partners.'">

<div class="home-concept">
    <section class="home-hero" aria-labelledby="home-title">
        <div class="home-hero-inner">
            <div class="home-hero-copy">
                <div class="home-eyebrow">{{ $isFr ? 'INFRASTRUCTURE NUMÉRIQUE DE SANTÉ CONNECTÉE' : 'CONNECTED DIGITAL HEALTH INFRASTRUCTURE' }}</div>
                <h1 id="home-title">{{ $isFr ? 'Un avenir plus connecté pour la santé en Afrique.' : 'A more connected future for health in Africa.' }}</h1>
                <p>{{ $isFr ? 'OPES relie les données de santé de confiance, les flux de soins, les médicaments et les personnes — et accueille les partenaires technologiques et de recherche.' : 'OPES connects trusted health data, care workflows, medicines and people — and welcomes technology and research partners.' }}</p>
                <div class="home-hero-actions">
                    <a class="home-button home-button-primary" href="{{ url($locale.'/products') }}">{{ $isFr ? 'Explorer nos plateformes' : 'Explore our platforms' }} <i data-lucide="arrow-right" aria-hidden="true"></i></a>
                    <a class="home-button home-button-secondary" href="{{ url($locale.'/partnerships') }}">{{ $isFr ? 'Devenir partenaire OPES' : 'Partner with OPES' }}</a>
                </div>
            </div>

            <div class="home-ecosystem" role="img" aria-label="{{ $isFr ? 'Illustration des technologies OPES connectées autour de l’anatomie, de l’ADN et d’un dossier de santé unifié' : 'Illustration of OPES technologies connected around anatomy, DNA and a unified health record' }}">
                <svg viewBox="0 0 720 440" aria-hidden="true" focusable="false">
                    <rect width="720" height="440" fill="#0b2b52"/>
                    <g fill="none" stroke="#91acc7" stroke-width="1.5" opacity=".75">
                        <path d="M150 100h85l76 44M150 213h94l56 0M150 328h98l63-34M570 87h-74l-91 56M570 190h-76l-86 22M570 307h-82l-81-62"/>
                        <circle cx="235" cy="144" r="4" fill="#9bc8df"/><circle cx="244" cy="213" r="4" fill="#9bc8df"/><circle cx="248" cy="294" r="4" fill="#9bc8df"/>
                        <circle cx="496" cy="143" r="4" fill="#9bc8df"/><circle cx="494" cy="212" r="4" fill="#9bc8df"/><circle cx="488" cy="245" r="4" fill="#9bc8df"/>
                    </g>
                    <g fill="#0f365f" stroke="#b8cfe0" stroke-width="1.25">
                        <rect x="22" y="68" width="142" height="62" rx="5"/><rect x="22" y="181" width="142" height="62" rx="5"/><rect x="22" y="296" width="142" height="62" rx="5"/>
                        <rect x="556" y="56" width="142" height="62" rx="5"/><rect x="556" y="159" width="142" height="62" rx="5"/><rect x="556" y="276" width="142" height="62" rx="5"/>
                    </g>
                    <g fill="#fff" font-family="Inter,Arial,sans-serif" font-size="14" font-weight="600">
                        <text x="51" y="96">{{ $isFr ? 'Données de santé' : 'Health Data' }}</text><text x="51" y="114">&amp; analytics</text>
                        <text x="51" y="206">Master Patient</text><text x="51" y="225">Index (MPI)</text>
                        <text x="51" y="321">{{ $isFr ? 'Accès basé' : 'Consent-based' }}</text><text x="51" y="340">{{ $isFr ? 'sur le consentement' : 'Access' }}</text>
                        <text x="605" y="93">OPES EMR</text><text x="594" y="196">{{ $isFr ? 'Télémédecine' : 'Telemedicine' }}</text>
                        <text x="594" y="303">{{ $isFr ? 'Aide à la décision' : 'Clinical Decision' }}</text><text x="594" y="322">{{ $isFr ? 'clinique' : 'Support' }}</text>
                    </g>
                    <g fill="none" stroke="#d8e6ef" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" opacity=".94">
                        <circle cx="359" cy="71" r="26"/><path d="M332 112q27-18 54 0l20 38-10 62-9 38 20 89m-75-189-18 62 13 42-13 73m26-115h40m-25 0v44m-19 0 19 26 20-26m-25 86-12 60m28-60 12 60"/>
                    </g>
                    <g fill="none" stroke="#00a8d2" stroke-width="2.3" opacity=".95">
                        <path d="M344 128c31 17 31 32 0 49s-31 32 0 49 31 32 0 49  -31 32 0 49"/><path d="M374 128c-31 17-31 32 0 49s31 32 0 49-31 32 0 49 31 32 0 49"/>
                        <path d="M344 128h30M344 177h30M344 226h30M344 275h30M344 324h30"/>
                    </g>
                    <g>
                        <rect x="302" y="177" width="116" height="130" rx="5" fill="#f8fbfd" stroke="#91acc7"/>
                        <rect x="315" y="190" width="20" height="20" rx="3" fill="#dcecf3"/><path d="M320 200h10m-5-5v10" stroke="#315f7d" stroke-width="1.5"/>
                        <text x="342" y="199" fill="#102a43" font-family="Inter,Arial,sans-serif" font-size="10" font-weight="700">{{ $isFr ? 'Dossier unifié' : 'Unified Health' }}</text>
                        <text x="342" y="211" fill="#102a43" font-family="Inter,Arial,sans-serif" font-size="10" font-weight="700">{{ $isFr ? 'de santé' : 'Record' }}</text>
                        <path d="M314 222h92M314 254h92M314 286h92" stroke="#c9d7e2"/>
                        <text x="315" y="239" fill="#425a70" font-family="Inter,Arial,sans-serif" font-size="9">{{ $isFr ? 'Historique clinique' : 'Clinical history' }}</text>
                        <text x="315" y="271" fill="#425a70" font-family="Inter,Arial,sans-serif" font-size="9">{{ $isFr ? 'Médicaments' : 'Medication list' }}</text>
                        <text x="315" y="303" fill="#425a70" font-family="Inter,Arial,sans-serif" font-size="9">{{ $isFr ? 'Épisodes de soins' : 'Care events' }}</text>
                    </g>
                    <g fill="#00a8d2"><circle cx="281" cy="144" r="3"/><circle cx="300" cy="213" r="3"/><circle cx="298" cy="293" r="3"/><circle cx="496" cy="143" r="3"/><circle cx="494" cy="212" r="3"/><circle cx="488" cy="245" r="3"/></g>
                    <g fill="#fff" font-family="Inter,Arial,sans-serif" font-size="12" font-weight="600"><text x="570" y="365">OPES DrugTrace</text></g>
                    <path d="M570 349h-52l-24-40" fill="none" stroke="#91acc7" stroke-width="1.5"/>
                    <rect x="556" y="346" width="142" height="46" rx="5" fill="#0f365f" stroke="#b8cfe0" stroke-width="1.25"/>
                    <text x="589" y="374" fill="#fff" font-family="Inter,Arial,sans-serif" font-size="13" font-weight="600">OPES DrugTrace</text>
                </svg>
                <div class="home-ecosystem-caption"><i data-lucide="git-merge" aria-hidden="true"></i>{{ $isFr ? 'Conçu pour s’intégrer aux systèmes et partenaires concernés' : 'Designed to connect with relevant systems and partners' }}</div>
            </div>
        </div>
    </section>

    <div class="home-trust-band"><span aria-hidden="true"></span><p>{{ $isFr ? 'Une infrastructure numérique de santé de confiance pour les systèmes de santé, les professionnels, les chercheurs et les partenaires en Afrique.' : 'Trusted digital health infrastructure for health systems, providers, researchers and partners across Africa.' }}</p></div>

    <section class="home-section home-pillars" aria-labelledby="home-pillars-title">
        <div class="home-section-heading">
            <h2 id="home-pillars-title">{{ $isFr ? 'Des données de santé aux soins coordonnés.' : 'From health data to coordinated care.' }}</h2>
            <p>{{ $isFr ? 'Relier les données, les parcours cliniques, les médicaments et les équipes pour renforcer les systèmes de santé.' : 'Connect data, clinical workflows, medicines and teams to strengthen health systems.' }}</p>
        </div>
        <div class="home-pillar-grid">
            @foreach($pillars as [$icon, $title, $description])
            <article class="home-pillar-card"><i data-lucide="{{ $icon }}" aria-hidden="true"></i><h3>{{ $title }}</h3><p>{{ $description }}</p></article>
            @endforeach
        </div>
    </section>

    <section class="home-section home-products" id="software-catalogue" aria-labelledby="home-products-title">
        <div class="home-section-heading home-products-heading">
            <div><h2 id="home-products-title">{{ $isFr ? 'Un écosystème. Des outils conçus pour agir.' : 'One ecosystem. Purpose-built tools.' }}</h2><p>{{ $isFr ? 'Des solutions connectées pour renforcer les soins, la sécurité des médicaments et la performance des systèmes de santé.' : 'Connected solutions to strengthen care, medicine safety and health-system performance.' }}</p></div>
            <a class="home-catalogue-link" href="{{ url($locale.'/products') }}">{{ $isFr ? 'Voir tout le catalogue' : 'Explore the full catalogue' }} <i data-lucide="arrow-up-right" aria-hidden="true"></i></a>
        </div>
        <div class="home-product-grid">
            @foreach($homeProducts as $product)
            <a class="home-product-card" href="{{ $product['href'] }}">
                <i data-lucide="{{ $product['icon'] }}" aria-hidden="true"></i>
                <span class="home-product-copy"><strong>{{ $product['name'] }}</strong><span>{{ $product['description'] }}</span></span>
                <i class="home-product-arrow" data-lucide="arrow-right" aria-hidden="true"></i>
            </a>
            @endforeach
        </div>
        <div class="home-region-note"><i data-lucide="map" aria-hidden="true"></i><strong>{{ $isFr ? 'Conçu en Afrique. Pensé pour connecter le monde.' : 'Built in Africa. Designed to connect globally.' }}</strong></div>
    </section>

    <section class="home-partnership" id="health-services" aria-labelledby="home-partnership-title">
        <div class="home-partnership-copy">
            <div class="home-eyebrow">{{ $isFr ? 'PARTENARIATS EN SANTÉ NUMÉRIQUE' : 'HEALTH TECHNOLOGY PARTNERSHIPS' }}</div>
            <h2 id="home-partnership-title">{{ $isFr ? 'Vous développez une IA pour la santé ? Construisons ensemble les bons parcours.' : 'Building healthcare AI? Let’s connect it to the right health workflows.' }}</h2>
            <p>{{ $isFr ? 'OPES est prêt à collaborer avec les entreprises d’IA en santé, les institutions de recherche, les établissements et les organismes publics. OPES apporte des services de données de santé et des flux CDSS ; les partenaires apportent leurs solutions d’IA. Chaque projet définit séparément les données, les autorisations, la gouvernance et la validation.' : 'OPES is ready to partner with healthcare AI companies, research institutions, care providers and public organisations. OPES brings health data services and CDSS workflows; partners bring their AI solutions. Each project separately scopes data, permissions, governance and validation.' }}</p>
            <a class="home-button home-button-primary" href="{{ url($locale.'/partnerships') }}">{{ $isFr ? 'Parlons partenariat' : 'Explore a partnership' }} <i data-lucide="arrow-right" aria-hidden="true"></i></a>
        </div>
        <div class="home-partnership-services">
            <article><i data-lucide="database" aria-hidden="true"></i><div><h3>{{ $isFr ? 'Services de données de santé' : 'Health data services' }}</h3><p>{{ $isFr ? 'Des besoins et usages de données définis avec chaque organisation.' : 'Data needs and use defined with each organisation.' }}</p></div></article>
            <article><i data-lucide="stethoscope" aria-hidden="true"></i><div><h3>{{ $isFr ? 'Flux CDSS' : 'CDSS workflows' }}</h3><p>{{ $isFr ? 'Des flux cliniques à évaluer avec les équipes et partenaires concernés.' : 'Clinical workflows to assess with the teams and partners involved.' }}</p></div></article>
            <article><i data-lucide="flask-conical" aria-hidden="true"></i><div><h3>{{ $isFr ? 'Recherche et validation' : 'Research and validation' }}</h3><p>{{ $isFr ? 'Des collaborations selon des protocoles, autorisations et rôles convenus.' : 'Collaborations under agreed protocols, permissions and partner roles.' }}</p></div></article>
        </div>
    </section>

    @if(isset($articles) && $articles->isNotEmpty())
    <section class="home-section home-resources" aria-labelledby="home-resources-title">
        <div class="home-section-heading"><div><div class="home-eyebrow">{{ $isFr ? 'RESSOURCES' : 'RESOURCES' }}</div><h2 id="home-resources-title">{{ $isFr ? 'Préparer votre projet de santé numérique' : 'Plan your digital health project.' }}</h2></div><a class="home-catalogue-link" href="{{ url($locale.'/blog') }}">{{ $isFr ? 'Toutes les ressources' : 'All resources' }} <i data-lucide="arrow-up-right" aria-hidden="true"></i></a></div>
        <div class="home-resource-grid">
            @foreach($articles as $article)
            <a href="{{ url($locale.'/blog/'.$article->slug) }}"><span>{{ $article->published_at?->format('d M Y') }} · {{ $article->reading_time }} min</span><h3>{{ $article->getLocalizedTitle($locale) }}</h3><p>{{ $article->getLocalizedExcerpt($locale) }}</p></a>
            @endforeach
        </div>
    </section>
    @endif

    <section class="home-contact-band">
        <div><div class="home-eyebrow">{{ $isFr ? 'PARLONS DE VOTRE PROJET' : 'LET’S DISCUSS YOUR PROJECT' }}</div><h2>{{ $isFr ? 'Relions les personnes, les données et les soins.' : 'Let’s connect people, data and care.' }}</h2></div>
        <a class="home-button home-button-light" href="{{ url($locale.'/contact') }}">{{ $isFr ? 'Contacter OPES' : 'Talk to OPES' }} <i data-lucide="arrow-right" aria-hidden="true"></i></a>
    </section>
</div>
</x-layouts.app>

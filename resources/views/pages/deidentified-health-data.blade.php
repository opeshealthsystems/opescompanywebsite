@php
    $locale = app()->getLocale();
    $isFr = $locale === 'fr';
    $institutionTypes = $isFr
        ? ['Hôpitaux et hôpitaux d’enseignement', 'Cliniques et centres de soins', 'Pharmacies et réseaux pharmaceutiques', 'Entreprises pharmaceutiques et fabricants', 'Laboratoires de diagnostic et de recherche', 'Programmes de santé publique et institutions de recherche']
        : ['Hospitals and teaching hospitals', 'Clinics and other care providers', 'Pharmacies and pharmacy networks', 'Pharmaceutical companies and manufacturers', 'Diagnostic and research laboratories', 'Public health programmes and research institutions'];
    $possibleThemes = $isFr
        ? ['Parcours de soins, références et services utilisés', 'Tendances de délivrance et disponibilité des médicaments', 'Opérations de laboratoire et parcours diagnostiques', 'Besoins de maintenance et utilisation du matériel médical', 'Qualité et complétude des données de santé', 'Recherche appliquée et évaluation de technologies']
        : ['Care pathways, referrals and service utilisation', 'Medicine dispensing patterns and availability', 'Laboratory operations and diagnostic pathways', 'Medical device use and maintenance needs', 'Health data quality and completeness', 'Applied research and technology evaluation'];
    $safeguards = $isFr ? [
        ['Gouvernance et autorité', 'Confirmer les droits de l’institution, la base légale, les autorisations et, lorsque nécessaire, l’examen éthique avant toute utilisation.'],
        ['Désidentification et risque', 'Définir et évaluer les méthodes de désidentification et le risque résiduel de réidentification selon les données et le contexte.'],
        ['Usage limité', 'Documenter une finalité précise, les utilisateurs autorisés, la durée de conservation, les restrictions et les règles de suppression.'],
        ['Sécurité et responsabilité', 'Mettre en place des contrôles d’accès, des mesures de sécurité, des obligations de signalement et une interdiction contractuelle de réidentification.'],
    ] : [
        ['Governance and authority', 'Confirm the institution’s rights, lawful basis, approvals and, where required, ethics review before any use.'],
        ['De-identification and risk', 'Define and assess the de-identification methods and residual re-identification risk for the data and its context.'],
        ['Purpose limitation', 'Document a specific purpose, authorised users, retention period, restrictions and deletion requirements.'],
        ['Security and accountability', 'Set access controls, security measures, incident duties and a contractual prohibition on re-identification.'],
    ];
    $faqs = $isFr ? [
        ['Que signifie « désidentifiées » ?', 'Les identifiants directs sont retirés ou transformés et le risque résiduel est évalué. Le terme ne suffit pas, à lui seul, à prouver qu’un jeu de données est anonyme ou sans risque.'],
        ['L’OPES reçoit-il des dossiers identifiants ?', 'Aucun dossier patient ou identifiant ne doit être envoyé par le formulaire ou par courriel. Toute source et tout transfert nécessitent une validation préalable des autorisations, de la finalité, des protections et du cadre contractuel.'],
        ['Qui peut proposer un partenariat ?', 'Les hôpitaux, cliniques, pharmacies, entreprises pharmaceutiques, laboratoires, programmes de santé publique, universités et institutions de recherche peuvent présenter une proposition à haut niveau.'],
        ['La licence ou les données sont-elles déjà disponibles ?', 'Non. La page invite à discuter de propositions. Chaque projet, jeu de données, autorisation, disponibilité, licence et mesure de protection doit être évalué et convenu séparément.'],
    ] : [
        ['What does “de-identified” mean?', 'Direct identifiers are removed or transformed and residual risk is assessed. The label alone does not establish that a dataset is anonymous or risk-free.'],
        ['Does OPES accept identifiable patient records?', 'Do not send patient records or identifiers through the website form or by email. Any source or transfer requires prior review of authority, purpose, safeguards and contractual terms.'],
        ['Who can propose a partnership?', 'Hospitals, clinics, pharmacies, pharmaceutical companies, laboratories, public health programmes, universities and research institutions can submit a high-level proposal.'],
        ['Are a licence or datasets already available?', 'No. This page invites proposals for discussion. Each project, dataset, approval, availability, licence and safeguard must be reviewed and agreed separately.'],
    ];
    $faqSchema = ['@context' => 'https://schema.org', '@type' => 'FAQPage', 'inLanguage' => $locale, 'mainEntity' => array_map(fn ($item) => [
        '@type' => 'Question', 'name' => $item[0], 'acceptedAnswer' => ['@type' => 'Answer', 'text' => $item[1]],
    ], $faqs)];
@endphp

<x-layouts.app
    :title="$isFr ? 'Données de santé désidentifiées pour améliorer la santé | OPES' : 'De-identified Health Data for the Betterment of Health | OPES'"
    :description="$isFr ? 'OPES invite hôpitaux, cliniques, pharmacies, entreprises pharmaceutiques, laboratoires et institutions à discuter de licences de données de santé désidentifiées pour la recherche, sous réserve de gouvernance, d’autorisations et de protections adaptées.' : 'OPES invites hospitals, clinics, pharmacies, pharmaceutical companies, laboratories and health institutions to discuss licensing de-identified health data for research, subject to governance, authority and appropriate safeguards.'">

<style>
    .ohs-data { --data-line:#385671; --data-panel:#102A43; }
    .ohs-data-hero { max-width:1100px; margin:0 auto; padding:clamp(52px,8vw,90px) 24px 54px; text-align:center; }
    .ohs-data-kicker { display:inline-flex; align-items:center; gap:8px; margin-bottom:18px; color:#9ACBFF; font-size:12px; font-weight:700; letter-spacing:.1em; text-transform:uppercase; }
    .ohs-data-hero h1 { max-width:970px; margin:0 auto 18px; color:#F1F5F9; font-size:clamp(2rem,5vw,3.45rem); line-height:1.12; letter-spacing:-.035em; }
    .ohs-data-hero p { max-width:850px; margin:0 auto; color:#A7B8CC; font-size:clamp(1rem,2vw,1.13rem); line-height:1.8; }
    .ohs-data-actions { display:flex; justify-content:center; flex-wrap:wrap; gap:12px; margin-top:26px; }
    .ohs-data-button { min-height:46px; display:inline-flex; align-items:center; justify-content:center; gap:9px; padding:11px 17px; border:1px solid #7890AA; border-radius:9px; background:#315F7D; color:#F1F5F9; text-decoration:none; font-weight:700; transition:background-color .15s,border-color .15s; }
    .ohs-data-button:hover { background:#244A63; border-color:#9DB1C7; }
    .ohs-data-button:focus-visible { outline:3px solid #A9D8FF; outline-offset:3px; }
    .ohs-data-button--secondary { background:transparent; }
    .ohs-data-button--secondary:hover { background:#16354F; }
    .ohs-data-section { max-width:1120px; margin:0 auto; padding:clamp(34px,5vw,62px) 24px; scroll-margin-top:86px; }
    .ohs-data-section h2 { margin:0 0 12px; color:#F1F5F9; font-size:clamp(1.45rem,3vw,2.05rem); line-height:1.25; }
    .ohs-data-lead { max-width:850px; margin:0 0 24px; color:#A7B8CC; line-height:1.75; }
    .ohs-data-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:15px; }
    .ohs-data-card { height:100%; padding:21px; border:1px solid var(--data-line); border-radius:12px; background:var(--data-panel); }
    .ohs-data-card h3 { margin:0 0 8px; color:#F1F5F9; font-size:1rem; line-height:1.4; }
    .ohs-data-card p { margin:0; color:#A7B8CC; font-size:.93rem; line-height:1.7; }
    .ohs-data-list { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:10px 24px; margin:0; padding:0; list-style:none; }
    .ohs-data-list li { display:flex; align-items:flex-start; gap:10px; color:#CBD5E1; line-height:1.6; }
    .ohs-data-list i { flex:0 0 auto; margin-top:3px; color:#9ACBFF; }
    .ohs-data-guard { margin-top:22px; padding:16px 18px; border:1px solid #7890AA; border-radius:10px; background:#102A43; color:#E2E8F0; line-height:1.7; font-size:.92rem; }
    .ohs-data-guard strong { color:#F1F5F9; }
    .ohs-data-steps { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:12px; }
    .ohs-data-step { min-height:160px; border-top:2px solid #315F7D; border-radius:0 0 11px 11px; background:#102A43; padding:18px; }
    .ohs-data-step b { display:block; margin-bottom:12px; color:#9ACBFF; font-size:.8rem; letter-spacing:.08em; }
    .ohs-data-step h3 { margin:0 0 8px; color:#F1F5F9; font-size:.97rem; }
    .ohs-data-step p { margin:0; color:#A7B8CC; font-size:.9rem; line-height:1.65; }
    .ohs-data-faq { border-top:1px solid var(--data-line); }
    .ohs-data-faq details { padding:17px 0; border-bottom:1px solid var(--data-line); }
    .ohs-data-faq summary { color:#F1F5F9; font-weight:700; line-height:1.55; cursor:pointer; }
    .ohs-data-faq p { max-width:850px; margin:10px 0 0; color:#A7B8CC; line-height:1.7; }
    .ohs-data-callout { display:grid; grid-template-columns:minmax(0,1fr) auto; align-items:center; gap:20px; padding:clamp(20px,4vw,34px); border:1px solid var(--data-line); border-radius:14px; background:#102A43; }
    .ohs-data-callout h2 { margin:0 0 8px; }
    .ohs-data-callout p { margin:0; color:#A7B8CC; line-height:1.7; }
    @media(max-width:780px) { .ohs-data-steps { grid-template-columns:repeat(2,minmax(0,1fr)); } .ohs-data-callout { grid-template-columns:1fr; } }
    @media(max-width:560px) { .ohs-data-hero,.ohs-data-section { padding-left:18px; padding-right:18px; } .ohs-data-grid,.ohs-data-list,.ohs-data-steps { grid-template-columns:1fr; } .ohs-data-step { min-height:0; } .ohs-data-button { width:100%; } }
</style>

<div class="ohs-data">
    <header class="ohs-data-hero">
        <div class="ohs-data-kicker"><i data-lucide="database" aria-hidden="true"></i>{{ $isFr ? 'Partenariats de données de santé' : 'Health data partnerships' }}</div>
        <h1>{{ $isFr ? 'Des données de santé désidentifiées pour améliorer la santé' : 'De-identified Health Data for the Betterment of Health' }}</h1>
        <p>{{ $isFr ? 'OPES Health Systems invite les hôpitaux, cliniques, pharmacies, entreprises pharmaceutiques, laboratoires et autres institutions de santé à discuter de licences de données désidentifiées à OPES pour des projets de recherche et d’amélioration des systèmes de santé.' : 'OPES Health Systems invites hospitals, clinics, pharmacies, pharmaceutical companies, laboratories and other health institutions to discuss licensing de-identified data to OPES for research and health-system improvement projects.' }}</p>
        <div class="ohs-data-actions">
            <a class="ohs-data-button" href="{{ url($locale.'/contact?service=health-data') }}"><i data-lucide="messages-square" aria-hidden="true"></i>{{ $isFr ? 'Proposer un partenariat de données' : 'Discuss licensing data to OPES' }}</a>
            <a class="ohs-data-button ohs-data-button--secondary" href="{{ url($locale.'/research') }}">{{ $isFr ? 'Voir nos domaines de recherche' : 'Explore our research areas' }}<i data-lucide="arrow-right" aria-hidden="true"></i></a>
        </div>
    </header>

    <section class="ohs-data-section" aria-label="{{ $isFr ? 'Participer à un partenariat de données' : 'Participate in a health data partnership' }}">
        <x-participation-form kind="data-license" :locale="$locale" />
    </section>

    <section class="ohs-data-section" aria-labelledby="data-institutions-title">
        <div class="section-label">{{ $isFr ? 'PARTENAIRES RECHERCHÉS' : 'INSTITUTIONAL PARTNERS' }}</div>
        <h2 id="data-institutions-title">{{ $isFr ? 'Partageons les bénéfices d’une meilleure recherche en santé' : 'Partnering to make health research more useful' }}</h2>
        <p class="ohs-data-lead">{{ $isFr ? 'Nous souhaitons échanger avec les institutions qui produisent ou détiennent des données de santé et qui envisagent d’autoriser leur utilisation dans un objectif défini, encadré et utile aux soins ou à la santé publique.' : 'We want to hear from institutions that generate or hold health data and are considering authorising its use for a defined, governed purpose that can contribute to care or public health.' }}</p>
        <ul class="ohs-data-list">
            @foreach($institutionTypes as $institution)
                <li><i data-lucide="building-2" aria-hidden="true"></i><span>{{ $institution }}</span></li>
            @endforeach
        </ul>
    </section>

    <section class="ohs-data-section" aria-labelledby="data-themes-title">
        <div class="section-label">{{ $isFr ? 'FINALITÉS POSSIBLES' : 'POTENTIAL RESEARCH THEMES' }}</div>
        <h2 id="data-themes-title">{{ $isFr ? 'Des usages définis avant l’accès' : 'A clear purpose before any access' }}</h2>
        <p class="ohs-data-lead">{{ $isFr ? 'Les exemples ci-dessous sont des thèmes de discussion, pas une déclaration que les données concernées sont disponibles. La finalité, la source, les variables, la période et les résultats sont définis avec chaque institution.' : 'The examples below are topics for discussion, not a claim that these datasets are available. Purpose, source, fields, time period and outputs are agreed with each institution.' }}</p>
        <div class="ohs-data-grid">
            @foreach($possibleThemes as $theme)
                <article class="ohs-data-card"><h3>{{ $theme }}</h3><p>{{ $isFr ? 'Étudier des tendances agrégées ou des questions opérationnelles dans un périmètre et une durée convenus.' : 'Study aggregate patterns or operational questions within an agreed scope and time period.' }}</p></article>
            @endforeach
        </div>
    </section>

    <section class="ohs-data-section" aria-labelledby="data-safeguards-title">
        <div class="section-label">{{ $isFr ? 'GOUVERNANCE AVANT TRANSFERT' : 'GOVERNANCE BEFORE TRANSFER' }}</div>
        <h2 id="data-safeguards-title">{{ $isFr ? 'Les autorisations et les protections d’abord' : 'Authority and safeguards come first' }}</h2>
        <p class="ohs-data-lead">{{ $isFr ? 'Une licence ne remplace pas la base légale, les autorisations institutionnelles, les obligations de confidentialité ni les examens éthiques qui peuvent s’appliquer. Le fait de retirer des identifiants directs ne suffit pas à éliminer tout risque.' : 'A licence does not replace a lawful basis, institutional approvals, confidentiality obligations or ethics review that may apply. Removing direct identifiers alone does not eliminate all risk.' }}</p>
        <div class="ohs-data-grid">
            @foreach($safeguards as [$title, $description])
                <article class="ohs-data-card"><h3>{{ $title }}</h3><p>{{ $description }}</p></article>
            @endforeach
        </div>
        <div class="ohs-data-guard"><strong>{{ $isFr ? 'Évaluation selon le territoire.' : 'Review for each jurisdiction.' }}</strong> {{ $isFr ? 'Les projets au Cameroun doivent être évalués au regard de la Loi n° 2024/017 relative à la protection des données à caractère personnel et des règles sectorielles applicables. Les projets ailleurs sont examinés selon les lois locales. La structure source doit confirmer qu’elle peut autoriser le partage et l’utilisation proposés.' : 'Projects involving Cameroon must be assessed against Law No. 2024/017 on personal data protection and applicable sector rules. Projects elsewhere are reviewed under local law. The source institution must confirm it can authorise the proposed sharing and use.' }}</div>
    </section>

    <section class="ohs-data-section" aria-labelledby="data-process-title">
        <div class="section-label">{{ $isFr ? 'PROCESSUS PROPOSÉ' : 'PROPOSED PROCESS' }}</div>
        <h2 id="data-process-title">{{ $isFr ? 'De la première discussion à un accord encadré' : 'From an initial conversation to a governed agreement' }}</h2>
        <div class="ohs-data-steps">
            @foreach($isFr ? [
                ['01','Échange initial','Présenter l’institution et le besoin général, sans transmettre de données.'],
                ['02','Faisabilité et gouvernance','Examiner les droits, la finalité, les autorisations, les risques et les capacités.'],
                ['03','Protocole et licence','Convenir des variables, accès, responsabilités, durée, frais éventuels et restrictions.'],
                ['04','Transfert contrôlé ou analyse','Ne commencer qu’après validations, contrats et contrôles de sécurité requis.'],
            ] : [
                ['01','Initial discussion','Describe the institution and high-level need without sending any data.'],
                ['02','Feasibility and governance','Review rights, purpose, approvals, risks and operational capability.'],
                ['03','Protocol and licence','Agree fields, access, responsibilities, duration, any fees and restrictions.'],
                ['04','Controlled transfer or analysis','Start only after required approvals, contracts and security controls are in place.'],
            ] as [$number, $title, $description])
                <article class="ohs-data-step"><b>{{ $number }}</b><h3>{{ $title }}</h3><p>{{ $description }}</p></article>
            @endforeach
        </div>
    </section>

    <section class="ohs-data-section" aria-labelledby="data-faq-title">
        <div class="section-label">{{ $isFr ? 'QUESTIONS FRÉQUENTES' : 'FREQUENT QUESTIONS' }}</div>
        <h2 id="data-faq-title">{{ $isFr ? 'À savoir avant de nous contacter' : 'Before you get in touch' }}</h2>
        <div class="ohs-data-faq">
            @foreach($faqs as [$question, $answer])
                <details><summary>{{ $question }}</summary><p>{{ $answer }}</p></details>
            @endforeach
        </div>
    </section>

    <section class="ohs-data-section" id="data-partnership">
        <div class="ohs-data-callout">
            <div>
                <h2>{{ $isFr ? 'Votre institution détient-elle des données utiles à la recherche en santé ?' : 'Does your institution hold data that could support health research?' }}</h2>
                <p>{{ $isFr ? 'Présentez votre organisation et votre proposition à haut niveau. N’envoyez aucun dossier, identifiant patient, échantillon de données ni information sensible par le formulaire ou par courriel.' : 'Tell us about your organisation and proposal at a high level. Do not send records, patient identifiers, data samples or sensitive information through the form or by email.' }}</p>
            </div>
            <a class="ohs-data-button" href="{{ url($locale.'/contact?service=health-data') }}">{{ $isFr ? 'Contacter OPES' : 'Start a conversation' }}<i data-lucide="arrow-right" aria-hidden="true"></i></a>
        </div>
    </section>
</div>

@push('schema')
    <script type="application/ld+json"><?php echo json_encode($faqSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP); ?></script>
@endpush

</x-layouts.app>

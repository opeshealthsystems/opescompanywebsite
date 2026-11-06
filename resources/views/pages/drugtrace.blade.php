@php $isFr = app()->getLocale() === 'fr'; @endphp
<x-layouts.app
    title="{{ $isFr ? 'OPES DrugTrace — Sécurité et traçabilité des médicaments' : 'OPES DrugTrace — Medicine Safety & Traceability' }}"
    description="{{ $isFr ? 'Suivez l’origine, les lots, les dates, les rappels et les mouvements des médicaments avec OPES DrugTrace.' : 'Track medicine provenance, lots, expiry, recalls and supply movements with OPES DrugTrace.' }}">
<style>
.dt-hero { max-width:1120px; margin:0 auto; padding:64px 28px 44px; text-align:center; }
.dt-hero h1 { color:#f1f5f9; font-size:clamp(2rem,4vw,3.2rem); line-height:1.12; margin:10px auto 14px; max-width:820px; }
.dt-hero p { color:#b8c6d5; font-size:1.05rem; line-height:1.75; max-width:820px; margin:0 auto; }
.dt-actions { display:flex; justify-content:center; flex-wrap:wrap; gap:12px; margin-top:24px; }
.dt-section { max-width:1120px; margin:0 auto; padding:42px 28px; scroll-margin-top:90px; }
.dt-section h2 { color:#f1f5f9; font-size:clamp(1.4rem,3vw,2rem); margin:0 0 12px; }
.dt-lead { color:#b8c6d5; line-height:1.75; max-width:850px; margin:0 0 24px; }
.dt-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(min(100%,245px),1fr)); gap:14px; }
.dt-card { background:#0b1d31; border:1px solid #29445e; border-radius:12px; padding:20px; }
.dt-card h3 { color:#eaf2f8; font-size:1rem; margin:0 0 8px; }
.dt-card p,.dt-card li { color:#b8c6d5; line-height:1.65; font-size:.9rem; }
.dt-card ul { margin:8px 0 0; padding-left:19px; }
.dt-step { background:#0b1d31; border:1px solid #29445e; border-radius:12px; padding:17px; }
.dt-step b { display:block; color:#acd5ec; font-size:.78rem; letter-spacing:.1em; margin-bottom:9px; }
.dt-step h3 { color:#eaf2f8; font-size:.98rem; margin:0 0 7px; }
.dt-step p { color:#b8c6d5; font-size:.88rem; line-height:1.6; margin:0; }
.dt-note { border:1px solid #506a82; border-radius:10px; background:#0b1d31; padding:15px 18px; color:#d0dce7; line-height:1.7; font-size:.9rem; }
@media(max-width:640px) { .dt-hero,.dt-section { padding-left:18px; padding-right:18px; } }
</style>

<header class="dt-hero">
    <div class="section-label" style="justify-content:center">{{ $isFr ? 'Sécurité du médicament · Traçabilité pharmaceutique' : 'Medicine safety · Pharmaceutical traceability' }}</div>
    <h1>OPES DrugTrace</h1>
    <p>{{ $isFr ? 'Chaque médicament devrait pouvoir raconter son parcours. DrugTrace aide les acteurs autorisés à examiner l’origine, l’authenticité, la légalité, les lots, les rappels et les mouvements — du fournisseur jusqu’au point de délivrance.' : 'Every medicine should be able to tell its story. DrugTrace helps authorized participants review provenance, authenticity, supply-chain legitimacy, lots, recalls and movements from supplier to dispensing point.' }}</p>
    <div class="brand-notice" style="max-width:760px;margin:20px auto 0"><strong>{{ $isFr ? 'Disponible' : 'Released and available' }}</strong> · {{ $isFr ? 'Disponibilité confirmée par OPES le 30 octobre 2026. La portée et les options sont confirmées pendant l’évaluation du projet.' : 'Availability confirmed by OPES on 30 October 2026. Scope and options are confirmed during project evaluation.' }}</div>
    <div class="dt-actions">
        <a class="btn-primary" href="{{ route('book-demo', ['product' => 'opes-drugtrace']) }}">{{ $isFr ? 'Demander une démonstration' : 'Request a demonstration' }}</a>
        <a class="btn-secondary" href="{{ route('contact', ['locale' => app()->getLocale(), 'product_slug' => 'opes-drugtrace']) }}">{{ $isFr ? 'Demander un devis' : 'Request a quote' }}</a>
        <a class="btn-secondary" href="{{ route('products.index') }}">{{ $isFr ? 'Voir les produits OPES' : 'Explore OPES products' }}</a>
    </div>
</header>

<section class="dt-section" id="what-it-traces">
    <div class="section-label">{{ $isFr ? 'Au-delà du simple inventaire' : 'Beyond a simple stock count' }}</div>
    <h2>{{ $isFr ? 'Relier chaque lot à son origine et à son statut' : 'Connect each medicine lot to its origin and status' }}</h2>
    <p class="dt-lead">{{ $isFr ? 'Un stock total ne répond pas toujours aux questions essentielles : qui a fabriqué ou importé le produit, par quelle chaîne il est arrivé, quel lot est concerné, s’il est expiré ou rappelé, et ce qui a été reçu, délivré, mis en quarantaine, retourné ou détruit.' : 'A total stock number cannot answer every important question: who made or imported the product, how it entered the supply chain, which lot it belongs to, whether it has expired or been recalled, and what was received, dispensed, quarantined, returned or destroyed.' }}</p>
    <div class="dt-grid">
        @foreach($isFr ? [
            ['Origine et chaîne d’approvisionnement','Consigner les fournisseurs, la provenance et les mouvements des produits.'],
            ['Authenticité et légalité','Présenter séparément l’authenticité du produit et la légitimité du canal d’approvisionnement.'],
            ['Lots, stocks et péremption','Suivre les quantités, lots, emplacements, dates de péremption et statuts de quarantaine.'],
            ['Rappels, retours et pertes','Identifier les unités concernées, suivre les actions et conserver les preuves de rapprochement.'],
        ] : [
            ['Provenance and supply chain','Record suppliers, product origin and movements through the supply chain.'],
            ['Authenticity and legality','Show product authenticity separately from whether its supply channel is authorized.'],
            ['Lots, stock and expiry','Track quantities, batches, locations, expiry dates and quarantine status.'],
            ['Recalls, returns and losses','Locate affected units, track actions and retain reconciliation evidence.'],
        ] as $item)
        <article class="dt-card"><h3>{{ $item[0] }}</h3><p>{{ $item[1] }}</p></article>
        @endforeach
    </div>
    <p class="dt-note" style="margin-top:16px">{{ $isFr ? 'Un médicament authentique peut avoir suivi un canal non autorisé. DrugTrace affiche des signaux à examiner par les personnes responsables ; ces signaux ne constituent pas, à eux seuls, une conclusion réglementaire ou une accusation.' : 'A genuine medicine may still have entered through an unauthorized channel. DrugTrace surfaces signals for responsible people to investigate; a signal alone is not a regulatory determination or accusation.' }}</p>
</section>

<section class="dt-section" id="workflow">
    <div class="section-label">{{ $isFr ? 'Parcours de traçabilité' : 'Traceability workflow' }}</div>
    <h2>{{ $isFr ? 'De la réception à la délivrance' : 'From receiving to dispensing' }}</h2>
    <div class="dt-grid">
        @foreach($isFr ? [
            ['01','Réception','Comparer la livraison attendue au fournisseur, aux informations de provenance et aux quantités reçues.'],
            ['02','Scan et capture','Enregistrer le produit, le lot, la date, le numéro de série et la quantité.'],
            ['03','Décision','Accepter, signaler, mettre en quarantaine ou envoyer pour investigation selon les règles convenues.'],
            ['04','Stock par lot','Suivre l’emplacement, les transferts, les dates et les alertes de rupture ou péremption.'],
            ['05','Vérification avant délivrance','Contrôler le lot, la date, le rappel, la quarantaine et le statut du numéro de série.'],
            ['06','Délivrance et rapprochement','Tracer la délivrance, réduire le stock et confirmer le mouvement au système connecté lorsque cette intégration est prévue.'],
        ] : [
            ['01','Receive','Compare the expected delivery with supplier, provenance and received-quantity details.'],
            ['02','Scan and capture','Record product, lot, expiry, serial and quantity.'],
            ['03','Decide','Accept, warn, quarantine or refer for investigation under agreed rules.'],
            ['04','Track lot-level stock','Follow locations, transfers, expiry and shortage alerts.'],
            ['05','Verify before dispensing','Check the lot, expiry, recall, quarantine and serial status.'],
            ['06','Dispense and reconcile','Record dispensing, reduce stock and confirm the movement to a connected system where that integration is in scope.'],
        ] as $step)
        <article class="dt-step"><b>{{ $step[0] }}</b><h3>{{ $step[1] }}</h3><p>{{ $step[2] }}</p></article>
        @endforeach
    </div>
</section>

<section class="dt-section" id="capabilities">
    <div class="section-label">{{ $isFr ? 'Fonctions présentées' : 'Product capabilities' }}</div>
    <h2>{{ $isFr ? 'Des preuves pour agir sur les risques d’approvisionnement' : 'Traceability evidence for supply-chain action' }}</h2>
    <div class="dt-grid">
        @foreach($isFr ? [
            ['Vérification par scan','Résultats lisibles : vérifié, avertissement, inconnu, suspect, rappelé, expiré, en quarantaine, déjà délivré, perdu ou volé, ou canal non autorisé.'],
            ['Gestion des rappels','Localiser les stocks concernés, bloquer les unités restantes selon le processus configuré et suivre récupération et rapprochement.'],
            ['Alertes et contrôle des stocks','Suivre les stocks par lot et emplacement, les transferts, les ruptures et les échéances 30/60/90 jours selon le paramétrage.'],
            ['Signaux d’approvisionnement','Comparer les mouvements enregistrés et observés pour repérer les écarts à investiguer — sans attribuer automatiquement une faute.'],
            ['Application et intégration','Scan mobile, API et événements de traçabilité compatibles GS1/EPCIS selon le périmètre d’intégration convenu.'],
            ['Données d’audit','Conserver l’historique des mouvements, décisions, retours et destructions avec les éléments de preuve configurés.'],
        ] : [
            ['Scan verification','Readable outcomes include verified, warning, unknown, suspicious, recalled, expired, quarantined, previously dispensed, lost or stolen, and unauthorized supply channel.'],
            ['Recall management','Locate affected stock, block remaining units under the configured process, and track recovery and reconciliation.'],
            ['Stock alerts and control','Track inventory by lot and location, transfers, shortages and 30/60/90-day expiry alerts where configured.'],
            ['Supply-chain signals','Compare recorded and observed movements to surface discrepancies for investigation without assigning blame automatically.'],
            ['Mobile and integration','Mobile scanning, APIs and GS1/EPCIS-compatible trace events subject to the agreed integration scope.'],
            ['Audit evidence','Retain configured histories of movements, decisions, returns and destruction records.'],
        ] as $item)
        <article class="dt-card"><h3>{{ $item[0] }}</h3><p>{{ $item[1] }}</p></article>
        @endforeach
    </div>
</section>

<section class="dt-section" id="participants">
    <div class="section-label">{{ $isFr ? 'Un réseau de confiance' : 'A shared trust network' }}</div>
    <h2>{{ $isFr ? 'Pour les acteurs du circuit du médicament' : 'For medicine supply-chain participants' }}</h2>
    <div class="dt-grid">
        @foreach($isFr ? [
            ['Hôpitaux et pharmacies','Réception, contrôle par lot, délivrance, rappels, péremption et rapprochement.'],
            ['Fabricants et importateurs','Tracer les lots et signaux de circulation inhabituelle, selon les accès autorisés.'],
            ['Distributeurs et grossistes','Documenter la chaîne de garde et réduire les litiges sur les livraisons.'],
            ['Régulateurs et programmes','Étudier la coordination des rappels, les inspections et les signaux d’intégrité à l’échelle convenue.'],
            ['Professionnels et patients','Les patients peuvent vérifier les informations affichées par scan dans l’application, sans envoyer de code SMS ; les patients ne voient pas les données internes des organisations.'],
            ['Chercheurs','Étudier des indicateurs anonymisés de pertes, délais de rappel et écarts de réception avec les autorisations nécessaires.'],
        ] : [
            ['Hospitals and pharmacies','Manage receiving, lot checks, dispensing, recalls, expiry and reconciliation.'],
            ['Manufacturers and importers','Trace batches and review unusual circulation signals within authorized access.'],
            ['Distributors and wholesalers','Document chain of custody and reduce delivery disputes.'],
            ['Regulators and programmes','Explore recall coordination, inspection support and supply-integrity signals at the agreed scope.'],
            ['Health workers and patients','Patients can scan in the app to view the product, manufacturer, batch, expiry, recall status and warnings, without an SMS code; they do not see organizations’ internal data.'],
            ['Researchers','Study authorized, appropriately anonymized indicators for loss, recall timing and receiving discrepancies.'],
        ] as $item)
        <article class="dt-card"><h3>{{ $item[0] }}</h3><p>{{ $item[1] }}</p></article>
        @endforeach
    </div>
</section>

<section class="dt-section" id="deployment">
    <div class="section-label">{{ $isFr ? 'Déploiement et gouvernance' : 'Deployment and governance' }}</div>
    <h2>{{ $isFr ? 'À configurer selon les responsabilités de chaque organisation' : 'Configured around each organization’s responsibilities' }}</h2>
    <div class="dt-grid">
        @foreach($isFr ? [
            ['Options','Sur site, hébergement géré ou architecture hybride, sous réserve d’évaluation technique et d’accord de service.'],
            ['Accès et audit','Accès selon les rôles et journalisation des actions, avec les responsabilités de chaque partie définies avant déploiement.'],
            ['Connexion aux systèmes','Évaluer les systèmes de pharmacie, EMR, ERP et partenaires à connecter, ainsi que les données et tests d’acceptation nécessaires.'],
        ] : [
            ['Deployment options','On-premise, managed hosting or hybrid, subject to technical assessment and service agreement.'],
            ['Access and audit','Role-based access and activity logging, with each party’s responsibilities agreed before deployment.'],
            ['System connections','Assess pharmacy, EMR, ERP and partner systems to connect, along with data and acceptance tests required.'],
        ] as $item)
        <article class="dt-card"><h3>{{ $item[0] }}</h3><p>{{ $item[1] }}</p></article>
        @endforeach
    </div>
    <article class="dt-card" style="margin-top:14px">
        <h3>{{ $isFr ? 'Modèle tarifaire' : 'Pricing approach' }}</h3>
        <p>{{ $isFr ? 'Prix sur devis. Le document produit prévoit un abonnement par établissement, ainsi que des offres pour fabricants, distributeurs et API ; les déploiements nationaux sont évalués sur devis. Confirmez le périmètre, le nombre de sites et les services inclus.' : 'Pricing is by quote. The product material describes per-facility subscriptions and manufacturer, distributor and API plans; national deployments are scoped by quote. Confirm scope, site count and included services.' }}</p>
    </article>
    <p class="dt-note" style="margin-top:16px">{{ $isFr ? 'La disponibilité commerciale n’est pas une certification réglementaire ni une garantie universelle d’authenticité. La vérification dépend des données enregistrées, des participants et des intégrations activées. Confirmez les capacités de la version proposée, les responsabilités, la gouvernance et les tests de réception dans votre offre.' : 'Commercial availability is not regulatory certification or a universal guarantee of medicine authenticity. Verification depends on recorded data, participating organizations and enabled integrations. Confirm the proposed version’s capabilities, responsibilities, governance and acceptance tests in your offer.' }}</p>
</section>

<div class="demo-section">
    <h2>{{ $isFr ? 'Évaluons votre circuit pharmaceutique' : 'Assess your medicine supply workflow' }}</h2>
    <p>{{ $isFr ? 'Parlez-nous des acteurs, systèmes, parcours de réception et délivrance, exigences de traçabilité et objectifs de rappel de votre organisation.' : 'Tell us about the participants, systems, receiving and dispensing workflows, traceability needs and recall objectives for your organization.' }}</p>
    <div class="dt-actions">
        <a class="btn-primary" href="{{ route('book-demo', ['product' => 'opes-drugtrace']) }}">{{ $isFr ? 'Demander une démonstration' : 'Request a demonstration' }}</a>
        <a class="btn-secondary" href="{{ route('contact', ['locale' => app()->getLocale(), 'product_slug' => 'opes-drugtrace']) }}">{{ $isFr ? 'Demander un devis' : 'Request a quote' }}</a>
    </div>
</div>
</x-layouts.app>

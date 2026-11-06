@php $isFr = app()->getLocale() === 'fr'; $locale = app()->getLocale(); @endphp

<x-layouts.app
    bodyClass="public-drugtrace"
    :title="$isFr ? 'OPES DrugTrace | Traçabilité et sécurité des médicaments' : 'OPES DrugTrace | Medicine Traceability'"
    :description="$isFr ? 'Suivez les médicaments de la réception à la délivrance avec OPES DrugTrace. Évaluez les flux de vérification, de stock et de rappel adaptés à votre organisation.' : 'Track medicines from receipt to dispensing with OPES DrugTrace. Explore verification, stock and recall workflows scoped to your organisation.'">

<div class="drugtrace-page">
    <section class="dt-product-hero" aria-labelledby="drugtrace-title">
        <div class="dt-hero-inner">
            <div class="dt-hero-copy">
                <div class="dt-eyebrow">{{ $isFr ? 'TRAÇABILITÉ DES MÉDICAMENTS' : 'MEDICINE TRACEABILITY' }}</div>
                <h1 id="drugtrace-title">OPES DrugTrace</h1>
                <p>{{ $isFr ? 'Reliez la vérification des médicaments et la visibilité sur les approvisionnements entre les systèmes de santé — de la réception à l’utilisation dans les structures de soins.' : 'Connect medicine verification and supply visibility across health systems, helping teams track products from receipt to use across facilities and care settings.' }}</p>
                <div class="dt-hero-actions">
                    <a class="dt-button dt-button-primary" href="{{ route('book-demo', ['product' => 'opes-drugtrace']) }}">{{ $isFr ? 'Demander une démonstration' : 'Request a product walkthrough' }} <i data-lucide="arrow-right" aria-hidden="true"></i></a>
                    <a class="dt-button dt-button-secondary" href="{{ url($locale.'/partnerships') }}">{{ $isFr ? 'Explorer les partenariats' : 'Explore partnerships' }}</a>
                </div>
                <div class="dt-availability"><span></span>{{ $isFr ? 'Disponible — périmètre à confirmer selon votre projet' : 'Released and available · Scope confirmed for each project' }}</div>
            </div>

            <div class="dt-trace-illustration" aria-label="{{ $isFr ? 'Illustration du suivi des étapes de réception, vérification, mouvement et rapprochement' : 'Illustration of the receive, verify, move and reconcile workflow' }}">
                <div class="dt-pack" aria-hidden="true"><i data-lucide="pill"></i><span></span><span></span><b><i data-lucide="check" aria-hidden="true"></i></b></div>
                <div class="dt-trace-panel">
                    <div class="dt-trace-panel-heading"><i data-lucide="package-check" aria-hidden="true"></i><strong>{{ $isFr ? 'Traçabilité des médicaments' : 'Medicine Trace' }}</strong></div>
                    <ol>
                        @foreach($isFr ? ['Réception','Vérification','Mouvement','Rapprochement'] : ['Receive','Verify','Move','Reconcile'] as $step)
                        <li><span><i data-lucide="check" aria-hidden="true"></i></span><strong>{{ $step }}</strong><i class="dt-trace-line" aria-hidden="true"></i></li>
                        @endforeach
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="dt-workflow-section" aria-labelledby="dt-workflow-title">
        <div class="dt-section-inner">
            <h2 id="dt-workflow-title">{{ $isFr ? 'Tracer les médicaments tout au long du parcours.' : 'Trace medicines across the workflow.' }}</h2>
            <div class="dt-workflow-grid">
                @foreach($isFr ? [
                    ['package', 'Réceptionner', 'Enregistrer les médicaments entrants et les informations disponibles au point de réception.'],
                    ['shield-check', 'Vérifier', 'Examiner les données, le lot et le statut enregistré selon les règles convenues.'],
                    ['layers-3', 'Suivre', 'Consulter les mouvements et le statut des médicaments dans le périmètre configuré.'],
                ] : [
                    ['package', 'Receive', 'Record incoming medicines and available information at the receiving point.'],
                    ['shield-check', 'Verify', 'Review recorded product, batch and status details against agreed rules.'],
                    ['layers-3', 'Monitor', 'Review medicine movements and status within the configured scope.'],
                ] as [$icon, $title, $copy])
                <article class="dt-workflow-item"><i data-lucide="{{ $icon }}" aria-hidden="true"></i><div><h3>{{ $title }}</h3><p>{{ $copy }}</p></div></article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="dt-visibility-section" aria-labelledby="dt-visibility-title">
        <div class="dt-section-inner dt-visibility-grid">
            <div class="dt-dashboard-preview" aria-label="{{ $isFr ? 'Aperçu illustratif du tableau de bord DrugTrace' : 'Illustrative DrugTrace dashboard preview' }}">
                <div class="dt-dashboard-top"><i data-lucide="menu" aria-hidden="true"></i><strong>OPES DrugTrace</strong><span>{{ $isFr ? 'Aperçu produit' : 'Product preview' }}</span></div>
                <div class="dt-dashboard-body">
                    <nav aria-label="{{ $isFr ? 'Aperçu de navigation' : 'Preview navigation' }}">
                        @foreach($isFr ? [['house','Vue générale'],['package','Médicaments'],['arrow-left-right','Mouvements'],['layers-3','Stock'],['file-text','Rapports']] : [['house','Overview'],['package','Medicines'],['arrow-left-right','Movements'],['layers-3','Stock'],['file-text','Reports']] as [$icon, $label])
                        <span class="{{ $loop->first ? 'is-current' : '' }}"><i data-lucide="{{ $icon }}" aria-hidden="true"></i>{{ $label }}</span>
                        @endforeach
                    </nav>
                    <div class="dt-dashboard-content">
                        <h3>{{ $isFr ? 'Vue d’approvisionnement' : 'Supply overview' }}</h3>
                        <div class="dt-dashboard-stats"><div><b>{{ $isFr ? 'Médicaments' : 'Medicines' }}</b><span>{{ $isFr ? 'Catalogue' : 'Catalogue' }}</span></div><div><b>{{ $isFr ? 'Mouvements' : 'Movements' }}</b><span>{{ $isFr ? 'Activité' : 'Activity' }}</span></div><div><b>{{ $isFr ? 'Stock' : 'Stock' }}</b><span>{{ $isFr ? 'Disponibilité' : 'Availability' }}</span></div></div>
                        <div class="dt-activity">
                            <div class="dt-activity-heading">{{ $isFr ? 'Étapes du flux' : 'Workflow steps' }}<span>{{ $isFr ? 'Exemple illustratif' : 'Illustrative example' }}</span></div>
                            @foreach($isFr ? ['Réceptionner','Vérifier','Déplacer','Rapprocher'] : ['Receive','Verify','Move','Reconcile'] as $step)
                            <div class="dt-activity-row"><i data-lucide="{{ $loop->iteration === 2 ? 'shield-check' : ($loop->iteration === 3 ? 'arrow-left-right' : 'package') }}" aria-hidden="true"></i><span>{{ $step }}</span><span class="dt-activity-status">{{ $isFr ? 'Étape' : 'Workflow' }}</span><span class="dt-skeleton" aria-hidden="true"></span></div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            <div class="dt-visibility-copy">
                <h2 id="dt-visibility-title">{{ $isFr ? 'Une vue partagée des mouvements de médicaments.' : 'A shared view of medicine movement.' }}</h2>
                @foreach($isFr ? [
                    ['check-check','Réception vérifiée','Consigner les informations disponibles à l’arrivée des médicaments dans les structures et points d’approvisionnement.'],
                    ['arrow-left-right','Historique des transferts','Consulter les mouvements enregistrés entre les lieux participants.'],
                    ['layers-3','Visibilité du stock','Maintenir une vue claire sur la disponibilité des médicaments dans le périmètre convenu.'],
                ] : [
                    ['check-check','Verified intake','Record and confirm incoming medicines across facilities and supply points.'],
                    ['arrow-left-right','Transfer history','Review where medicines have moved across participating locations.'],
                    ['layers-3','Stock visibility','Keep a clear view of medicine availability across the agreed scope.'],
                ] as [$icon, $title, $copy])
                <article class="dt-benefit"><span><i data-lucide="{{ $icon }}" aria-hidden="true"></i></span><div><h3>{{ $title }}</h3><p>{{ $copy }}</p></div></article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="dt-integrations" aria-labelledby="dt-integrations-title">
        <div class="dt-section-inner">
            <h2 id="dt-integrations-title">{{ $isFr ? 'Conçu pour se connecter aux flux EMR, approvisionnement et données de santé.' : 'Designed to connect with EMR, procurement and health data workflows.' }}</h2>
            <div class="dt-integration-map" aria-label="{{ $isFr ? 'Les systèmes de santé et partenaires peuvent se connecter à DrugTrace selon le périmètre du projet.' : 'Health systems and partners can connect to DrugTrace according to project scope.' }}">
                <div><i data-lucide="stethoscope" aria-hidden="true"></i><strong>OPES EMR</strong></div>
                <div><i data-lucide="shopping-cart" aria-hidden="true"></i><strong>{{ $isFr ? 'Approvisionnement' : 'Procurement' }}</strong></div>
                <b>OPES DrugTrace</b>
                <div><i data-lucide="database" aria-hidden="true"></i><strong>{{ $isFr ? 'Données de santé' : 'Health Data Platform' }}</strong></div>
                <div><i data-lucide="chart-no-axes-column-increasing" aria-hidden="true"></i><strong>{{ $isFr ? 'Analytique et rapports' : 'Analytics & Reporting' }}</strong></div>
            </div>
            <p class="dt-scope-note">{{ $isFr ? 'Les systèmes, les données échangées, les responsabilités, les tests et les options de déploiement sont évalués et convenus avant chaque intégration.' : 'Systems, exchanged data, responsibilities, testing and deployment options are assessed and agreed before each integration.' }}</p>
        </div>
    </section>

    <section class="dt-related" aria-labelledby="dt-related-title">
        <div class="dt-section-inner">
            <h2 id="dt-related-title">{{ $isFr ? 'Produits associés' : 'Related products' }}</h2>
            <p>{{ $isFr ? 'Des solutions complémentaires pour un système de santé plus connecté.' : 'Complementary OPES solutions for a more connected health system.' }}</p>
            <div class="dt-related-grid">
                @foreach([
                    ['briefcase-medical', 'OPES EMR', $isFr ? 'Dossiers et flux cliniques pour les établissements.' : 'Clinical records and care workflows for facilities.', 'opes-emr'],
                    ['heart-pulse', $isFr ? 'Triage santé' : 'Health Triage', $isFr ? 'Accueil numérique et priorisation des patients.' : 'Digital intake and patient prioritisation.', 'opes-triage'],
                    ['search', $isFr ? 'Recherche de médicaments' : 'Medicine Finder', $isFr ? 'Recherche de médicaments et d’informations de disponibilité.' : 'Find medicines and availability information.', null],
                ] as [$icon, $name, $copy, $slug])
                <a href="{{ $slug ? url($locale.'/products/'.$slug) : url($locale.'/products') }}" class="dt-related-card"><i data-lucide="{{ $icon }}" aria-hidden="true"></i><span><strong>{{ $name }}</strong><small>{{ $copy }}</small></span><i class="dt-related-arrow" data-lucide="arrow-right" aria-hidden="true"></i></a>
                @endforeach
            </div>
        </div>
    </section>

    <section class="dt-final-cta">
        <div class="dt-final-copy"><h2>{{ $isFr ? 'Améliorez la visibilité des médicaments dans votre système de santé.' : 'Bring medicine visibility into your health system.' }}</h2><p>{{ $isFr ? 'Échangeons sur la vérification, le suivi et la coordination des médicaments dans vos structures, programmes et parcours de soins.' : 'Explore how your teams can verify, track and coordinate medicines across facilities, programmes and care settings.' }}</p></div>
        <a class="dt-button dt-button-light" href="{{ route('contact', ['locale' => $locale, 'product_slug' => 'opes-drugtrace']) }}">{{ $isFr ? 'Parler à OPES' : 'Talk to OPES' }} <i data-lucide="arrow-right" aria-hidden="true"></i></a>
    </section>

    <p class="dt-disclaimer">{{ $isFr ? 'La vérification dépend des données disponibles, des participants et des intégrations activées. DrugTrace n’est pas une certification réglementaire ni une garantie universelle d’authenticité. Toute configuration, portée, responsabilité et option tarifaire est confirmée dans le cadre du projet.' : 'Verification depends on available records, participating organisations and enabled integrations. DrugTrace is not a regulatory certification or a universal guarantee of authenticity. Configuration, scope, responsibilities and pricing are confirmed for each project.' }}</p>
</div>
</x-layouts.app>

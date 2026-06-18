@php $locale = app()->getLocale(); $isFr = $locale === 'fr'; @endphp

<x-layouts.app
    title="{{ $isFr ? 'Cadre d\'implémentation — OPES Health Systems' : 'Implementation Framework — OPES Health Systems' }}"
    description="{{ $isFr
        ? 'Le cadre d\'implémentation structuré d\'OPES : de la découverte au déploiement et à l\'optimisation continue.'
        : 'OPES structured implementation framework: from discovery through deployment to continuous optimisation.' }}">

{{-- HERO --}}
<div class="pricing-hero">
    <div class="section-label" style="justify-content:center;margin-bottom:16px">
        <i data-lucide="map" style="width:12px;height:12px"></i>
        {{ $isFr ? 'Cadre d\'implémentation' : 'Implementation Framework' }}
    </div>
    <h1>
        {{ $isFr ? 'De zéro au numérique en' : 'From zero to digital in' }}
        <span class="gradient-text">{{ $isFr ? '90 jours' : '90 days' }}</span>
    </h1>
    <p>
        {{ $isFr
            ? 'Notre cadre d\'implémentation éprouvé guide chaque établissement à travers chaque étape — de l\'évaluation initiale au déploiement opérationnel — avec un accompagnement dédié à chaque phase.'
            : 'Our proven implementation framework guides every facility through every step — from initial assessment to live operations — with dedicated support at each phase.' }}
    </p>
</div>

{{-- PHASES TIMELINE --}}
<div class="section" style="max-width:960px;margin:0 auto">
    @php
    $phases = $isFr ? [
        [
            'num'  => '01',
            'icon' => 'search',
            'color'=> '#1A6FE8',
            'title'=> 'Découverte',
            'duration' => '1–2 semaines',
            'desc' => 'Évaluation approfondie de l\'établissement : cartographie des flux de travail existants, inventaire des équipements, analyse des lacunes, et définition du périmètre fonctionnel. Livrable : rapport d\'évaluation et cahier des charges initial.',
            'deliverables' => ['Rapport d\'état des lieux','Cartographie des processus métier','Périmètre fonctionnel validé','Plan de migration des données'],
        ],
        [
            'num'  => '02',
            'icon' => 'pen-tool',
            'color'=> '#00C896',
            'title'=> 'Conception & Configuration',
            'duration' => '2–3 semaines',
            'desc' => 'Configuration de la solution selon les spécificités de l\'établissement : organigrammes, rôles et permissions, modèles d\'ordonnances, codes diagnostiques, et paramétrage des modules cliniques.',
            'deliverables' => ['Système configuré et paramétré','Modèles de documents créés','Référentiels médicaux chargés','Comptes utilisateurs créés'],
        ],
        [
            'num'  => '03',
            'icon' => 'server',
            'color'=> '#1A6FE8',
            'title'=> 'Infrastructure',
            'duration' => '1–2 semaines',
            'desc' => 'Déploiement de l\'infrastructure selon le modèle choisi (on-premise, cloud ou hybride) : installation des serveurs, configuration réseau, sécurisation, sauvegardes automatiques, et tests de performance.',
            'deliverables' => ['Environnement de production opérationnel','Sauvegardes automatisées configurées','Monitoring activé','Documentation technique'],
        ],
        [
            'num'  => '04',
            'icon' => 'database',
            'color'=> '#00C896',
            'title'=> 'Migration des données',
            'duration' => '1–3 semaines',
            'desc' => 'Migration structurée des données existantes vers OPES Health OS : nettoyage, standardisation, import des dossiers patients historiques, et validation par l\'équipe médicale.',
            'deliverables' => ['Données historiques migrées','Contrôle qualité des données','Registres de validation','Zéro perte de données certifiée'],
        ],
        [
            'num'  => '05',
            'icon' => 'graduation-cap',
            'color'=> '#1A6FE8',
            'title'=> 'Formation',
            'duration' => '1–2 semaines',
            'desc' => 'Formation structurée par rôle : médecins, infirmiers, techniciens de laboratoire, agents de caisse, administrateurs IT. Supports bilingues (FR/EN) fournis, certification des utilisateurs clés.',
            'deliverables' => ['Personnel clé certifié','Supports de formation remis','Champions utilisateurs identifiés','Procédures standard documentées'],
        ],
        [
            'num'  => '06',
            'icon' => 'play-circle',
            'color'=> '#00C896',
            'title'=> 'Pilote & Go-Live',
            'duration' => '1–2 semaines',
            'desc' => 'Déploiement progressif : pilote sur un service, puis extension à l\'ensemble de l\'établissement. Équipe de support sur site pendant le go-live pour résoudre les problèmes en temps réel.',
            'deliverables' => ['Pilote validé','Go-live réussi','Rapport de lancement','KPIs initiaux établis'],
        ],
        [
            'num'  => '07',
            'icon' => 'trending-up',
            'color'=> '#1A6FE8',
            'title'=> 'Optimisation continue',
            'duration' => 'Continu',
            'desc' => 'Suivi des indicateurs de performance, ajustements de configuration, mises à jour fonctionnelles, et revues trimestrielles avec votre responsable de compte OPES.',
            'deliverables' => ['Rapports de performance mensuels','Mises à jour fonctionnelles','Revues trimestrielles','Roadmap d\'évolution'],
        ],
    ] : [
        [
            'num'  => '01',
            'icon' => 'search',
            'color'=> '#1A6FE8',
            'title'=> 'Discovery',
            'duration' => '1–2 weeks',
            'desc' => 'In-depth facility assessment: mapping existing workflows, equipment inventory, gap analysis, and defining the functional scope. Deliverable: assessment report and initial requirements specification.',
            'deliverables' => ['Current-state assessment report','Business process maps','Validated functional scope','Data migration plan'],
        ],
        [
            'num'  => '02',
            'icon' => 'pen-tool',
            'color'=> '#00C896',
            'title'=> 'Design & Configuration',
            'duration' => '2–3 weeks',
            'desc' => 'Solution configuration tailored to the facility: org structures, roles and permissions, prescription templates, diagnostic codes, and clinical module settings.',
            'deliverables' => ['Configured and parameterised system','Document templates created','Medical reference data loaded','User accounts provisioned'],
        ],
        [
            'num'  => '03',
            'icon' => 'server',
            'color'=> '#1A6FE8',
            'title'=> 'Infrastructure',
            'duration' => '1–2 weeks',
            'desc' => 'Infrastructure deployment under the chosen model (on-premise, cloud, or hybrid): server installation, network configuration, hardening, automated backups, and performance testing.',
            'deliverables' => ['Production environment live','Automated backups configured','Monitoring activated','Technical documentation'],
        ],
        [
            'num'  => '04',
            'icon' => 'database',
            'color'=> '#00C896',
            'title'=> 'Data Migration',
            'duration' => '1–3 weeks',
            'desc' => 'Structured migration of existing data into OPES Health OS: cleansing, standardisation, import of historical patient records, and validation by the medical team.',
            'deliverables' => ['Historical data migrated','Data quality review','Validation registers','Zero data loss certified'],
        ],
        [
            'num'  => '05',
            'icon' => 'graduation-cap',
            'color'=> '#1A6FE8',
            'title'=> 'Training',
            'duration' => '1–2 weeks',
            'desc' => 'Role-based training: doctors, nurses, lab technicians, cashiers, IT administrators. Bilingual (FR/EN) materials provided, key user certification included.',
            'deliverables' => ['Key staff certified','Training materials delivered','Super-users identified','Standard procedures documented'],
        ],
        [
            'num'  => '06',
            'icon' => 'play-circle',
            'color'=> '#00C896',
            'title'=> 'Pilot & Go-Live',
            'duration' => '1–2 weeks',
            'desc' => 'Phased rollout: pilot on one department, then facility-wide go-live. On-site OPES support team during launch to resolve issues in real time.',
            'deliverables' => ['Pilot sign-off','Successful go-live','Launch report','Initial KPIs established'],
        ],
        [
            'num'  => '07',
            'icon' => 'trending-up',
            'color'=> '#1A6FE8',
            'title'=> 'Continuous Optimisation',
            'duration' => 'Ongoing',
            'desc' => 'Performance indicator tracking, configuration adjustments, feature updates, and quarterly reviews with your OPES account manager.',
            'deliverables' => ['Monthly performance reports','Feature updates','Quarterly reviews','Evolution roadmap'],
        ],
    ];
    @endphp

    <div style="display:flex;flex-direction:column;gap:0">
        @foreach($phases as $i => $phase)
        <div style="display:flex;gap:0;position:relative">
            {{-- Left: number + connector --}}
            <div style="display:flex;flex-direction:column;align-items:center;width:56px;flex-shrink:0">
                <div style="width:44px;height:44px;border-radius:50%;background:{{ $phase['color'] }}20;border:2px solid {{ $phase['color'] }};display:flex;align-items:center;justify-content:center;font-weight:800;font-size:13px;color:{{ $phase['color'] }};z-index:1">
                    {{ $phase['num'] }}
                </div>
                @if(!$loop->last)
                <div style="width:2px;flex:1;background:linear-gradient({{ $phase['color'] }},{{ $phases[$i+1]['color'] }});min-height:40px;margin:4px 0"></div>
                @endif
            </div>
            {{-- Right: content --}}
            <div style="flex:1;padding:0 0 36px 20px">
                <div style="display:flex;align-items:center;gap:10px;margin-bottom:4px">
                    <i data-lucide="{{ $phase['icon'] }}" style="width:16px;height:16px;color:{{ $phase['color'] }}"></i>
                    <span style="font-weight:800;color:#e2e8f0;font-size:16px">{{ $phase['title'] }}</span>
                    <span style="font-size:11px;color:#475569;background:#1e293b;padding:2px 8px;border-radius:20px;margin-left:4px">{{ $phase['duration'] }}</span>
                </div>
                <p style="color:#64748b;font-size:13px;line-height:1.65;margin:8px 0 12px;max-width:680px">{{ $phase['desc'] }}</p>
                <div style="display:flex;flex-wrap:wrap;gap:8px">
                    @foreach($phase['deliverables'] as $d)
                    <span style="font-size:11px;color:#94a3b8;background:#0F172A;border:1px solid #1e293b;border-radius:6px;padding:3px 10px">
                        <i data-lucide="check" style="width:10px;height:10px;color:#00C896;margin-right:4px"></i>{{ $d }}
                    </span>
                    @endforeach
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<div class="divider"></div>

{{-- WHAT SETS US APART --}}
<div class="section" style="max-width:960px;margin:0 auto;text-align:center">
    <div class="section-label" style="justify-content:center;margin-bottom:16px">
        <i data-lucide="star" style="width:12px;height:12px"></i>
        {{ $isFr ? 'Notre différence' : 'What sets us apart' }}
    </div>
    <h2 class="section-title">{{ $isFr ? 'Un accompagnement pensé pour l\'Afrique' : 'Implementation designed for Africa' }}</h2>
    <div class="pi-grid" style="max-width:900px;margin:32px auto 0">
        @php
        $diffs = $isFr ? [
            ['icon'=>'users','color'=>'#00C896','title'=>'Équipe locale','desc'=>'Ingénieurs et formateurs basés à Douala. Pas de délais de voyage ni de décalage horaire.'],
            ['icon'=>'language','color'=>'#1A6FE8','title'=>'Bilingue FR/EN','desc'=>'Tous les supports, formations et communications disponibles en français et en anglais.'],
            ['icon'=>'wifi-off','color'=>'#00C896','title'=>'Mode hors ligne','desc'=>'OPES fonctionne sans connexion Internet permanente — conçu pour les réalités africaines.'],
            ['icon'=>'smartphone','color'=>'#1A6FE8','title'=>'Mobile Money intégré','desc'=>'Paiements MTN MoMo et Orange Money natifs — aucun terminal bancaire requis.'],
        ] : [
            ['icon'=>'users','color'=>'#00C896','title'=>'Local team','desc'=>'Engineers and trainers based in Douala. No travel delays or time zone gaps.'],
            ['icon'=>'language','color'=>'#1A6FE8','title'=>'Bilingual FR/EN','desc'=>'All materials, training, and communications available in both French and English.'],
            ['icon'=>'wifi-off','color'=>'#00C896','title'=>'Offline-capable','desc'=>'OPES operates without a permanent internet connection — designed for African realities.'],
            ['icon'=>'smartphone','color'=>'#1A6FE8','title'=>'Mobile Money built-in','desc'=>'Native MTN MoMo and Orange Money payments — no banking terminal required.'],
        ];
        @endphp
        @foreach($diffs as $d)
        <div class="pi-card" style="flex-direction:column;align-items:flex-start">
            <div style="width:40px;height:40px;border-radius:10px;background:{{ $d['color'] }}15;display:flex;align-items:center;justify-content:center;margin-bottom:12px">
                <i data-lucide="{{ $d['icon'] }}" style="width:18px;height:18px;color:{{ $d['color'] }}"></i>
            </div>
            <div style="font-weight:700;color:#e2e8f0;font-size:14px;margin-bottom:6px">{{ $d['title'] }}</div>
            <div style="font-size:13px;color:#64748b;line-height:1.6">{{ $d['desc'] }}</div>
        </div>
        @endforeach
    </div>
</div>

{{-- CTA --}}
<div class="demo-section">
    <h2>{{ $isFr ? 'Prêt à démarrer votre déploiement ?' : 'Ready to start your deployment?' }}</h2>
    <p>{{ $isFr
        ? 'Contactez notre équipe pour une évaluation gratuite de votre établissement et un plan de déploiement personnalisé.'
        : 'Contact our team for a free facility assessment and a personalised deployment plan.' }}</p>
    <div style="display:flex;gap:14px;justify-content:center;flex-wrap:wrap;margin-top:12px">
        <a href="{{ url($locale.'/contact') }}" class="btn-primary">
            {{ $isFr ? 'Demander une évaluation' : 'Request an assessment' }} <i data-lucide="arrow-right" style="width:15px;height:15px"></i>
        </a>
        <a href="{{ url($locale.'/support') }}" class="btn-secondary">
            {{ $isFr ? 'Voir les niveaux de support' : 'View support tiers' }} <i data-lucide="headphones" style="width:15px;height:15px;color:#94a3b8"></i>
        </a>
    </div>
</div>

</x-layouts.app>

@php
$locale = app()->getLocale();
$isFr   = $locale === 'fr';

// Monthly prices in FCFA
$prices = [
    'clinic'       => ['monthly' => 35000,  'annual' => 28000],
    'professional' => ['monthly' => 95000,  'annual' => 76000],
    'enterprise'   => ['monthly' => null,   'annual' => null],
];
@endphp

<x-layouts.app
    title="{{ $isFr ? 'Tarifs — OPES Health Systems' : 'Pricing — OPES Health Systems' }}"
    description="{{ $isFr
        ? 'Tarifs transparents et modulaires pour numériser votre établissement de santé avec OPES HMS.'
        : 'Transparent, modular pricing to digitise your health facility with OPES HMS.' }}">

{{-- ── HERO ─────────────────────────────────────────────────────── --}}
<div class="pricing-hero">
    <div class="section-label" style="justify-content:center;margin-bottom:16px">
        <i data-lucide="tag" style="width:12px;height:12px"></i>
        {{ $isFr ? 'Tarification transparente' : 'Transparent Pricing' }}
    </div>
    <h1>
        {{ $isFr ? 'Payez seulement ce dont' : 'Pay only for what' }}
        <span class="gradient-text">{{ $isFr ? 'vous avez besoin' : 'you need' }}</span>
    </h1>
    <p>
        {{ $isFr
            ? 'Modules à la carte, facturation mensuelle ou annuelle, sans frais cachés. Conçu pour les établissements de santé du Cameroun et de la région CEMAC.'
            : 'Pick the modules your facility needs, billed monthly or annually, with no hidden fees. Built for health facilities across Cameroon and the CEMAC region.' }}
    </p>
</div>

{{-- ── BILLING TOGGLE ──────────────────────────────────────────── --}}
<div class="pricing-toggle-wrap" id="billing-wrap">
    <span class="pricing-toggle-label active" id="lbl-monthly">
        {{ $isFr ? 'Mensuel' : 'Monthly' }}
    </span>
    <button class="pricing-toggle" id="billing-toggle" aria-label="Toggle billing period" type="button"></button>
    <span class="pricing-toggle-label" id="lbl-annual">
        {{ $isFr ? 'Annuel' : 'Annual' }}
        <span class="pricing-toggle-save" style="margin-left:6px">{{ $isFr ? 'Économisez 20 %' : 'Save 20%' }}</span>
    </span>
</div>

{{-- ── PRICING CARDS ────────────────────────────────────────────── --}}
<div class="pricing-grid">

    {{-- Starter --}}
    <div class="pricing-card">
        <div class="pricing-tier">{{ $isFr ? 'DÉBUTANT' : 'STARTER' }}</div>
        <div class="pricing-name">Clinic</div>
        <div class="pricing-desc">
            {{ $isFr
                ? 'Parfait pour les cliniques, cabinets médicaux et centres de santé de petite taille.'
                : 'Perfect for small clinics, private practices, and community health centres.' }}
        </div>

        <div class="pricing-price">
            <div>
                <span class="pricing-currency">FCFA</span>
                <span class="pricing-amount" data-monthly="{{ number_format(35000,0,',',' ') }}" data-annual="{{ number_format(28000,0,',',' ') }}">
                    {{ number_format(35000,0,',',' ') }}
                </span>
            </div>
            <div class="pricing-period">
                <span class="period-monthly">{{ $isFr ? '/mois · par établissement' : '/month · per facility' }}</span>
                <span class="period-annual" style="display:none">{{ $isFr ? '/mois · facturé annuellement' : '/month · billed annually' }}</span>
            </div>
        </div>

        <div class="pricing-modules-label">{{ $isFr ? 'Modules inclus' : 'Modules included' }}</div>
        <div class="pricing-modules" style="margin-bottom:20px">
            @foreach(['OPES EMR','OPESCare','Opes Triage'] as $m)
            <span class="pricing-module-tag active">{{ $m }}</span>
            @endforeach
        </div>

        <a href="{{ route('contact', ['locale' => $locale]) }}" class="pricing-cta pricing-cta-secondary">
            <i data-lucide="send" style="width:13px;height:13px"></i>
            {{ $isFr ? 'Démarrer un essai' : 'Start a trial' }}
        </a>
        <div class="pricing-divider"></div>

        <ul class="pricing-features">
            <li><i data-lucide="check" class="fi pricing-check"></i> {{ $isFr ? 'Dossier patient électronique (EMR)' : 'Electronic medical records (EMR)' }}</li>
            <li><i data-lucide="check" class="fi pricing-check"></i> {{ $isFr ? 'Identifiant patient universel OPESCare' : 'OPESCare universal patient ID' }}</li>
            <li><i data-lucide="check" class="fi pricing-check"></i> {{ $isFr ? 'Module de triage & admissions' : 'Triage & admissions module' }}</li>
            <li><i data-lucide="check" class="fi pricing-check"></i> {{ $isFr ? 'Ordonnances & prescriptions' : 'Prescriptions & orders' }}</li>
            <li><i data-lucide="check" class="fi pricing-check"></i> {{ $isFr ? 'Interface bilingue EN/FR' : 'Bilingual EN/FR interface' }}</li>
            <li><i data-lucide="check" class="fi pricing-check"></i> {{ $isFr ? 'Support par e-mail (72 h)' : 'Email support (72 h SLA)' }}</li>
            <li><i data-lucide="x" class="fi pricing-x"></i> {{ $isFr ? 'Laboratoire & radiologie' : 'Laboratory & radiology' }}</li>
            <li><i data-lucide="x" class="fi pricing-x"></i> {{ $isFr ? 'Portail patient mobile' : 'Patient mobile portal' }}</li>
            <li><i data-lucide="x" class="fi pricing-x"></i> {{ $isFr ? 'API & intégrations tierces' : 'API & third-party integrations' }}</li>
        </ul>
    </div>

    {{-- Professional (featured) --}}
    <div class="pricing-card pricing-card-featured">
        <div class="pricing-recommended">{{ $isFr ? 'LE PLUS POPULAIRE' : 'MOST POPULAR' }}</div>
        <div class="pricing-tier">{{ $isFr ? 'PROFESSIONNEL' : 'PROFESSIONAL' }}</div>
        <div class="pricing-name">Facility</div>
        <div class="pricing-desc">
            {{ $isFr
                ? 'Conçu pour les hôpitaux de district, les laboratoires et les pharmacies.'
                : 'Built for district hospitals, stand-alone labs, and pharmacy chains.' }}
        </div>

        <div class="pricing-price">
            <div>
                <span class="pricing-currency">FCFA</span>
                <span class="pricing-amount" data-monthly="{{ number_format(95000,0,',',' ') }}" data-annual="{{ number_format(76000,0,',',' ') }}">
                    {{ number_format(95000,0,',',' ') }}
                </span>
            </div>
            <div class="pricing-period">
                <span class="period-monthly">{{ $isFr ? '/mois · par établissement' : '/month · per facility' }}</span>
                <span class="period-annual" style="display:none">{{ $isFr ? '/mois · facturé annuellement' : '/month · billed annually' }}</span>
            </div>
        </div>

        <div class="pricing-modules-label">{{ $isFr ? 'Modules inclus' : 'Modules included' }}</div>
        <div class="pricing-modules" style="margin-bottom:20px">
            @foreach(['OPES Hospital HIS','OPES EMR','OPESCare','OPES Lab','PHARMIS','RADIS'] as $m)
            <span class="pricing-module-tag active">{{ $m }}</span>
            @endforeach
        </div>

        <a href="{{ route('contact', ['locale' => $locale]) }}" class="pricing-cta pricing-cta-primary">
            <i data-lucide="send" style="width:13px;height:13px"></i>
            {{ $isFr ? 'Demander une démo' : 'Request a demo' }}
        </a>
        <div class="pricing-divider"></div>

        <ul class="pricing-features">
            <li><i data-lucide="check" class="fi pricing-check"></i> {{ $isFr ? 'Tout le niveau Clinic' : 'Everything in Clinic' }}</li>
            <li><i data-lucide="check" class="fi pricing-check"></i> {{ $isFr ? 'SIH hospitalier complet' : 'Full hospital information system' }}</li>
            <li><i data-lucide="check" class="fi pricing-check"></i> {{ $isFr ? 'Système d\'information de laboratoire' : 'Laboratory information system (LIS)' }}</li>
            <li><i data-lucide="check" class="fi pricing-check"></i> {{ $isFr ? 'Gestion de pharmacie (PHARMIS)' : 'Pharmacy management (PHARMIS)' }}</li>
            <li><i data-lucide="check" class="fi pricing-check"></i> {{ $isFr ? 'Imagerie & radiologie (RADIS)' : 'Imaging & radiology (RADIS)' }}</li>
            <li><i data-lucide="check" class="fi pricing-check"></i> {{ $isFr ? 'Tableau de bord analytique' : 'Analytics dashboard' }}</li>
            <li><i data-lucide="check" class="fi pricing-check"></i> {{ $isFr ? 'Support prioritaire (24 h)' : 'Priority support (24 h SLA)' }}</li>
            <li><i data-lucide="check" class="fi pricing-check"></i> {{ $isFr ? 'Formation des utilisateurs incluse' : 'User training included' }}</li>
            <li><i data-lucide="x" class="fi pricing-x"></i> {{ $isFr ? 'API ouverte & intégrations HL7 FHIR' : 'Open API & HL7 FHIR integrations' }}</li>
        </ul>
    </div>

    {{-- Enterprise --}}
    <div class="pricing-card">
        <div class="pricing-tier">{{ $isFr ? 'ENTREPRISE' : 'ENTERPRISE' }}</div>
        <div class="pricing-name">System</div>
        <div class="pricing-desc">
            {{ $isFr
                ? 'Pour les hôpitaux généraux, les HMO, les ministères et les réseaux multi-sites.'
                : 'For general hospitals, HMOs, ministries of health, and multi-site networks.' }}
        </div>

        <div class="pricing-price">
            <div class="pricing-amount-custom">{{ $isFr ? 'Sur devis' : 'Custom' }}</div>
            <div class="pricing-period">{{ $isFr ? 'Tarification selon le volume de modules' : 'Priced by module volume & seats' }}</div>
        </div>

        <div class="pricing-modules-label">{{ $isFr ? 'Modules disponibles' : 'Available modules' }}</div>
        <div class="pricing-modules" style="margin-bottom:20px">
            @foreach(['22 modules','RCMIS','UHC IS','OPES CDMS','HL7 FHIR','Multi-site'] as $m)
            <span class="pricing-module-tag active">{{ $m }}</span>
            @endforeach
        </div>

        <a href="{{ route('contact', ['locale' => $locale]) }}" class="pricing-cta pricing-cta-outline">
            <i data-lucide="phone" style="width:13px;height:13px"></i>
            {{ $isFr ? 'Contacter l\'équipe commerciale' : 'Talk to sales' }}
        </a>
        <div class="pricing-divider"></div>

        <ul class="pricing-features">
            <li><i data-lucide="check" class="fi pricing-check"></i> {{ $isFr ? 'Tout le niveau Facility' : 'Everything in Facility' }}</li>
            <li><i data-lucide="check" class="fi pricing-check"></i> {{ $isFr ? 'API ouverte HL7 FHIR R4' : 'Open API with HL7 FHIR R4' }}</li>
            <li><i data-lucide="check" class="fi pricing-check"></i> {{ $isFr ? 'Système de couverture de santé universelle' : 'Universal health coverage IS' }}</li>
            <li><i data-lucide="check" class="fi pricing-check"></i> {{ $isFr ? 'Gestion centralisée multi-sites' : 'Centralised multi-site management' }}</li>
            <li><i data-lucide="check" class="fi pricing-check"></i> {{ $isFr ? 'Hébergement sur site ou cloud privé' : 'On-premise or private cloud hosting' }}</li>
            <li><i data-lucide="check" class="fi pricing-check"></i> {{ $isFr ? 'Responsable de compte dédié' : 'Dedicated account manager' }}</li>
            <li><i data-lucide="check" class="fi pricing-check"></i> {{ $isFr ? 'SLA de disponibilité 99,9 %' : '99.9% uptime SLA' }}</li>
            <li><i data-lucide="check" class="fi pricing-check"></i> {{ $isFr ? 'Formation & déploiement sur site' : 'On-site training & deployment' }}</li>
            <li><i data-lucide="check" class="fi pricing-check"></i> {{ $isFr ? 'Support 24/7 avec astreinte téléphonique' : '24/7 support with phone escalation' }}</li>
        </ul>
    </div>

</div>

{{-- ── FEATURE COMPARISON ───────────────────────────────────────── --}}
<div class="pricing-compare">
    <h2>{{ $isFr ? 'Comparaison complète des fonctionnalités' : 'Full feature comparison' }}</h2>
    <table class="compare-table">
        <thead>
            <tr>
                <th style="width:40%">{{ $isFr ? 'Fonctionnalité' : 'Feature' }}</th>
                <th>Clinic</th>
                <th class="col-featured">Facility</th>
                <th>System</th>
            </tr>
        </thead>
        <tbody>
            <tr class="section-row"><td colspan="4">{{ $isFr ? 'Dossier patient' : 'Patient Record' }}</td></tr>
            <tr><td>{{ $isFr ? 'EMR complet' : 'Full EMR' }}</td><td><span class="compare-check">✓</span></td><td><span class="compare-check">✓</span></td><td><span class="compare-check">✓</span></td></tr>
            <tr><td>{{ $isFr ? 'OPESCare (ID universel)' : 'OPESCare (universal ID)' }}</td><td><span class="compare-check">✓</span></td><td><span class="compare-check">✓</span></td><td><span class="compare-check">✓</span></td></tr>
            <tr><td>{{ $isFr ? 'Triage & admissions' : 'Triage & admissions' }}</td><td><span class="compare-check">✓</span></td><td><span class="compare-check">✓</span></td><td><span class="compare-check">✓</span></td></tr>
            <tr><td>{{ $isFr ? 'Prescriptions' : 'Prescriptions' }}</td><td><span class="compare-check">✓</span></td><td><span class="compare-check">✓</span></td><td><span class="compare-check">✓</span></td></tr>
            <tr class="section-row"><td colspan="4">{{ $isFr ? 'Modules cliniques' : 'Clinical Modules' }}</td></tr>
            <tr><td>{{ $isFr ? 'SIH hospitalier' : 'Hospital HIS' }}</td><td><span class="compare-x">✕</span></td><td><span class="compare-check">✓</span></td><td><span class="compare-check">✓</span></td></tr>
            <tr><td>{{ $isFr ? 'Laboratoire (OPES Lab)' : 'Laboratory (OPES Lab)' }}</td><td><span class="compare-x">✕</span></td><td><span class="compare-check">✓</span></td><td><span class="compare-check">✓</span></td></tr>
            <tr><td>{{ $isFr ? 'Pharmacie (PHARMIS)' : 'Pharmacy (PHARMIS)' }}</td><td><span class="compare-x">✕</span></td><td><span class="compare-check">✓</span></td><td><span class="compare-check">✓</span></td></tr>
            <tr><td>{{ $isFr ? 'Radiologie (RADIS)' : 'Radiology (RADIS)' }}</td><td><span class="compare-x">✕</span></td><td><span class="compare-check">✓</span></td><td><span class="compare-check">✓</span></td></tr>
            <tr><td>{{ $isFr ? 'Maternité & néonatologie' : 'Maternity & neonatology' }}</td><td><span class="compare-x">✕</span></td><td><span class="compare-partial">Add-on</span></td><td><span class="compare-check">✓</span></td></tr>
            <tr class="section-row"><td colspan="4">{{ $isFr ? 'Système & intégrations' : 'System & Integrations' }}</td></tr>
            <tr><td>{{ $isFr ? 'Tableau de bord analytique' : 'Analytics dashboard' }}</td><td><span class="compare-partial">{{ $isFr ? 'Basique' : 'Basic' }}</span></td><td><span class="compare-check">✓</span></td><td><span class="compare-check">✓</span></td></tr>
            <tr><td>{{ $isFr ? 'API ouverte HL7 FHIR' : 'HL7 FHIR open API' }}</td><td><span class="compare-x">✕</span></td><td><span class="compare-x">✕</span></td><td><span class="compare-check">✓</span></td></tr>
            <tr><td>{{ $isFr ? 'Gestion multi-sites' : 'Multi-site management' }}</td><td><span class="compare-x">✕</span></td><td><span class="compare-x">✕</span></td><td><span class="compare-check">✓</span></td></tr>
            <tr><td>{{ $isFr ? 'Couverture santé universelle (UHC IS)' : 'Universal health coverage (UHC IS)' }}</td><td><span class="compare-x">✕</span></td><td><span class="compare-x">✕</span></td><td><span class="compare-check">✓</span></td></tr>
            <tr class="section-row"><td colspan="4">{{ $isFr ? 'Support & déploiement' : 'Support & Deployment' }}</td></tr>
            <tr><td>{{ $isFr ? 'Interface bilingue EN/FR' : 'Bilingual EN/FR interface' }}</td><td><span class="compare-check">✓</span></td><td><span class="compare-check">✓</span></td><td><span class="compare-check">✓</span></td></tr>
            <tr><td>{{ $isFr ? 'SLA de support' : 'Support SLA' }}</td><td>72 h</td><td>24 h</td><td>{{ $isFr ? '24/7' : '24/7' }}</td></tr>
            <tr><td>{{ $isFr ? 'Formation des utilisateurs' : 'User training' }}</td><td><span class="compare-partial">{{ $isFr ? 'En ligne' : 'Online' }}</span></td><td><span class="compare-check">✓</span></td><td><span class="compare-check">{{ $isFr ? 'Sur site' : 'On-site' }}</span></td></tr>
            <tr><td>{{ $isFr ? 'Hébergement cloud dédié' : 'Dedicated cloud hosting' }}</td><td><span class="compare-x">✕</span></td><td><span class="compare-partial">{{ $isFr ? 'Partagé' : 'Shared' }}</span></td><td><span class="compare-check">✓</span></td></tr>
        </tbody>
    </table>
</div>

{{-- ── LICENSING MODELS ──────────────────────────────────────────── --}}
<div class="section" style="max-width:960px;margin:0 auto">
    <div class="section-label" style="margin-bottom:16px">
        <i data-lucide="file-text" style="width:12px;height:12px"></i>
        {{ $isFr ? 'Modèles de licence' : 'Licensing models' }}
    </div>
    <h2 class="section-title" style="font-size:clamp(18px,2.5vw,24px)">
        {{ $isFr ? 'Choisissez votre modèle d\'acquisition' : 'Choose your acquisition model' }}
    </h2>
    <p style="color:#64748b;max-width:700px;font-size:14px;line-height:1.75;margin:12px 0 32px">
        {{ $isFr
            ? 'OPES propose trois modèles de licence adaptés à la situation financière de chaque établissement — de l\'abonnement mensuel flexible au modèle perpétuel pour les établissements publics.'
            : 'OPES offers three licensing models suited to every facility\'s financial situation — from flexible monthly subscription to perpetual licence for public institutions.' }}
    </p>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:20px">
        @php
        $licenseModels = $isFr ? [
            [
                'icon'    => 'repeat',
                'color'   => '#00C896',
                'title'   => 'Abonnement mensuel',
                'badge'   => 'Flexible',
                'desc'    => 'Payez mois par mois, sans engagement annuel. Idéal pour les cliniques qui démarrent ou souhaitent tester la plateforme avant un engagement plus long.',
                'points'  => ['Aucun engagement annuel','Activation et désactivation à tout moment','Facturation en début de mois (XAF, EUR, USD)','Mobile Money accepté'],
            ],
            [
                'icon'    => 'calendar',
                'color'   => '#1A6FE8',
                'title'   => 'Abonnement annuel',
                'badge'   => 'Économique · −20 %',
                'desc'    => 'Engagement de 12 mois en échange d\'une réduction de 20 % sur le tarif mensuel. Le modèle le plus choisi par les hôpitaux de district et les structures privées.',
                'points'  => ['20 % de réduction sur le tarif mensuel','Facture annuelle unique ou trimestrielle','Renouvellement automatique avec préavis 30 j','Formation de mise à niveau incluse'],
            ],
            [
                'icon'    => 'key',
                'color'   => '#00C896',
                'title'   => 'Licence perpétuelle',
                'badge'   => 'Établissements publics',
                'desc'    => 'Acquisition unique du droit d\'utilisation, assortie d\'une maintenance annuelle de 18 % du prix de licence. Recommandé pour les hôpitaux généraux, ministères et HMO.',
                'points'  => ['Paiement unique du droit de licence','Maintenance annuelle 18 % (mises à jour + support)','Hébergement on-premise ou cloud inclus au choix','Adapté aux marchés publics et budgets dotés'],
            ],
        ] : [
            [
                'icon'    => 'repeat',
                'color'   => '#00C896',
                'title'   => 'Monthly subscription',
                'badge'   => 'Flexible',
                'desc'    => 'Pay month by month with no annual commitment. Ideal for clinics starting out or testing the platform before a longer engagement.',
                'points'  => ['No annual commitment','Activate or deactivate at any time','Invoiced at start of month (XAF, EUR, USD)','Mobile Money accepted'],
            ],
            [
                'icon'    => 'calendar',
                'color'   => '#1A6FE8',
                'title'   => 'Annual subscription',
                'badge'   => 'Best value · −20%',
                'desc'    => '12-month commitment in exchange for a 20% discount on the monthly rate. The most-chosen model by district hospitals and private facilities.',
                'points'  => ['20% off the monthly rate','Single annual invoice or quarterly','Auto-renewal with 30-day notice','Upgrade training included'],
            ],
            [
                'icon'    => 'key',
                'color'   => '#00C896',
                'title'   => 'Perpetual licence',
                'badge'   => 'Public institutions',
                'desc'    => 'One-time acquisition of usage rights, paired with an 18% annual maintenance fee. Recommended for general hospitals, ministries, and HMOs.',
                'points'  => ['One-time licence fee','Annual maintenance 18% (updates + support)','On-premise or cloud hosting — your choice','Suited to public procurement and budgeted capex'],
            ],
        ];
        @endphp
        @foreach($licenseModels as $lm)
        <div class="pi-card" style="flex-direction:column;align-items:flex-start">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:14px">
                <div style="width:40px;height:40px;border-radius:10px;background:{{ $lm['color'] }}15;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    <i data-lucide="{{ $lm['icon'] }}" style="width:18px;height:18px;color:{{ $lm['color'] }}"></i>
                </div>
                <div>
                    <div style="font-weight:800;color:#e2e8f0;font-size:14px">{{ $lm['title'] }}</div>
                    <div style="font-size:10px;color:{{ $lm['color'] }};font-weight:700;text-transform:uppercase;letter-spacing:0.07em">{{ $lm['badge'] }}</div>
                </div>
            </div>
            <p style="font-size:12px;color:#64748b;line-height:1.6;margin-bottom:14px">{{ $lm['desc'] }}</p>
            <ul style="list-style:none;padding:0;margin:0;display:flex;flex-direction:column;gap:7px">
                @foreach($lm['points'] as $pt)
                <li style="display:flex;align-items:flex-start;gap:7px;font-size:12px;color:#94a3b8">
                    <i data-lucide="check" style="width:12px;height:12px;color:#00C896;flex-shrink:0;margin-top:1px"></i>{{ $pt }}
                </li>
                @endforeach
            </ul>
        </div>
        @endforeach
    </div>
</div>

<div class="divider"></div>

{{-- ── IMPLEMENTATION SERVICES ──────────────────────────────────── --}}
<div class="section" style="max-width:960px;margin:0 auto">
    <div class="section-label" style="margin-bottom:16px">
        <i data-lucide="map" style="width:12px;height:12px"></i>
        {{ $isFr ? 'Services d\'implémentation' : 'Implementation services' }}
    </div>
    <h2 class="section-title" style="font-size:clamp(18px,2.5vw,24px)">
        {{ $isFr ? 'Forfaits de déploiement' : 'Deployment packages' }}
    </h2>
    <p style="color:#64748b;max-width:700px;font-size:14px;line-height:1.75;margin:12px 0 32px">
        {{ $isFr
            ? 'L\'implémentation est facturée en une seule fois, séparément de l\'abonnement. Les forfaits ci-dessous couvrent l\'ensemble du cycle — de la découverte au go-live. Les abonnements Facility et System bénéficient de tarifs réduits.'
            : 'Implementation is billed once, separately from the subscription. The packages below cover the full cycle — from discovery to go-live. Facility and System subscriptions receive discounted rates.' }}
    </p>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:20px">
        @php
        $implPackages = $isFr ? [
            [
                'name'      => 'Essentials',
                'target'    => 'Cliniques & cabinets',
                'price'     => '250 000 – 500 000 FCFA',
                'duration'  => '2–3 semaines',
                'color'     => '#475569',
                'includes'  => ['Configuration du système','Migration de données (jusqu\'à 5 000 dossiers)','Formation en ligne (4 h, rôles clés)','Assistance au lancement à distance'],
            ],
            [
                'name'      => 'Professional',
                'target'    => 'Hôpitaux de district',
                'price'     => '1 500 000 – 3 000 000 FCFA',
                'duration'  => '4–6 semaines',
                'color'     => '#1A6FE8',
                'includes'  => ['Découverte & cartographie des processus','Configuration & paramétrage complet','Migration de données illimitée','Formation sur site (tous rôles, 3 jours)','Accompagnement au go-live (2 jours sur site)','30 jours de support post-lancement prioritaire'],
                'featured'  => true,
            ],
            [
                'name'      => 'Enterprise',
                'target'    => 'Hôpitaux généraux & réseaux',
                'price'     => 'Sur devis',
                'duration'  => '8–16 semaines',
                'color'     => '#00C896',
                'includes'  => ['Tout Professional','Architecture & infrastructure sur mesure','Intégrations HL7 FHIR avec systèmes existants','Migration & nettoyage de données complexe','Formation multi-sites (formateurs certifiés)','Ingénieur attitré pendant tout le déploiement','SLA go-live contractualisé'],
            ],
        ] : [
            [
                'name'      => 'Essentials',
                'target'    => 'Clinics & practices',
                'price'     => '250,000 – 500,000 FCFA',
                'duration'  => '2–3 weeks',
                'color'     => '#475569',
                'includes'  => ['System configuration','Data migration (up to 5,000 records)','Online training (4 h, key roles)','Remote launch support'],
            ],
            [
                'name'      => 'Professional',
                'target'    => 'District hospitals',
                'price'     => '1,500,000 – 3,000,000 FCFA',
                'duration'  => '4–6 weeks',
                'color'     => '#1A6FE8',
                'includes'  => ['Discovery & process mapping','Full configuration & parameterisation','Unlimited data migration','On-site training (all roles, 3 days)','Go-live support (2 days on-site)','30-day priority post-launch support'],
                'featured'  => true,
            ],
            [
                'name'      => 'Enterprise',
                'target'    => 'General hospitals & networks',
                'price'     => 'Custom quote',
                'duration'  => '8–16 weeks',
                'color'     => '#00C896',
                'includes'  => ['Everything in Professional','Custom architecture & infrastructure','HL7 FHIR integrations with existing systems','Complex data migration & cleansing','Multi-site training (certified trainers)','Dedicated engineer for full deployment','Contractual go-live SLA'],
            ],
        ];
        @endphp
        @foreach($implPackages as $pkg)
        <div style="background:#0F172A;border:1px solid {{ $pkg['color'] }}30;border-radius:14px;padding:24px 20px;display:flex;flex-direction:column;position:relative">
            @if(isset($pkg['featured']))
            <div style="position:absolute;top:-1px;left:50%;transform:translateX(-50%);background:#1A6FE8;color:#fff;font-size:9px;font-weight:800;letter-spacing:0.1em;padding:3px 12px;border-radius:0 0 8px 8px;text-transform:uppercase;white-space:nowrap">{{ $isFr ? 'LE PLUS CHOISI' : 'MOST CHOSEN' }}</div>
            @endif
            <div style="font-size:11px;font-weight:800;color:{{ $pkg['color'] }};text-transform:uppercase;letter-spacing:0.08em;margin-bottom:4px;margin-top:{{ isset($pkg['featured']) ? '10px' : '0' }}">{{ $pkg['name'] }}</div>
            <div style="font-weight:700;color:#e2e8f0;font-size:15px;margin-bottom:12px">{{ $pkg['target'] }}</div>
            <div style="margin-bottom:14px">
                <div style="font-size:clamp(14px,2vw,17px);font-weight:800;color:{{ $pkg['color'] }};line-height:1.3">{{ $pkg['price'] }}</div>
                <div style="font-size:11px;color:#475569;display:flex;align-items:center;gap:4px;margin-top:3px">
                    <i data-lucide="clock" style="width:10px;height:10px"></i>{{ $pkg['duration'] }}
                </div>
            </div>
            <a href="{{ route('contact', ['locale' => $locale]) }}" style="display:inline-flex;align-items:center;justify-content:center;gap:6px;background:{{ isset($pkg['featured']) ? '#1A6FE8' : 'transparent' }};color:{{ isset($pkg['featured']) ? '#fff' : $pkg['color'] }};border:1px solid {{ isset($pkg['featured']) ? '#1A6FE8' : $pkg['color'].'60' }};border-radius:8px;padding:9px 16px;font-size:12px;font-weight:700;text-decoration:none;margin-bottom:16px">
                <i data-lucide="send" style="width:12px;height:12px"></i>
                {{ $isFr ? 'Demander un devis' : 'Get a quote' }}
            </a>
            <div style="border-top:1px solid #1e293b;padding-top:14px">
                <ul style="list-style:none;padding:0;margin:0;display:flex;flex-direction:column;gap:7px">
                    @foreach($pkg['includes'] as $inc)
                    <li style="display:flex;align-items:flex-start;gap:7px;font-size:12px;color:#94a3b8">
                        <i data-lucide="check" style="width:12px;height:12px;color:{{ $pkg['color'] }};flex-shrink:0;margin-top:1px"></i>{{ $inc }}
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endforeach
    </div>
    <p style="color:#475569;font-size:12px;margin-top:16px;text-align:center">
        {{ $isFr
            ? '* Les abonnements annuels et System bénéficient d\'une réduction de 15 % sur le forfait d\'implémentation.'
            : '* Annual and System subscriptions receive a 15% discount on the implementation package.' }}
    </p>
</div>

<div class="divider"></div>

{{-- ── TRAINING PACKAGES ─────────────────────────────────────────── --}}
<div class="section" style="max-width:960px;margin:0 auto">
    <div class="section-label" style="margin-bottom:16px">
        <i data-lucide="graduation-cap" style="width:12px;height:12px"></i>
        {{ $isFr ? 'Formations complémentaires' : 'Additional training' }}
    </div>
    <h2 class="section-title" style="font-size:clamp(18px,2.5vw,24px)">
        {{ $isFr ? 'Programmes de formation à la carte' : 'À la carte training programmes' }}
    </h2>
    <p style="color:#64748b;max-width:700px;font-size:14px;line-height:1.75;margin:12px 0 32px">
        {{ $isFr
            ? 'En dehors de la formation incluse dans l\'implémentation, OPES Academy propose des sessions complémentaires pour l\'intégration de nouveaux employés, les mises à niveau fonctionnelles, et la certification avancée.'
            : 'Beyond the training included in implementation, OPES Academy offers additional sessions for new-staff onboarding, feature upgrades, and advanced certification.' }}
    </p>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:16px">
        @php
        $trainingPkgs = $isFr ? [
            ['icon'=>'monitor',        'color'=>'#1A6FE8','title'=>'E-learning OPES Academy',   'price'=>'Inclus dans l\'abonnement','desc'=>'Accès illimité aux parcours de certification en ligne pour tous les utilisateurs de votre établissement.'],
            ['icon'=>'users',          'color'=>'#00C896','title'=>'Formation sur site',         'price'=>'85 000 FCFA / jour formateur','desc'=>'Formateur certifié OPES déployé dans votre établissement. Jusqu\'à 15 participants par session.'],
            ['icon'=>'video',          'color'=>'#1A6FE8','title'=>'Formation à distance',       'price'=>'35 000 FCFA / session (2 h)','desc'=>'Sessions en visioconférence pour les équipes dispersées ou pour les rappels de fonctionnalités.'],
            ['icon'=>'refresh-cw',     'color'=>'#00C896','title'=>'Mise à niveau fonctionnelle','price'=>'20 000 FCFA / session','desc'=>'Formation ciblée sur les nouvelles fonctionnalités après une mise à jour majeure de la plateforme.'],
            ['icon'=>'award',          'color'=>'#1A6FE8','title'=>'Certification avancée',      'price'=>'45 000 FCFA / candidat','desc'=>'Passage de l\'examen de certification OPES avec attestation numérique et accès CPD.'],
            ['icon'=>'book-open',      'color'=>'#00C896','title'=>'Kit de démarrage administrateur','price'=>'Gratuit','desc'=>'Documentation complète, guides de configuration et vidéos de prise en main pour les administrateurs système.'],
        ] : [
            ['icon'=>'monitor',        'color'=>'#1A6FE8','title'=>'OPES Academy e-learning',   'price'=>'Included in subscription','desc'=>'Unlimited access to online certification tracks for all users at your facility.'],
            ['icon'=>'users',          'color'=>'#00C896','title'=>'On-site training',           'price'=>'85,000 FCFA / trainer-day','desc'=>'Certified OPES trainer deployed at your facility. Up to 15 participants per session.'],
            ['icon'=>'video',          'color'=>'#1A6FE8','title'=>'Remote training',            'price'=>'35,000 FCFA / session (2 h)','desc'=>'Video conference sessions for distributed teams or feature refreshers.'],
            ['icon'=>'refresh-cw',     'color'=>'#00C896','title'=>'Feature upgrade session',    'price'=>'20,000 FCFA / session','desc'=>'Targeted training on new features after a major platform update.'],
            ['icon'=>'award',          'color'=>'#1A6FE8','title'=>'Advanced certification',     'price'=>'45,000 FCFA / candidate','desc'=>'OPES certification exam with digital certificate and CPD credits.'],
            ['icon'=>'book-open',      'color'=>'#00C896','title'=>'Admin starter kit',          'price'=>'Free','desc'=>'Complete documentation, configuration guides, and getting-started videos for system administrators.'],
        ];
        @endphp
        @foreach($trainingPkgs as $tp)
        <div style="background:#0F172A;border:1px solid #1e293b;border-radius:12px;padding:18px 16px">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px">
                <div style="width:36px;height:36px;border-radius:9px;background:{{ $tp['color'] }}15;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    <i data-lucide="{{ $tp['icon'] }}" style="width:16px;height:16px;color:{{ $tp['color'] }}"></i>
                </div>
                <div style="font-weight:700;color:#e2e8f0;font-size:13px;line-height:1.3">{{ $tp['title'] }}</div>
            </div>
            <div style="font-size:12px;font-weight:700;color:{{ $tp['color'] }};margin-bottom:6px">{{ $tp['price'] }}</div>
            <div style="font-size:12px;color:#64748b;line-height:1.55">{{ $tp['desc'] }}</div>
        </div>
        @endforeach
    </div>
</div>

<div class="divider"></div>

{{-- ── MAINTENANCE & RENEWALS ───────────────────────────────────── --}}
<div class="section" style="max-width:960px;margin:0 auto">
    <div class="section-label" style="margin-bottom:16px">
        <i data-lucide="wrench" style="width:12px;height:12px"></i>
        {{ $isFr ? 'Maintenance annuelle' : 'Annual maintenance' }}
    </div>
    <h2 class="section-title" style="font-size:clamp(18px,2.5vw,24px)">
        {{ $isFr ? 'Maintenance & renouvellements' : 'Maintenance & renewals' }}
    </h2>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:32px;margin-top:28px;align-items:start">
        <div>
            <p style="color:#64748b;font-size:14px;line-height:1.75;margin-bottom:24px">
                {{ $isFr
                    ? 'Pour les clients sous licence perpétuelle, la maintenance annuelle couvre les mises à jour de sécurité, les nouvelles versions majeures, et le niveau de support Bronze. Elle peut être étendue à un niveau de support supérieur.'
                    : 'For perpetual licence customers, annual maintenance covers security updates, major new releases, and Bronze support tier. It can be extended to a higher support tier.' }}
            </p>
            <div style="background:#0F172A;border:1px solid #1e293b;border-radius:12px;padding:20px 18px">
                <div style="font-weight:700;color:#e2e8f0;font-size:13px;margin-bottom:16px">
                    {{ $isFr ? 'Grille de maintenance (licence perpétuelle)' : 'Maintenance schedule (perpetual licence)' }}
                </div>
                @foreach($isFr
                    ? [['Maintenance standard','18 % du prix de licence / an','Mises à jour + support Bronze'],['Maintenance étendue','22 % du prix de licence / an','Mises à jour + support Silver'],['Maintenance premium','28 % du prix de licence / an','Mises à jour + support Gold']]
                    : [['Standard maintenance','18% of licence price / year','Updates + Bronze support'],['Extended maintenance','22% of licence price / year','Updates + Silver support'],['Premium maintenance','28% of licence price / year','Updates + Gold support']]
                as $mrow)
                <div style="display:flex;justify-content:space-between;align-items:flex-start;padding:10px 0;border-bottom:1px solid #1e293b;gap:8px">
                    <div>
                        <div style="font-weight:600;color:#e2e8f0;font-size:12px">{{ $mrow[0] }}</div>
                        <div style="font-size:11px;color:#475569">{{ $mrow[2] }}</div>
                    </div>
                    <div style="color:#00C896;font-weight:700;font-size:12px;white-space:nowrap">{{ $mrow[1] }}</div>
                </div>
                @endforeach
            </div>
        </div>
        <div>
            <div style="background:#0f1a2e;border:1px solid rgba(0,200,150,0.2);border-radius:12px;padding:20px 18px;margin-bottom:16px">
                <div style="font-weight:700;color:#00C896;font-size:13px;margin-bottom:10px">
                    {{ $isFr ? 'Réductions multi-sites' : 'Multi-site discounts' }}
                </div>
                <p style="font-size:12px;color:#64748b;line-height:1.65;margin-bottom:14px">
                    {{ $isFr
                        ? 'Les réseaux de santé déployant OPES sur plusieurs sites bénéficient de déductions progressives sur la licence et la maintenance.'
                        : 'Health networks deploying OPES across multiple sites receive progressive discounts on licence and maintenance fees.' }}
                </p>
                @foreach($isFr
                    ? [['2–4 sites','−10 %'],['5–9 sites','−18 %'],['10–19 sites','−25 %'],['20+ sites','Sur devis']]
                    : [['2–4 sites','−10%'],['5–9 sites','−18%'],['10–19 sites','−25%'],['20+ sites','Custom']]
                as $disc)
                <div style="display:flex;justify-content:space-between;align-items:center;padding:7px 0;border-bottom:1px solid #1e293b">
                    <span style="font-size:12px;color:#94a3b8">{{ $disc[0] }}</span>
                    <span style="font-size:12px;font-weight:700;color:#00C896">{{ $disc[1] }}</span>
                </div>
                @endforeach
            </div>
            <div style="background:#0f1a2e;border:1px solid rgba(26,111,232,0.2);border-radius:12px;padding:20px 18px">
                <div style="font-weight:700;color:#1A6FE8;font-size:13px;margin-bottom:10px">
                    {{ $isFr ? 'Réductions secteur public' : 'Public sector discounts' }}
                </div>
                <p style="font-size:12px;color:#64748b;line-height:1.65;margin-bottom:14px">
                    {{ $isFr
                        ? 'Hôpitaux publics, centres de santé gouvernementaux, ministères et ONG bénéficient de tarifs préférentiels.'
                        : 'Public hospitals, government health centres, ministries, and NGOs qualify for preferential rates.' }}
                </p>
                @foreach($isFr
                    ? [['Hôpitaux publics','−30 % sur l\'abonnement'],['Ministères & DSP','Tarif institutionnel'],['ONG / bailleurs','Sur devis dédié']]
                    : [['Public hospitals','−30% on subscription'],['Ministries & PHD','Institutional rate'],['NGOs / donors','Dedicated quote']]
                as $pub)
                <div style="display:flex;justify-content:space-between;align-items:center;padding:7px 0;border-bottom:1px solid #1e293b">
                    <span style="font-size:12px;color:#94a3b8">{{ $pub[0] }}</span>
                    <span style="font-size:12px;font-weight:700;color:#1A6FE8">{{ $pub[1] }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<div class="divider"></div>

{{-- ── PAYMENT METHODS ──────────────────────────────────────────── --}}
<div class="section" style="max-width:960px;margin:0 auto;text-align:center">
    <div class="section-label" style="justify-content:center;margin-bottom:16px">
        <i data-lucide="credit-card" style="width:12px;height:12px"></i>
        {{ $isFr ? 'Modes de règlement' : 'Payment methods' }}
    </div>
    <h2 class="section-title" style="font-size:clamp(18px,2.5vw,22px)">
        {{ $isFr ? 'Payez comme vous le souhaitez' : 'Pay the way that works for you' }}
    </h2>
    <p style="color:#64748b;max-width:640px;margin:12px auto 32px;font-size:14px;line-height:1.75">
        {{ $isFr
            ? 'OPES accepte tous les modes de règlement courants en Afrique centrale — y compris Mobile Money — pour que les établissements de toute taille puissent accéder à la plateforme sans friction bancaire.'
            : 'OPES accepts all common payment methods in Central Africa — including Mobile Money — so facilities of every size can access the platform without banking friction.' }}
    </p>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:12px;max-width:800px;margin:0 auto">
        @foreach($isFr
            ? [['smartphone','#FFCC00','MTN Mobile Money','Paiements MoMo XAF'],['smartphone','#FF6600','Orange Money','Paiements Orange XAF'],['landmark','#1A6FE8','Virement bancaire','XAF · EUR · USD'],['credit-card','#00C896','Carte bancaire','Visa · Mastercard (annuel)'],['file-text','#475569','Bon de commande','Marchés publics & PME']]
            : [['smartphone','#FFCC00','MTN Mobile Money','MoMo XAF payments'],['smartphone','#FF6600','Orange Money','Orange XAF payments'],['landmark','#1A6FE8','Bank transfer','XAF · EUR · USD'],['credit-card','#00C896','Card payment','Visa · Mastercard (annual)'],['file-text','#475569','Purchase order','Public procurement & SMEs']]
        as $pm)
        <div style="background:#0F172A;border:1px solid #1e293b;border-radius:10px;padding:14px;text-align:center">
            <i data-lucide="{{ $pm[0] }}" style="width:22px;height:22px;color:{{ $pm[1] }};margin-bottom:8px"></i>
            <div style="font-weight:700;color:#e2e8f0;font-size:12px;margin-bottom:3px">{{ $pm[2] }}</div>
            <div style="font-size:11px;color:#64748b">{{ $pm[3] }}</div>
        </div>
        @endforeach
    </div>
</div>

{{-- ── CTA STRIP ────────────────────────────────────────────────── --}}
<div class="pricing-cta-strip">
    <h2>{{ $isFr ? 'Besoin d\'un devis personnalisé ?' : 'Need a custom quote?' }}</h2>
    <p>{{ $isFr
        ? 'Notre équipe commerciale vous accompagne dans le choix des modules adaptés à votre établissement et votre budget.'
        : 'Our sales team will help you select the right modules for your facility and budget.' }}</p>
    <div class="pricing-cta-strip-btns">
        <a href="{{ route('contact', ['locale' => $locale]) }}" class="btn-primary">
            <i data-lucide="send" style="width:14px;height:14px"></i>
            {{ $isFr ? 'Demander un devis' : 'Request a quote' }}
        </a>
        <a href="{{ route('products.index', ['locale' => $locale]) }}" class="btn-secondary">
            <i data-lucide="layout-grid" style="width:14px;height:14px;color:#00C896"></i>
            {{ $isFr ? 'Explorer les modules' : 'Explore modules' }}
        </a>
    </div>
</div>

{{-- ── FAQ ─────────────────────────────────────────────────────── --}}
<div class="pricing-faq">
    <h2>{{ $isFr ? 'Questions fréquentes' : 'Frequently asked questions' }}</h2>

    @php
    $faqs = $isFr ? [
        ['q' => 'Puis-je ajouter des modules individuellement ?',
         'a' => 'Oui. Chaque module OPES est disponible à la carte. Commencez avec le niveau Clinic et ajoutez Laboratoire, Pharmacie ou Radiologie selon vos besoins. Votre responsable de compte vous accompagnera dans la configuration.'],
        ['q' => 'Y a-t-il des frais de mise en place ?',
         'a' => 'Les niveaux Clinic et Facility n\'ont pas de frais d\'installation. Le niveau System Enterprise inclut un déploiement sur site et une formation dont le coût est défini au cas par cas selon la taille de l\'établissement.'],
        ['q' => 'Quels modes de paiement acceptez-vous ?',
         'a' => 'Nous acceptons les virements bancaires (XAF, EUR, USD), Mobile Money (MTN/Orange Cameroun) et les paiements par carte pour les abonnements annuels. Les abonnements mensuels sont facturés en début de période.'],
        ['q' => 'Mes données sont-elles hébergées au Cameroun ?',
         'a' => 'Oui. Par défaut, toutes les données de santé sont hébergées dans nos datacenters au Cameroun, en conformité avec la législation camerounaise sur la protection des données et la stratégie numérique du Ministère de la Santé 2026-2030.'],
        ['q' => 'Puis-je migrer depuis mon logiciel existant ?',
         'a' => 'OPES propose des outils d\'import et une assistance à la migration pour les données provenant de la plupart des formats standards (CSV, HL7, FHIR). L\'équipe technique vous accompagne tout au long du processus.'],
        ['q' => 'Existe-t-il un essai gratuit ?',
         'a' => 'Nous proposons une démonstration guidée de 30 minutes et un accès sandbox de 14 jours pour les niveaux Clinic et Facility. Contactez-nous pour planifier votre session.'],
    ] : [
        ['q' => 'Can I add individual modules?',
         'a' => 'Yes. Every OPES module is available à la carte. Start with the Clinic tier and add Laboratory, Pharmacy, or Radiology as you grow. Your account manager will help you configure the right set.'],
        ['q' => 'Are there any setup fees?',
         'a' => 'The Clinic and Facility tiers have no setup fees. The Enterprise System tier includes on-site deployment and training; those costs are scoped individually based on facility size and complexity.'],
        ['q' => 'What payment methods do you accept?',
         'a' => 'We accept bank transfers (XAF, EUR, USD), Mobile Money (MTN/Orange Cameroon), and card payments for annual subscriptions. Monthly subscriptions are invoiced at the start of each billing period.'],
        ['q' => 'Where is my data hosted?',
         'a' => 'By default all health data is hosted in our data centres in Cameroon, in compliance with Cameroonian data protection laws and the Ministry of Health Digital Health Strategy 2026–2030.'],
        ['q' => 'Can I migrate from my existing software?',
         'a' => 'OPES provides import tools and migration assistance for data in most standard formats (CSV, HL7, FHIR). Our technical team will guide you through the process at no additional cost on Facility and System tiers.'],
        ['q' => 'Is there a free trial?',
         'a' => 'We offer a guided 30-minute demo and a 14-day sandbox environment for the Clinic and Facility tiers. Contact us to schedule your session.'],
    ];
    @endphp

    @foreach($faqs as $faq)
    <details class="faq-item">
        <summary class="faq-q">{{ $faq['q'] }}</summary>
        <p class="faq-a">{{ $faq['a'] }}</p>
    </details>
    @endforeach
</div>

{{-- ── JS: billing toggle ───────────────────────────────────────── --}}
<script>
(function () {
    const btn     = document.getElementById('billing-toggle');
    const amounts = document.querySelectorAll('.pricing-amount[data-monthly]');
    const lblM    = document.getElementById('lbl-monthly');
    const lblA    = document.getElementById('lbl-annual');
    const perM    = document.querySelectorAll('.period-monthly');
    const perA    = document.querySelectorAll('.period-annual');
    let annual    = false;

    btn.addEventListener('click', function () {
        annual = !annual;
        btn.classList.toggle('annual', annual);
        lblM.classList.toggle('active', !annual);
        lblA.classList.toggle('active',  annual);
        amounts.forEach(function (el) {
            el.textContent = annual ? el.dataset.annual : el.dataset.monthly;
        });
        perM.forEach(function (el) { el.style.display = annual ? 'none' : ''; });
        perA.forEach(function (el) { el.style.display = annual ? '' : 'none'; });
    });
}());
</script>

</x-layouts.app>

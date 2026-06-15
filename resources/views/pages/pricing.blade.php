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

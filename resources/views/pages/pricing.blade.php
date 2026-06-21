@php $locale = app()->getLocale(); $isFr = $locale === 'fr'; @endphp

<x-layouts.app
    title="{{ $isFr ? 'Tarification & licences — OPES Health Systems' : 'Pricing & Licensing — OPES Health Systems' }}"
    description="{{ $isFr
        ? 'Tarification officielle OPES Health Systems : licences SaaS, perpétuelles et hybrides pour cliniques, hôpitaux, spécialités, interopérabilité et programmes nationaux.'
        : 'Official OPES Health Systems pricing: SaaS, perpetual, and hybrid licenses for clinics, hospitals, specialty systems, interoperability, and national programmes.' }}">

<style>
/* ── Pricing page — mobile responsive ─────────────────────────── */
@media (max-width: 768px) {
    .pricing-hero { padding: 48px 20px 32px !important; }
    .pricing-faq  { padding: 0 20px !important; }
    .pricing-cta-strip { padding: 48px 20px !important; }
    .pricing-cta-strip h2 { font-size: 1.4rem !important; }
    .section { padding: 44px 20px !important; }
    /* grid overrides */
    .pc-gi  { grid-template-columns: 1fr !important; }
    .pc-gi > div:first-child { border-right: none !important; border-bottom: 1px solid #1e293b; }
    .pc-g2  { grid-template-columns: 1fr !important; }
    .pc-g3  { grid-template-columns: 1fr !important; }
    .pc-g4  { grid-template-columns: 1fr 1fr !important; }
    .pc-g5  { grid-template-columns: 1fr 1fr !important; }
    .pc-host { grid-template-columns: 1fr !important; }
    .pc-host > div:last-child { min-width: 0 !important; }
    .prod-card { margin-left: 0 !important; margin-right: 0 !important; }
}
@media (max-width: 480px) {
    .pc-g4 { grid-template-columns: 1fr !important; }
    .pc-g5 { grid-template-columns: 1fr !important; }
}
</style>

{{-- ── HERO ─────────────────────────────────────────────────────── --}}
<div class="pricing-hero">
    <div class="section-label" style="justify-content:center;margin-bottom:16px">
        <i data-lucide="tag" style="width:12px;height:12px"></i>
        {{ $isFr ? 'Tarification & licences officielle' : 'Official pricing & licensing' }}
    </div>
    <h1>
        {{ $isFr ? 'Modèles de déploiement flexibles' : 'Flexible deployment models' }}
        <span class="gradient-text">{{ $isFr ? 'pour chaque organisation' : 'for every organisation' }}</span>
    </h1>
    <p>
        {{ $isFr
            ? 'Abonnement SaaS, licence perpétuelle ou hybride — OPES s\'adapte à vos exigences opérationnelles, réglementaires et de souveraineté des données.'
            : 'SaaS subscription, perpetual licence, or hybrid — OPES adapts to your operational, regulatory, and data sovereignty requirements.' }}
    </p>
</div>

{{-- ── DEPLOYMENT OPTIONS ────────────────────────────────────────── --}}
<div class="section" style="max-width:960px;margin:0 auto">
    <div class="section-label" style="margin-bottom:16px">
        <i data-lucide="server" style="width:12px;height:12px"></i>
        {{ $isFr ? 'Options de déploiement' : 'Deployment options' }}
    </div>
    <h2 class="section-title" style="font-size:clamp(18px,2.5vw,24px)">
        {{ $isFr ? 'Choisissez votre modèle d\'infrastructure' : 'Choose your infrastructure model' }}
    </h2>
    <div class="pc-g3" style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:20px;margin-top:28px">
        @php
        $deployments = $isFr ? [
            [
                'num'   => 'Option 1',
                'icon'  => 'server',
                'color' => '#00C896',
                'title' => 'On-Premise',
                'model' => 'Licence perpétuelle + Maintenance annuelle',
                'for'   => ['Hôpitaux','Réseaux hospitaliers','Institutions gouvernementales','Organismes d\'assurance'],
                'bens'  => ['Propriété complète de l\'infrastructure','Souveraineté des données','Contrôle local des dossiers','Fonctionnement hors ligne','Synchronisation cloud optionnelle'],
            ],
            [
                'num'   => 'Option 2',
                'icon'  => 'cloud',
                'color' => '#1A6FE8',
                'title' => 'Cloud OPES',
                'model' => 'Abonnement annuel',
                'for'   => ['Cliniques','Centres médicaux','Organisations en croissance'],
                'bens'  => ['Aucun serveur à acheter','Infrastructure gérée','Mises à jour automatiques','Sauvegardes gérées','Reprise après sinistre incluse'],
            ],
            [
                'num'   => 'Option 3',
                'icon'  => 'layers',
                'color' => '#00C896',
                'title' => 'Hybride',
                'model' => 'Licence perpétuelle + Services cloud',
                'for'   => ['Hôpitaux régionaux','Groupes hospitaliers','Programmes gouvernementaux'],
                'bens'  => ['Serveur on-premise local','Synchronisation cloud sécurisée','Souveraineté des données','Rapports centralisés','Continuité des activités'],
            ],
        ] : [
            [
                'num'   => 'Option 1',
                'icon'  => 'server',
                'color' => '#00C896',
                'title' => 'On-Premise',
                'model' => 'Perpetual licence + Annual maintenance',
                'for'   => ['Hospitals','Hospital networks','Government institutions','Insurance organisations'],
                'bens'  => ['Full infrastructure ownership','Data sovereignty','Local control of patient records','Offline operation capability','Optional cloud synchronisation'],
            ],
            [
                'num'   => 'Option 2',
                'icon'  => 'cloud',
                'color' => '#1A6FE8',
                'title' => 'Cloud by OPES',
                'model' => 'Annual subscription',
                'for'   => ['Clinics','Medical centres','Growing healthcare organisations'],
                'bens'  => ['No server purchase required','Managed infrastructure','Automatic updates','Managed backups','Disaster recovery included'],
            ],
            [
                'num'   => 'Option 3',
                'icon'  => 'layers',
                'color' => '#00C896',
                'title' => 'Hybrid',
                'model' => 'Perpetual licence + Cloud services',
                'for'   => ['Regional hospitals','Hospital groups','Government programmes'],
                'bens'  => ['Local on-premise server','Secure cloud synchronisation','Data sovereignty','Centralised reporting','Business continuity'],
            ],
        ];
        @endphp
        @foreach($deployments as $dep)
        <div style="background:#080E1A;border:1px solid #1e293b;border-radius:14px;padding:22px 18px;display:flex;flex-direction:column">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:14px">
                <div style="width:40px;height:40px;border-radius:10px;background:{{ $dep['color'] }}15;border:1px solid {{ $dep['color'] }}30;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    <i data-lucide="{{ $dep['icon'] }}" style="width:18px;height:18px;color:{{ $dep['color'] }}"></i>
                </div>
                <div>
                    <div style="font-size:10px;color:var(--text-faint);font-weight:700;text-transform:uppercase;letter-spacing:0.06em">{{ $dep['num'] }}</div>
                    <div style="font-weight:800;color:#e2e8f0;font-size:15px">{{ $dep['title'] }}</div>
                </div>
            </div>
            <div style="font-size:10px;font-weight:700;color:{{ $dep['color'] }};text-transform:uppercase;letter-spacing:0.06em;margin-bottom:8px">
                {{ $isFr ? 'Recommandé pour' : 'Recommended for' }}
            </div>
            <div style="display:flex;flex-wrap:wrap;gap:5px;margin-bottom:16px">
                @foreach($dep['for'] as $f)
                <span style="background:#0F172A;border:1px solid #1e293b;border-radius:12px;padding:3px 9px;font-size:10px;color:var(--text-muted)">{{ $f }}</span>
                @endforeach
            </div>
            <div style="font-size:10px;font-weight:700;color:var(--text-faint);text-transform:uppercase;letter-spacing:0.06em;margin-bottom:8px">
                {{ $isFr ? 'Avantages' : 'Benefits' }}
            </div>
            <ul style="list-style:none;padding:0;margin:0 0 16px;display:flex;flex-direction:column;gap:6px;flex:1">
                @foreach($dep['bens'] as $b)
                <li style="display:flex;align-items:flex-start;gap:7px;font-size:11px;color:var(--text-muted)">
                    <i data-lucide="check" style="width:11px;height:11px;color:{{ $dep['color'] }};flex-shrink:0;margin-top:1px"></i>{{ $b }}
                </li>
                @endforeach
            </ul>
            <div style="border-top:1px solid #1e293b;padding-top:12px">
                <div style="font-size:10px;font-weight:700;color:var(--text-faint);text-transform:uppercase;letter-spacing:0.06em;margin-bottom:3px">{{ $isFr ? 'Modèle de licence' : 'License model' }}</div>
                <div style="font-size:11px;font-weight:600;color:{{ $dep['color'] }}">{{ $dep['model'] }}</div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<div class="divider"></div>

{{-- ── PRODUCT CATALOG HEADER ───────────────────────────────────── --}}
<div class="section" style="max-width:960px;margin:0 auto;text-align:center">
    <div class="section-label" style="justify-content:center;margin-bottom:12px">
        <i data-lucide="layout-grid" style="width:12px;height:12px"></i>
        {{ $isFr ? 'Catalogue de licences' : 'Software licenses' }}
    </div>
    <h2 class="section-title" style="font-size:clamp(18px,2.5vw,26px)">
        {{ $isFr ? 'Tarification par produit' : 'Pricing by product' }}
    </h2>
    <p style="color:var(--text-muted);font-size:14px;line-height:1.75;max-width:660px;margin:12px auto 0">
        {{ $isFr
            ? 'Tous les prix sont des prix de départ HT. Maintenance annuelle : 15 %–20 % du prix de licence pour les licences perpétuelles.'
            : 'All prices are starting prices excluding tax. Annual maintenance: 15%–20% of licence price for perpetual licences.' }}
    </p>
</div>

@php
/* Reusable styles */
$prodCard  = 'background:#080E1A;border:1px solid #1e293b;border-radius:16px;overflow:hidden;margin-bottom:24px;max-width:960px;margin-left:auto;margin-right:auto';
$inclCol   = 'padding:20px 24px;border-right:1px solid #1e293b';
$priceCol  = 'padding:20px 24px';
$priceTile = 'background:#0F172A;border:1px solid #1e293b;border-radius:10px;padding:16px;display:flex;flex-direction:column;align-items:center;text-align:center';
$rfqTile   = 'background:#0F172A;border:1px dashed #1e293b;border-radius:10px;padding:16px;display:flex;flex-direction:column;align-items:center;text-align:center';
@endphp

{{-- ── OPES CLINIC ─────────────────────────────────────────────── --}}
<div class="prod-card" style="{{ $prodCard }}">
    <div style="padding:18px 24px;border-bottom:1px solid #1e293b;display:flex;align-items:center;gap:14px;background:#00C89606">
        <div style="width:38px;height:38px;border-radius:9px;background:#00C89615;border:1px solid #00C89630;display:flex;align-items:center;justify-content:center;flex-shrink:0">
            <i data-lucide="stethoscope" style="width:18px;height:18px;color:#00C896"></i>
        </div>
        <div>
            <div style="font-size:10px;font-weight:700;color:#00C896;text-transform:uppercase;letter-spacing:0.07em">{{ $isFr ? 'Cliniques, cabinets médicaux & centres ambulatoires' : 'Clinics, medical practices & outpatient centres' }}</div>
            <div style="font-size:17px;font-weight:800;color:#e2e8f0">OPES Clinic</div>
        </div>
    </div>
    <div class="pc-gi" style="display:grid;grid-template-columns:240px 1fr">
        <div style="{{ $inclCol }}">
            <div style="font-size:10px;font-weight:700;color:var(--text-faint);text-transform:uppercase;letter-spacing:0.06em;margin-bottom:10px">{{ $isFr ? 'Inclus' : 'Includes' }}</div>
            @foreach($isFr
                ? ['Dossier médical électronique','Rendez-vous','Facturation','Ordonnances','Documentation clinique','Gestion du stock','Rapports']
                : ['Electronic Medical Records','Appointments','Billing','Prescriptions','Clinical Documentation','Inventory','Reporting']
            as $inc)
            <div style="display:flex;align-items:center;gap:7px;font-size:11px;color:var(--text-muted);margin-bottom:6px">
                <i data-lucide="check" style="width:11px;height:11px;color:#00C896;flex-shrink:0"></i>{{ $inc }}
            </div>
            @endforeach
        </div>
        <div style="{{ $priceCol }}">
            <div class="pc-g2" style="display:grid;grid-template-columns:1fr 1fr;gap:14px">
                <div style="{{ $priceTile }}">
                    <div style="font-size:10px;font-weight:700;color:#1A6FE8;text-transform:uppercase;letter-spacing:0.07em;margin-bottom:10px">SaaS</div>
                    <div style="font-size:11px;color:var(--text-faint);margin-bottom:4px">{{ $isFr ? 'À partir de' : 'Starting from' }}</div>
                    <div style="font-size:22px;font-weight:800;color:#e2e8f0;line-height:1.1">150 000</div>
                    <div style="font-size:12px;color:#00C896;font-weight:700;margin-bottom:12px">FCFA / {{ $isFr ? 'mois' : 'month' }}</div>
                    <a href="{{ route('contact', ['locale' => $locale]) }}" style="display:inline-flex;align-items:center;gap:5px;background:#1A6FE820;border:1px solid #1A6FE840;border-radius:7px;padding:7px 14px;font-size:11px;font-weight:700;color:#1A6FE8;text-decoration:none">
                        <i data-lucide="send" style="width:11px;height:11px"></i>{{ $isFr ? 'Démarrer' : 'Get started' }}
                    </a>
                </div>
                <div style="{{ $priceTile }}">
                    <div style="font-size:10px;font-weight:700;color:#00C896;text-transform:uppercase;letter-spacing:0.07em;margin-bottom:10px">{{ $isFr ? 'Licence perpétuelle' : 'Perpetual licence' }}</div>
                    <div style="font-size:11px;color:var(--text-faint);margin-bottom:4px">{{ $isFr ? 'À partir de' : 'Starting from' }}</div>
                    <div style="font-size:22px;font-weight:800;color:#e2e8f0;line-height:1.1">2 500 000</div>
                    <div style="font-size:12px;color:#00C896;font-weight:700;margin-bottom:3px">FCFA</div>
                    <div style="font-size:10px;color:var(--text-faint);margin-bottom:12px">{{ $isFr ? '+ maintenance 15–20 %/an' : '+ 15–20% annual maintenance' }}</div>
                    <a href="{{ route('contact', ['locale' => $locale]) }}" style="display:inline-flex;align-items:center;gap:5px;background:#00C89615;border:1px solid #00C89630;border-radius:7px;padding:7px 14px;font-size:11px;font-weight:700;color:#00C896;text-decoration:none">
                        <i data-lucide="file-text" style="width:11px;height:11px"></i>{{ $isFr ? 'Demander un devis' : 'Request a quote' }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── OPES HOSPITAL ────────────────────────────────────────────── --}}
<div class="prod-card" style="{{ $prodCard }}">
    <div style="padding:18px 24px;border-bottom:1px solid #1e293b;display:flex;align-items:center;gap:14px;background:#1A6FE806">
        <div style="width:38px;height:38px;border-radius:9px;background:#1A6FE815;border:1px solid #1A6FE830;display:flex;align-items:center;justify-content:center;flex-shrink:0">
            <i data-lucide="hospital" style="width:18px;height:18px;color:#1A6FE8"></i>
        </div>
        <div>
            <div style="font-size:10px;font-weight:700;color:#1A6FE8;text-transform:uppercase;letter-spacing:0.07em">{{ $isFr ? 'Hôpitaux de district, régionaux & de référence' : 'District, regional & referral hospitals' }}</div>
            <div style="font-size:17px;font-weight:800;color:#e2e8f0">OPES Hospital</div>
        </div>
    </div>
    <div class="pc-gi" style="display:grid;grid-template-columns:240px 1fr">
        <div style="{{ $inclCol }}">
            <div style="font-size:10px;font-weight:700;color:var(--text-faint);text-transform:uppercase;letter-spacing:0.06em;margin-bottom:10px">{{ $isFr ? 'Inclus' : 'Includes' }}</div>
            @foreach($isFr
                ? ['Système d\'information hospitalier','DME','Admissions','Facturation','Stock','Achats','Documentation clinique','Rapports']
                : ['Hospital Information System','EMR','Admissions','Billing','Inventory','Procurement','Clinical Documentation','Reporting']
            as $inc)
            <div style="display:flex;align-items:center;gap:7px;font-size:11px;color:var(--text-muted);margin-bottom:6px">
                <i data-lucide="check" style="width:11px;height:11px;color:#1A6FE8;flex-shrink:0"></i>{{ $inc }}
            </div>
            @endforeach
        </div>
        <div style="{{ $priceCol }}">
            <div style="font-size:10px;font-weight:700;color:var(--text-faint);text-transform:uppercase;letter-spacing:0.06em;margin-bottom:12px">{{ $isFr ? 'Licence perpétuelle — par taille d\'hôpital' : 'Perpetual licence — by hospital size' }}</div>
            <div class="pc-g2" style="display:grid;grid-template-columns:1fr 1fr;gap:14px">
                <div style="{{ $priceTile }}">
                    <div style="font-size:10px;font-weight:700;color:#1A6FE8;text-transform:uppercase;letter-spacing:0.07em;margin-bottom:10px">{{ $isFr ? 'Petit hôpital' : 'Small hospital' }}</div>
                    <div style="font-size:11px;color:var(--text-faint);margin-bottom:4px">{{ $isFr ? 'À partir de' : 'Starting from' }}</div>
                    <div style="font-size:22px;font-weight:800;color:#e2e8f0;line-height:1.1">15 000 000</div>
                    <div style="font-size:12px;color:#1A6FE8;font-weight:700;margin-bottom:3px">FCFA</div>
                    <div style="font-size:10px;color:var(--text-faint);margin-bottom:12px">{{ $isFr ? '+ maintenance 15–20 %/an' : '+ 15–20% annual maintenance' }}</div>
                    <a href="{{ route('contact', ['locale' => $locale]) }}" style="display:inline-flex;align-items:center;gap:5px;background:#1A6FE815;border:1px solid #1A6FE830;border-radius:7px;padding:7px 14px;font-size:11px;font-weight:700;color:#1A6FE8;text-decoration:none">
                        <i data-lucide="send" style="width:11px;height:11px"></i>{{ $isFr ? 'Contacter' : 'Contact us' }}
                    </a>
                </div>
                <div style="{{ $priceTile }}">
                    <div style="font-size:10px;font-weight:700;color:#1A6FE8;text-transform:uppercase;letter-spacing:0.07em;margin-bottom:10px">{{ $isFr ? 'Hôpital moyen & grand' : 'Medium & large hospital' }}</div>
                    <div style="font-size:11px;color:var(--text-faint);margin-bottom:4px">{{ $isFr ? 'À partir de' : 'Starting from' }}</div>
                    <div style="font-size:20px;font-weight:800;color:#e2e8f0;line-height:1.1">25 000 000 – 50 000 000</div>
                    <div style="font-size:12px;color:#1A6FE8;font-weight:700;margin-bottom:3px">FCFA</div>
                    <div style="font-size:10px;color:var(--text-faint);margin-bottom:12px">{{ $isFr ? '+ maintenance 15–20 %/an' : '+ 15–20% annual maintenance' }}</div>
                    <a href="{{ route('contact', ['locale' => $locale]) }}" style="display:inline-flex;align-items:center;gap:5px;background:#1A6FE815;border:1px solid #1A6FE830;border-radius:7px;padding:7px 14px;font-size:11px;font-weight:700;color:#1A6FE8;text-decoration:none">
                        <i data-lucide="send" style="width:11px;height:11px"></i>{{ $isFr ? 'Demander un devis' : 'Request a quote' }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── SPECIALTY SUITE ──────────────────────────────────────────── --}}
<div class="section" style="max-width:960px;margin:0 auto">
    <div style="display:flex;align-items:center;gap:14px;margin-bottom:16px">
        <div style="width:38px;height:38px;border-radius:9px;background:#00C89615;border:1px solid #00C89630;display:flex;align-items:center;justify-content:center;flex-shrink:0">
            <i data-lucide="grid-2x2" style="width:18px;height:18px;color:#00C896"></i>
        </div>
        <div>
            <div style="font-size:10px;font-weight:700;color:#00C896;text-transform:uppercase;letter-spacing:0.07em">{{ $isFr ? 'Systèmes d\'information départementaux' : 'Departmental information systems' }}</div>
            <div style="font-size:17px;font-weight:800;color:#e2e8f0">{{ $isFr ? 'Suite spécialisée OPES' : 'OPES Specialty Suite' }}</div>
        </div>
    </div>
    <div class="pc-g4" style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px">
        @php
        $specialties = $isFr ? [
            ['microscope','#00C896','OPES Lab IS','3 000 000'],
            ['pill','#1A6FE8','OPES Pharmacie IS','2 500 000'],
            ['scan','#00C896','OPES Radiologie IS','4 000 000'],
            ['heart-pulse','#1A6FE8','OPES Cardiologie IS','4 000 000'],
            ['shield','#00C896','OPES Dentaire IS','2 500 000'],
            ['sun','#1A6FE8','OPES Dermatologie IS','2 500 000'],
            ['baby','#00C896','OPES Pédiatrie IS','3 000 000'],
            ['circle','#1A6FE8','OPES Gynécologie-Obstétrique IS','3 500 000'],
        ] : [
            ['microscope','#00C896','OPES Lab IS','3 000 000'],
            ['pill','#1A6FE8','OPES Pharmacy IS','2 500 000'],
            ['scan','#00C896','OPES Radiology IS','4 000 000'],
            ['heart-pulse','#1A6FE8','OPES Cardiology IS','4 000 000'],
            ['shield','#00C896','OPES Dental IS','2 500 000'],
            ['sun','#1A6FE8','OPES Dermatology IS','2 500 000'],
            ['baby','#00C896','OPES Pediatrics IS','3 000 000'],
            ['circle','#1A6FE8','OPES Obstetrics & Gynecology IS','3 500 000'],
        ];
        @endphp
        @foreach($specialties as $sp)
        <div style="background:#080E1A;border:1px solid #1e293b;border-radius:12px;padding:16px;display:flex;flex-direction:column;align-items:center;text-align:center">
            <i data-lucide="{{ $sp[0] }}" style="width:20px;height:20px;color:{{ $sp[1] }};margin-bottom:8px"></i>
            <div style="font-size:11px;font-weight:700;color:#e2e8f0;margin-bottom:6px;line-height:1.4">{{ $sp[2] }}</div>
            <div style="font-size:10px;color:var(--text-faint);margin-bottom:2px">{{ $isFr ? 'À partir de' : 'From' }}</div>
            <div style="font-size:14px;font-weight:800;color:{{ $sp[1] }}">{{ $sp[3] }}</div>
            <div style="font-size:10px;color:var(--text-faint);margin-bottom:12px">FCFA</div>
            <a href="{{ route('contact', ['locale' => $locale]) }}" style="font-size:10px;font-weight:700;color:{{ $sp[1] }};text-decoration:none;border:1px solid {{ $sp[1] }}30;border-radius:6px;padding:5px 10px">{{ $isFr ? 'Devis' : 'Quote' }}</a>
        </div>
        @endforeach
    </div>
    <p style="font-size:11px;color:var(--text-faint);margin-top:12px">{{ $isFr ? '* Maintenance annuelle : 15–20 % du prix de licence pour tous les systèmes de spécialité.' : '* Annual maintenance: 15–20% of licence price for all specialty systems.' }}</p>
</div>

<div class="divider"></div>

{{-- ── OPES CARE ────────────────────────────────────────────────── --}}
<div class="prod-card" style="{{ $prodCard }}">
    <div style="padding:18px 24px;border-bottom:1px solid #1e293b;display:flex;align-items:center;gap:14px;background:#1A6FE806">
        <div style="width:38px;height:38px;border-radius:9px;background:#1A6FE815;border:1px solid #1A6FE830;display:flex;align-items:center;justify-content:center;flex-shrink:0">
            <i data-lucide="share-2" style="width:18px;height:18px;color:#1A6FE8"></i>
        </div>
        <div>
            <div style="font-size:10px;font-weight:700;color:#1A6FE8;text-transform:uppercase;letter-spacing:0.07em">{{ $isFr ? 'Plateforme d\'interopérabilité de santé' : 'Healthcare interoperability platform' }}</div>
            <div style="font-size:17px;font-weight:800;color:#e2e8f0">OPES Care</div>
        </div>
    </div>
    <div class="pc-gi" style="display:grid;grid-template-columns:240px 1fr">
        <div style="{{ $inclCol }}">
            <div style="font-size:10px;font-weight:700;color:var(--text-faint);text-transform:uppercase;letter-spacing:0.06em;margin-bottom:10px">{{ $isFr ? 'Inclus' : 'Includes' }}</div>
            @foreach($isFr
                ? ['Health ID','Index Patient Maître','Échange d\'informations de santé','Échange de référencements','Registre des prestataires','Registre des établissements','Portail patient']
                : ['Health ID','Master Patient Index','Health Information Exchange','Referral Exchange','Provider Registry','Facility Registry','Patient Portal']
            as $inc)
            <div style="display:flex;align-items:center;gap:7px;font-size:11px;color:var(--text-muted);margin-bottom:6px">
                <i data-lucide="check" style="width:11px;height:11px;color:#1A6FE8;flex-shrink:0"></i>{{ $inc }}
            </div>
            @endforeach
        </div>
        <div style="{{ $priceCol }}">
            <div class="pc-g3" style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px">
                <div style="{{ $priceTile }}">
                    <div style="font-size:10px;font-weight:700;color:#1A6FE8;text-transform:uppercase;letter-spacing:0.07em;margin-bottom:10px">{{ $isFr ? 'Hôpital unique' : 'Single hospital' }}</div>
                    <div style="font-size:11px;color:var(--text-faint);margin-bottom:4px">{{ $isFr ? 'À partir de' : 'Starting from' }}</div>
                    <div style="font-size:20px;font-weight:800;color:#e2e8f0;line-height:1.1">25 000 000</div>
                    <div style="font-size:11px;color:#1A6FE8;font-weight:700;margin-bottom:12px">FCFA</div>
                    <a href="{{ route('contact', ['locale' => $locale]) }}" style="font-size:10px;font-weight:700;color:#1A6FE8;text-decoration:none;border:1px solid #1A6FE830;border-radius:6px;padding:6px 12px">{{ $isFr ? 'Contacter' : 'Contact' }}</a>
                </div>
                <div style="{{ $priceTile }}">
                    <div style="font-size:10px;font-weight:700;color:#00C896;text-transform:uppercase;letter-spacing:0.07em;margin-bottom:10px">{{ $isFr ? 'Réseau multi-hôpitaux' : 'Multi-hospital network' }}</div>
                    <div style="font-size:11px;color:var(--text-faint);margin-bottom:4px">{{ $isFr ? 'À partir de' : 'Starting from' }}</div>
                    <div style="font-size:20px;font-weight:800;color:#e2e8f0;line-height:1.1">50 000 000</div>
                    <div style="font-size:11px;color:#00C896;font-weight:700;margin-bottom:12px">FCFA</div>
                    <a href="{{ route('contact', ['locale' => $locale]) }}" style="font-size:10px;font-weight:700;color:#00C896;text-decoration:none;border:1px solid #00C89630;border-radius:6px;padding:6px 12px">{{ $isFr ? 'Contacter' : 'Contact' }}</a>
                </div>
                <div style="{{ $rfqTile }}">
                    <div style="font-size:10px;font-weight:700;color:#F59E0B;text-transform:uppercase;letter-spacing:0.07em;margin-bottom:10px">{{ $isFr ? 'Programme national / régional' : 'National / regional programme' }}</div>
                    <div style="font-size:28px;margin-bottom:4px">—</div>
                    <div style="font-size:11px;font-weight:700;color:#F59E0B;margin-bottom:12px">{{ $isFr ? 'Sur demande de devis' : 'Request quotation' }}</div>
                    <a href="{{ route('contact', ['locale' => $locale]) }}" style="font-size:10px;font-weight:700;color:#F59E0B;text-decoration:none;border:1px solid #F59E0B30;border-radius:6px;padding:6px 12px">{{ $isFr ? 'Demander' : 'Enquire' }}</a>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── OPES HEALTH ID ───────────────────────────────────────────── --}}
<div class="prod-card" style="{{ $prodCard }}">
    <div style="padding:18px 24px;border-bottom:1px solid #1e293b;display:flex;align-items:center;gap:14px;background:#00C89606">
        <div style="width:38px;height:38px;border-radius:9px;background:#00C89615;border:1px solid #00C89630;display:flex;align-items:center;justify-content:center;flex-shrink:0">
            <i data-lucide="fingerprint" style="width:18px;height:18px;color:#00C896"></i>
        </div>
        <div>
            <div style="font-size:10px;font-weight:700;color:#00C896;text-transform:uppercase;letter-spacing:0.07em">{{ $isFr ? 'Infrastructure d\'identité unique patient' : 'Unique patient identity infrastructure' }}</div>
            <div style="font-size:17px;font-weight:800;color:#e2e8f0">OPES Health ID</div>
        </div>
    </div>
    <div class="pc-gi" style="display:grid;grid-template-columns:240px 1fr">
        <div style="{{ $inclCol }}">
            <div style="font-size:10px;font-weight:700;color:var(--text-faint);text-transform:uppercase;letter-spacing:0.06em;margin-bottom:10px">{{ $isFr ? 'Inclus' : 'Includes' }}</div>
            @foreach($isFr
                ? ['Gestion de l\'identité patient','Identification multi-établissements','Identité QR','Résolution d\'identité']
                : ['Patient identity management','Cross-facility identification','QR identity','Identity resolution']
            as $inc)
            <div style="display:flex;align-items:center;gap:7px;font-size:11px;color:var(--text-muted);margin-bottom:6px">
                <i data-lucide="check" style="width:11px;height:11px;color:#00C896;flex-shrink:0"></i>{{ $inc }}
            </div>
            @endforeach
        </div>
        <div style="{{ $priceCol }}">
            <div class="pc-g3" style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px">
                <div style="{{ $priceTile }}">
                    <div style="font-size:10px;font-weight:700;color:#00C896;text-transform:uppercase;letter-spacing:0.07em;margin-bottom:10px">{{ $isFr ? 'Déploiement établissement' : 'Facility deployment' }}</div>
                    <div style="font-size:11px;color:var(--text-faint);margin-bottom:4px">{{ $isFr ? 'À partir de' : 'Starting from' }}</div>
                    <div style="font-size:22px;font-weight:800;color:#e2e8f0;line-height:1.1">5 000 000</div>
                    <div style="font-size:11px;color:#00C896;font-weight:700;margin-bottom:12px">FCFA</div>
                    <a href="{{ route('contact', ['locale' => $locale]) }}" style="font-size:10px;font-weight:700;color:#00C896;text-decoration:none;border:1px solid #00C89630;border-radius:6px;padding:6px 12px">{{ $isFr ? 'Contacter' : 'Contact' }}</a>
                </div>
                <div style="{{ $rfqTile }}">
                    <div style="font-size:10px;font-weight:700;color:#F59E0B;text-transform:uppercase;letter-spacing:0.07em;margin-bottom:10px">{{ $isFr ? 'Multi-établissements' : 'Multi-facility' }}</div>
                    <div style="font-size:28px;margin-bottom:4px">—</div>
                    <div style="font-size:11px;font-weight:700;color:#F59E0B;margin-bottom:12px">{{ $isFr ? 'Sur demande de devis' : 'Request quotation' }}</div>
                    <a href="{{ route('contact', ['locale' => $locale]) }}" style="font-size:10px;font-weight:700;color:#F59E0B;text-decoration:none;border:1px solid #F59E0B30;border-radius:6px;padding:6px 12px">{{ $isFr ? 'Demander' : 'Enquire' }}</a>
                </div>
                <div style="{{ $rfqTile }}">
                    <div style="font-size:10px;font-weight:700;color:#F59E0B;text-transform:uppercase;letter-spacing:0.07em;margin-bottom:10px">{{ $isFr ? 'Déploiement national' : 'National deployment' }}</div>
                    <div style="font-size:28px;margin-bottom:4px">—</div>
                    <div style="font-size:11px;font-weight:700;color:#F59E0B;margin-bottom:12px">{{ $isFr ? 'Sur demande de devis' : 'Request quotation' }}</div>
                    <a href="{{ route('contact', ['locale' => $locale]) }}" style="font-size:10px;font-weight:700;color:#F59E0B;text-decoration:none;border:1px solid #F59E0B30;border-radius:6px;padding:6px 12px">{{ $isFr ? 'Demander' : 'Enquire' }}</a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="divider"></div>

{{-- ── CDMS + TRIAGE + CDSS ────────────────────────────────────── --}}
<div class="section" style="max-width:960px;margin:0 auto">
    <div class="section-label" style="margin-bottom:16px">
        <i data-lucide="cpu" style="width:12px;height:12px"></i>
        {{ $isFr ? 'Systèmes cliniques' : 'Clinical systems' }}
    </div>
    <div class="pc-g3" style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:20px">

        {{-- CDMS --}}
        <div style="background:#080E1A;border:1px solid #1e293b;border-radius:14px;overflow:hidden">
            <div style="padding:16px 18px;border-bottom:1px solid #1e293b;background:#00C89606">
                <div style="font-size:10px;font-weight:700;color:#00C896;text-transform:uppercase;letter-spacing:0.06em;margin-bottom:3px">CDMS</div>
                <div style="font-size:14px;font-weight:800;color:#e2e8f0">{{ $isFr ? 'Gestion de documents cliniques' : 'Clinical Document Management' }}</div>
            </div>
            <div style="padding:16px 18px">
                @foreach($isFr
                    ? ['Dossiers numériques','Archivage de documents','Gestion des flux','Signatures numériques']
                    : ['Digital records','Document archiving','Workflow management','Digital signatures']
                as $i)<div style="display:flex;align-items:center;gap:6px;font-size:11px;color:var(--text-muted);margin-bottom:5px"><i data-lucide="check" style="width:10px;height:10px;color:#00C896;flex-shrink:0"></i>{{ $i }}</div>@endforeach
            </div>
            <div style="padding:0 18px 18px;display:grid;grid-template-columns:1fr 1fr;gap:10px">
                <div style="background:#0F172A;border:1px solid #1e293b;border-radius:9px;padding:12px;text-align:center">
                    <div style="font-size:9px;font-weight:700;color:#1A6FE8;text-transform:uppercase;margin-bottom:6px">SaaS</div>
                    <div style="font-size:16px;font-weight:800;color:#e2e8f0">100 000</div>
                    <div style="font-size:10px;color:#1A6FE8;font-weight:600">FCFA/{{ $isFr ? 'mois' : 'mo' }}</div>
                </div>
                <div style="background:#0F172A;border:1px solid #1e293b;border-radius:9px;padding:12px;text-align:center">
                    <div style="font-size:9px;font-weight:700;color:#00C896;text-transform:uppercase;margin-bottom:6px">{{ $isFr ? 'Perpétuel' : 'Perpetual' }}</div>
                    <div style="font-size:16px;font-weight:800;color:#e2e8f0">4 000 000</div>
                    <div style="font-size:10px;color:#00C896;font-weight:600">FCFA</div>
                </div>
            </div>
        </div>

        {{-- Triage --}}
        <div style="background:#080E1A;border:1px solid #1e293b;border-radius:14px;overflow:hidden">
            <div style="padding:16px 18px;border-bottom:1px solid #1e293b;background:#1A6FE806">
                <div style="font-size:10px;font-weight:700;color:#1A6FE8;text-transform:uppercase;letter-spacing:0.06em;margin-bottom:3px">OPES Triage</div>
                <div style="font-size:14px;font-weight:800;color:#e2e8f0">{{ $isFr ? 'Triage numérique & évaluation des risques' : 'Digital triage & risk assessment' }}</div>
            </div>
            <div style="padding:16px 18px">
                @foreach($isFr
                    ? ['Priorisation des patients','Classification des urgences','Score de risque','Escalade clinique']
                    : ['Patient prioritisation','Emergency classification','Risk scoring','Clinical escalation']
                as $i)<div style="display:flex;align-items:center;gap:6px;font-size:11px;color:var(--text-muted);margin-bottom:5px"><i data-lucide="check" style="width:10px;height:10px;color:#1A6FE8;flex-shrink:0"></i>{{ $i }}</div>@endforeach
            </div>
            <div style="padding:0 18px 18px;display:grid;grid-template-columns:1fr 1fr;gap:10px">
                <div style="background:#0F172A;border:1px solid #1e293b;border-radius:9px;padding:12px;text-align:center">
                    <div style="font-size:9px;font-weight:700;color:#1A6FE8;text-transform:uppercase;margin-bottom:6px">SaaS</div>
                    <div style="font-size:16px;font-weight:800;color:#e2e8f0">150 000</div>
                    <div style="font-size:10px;color:#1A6FE8;font-weight:600">FCFA/{{ $isFr ? 'mois' : 'mo' }}</div>
                </div>
                <div style="background:#0F172A;border:1px solid #1e293b;border-radius:9px;padding:12px;text-align:center">
                    <div style="font-size:9px;font-weight:700;color:#00C896;text-transform:uppercase;margin-bottom:6px">{{ $isFr ? 'Perpétuel' : 'Perpetual' }}</div>
                    <div style="font-size:16px;font-weight:800;color:#e2e8f0">5 000 000</div>
                    <div style="font-size:10px;color:#00C896;font-weight:600">FCFA</div>
                </div>
            </div>
        </div>

        {{-- CDSS --}}
        <div style="background:#080E1A;border:1px solid #1e293b;border-radius:14px;overflow:hidden">
            <div style="padding:16px 18px;border-bottom:1px solid #1e293b;background:#00C89606">
                <div style="font-size:10px;font-weight:700;color:#00C896;text-transform:uppercase;letter-spacing:0.06em;margin-bottom:3px">OPES CDSS</div>
                <div style="font-size:14px;font-weight:800;color:#e2e8f0">{{ $isFr ? 'Aide à la décision clinique' : 'Clinical Decision Support' }}</div>
            </div>
            <div style="padding:16px 18px">
                @foreach($isFr
                    ? ['Règles cliniques','Interactions médicamenteuses','Alertes cliniques','Recommandations de traitement','Aide au diagnostic']
                    : ['Clinical rules','Drug interaction checking','Clinical alerts','Treatment recommendations','Diagnostic assistance']
                as $i)<div style="display:flex;align-items:center;gap:6px;font-size:11px;color:var(--text-muted);margin-bottom:5px"><i data-lucide="check" style="width:10px;height:10px;color:#00C896;flex-shrink:0"></i>{{ $i }}</div>@endforeach
            </div>
            <div style="padding:0 18px 18px;display:flex;flex-direction:column;gap:8px">
                <div style="background:#0F172A;border:1px solid #1e293b;border-radius:9px;padding:10px;display:flex;justify-content:space-between;align-items:center">
                    <span style="font-size:10px;color:var(--text-muted)">{{ $isFr ? 'Hôpital' : 'Hospital' }}</span>
                    <span style="font-size:13px;font-weight:800;color:#e2e8f0">10 000 000 <span style="font-size:10px;color:#00C896;font-weight:600">FCFA</span></span>
                </div>
                <div style="background:#0F172A;border:1px dashed #1e293b;border-radius:9px;padding:10px;display:flex;justify-content:space-between;align-items:center">
                    <span style="font-size:10px;color:var(--text-muted)">{{ $isFr ? 'Réseau / National' : 'Network / National' }}</span>
                    <span style="font-size:11px;font-weight:700;color:#F59E0B">{{ $isFr ? 'Sur devis' : 'On request' }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="divider"></div>

{{-- ── ENTERPRISE & GOVERNMENT (all RFQ) ───────────────────────── --}}
<div class="section" style="max-width:960px;margin:0 auto">
    <div class="section-label" style="margin-bottom:16px">
        <i data-lucide="building-2" style="width:12px;height:12px"></i>
        {{ $isFr ? 'Entreprise & gouvernement' : 'Enterprise & government' }}
    </div>
    <h2 class="section-title" style="font-size:clamp(16px,2vw,22px);margin-bottom:8px">
        {{ $isFr ? 'Programmes nationaux, assurance & santé publique' : 'National programmes, insurance & public health' }}
    </h2>
    <p style="color:var(--text-muted);font-size:13px;margin-bottom:24px;max-width:680px">
        {{ $isFr
            ? 'Ces solutions sont tarifées sur mesure selon la population, le nombre d\'établissements, l\'étendue du déploiement et les exigences de renforcement des capacités. Chaque projet fait l\'objet d\'une proposition commerciale dédiée.'
            : 'These solutions are priced individually based on population size, number of facilities, scope of deployment, and capacity-building requirements. Each project receives a dedicated commercial proposal.' }}
    </p>
    <div class="pc-g3" style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:20px">
        @php
        $govProducts = $isFr ? [
            ['shield-check','#8B5CF6','OPES Assurance',['Gestion des membres','Traitement des sinistres','Gestion des prestataires','Gestion des tarifs','Remboursements','Surveillance des fraudes']],
            ['activity','#EF4444','OPES Santé publique',['Surveillance des maladies','Rapports de programme','Programmes de vaccination','Santé maternelle','Tableaux de bord nationaux']],
            ['globe-2','#F59E0B','Plateforme nationale de santé numérique',['Health ID national','HIE nationale','Registre national des prestataires','Registre national des établissements','Plateforme CSU','Surveillance de santé publique','Analytique nationale','Tableaux de bord nationaux']],
        ] : [
            ['shield-check','#8B5CF6','OPES Insurance',['Member management','Claims processing','Provider management','Tariff management','Reimbursements','Fraud monitoring']],
            ['activity','#EF4444','OPES Public Health',['Disease surveillance','Programme reporting','Vaccination programmes','Maternal health','National dashboards']],
            ['globe-2','#F59E0B','National Digital Health Platform',['National Health ID','National HIE','National provider registry','National facility registry','UHC platform','Public health surveillance','National analytics','National dashboards']],
        ];
        @endphp
        @foreach($govProducts as $gp)
        <div style="background:#080E1A;border:1px solid {{ $gp[1] }}30;border-radius:14px;overflow:hidden">
            <div style="padding:16px 18px;border-bottom:1px solid #1e293b">
                <div style="display:flex;align-items:center;gap:10px;margin-bottom:0">
                    <i data-lucide="{{ $gp[0] }}" style="width:16px;height:16px;color:{{ $gp[1] }};flex-shrink:0"></i>
                    <div style="font-size:13px;font-weight:800;color:#e2e8f0">{{ $gp[2] }}</div>
                </div>
            </div>
            <div style="padding:14px 18px">
                @foreach($gp[3] as $inc)
                <div style="display:flex;align-items:center;gap:6px;font-size:11px;color:var(--text-muted);margin-bottom:5px">
                    <i data-lucide="check" style="width:10px;height:10px;color:{{ $gp[1] }};flex-shrink:0"></i>{{ $inc }}
                </div>
                @endforeach
            </div>
            <div style="padding:14px 18px;border-top:1px solid #1e293b;text-align:center">
                <div style="font-size:11px;font-weight:700;color:{{ $gp[1] }};margin-bottom:10px">{{ $isFr ? 'Sur demande de devis' : 'Request quotation' }}</div>
                <a href="{{ route('contact', ['locale' => $locale]) }}" style="display:inline-flex;align-items:center;gap:6px;background:{{ $gp[1] }}15;border:1px solid {{ $gp[1] }}30;border-radius:7px;padding:8px 16px;font-size:11px;font-weight:700;color:{{ $gp[1] }};text-decoration:none">
                    <i data-lucide="mail" style="width:11px;height:11px"></i>{{ $isFr ? 'Contacter l\'équipe commerciale' : 'Contact sales team' }}
                </a>
            </div>
        </div>
        @endforeach
    </div>
</div>

<div class="divider"></div>

{{-- ── PROFESSIONAL SERVICES ────────────────────────────────────── --}}
<div class="section" style="max-width:960px;margin:0 auto">
    <div class="section-label" style="margin-bottom:16px">
        <i data-lucide="tool" style="width:12px;height:12px"></i>
        {{ $isFr ? 'Services professionnels' : 'Professional services' }}
    </div>
    <div class="pc-g4" style="display:grid;grid-template-columns:1fr 1fr 1fr 1fr;gap:14px">
        @foreach($isFr
            ? [['map','#00C896','Implémentation','À partir de 1 500 000 FCFA','De l\'installation à la mise en production'],['database','#1A6FE8','Migration de données','À partir de 1 000 000 FCFA','Depuis les formats CSV, HL7, FHIR'],['graduation-cap','#00C896','Formation','À partir de 500 000 FCFA','Tous rôles, en ligne ou sur site'],['code','#1A6FE8','Intégrations sur mesure','Sur demande de devis','Connexion aux systèmes existants']]
            : [['map','#00C896','Implementation','Starting from 1 500 000 FCFA','From installation to go-live'],['database','#1A6FE8','Data migration','Starting from 1 000 000 FCFA','From CSV, HL7, FHIR formats'],['graduation-cap','#00C896','Training','Starting from 500 000 FCFA','All roles, online or on-site'],['code','#1A6FE8','Custom integrations','Request quotation','Connect to existing systems']]
        as $svc)
        <div style="background:#080E1A;border:1px solid #1e293b;border-radius:12px;padding:18px;text-align:center">
            <i data-lucide="{{ $svc[0] }}" style="width:22px;height:22px;color:{{ $svc[1] }};margin-bottom:10px"></i>
            <div style="font-weight:800;color:#e2e8f0;font-size:13px;margin-bottom:8px">{{ $svc[2] }}</div>
            <div style="font-size:12px;font-weight:700;color:{{ $svc[1] }};margin-bottom:6px">{{ $svc[3] }}</div>
            <div style="font-size:11px;color:var(--text-faint)">{{ $svc[4] }}</div>
        </div>
        @endforeach
    </div>
</div>

<div class="divider"></div>

{{-- ── MAINTENANCE & SUPPORT ────────────────────────────────────── --}}
<div class="section" style="max-width:960px;margin:0 auto">
    <div class="section-label" style="margin-bottom:16px">
        <i data-lucide="wrench" style="width:12px;height:12px"></i>
        {{ $isFr ? 'Maintenance & support annuels' : 'Annual maintenance & support' }}
    </div>
    <h2 class="section-title" style="font-size:clamp(18px,2.5vw,24px)">
        {{ $isFr ? 'Maintien en condition opérationnelle' : 'Keeping you operational' }}
    </h2>
    <p style="color:var(--text-muted);font-size:14px;line-height:1.75;max-width:660px;margin:12px 0 28px">
        {{ $isFr
            ? 'Inclus : mises à jour logicielles, mises à jour de sécurité, support technique, correctifs, accès à la base de connaissances, assistance à distance.'
            : 'Includes: software updates, security updates, technical support, bug fixes, knowledge base access, remote assistance.' }}
    </p>
    <div class="pc-g3" style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:18px">
        @foreach($isFr
            ? [['var(--text-faint)','Standard','15 % de la valeur de la licence','Mises à jour + support Bronze','Établissements de santé standards'],['#00C896','Premium','20 % de la valeur de la licence','Mises à jour + support Gold','Hôpitaux & groupes de santé'],['#F59E0B','Enterprise','Sur devis','Contrat de service personnalisé','Ministères & programmes nationaux']]
            : [['var(--text-faint)','Standard','15% of licence value','Updates + Bronze support','Standard health facilities'],['#00C896','Premium','20% of licence value','Updates + Gold support','Hospitals & health groups'],['#F59E0B','Enterprise','Custom','Custom service contract','Ministries & national programmes']]
        as $tier)
        <div style="background:#0F172A;border:1px solid {{ $tier[0] }}30;border-radius:12px;padding:22px;text-align:center">
            <div style="font-size:11px;font-weight:800;color:{{ $tier[0] }};text-transform:uppercase;letter-spacing:0.08em;margin-bottom:8px">{{ $tier[1] }}</div>
            <div style="font-size:22px;font-weight:800;color:#e2e8f0;margin-bottom:4px">{{ $tier[2] }}</div>
            <div style="font-size:11px;color:var(--text-faint);margin-bottom:8px">{{ $tier[3] }}</div>
            <div style="font-size:11px;font-weight:600;color:{{ $tier[0] }}">{{ $tier[4] }}</div>
        </div>
        @endforeach
    </div>
</div>

<div class="divider"></div>

{{-- ── HOSTING ──────────────────────────────────────────────────── --}}
<div class="section" style="max-width:960px;margin:0 auto">
    <div class="pc-host" style="background:#080E1A;border:1px solid #1e293b;border-radius:14px;padding:28px;display:grid;grid-template-columns:1fr auto;gap:32px;align-items:center">
        <div>
            <div class="section-label" style="margin-bottom:12px">
                <i data-lucide="cloud" style="width:12px;height:12px"></i>
                {{ $isFr ? 'Hébergement dans le datacenter OPES, Douala' : 'Hosting in OPES Data Centre, Douala' }}
            </div>
            <h3 style="font-size:17px;font-weight:700;color:#e2e8f0;margin-bottom:12px">{{ $isFr ? 'Services d\'hébergement managés' : 'Managed hosting services' }}</h3>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px">
                @foreach($isFr
                    ? ['Infrastructure managée','Sauvegardes managées','Surveillance de sécurité','Reprise après sinistre','Support technique']
                    : ['Managed infrastructure','Managed backups','Security monitoring','Disaster recovery','Technical support']
                as $h)
                <div style="display:flex;align-items:center;gap:7px;font-size:12px;color:var(--text-muted)">
                    <i data-lucide="check" style="width:11px;height:11px;color:#00C896;flex-shrink:0"></i>{{ $h }}
                </div>
                @endforeach
            </div>
        </div>
        <div style="text-align:center;min-width:180px">
            <div style="font-size:11px;color:var(--text-faint);margin-bottom:4px">{{ $isFr ? 'À partir de' : 'Starting from' }}</div>
            <div style="font-size:32px;font-weight:800;color:#e2e8f0;line-height:1.1">100 000</div>
            <div style="font-size:14px;color:#00C896;font-weight:700;margin-bottom:4px">FCFA / {{ $isFr ? 'mois' : 'month' }}</div>
            <div style="font-size:11px;color:var(--text-faint);margin-bottom:16px">{{ $isFr ? 'Selon la charge de travail & l\'infrastructure' : 'Based on workload & infrastructure' }}</div>
            <a href="{{ route('contact', ['locale' => $locale]) }}" style="display:inline-flex;align-items:center;gap:6px;background:#00C89615;border:1px solid #00C89630;border-radius:8px;padding:9px 16px;font-size:12px;font-weight:700;color:#00C896;text-decoration:none">
                <i data-lucide="send" style="width:12px;height:12px"></i>{{ $isFr ? 'Demander un devis' : 'Request a quote' }}
            </a>
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
    <h2 class="section-title" style="font-size:clamp(18px,2.5vw,22px)">{{ $isFr ? 'Payez comme vous le souhaitez' : 'Pay the way that works for you' }}</h2>
    <div class="pc-g5" style="display:grid;grid-template-columns:repeat(5,1fr);gap:12px;max-width:800px;margin:24px auto 0">
        @foreach($isFr
            ? [['smartphone','#FFCC00','MTN Mobile Money','Paiements MoMo XAF'],['smartphone','#FF6600','Orange Money','Paiements Orange XAF'],['landmark','#1A6FE8','Virement bancaire','XAF · EUR · USD'],['credit-card','#00C896','Carte bancaire','Visa · Mastercard'],['file-text','var(--text-faint)','Bon de commande','Marchés publics & PME']]
            : [['smartphone','#FFCC00','MTN Mobile Money','MoMo XAF payments'],['smartphone','#FF6600','Orange Money','Orange XAF payments'],['landmark','#1A6FE8','Bank transfer','XAF · EUR · USD'],['credit-card','#00C896','Card payment','Visa · Mastercard'],['file-text','var(--text-faint)','Purchase order','Public procurement & SMEs']]
        as $pm)
        <div style="background:#0F172A;border:1px solid #1e293b;border-radius:10px;padding:14px;text-align:center">
            <i data-lucide="{{ $pm[0] }}" style="width:20px;height:20px;color:{{ $pm[1] }};margin-bottom:6px"></i>
            <div style="font-weight:700;color:#e2e8f0;font-size:11px;margin-bottom:2px">{{ $pm[2] }}</div>
            <div style="font-size:10px;color:var(--text-muted)">{{ $pm[3] }}</div>
        </div>
        @endforeach
    </div>
</div>

{{-- ── CTA STRIP ────────────────────────────────────────────────── --}}
<div class="pricing-cta-strip">
    <h2>{{ $isFr ? 'Besoin d\'une proposition commerciale personnalisée ?' : 'Need a custom commercial proposal?' }}</h2>
    <p>{{ $isFr
        ? 'Clinique, hôpital, réseau, assureur, ONG ou Ministère de la Santé — notre équipe commerciale conçoit un modèle de déploiement adapté à vos exigences opérationnelles, réglementaires et budgétaires.'
        : 'Clinic, hospital, network, insurer, NGO, or Ministry of Health — our sales team designs a deployment model aligned to your operational, regulatory, and budget requirements.' }}</p>
    <div class="pricing-cta-strip-btns">
        <a href="{{ route('contact', ['locale' => $locale]) }}" class="btn-primary">
            <i data-lucide="send" style="width:14px;height:14px"></i>
            {{ $isFr ? 'Demander une proposition' : 'Request a proposal' }}
        </a>
        <a href="mailto:{{ config('company.email') }}" class="btn-secondary">
            <i data-lucide="mail" style="width:14px;height:14px;color:#00C896"></i>
            {{ config('company.email') }}
        </a>
    </div>
</div>

{{-- ── FAQ ─────────────────────────────────────────────────────── --}}
<div class="pricing-faq">
    <h2>{{ $isFr ? 'Questions fréquentes' : 'Frequently asked questions' }}</h2>
    @php
    $faqs = $isFr ? [
        ['q' => 'Quelle est la différence entre abonnement SaaS et licence perpétuelle ?',
         'a' => 'L\'abonnement SaaS (cloud OPES) comprend l\'hébergement, les mises à jour et le support dans un paiement mensuel — sans investissement initial en infrastructure. La licence perpétuelle donne un droit d\'usage illimité du logiciel pour un paiement unique, complété par une maintenance annuelle de 15–20 % pour les mises à jour et le support. Les institutions publiques et les hôpitaux disposant d\'une infrastructure IT préfèrent généralement la licence perpétuelle.'],
        ['q' => 'Puis-je combiner des modules de différentes familles de produits ?',
         'a' => 'Oui. Tous les produits OPES sont interopérables et peuvent être combinés. Par exemple, un hôpital peut déployer OPES Hospital HIS + OPES Lab IS + OPES Triage, chacun licencié séparément et s\'intégrant via la couche interopérabilité OPES Care.'],
        ['q' => 'Comment fonctionne la tarification pour les déploiements nationaux ?',
         'a' => 'Les projets nationaux (Plateforme nationale de santé numérique, Health ID national, programmes de santé publique) sont tarifés sur la base d\'une proposition commerciale dédiée tenant compte de la taille de la population, du nombre d\'établissements, de l\'étendue fonctionnelle et des exigences de renforcement des capacités. Contactez notre équipe commercial pour initier la discussion.'],
        ['q' => 'La maintenance annuelle est-elle obligatoire pour les licences perpétuelles ?',
         'a' => 'Oui. La maintenance annuelle (15–20 % du prix de licence) est requise pour recevoir les mises à jour de sécurité, les nouvelles versions majeures et le support technique. Sans contrat de maintenance actif, le logiciel reste fonctionnel mais ne reçoit plus de mises à jour.'],
        ['q' => 'Mes données sont-elles hébergées au Cameroun ?',
         'a' => 'Par défaut, toutes les données de santé sont hébergées dans notre datacenter à Douala, Cameroun, en conformité avec la législation camerounaise sur la protection des données. Pour les déploiements on-premise, vos données restent intégralement sur vos propres serveurs.'],
        ['q' => 'Quels modes de paiement acceptez-vous ?',
         'a' => 'MTN Mobile Money, Orange Money, virement bancaire (XAF/EUR/USD), Visa/Mastercard et bons de commande pour les marchés publics. Pour les institutions publiques et les projets nationaux, nous préparons une proposition commerciale formelle compatible avec les procédures d\'appel d\'offres.'],
    ] : [
        ['q' => 'What is the difference between SaaS subscription and perpetual licence?',
         'a' => 'A SaaS subscription (OPES cloud) bundles hosting, updates, and support into a monthly payment — with no upfront infrastructure investment. A perpetual licence grants unlimited software usage rights for a one-time payment, supplemented by 15–20% annual maintenance for updates and support. Public institutions and hospitals with existing IT infrastructure typically prefer perpetual licences.'],
        ['q' => 'Can I combine modules from different product families?',
         'a' => 'Yes. All OPES products are interoperable and can be combined. For example, a hospital can deploy OPES Hospital HIS + OPES Lab IS + OPES Triage, each licensed separately and integrating via the OPES Care interoperability layer.'],
        ['q' => 'How does pricing work for national deployments?',
         'a' => 'National projects (National Digital Health Platform, national Health ID, public health programmes) are priced through a dedicated commercial proposal based on population size, number of facilities, functional scope, and capacity-building requirements. Contact our sales team to start the discussion.'],
        ['q' => 'Is annual maintenance mandatory for perpetual licences?',
         'a' => 'Yes. Annual maintenance (15–20% of licence price) is required to receive security updates, major new releases, and technical support. Without an active maintenance contract, the software remains functional but no longer receives updates.'],
        ['q' => 'Where is my data hosted?',
         'a' => 'By default, all health data is hosted in our data centre in Douala, Cameroon, in compliance with Cameroonian data protection laws. For on-premise deployments, your data remains entirely on your own servers.'],
        ['q' => 'What payment methods do you accept?',
         'a' => 'MTN Mobile Money, Orange Money, bank transfer (XAF/EUR/USD), Visa/Mastercard, and purchase orders for public procurement. For public institutions and national projects, we prepare a formal commercial proposal compatible with tender procedures.'],
    ];
    @endphp
    @foreach($faqs as $faq)
    <details class="faq-item">
        <summary class="faq-q">{{ $faq['q'] }}</summary>
        <p class="faq-a">{{ $faq['a'] }}</p>
    </details>
    @endforeach
</div>

</x-layouts.app>

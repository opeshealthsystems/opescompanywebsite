@php $locale = app()->getLocale(); $isFr = $locale === 'fr'; @endphp

<x-layouts.app
    title="{{ $isFr ? 'FAQ — Questions fréquentes — OPES Health Systems' : 'FAQ — Frequently Asked Questions — OPES Health Systems' }}"
    description="{{ $isFr ? 'Réponses aux questions les plus fréquentes sur la plateforme OPES Health Systems — produits, tarification, déploiement, sécurité et partenariat.' : 'Answers to the most common questions about the OPES Health Systems platform — products, pricing, deployment, security, and partnership.' }}">

<style>
.faq-hero{text-align:center;padding:64px 24px 40px;max-width:680px;margin:0 auto}
.faq-hero h1{font-size:clamp(28px,4vw,42px);font-weight:800;color:#e2e8f0;margin:16px 0 12px;line-height:1.15}
.faq-hero p{color:var(--text-muted);font-size:15px;line-height:1.7;max-width:560px;margin:0 auto}
.faq-cats{display:flex;flex-wrap:wrap;gap:8px;justify-content:center;padding:0 24px 36px}
.faq-cat-pill{display:inline-flex;align-items:center;gap:6px;padding:6px 14px;border-radius:20px;background:#0f172a;border:1px solid #1e293b;color:var(--text-muted);font-size:12px;font-weight:600;text-decoration:none;transition:all 0.15s;cursor:pointer}
.faq-cat-pill:hover,.faq-cat-pill.active{background:#00C89612;border-color:#00C89640;color:#00C896}
.faq-body{max-width:820px;margin:0 auto;padding:0 24px 80px}
.faq-section{margin-bottom:48px}
.faq-section-header{display:flex;align-items:center;gap:10px;margin-bottom:20px;padding-bottom:12px;border-bottom:1px solid #1e293b}
.faq-section-icon{width:32px;height:32px;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0}
.faq-section-title{font-size:14px;font-weight:700;color:var(--text-muted);letter-spacing:.06em;text-transform:uppercase}
details.faq-item{background:#0a1628;border:1px solid #1e293b;border-radius:10px;margin-bottom:8px;overflow:hidden;transition:border-color 0.2s}
details.faq-item[open]{border-color:#1A6FE840}
details.faq-item[open] summary .faq-chevron{transform:rotate(180deg)}
summary.faq-q{display:flex;align-items:flex-start;justify-content:space-between;gap:12px;padding:16px 18px;cursor:pointer;list-style:none;font-size:14px;font-weight:600;color:#e2e8f0;line-height:1.5}
summary.faq-q::-webkit-details-marker{display:none}
summary.faq-q:hover{color:#fff}
.faq-q-text{flex:1}
.faq-chevron{flex-shrink:0;margin-top:2px;color:var(--text-faint);transition:transform 0.2s}
.faq-a{padding:0 18px 16px;color:var(--text-muted);font-size:13.5px;line-height:1.75;border-top:1px solid #1e293b}
.faq-a p{margin:12px 0 0}
.faq-a p:first-child{margin-top:12px}
.faq-a ul{margin:8px 0 0 0;padding-left:18px}
.faq-a ul li{margin-bottom:4px;color:var(--text-muted)}
.faq-a a{color:#00C896;text-decoration:none}
.faq-a a:hover{text-decoration:underline}
.faq-a strong{color:#cbd5e1}
.faq-cta{text-align:center;padding:48px 24px;background:linear-gradient(135deg,rgba(0,200,150,0.05),rgba(26,111,232,0.05));border:1px solid #1e293b;border-radius:16px;max-width:600px;margin:0 auto}
.faq-cta h2{font-size:22px;font-weight:700;color:#e2e8f0;margin-bottom:10px}
.faq-cta p{color:var(--text-muted);font-size:14px;margin-bottom:24px}
.faq-cta-btns{display:flex;gap:12px;justify-content:center;flex-wrap:wrap}
</style>

{{-- HERO --}}
<div class="faq-hero">
    <div class="section-label" style="justify-content:center;margin-bottom:16px">
        <i data-lucide="help-circle" style="width:12px;height:12px"></i>
        {{ $isFr ? 'Questions fréquentes' : 'Frequently Asked Questions' }}
    </div>
    <h1>{{ $isFr ? 'Tout ce que vous voulez savoir sur OPES' : 'Everything you need to know about OPES' }}</h1>
    <p>{{ $isFr ? 'Des réponses claires aux questions les plus posées par les hôpitaux, cliniques, gouvernements et investisseurs envisageant la plateforme OPES.' : 'Clear answers to the questions most asked by hospitals, clinics, governments, and investors considering the OPES platform.' }}</p>
</div>

{{-- CATEGORY PILLS --}}
<div class="faq-cats">
    @foreach($isFr ? [
        ['icon'=>'info','label'=>'Général'],
        ['icon'=>'layout-grid','label'=>'Produits'],
        ['icon'=>'map','label'=>'Déploiement'],
        ['icon'=>'tag','label'=>'Tarification'],
        ['icon'=>'share-2','label'=>'Interopérabilité'],
        ['icon'=>'headphones','label'=>'Support'],
        ['icon'=>'shield','label'=>'Données & sécurité'],
        ['icon'=>'handshake','label'=>'Partenariat'],
    ] : [
        ['icon'=>'info','label'=>'General'],
        ['icon'=>'layout-grid','label'=>'Products'],
        ['icon'=>'map','label'=>'Deployment'],
        ['icon'=>'tag','label'=>'Pricing'],
        ['icon'=>'share-2','label'=>'Interoperability'],
        ['icon'=>'headphones','label'=>'Support'],
        ['icon'=>'shield','label'=>'Data & Security'],
        ['icon'=>'handshake','label'=>'Partnership'],
    ] as $pill)
    <div class="faq-cat-pill">
        <i data-lucide="{{ $pill['icon'] }}" style="width:11px;height:11px"></i>
        {{ $pill['label'] }}
    </div>
    @endforeach
</div>

<div class="faq-body">

{{-- ═══════════════════════════ 1. GENERAL ═══════════════════════════ --}}
<div class="faq-section" id="general">
    <div class="faq-section-header">
        <div class="faq-section-icon" style="background:rgba(0,200,150,0.1)">
            <i data-lucide="info" style="width:16px;height:16px;color:#00C896"></i>
        </div>
        <div class="faq-section-title">{{ $isFr ? 'Général' : 'General' }}</div>
    </div>

    <details class="faq-item">
        <summary class="faq-q">
            <span class="faq-q-text">{{ $isFr ? 'Qu\'est-ce qu\'OPES Health Systems ?' : 'What is OPES Health Systems?' }}</span>
            <i data-lucide="chevron-down" class="faq-chevron" style="width:16px;height:16px"></i>
        </summary>
        <div class="faq-a">
            <p>{{ $isFr ? 'OPES Health Systems est une entreprise camerounaise de technologie de la santé développant un écosystème complet de logiciels de santé numérique pour l\'Afrique subsaharienne. Notre plateforme comprend 22 systèmes logiciels intégrés — des dossiers médicaux électroniques aux systèmes d\'information de laboratoire, en passant par la gestion hospitalière, la radiologie, la pharmacie et bien plus encore.' : 'OPES Health Systems is a Cameroonian health technology company developing a comprehensive digital health software ecosystem for sub-Saharan Africa. Our platform includes 22 integrated software systems — from Electronic Medical Records to Laboratory Information Systems, Hospital Management, Radiology, Pharmacy, and more.' }}</p>
        </div>
    </details>

    <details class="faq-item">
        <summary class="faq-q">
            <span class="faq-q-text">{{ $isFr ? 'Où est basé OPES Health Systems ?' : 'Where is OPES Health Systems based?' }}</span>
            <i data-lucide="chevron-down" class="faq-chevron" style="width:16px;height:16px"></i>
        </summary>
        <div class="faq-a">
            <p>{{ $isFr ? 'OPES Health Systems SARL est enregistrée sous le droit OHADA, avec son siège social à Bonamousadi, Douala, Cameroun. Nous opérons dans toute la région CEMAC (Cameroun, Centrafrique, Congo, Gabon, Guinée équatoriale, Tchad) et plus largement en Afrique subsaharienne.' : 'OPES Health Systems SARL is registered under OHADA law, headquartered at Bonamousadi, Douala, Cameroon. We operate across the CEMAC region (Cameroon, Central African Republic, Congo, Gabon, Equatorial Guinea, Chad) and broader sub-Saharan Africa.' }}</p>
        </div>
    </details>

    <details class="faq-item">
        <summary class="faq-q">
            <span class="faq-q-text">{{ $isFr ? 'OPES est-il aligné avec le Ministère de la Santé ?' : 'Is OPES aligned with the Ministry of Health?' }}</span>
            <i data-lucide="chevron-down" class="faq-chevron" style="width:16px;height:16px"></i>
        </summary>
        <div class="faq-a">
            <p>{{ $isFr ? 'Oui. La plateforme OPES est conçue en conformité avec la Stratégie nationale de santé numérique 2026–2030 du Ministère de la Santé du Cameroun. Nos systèmes supportent les objectifs de couverture sanitaire universelle (CSU), les registres nationaux des patients et les échanges d\'informations de santé requis par les politiques nationales.' : 'Yes. The OPES platform is designed in alignment with the Cameroon Ministry of Health Digital Health Strategy 2026–2030. Our systems support Universal Health Coverage (UHC) goals, national patient registries, and the health information exchange requirements stipulated by national policy.' }}</p>
        </div>
    </details>

    <details class="faq-item">
        <summary class="faq-q">
            <span class="faq-q-text">{{ $isFr ? 'La plateforme est-elle disponible en français et en anglais ?' : 'Is the platform available in both French and English?' }}</span>
            <i data-lucide="chevron-down" class="faq-chevron" style="width:16px;height:16px"></i>
        </summary>
        <div class="faq-a">
            <p>{{ $isFr ? 'Absolument. OPES est une plateforme entièrement bilingue (EN/FR). Le personnel de votre établissement peut basculer entre les deux langues à tout moment. Toutes les interfaces, rapports, notifications et documents sont disponibles dans les deux langues. Cette bilingualité est une caractéristique fondamentale, pas une option à payer séparément.' : 'Absolutely. OPES is a fully bilingual platform (EN/FR). Your facility staff can switch between languages at any time. All interfaces, reports, notifications, and documents are available in both languages. This bilingualism is a core feature, not an add-on.' }}</p>
        </div>
    </details>

    <details class="faq-item">
        <summary class="faq-q">
            <span class="faq-q-text">{{ $isFr ? 'Qui peut utiliser OPES ?' : 'Who is OPES designed for?' }}</span>
            <i data-lucide="chevron-down" class="faq-chevron" style="width:16px;height:16px"></i>
        </summary>
        <div class="faq-a">
            <p>{{ $isFr ? 'OPES s\'adresse à tous les acteurs du système de santé :' : 'OPES is built for all actors in the health system:' }}</p>
            <ul>
                @if($isFr)
                <li><strong>Cliniques et cabinets privés</strong> — OPES EMR + Opes Triage</li>
                <li><strong>Hôpitaux de district et généraux</strong> — OPES Hospital HIS avec modules spécialisés</li>
                <li><strong>Laboratoires et pharmacies indépendants</strong> — OPES Lab (LABIS) et PHARMIS</li>
                <li><strong>Ministères de la Santé et gouvernements</strong> — Plateforme nationale + OPESCare HIE</li>
                <li><strong>Compagnies d\'assurance maladie</strong> — OPES Insurance IS</li>
                <li><strong>Hôpitaux de référence et CHU</strong> — Suite enterprise complète</li>
                @else
                <li><strong>Clinics and private practices</strong> — OPES EMR + Opes Triage</li>
                <li><strong>District and general hospitals</strong> — OPES Hospital HIS with specialty modules</li>
                <li><strong>Standalone labs and pharmacies</strong> — OPES Lab (LABIS) and PHARMIS</li>
                <li><strong>Ministries of Health and governments</strong> — National Platform + OPESCare HIE</li>
                <li><strong>Health insurance companies</strong> — OPES Insurance IS</li>
                <li><strong>Teaching hospitals and CHUs</strong> — Full enterprise suite</li>
                @endif
            </ul>
        </div>
    </details>
</div>

{{-- ═══════════════════════════ 2. PRODUCTS ═══════════════════════════ --}}
<div class="faq-section" id="products">
    <div class="faq-section-header">
        <div class="faq-section-icon" style="background:rgba(26,111,232,0.1)">
            <i data-lucide="layout-grid" style="width:16px;height:16px;color:#1A6FE8"></i>
        </div>
        <div class="faq-section-title">{{ $isFr ? 'Produits & systèmes' : 'Products & Systems' }}</div>
    </div>

    <details class="faq-item">
        <summary class="faq-q">
            <span class="faq-q-text">{{ $isFr ? 'Combien de systèmes logiciels OPES propose-t-il ?' : 'How many software systems does OPES offer?' }}</span>
            <i data-lucide="chevron-down" class="faq-chevron" style="width:16px;height:16px"></i>
        </summary>
        <div class="faq-a">
            <p>{{ $isFr ? 'OPES propose 22 systèmes logiciels intégrés regroupés en 4 familles :' : 'OPES offers 22 integrated software systems grouped into 4 families:' }}</p>
            <ul>
                @if($isFr)
                <li><strong>Plateforme principale</strong> — OPESCare (Health ID), OPES EMR, OPES Hospital HIS, Opes Triage, OPES Intelligence</li>
                <li><strong>Diagnostics</strong> — OPES Lab (LABIS), PHARMIS, RADIS, PATHIS</li>
                <li><strong>Systèmes spécialisés</strong> — CARDIS, MHIS, DENTIS, PEDIS, GYNOBSIS, DERMIS, OPHIS, ORTIS, NEURIS</li>
                <li><strong>Administration & Finance</strong> — OPES HRM, OPES Billing, OPES Inventory, OPES Insurance IS, OPES Public Health IS</li>
                @else
                <li><strong>Core Platform</strong> — OPESCare (Health ID), OPES EMR, OPES Hospital HIS, Opes Triage, OPES Intelligence</li>
                <li><strong>Diagnostics</strong> — OPES Lab (LABIS), PHARMIS, RADIS, PATHIS</li>
                <li><strong>Specialist Systems</strong> — CARDIS, MHIS, DENTIS, PEDIS, GYNOBSIS, DERMIS, OPHIS, ORTIS, NEURIS</li>
                <li><strong>Administration & Finance</strong> — OPES HRM, OPES Billing, OPES Inventory, OPES Insurance IS, OPES Public Health IS</li>
                @endif
            </ul>
            <p>{{ $isFr ? 'Tous les systèmes sont connectés via OPESCare — l\'identifiant de santé universel du patient.' : 'All systems connect through OPESCare — the patient\'s universal Health ID.' }}</p>
        </div>
    </details>

    <details class="faq-item">
        <summary class="faq-q">
            <span class="faq-q-text">{{ $isFr ? 'Qu\'est-ce qu\'OPESCare et pourquoi est-il central ?' : 'What is OPESCare and why is it central to the platform?' }}</span>
            <i data-lucide="chevron-down" class="faq-chevron" style="width:16px;height:16px"></i>
        </summary>
        <div class="faq-a">
            <p>{{ $isFr ? 'OPESCare est la couche d\'identité et d\'interopérabilité au cœur de l\'écosystème OPES. Il attribue à chaque patient un identifiant de santé unique (Health ID) qui le suit dans tous les établissements, tous les systèmes et toutes les régions. Grâce à OPESCare :' : 'OPESCare is the identity and interoperability layer at the heart of the OPES ecosystem. It assigns every patient a unique Health ID that follows them across all facilities, all systems, and all regions. With OPESCare:' }}</p>
            <ul>
                @if($isFr)
                <li>Un patient consulté à la clinique de quartier a ses antécédents disponibles à l\'hôpital régional</li>
                <li>Les résultats de laboratoire sont liés automatiquement au dossier clinique</li>
                <li>La pharmacie connaît les ordonnances actives avant même que le patient ne se présente</li>
                <li>Les référencements entre établissements sont traçables et complets</li>
                @else
                <li>A patient seen at a local clinic has their history available at the regional hospital</li>
                <li>Lab results link automatically to the clinical record</li>
                <li>The pharmacy knows active prescriptions before the patient arrives</li>
                <li>Inter-facility referrals are fully traceable and complete</li>
                @endif
            </ul>
        </div>
    </details>

    <details class="faq-item">
        <summary class="faq-q">
            <span class="faq-q-text">{{ $isFr ? 'Quelle est la différence entre OPES EMR et OPES Hospital HIS ?' : 'What is the difference between OPES EMR and OPES Hospital HIS?' }}</span>
            <i data-lucide="chevron-down" class="faq-chevron" style="width:16px;height:16px"></i>
        </summary>
        <div class="faq-a">
            <p>{{ $isFr ? '<strong>OPES EMR</strong> est conçu pour les cliniques et petits établissements (1–30 lits). Il couvre les consultations, les ordonnances, la facturation simple et les dossiers patients. Déploiement rapide en moins de 30 jours.' : '<strong>OPES EMR</strong> is designed for clinics and small facilities (1–30 beds). It covers consultations, prescriptions, simple billing, and patient records. Fast deployment in under 30 days.' }}</p>
            <p>{{ $isFr ? '<strong>OPES Hospital HIS</strong> est le système d\'information hospitalier complet pour les établissements moyens à grands (30+ lits). Il inclut la gestion des admissions, des services, des blocs opératoires, des salles d\'urgence, la facturation avancée, les rapports institutionnels, et s\'intègre avec tous les modules spécialisés OPES.' : '<strong>OPES Hospital HIS</strong> is the full Hospital Information System for medium-to-large facilities (30+ beds). It includes admissions management, ward management, operating rooms, emergency departments, advanced billing, institutional reporting, and integrates with all OPES specialist modules.' }}</p>
        </div>
    </details>

    <details class="faq-item">
        <summary class="faq-q">
            <span class="faq-q-text">{{ $isFr ? 'Puis-je utiliser un seul système sans acheter toute la suite ?' : 'Can I deploy just one system without buying the full suite?' }}</span>
            <i data-lucide="chevron-down" class="faq-chevron" style="width:16px;height:16px"></i>
        </summary>
        <div class="faq-a">
            <p>{{ $isFr ? 'Oui. Tous les systèmes OPES sont disponibles individuellement. Vous pouvez commencer avec un seul module — par exemple OPES Lab pour un laboratoire indépendant, ou Opes Triage pour un hôpital souhaitant uniquement améliorer son accueil — et ajouter des modules au fur et à mesure. L\'intégration avec OPESCare est disponible dès l\'entrée dans l\'écosystème.' : 'Yes. Every OPES system is available individually. You can start with a single module — for example OPES Lab for a standalone laboratory, or Opes Triage for a hospital that only wants to improve its patient intake — and add modules over time. Integration with OPESCare is available from your very first module.' }}</p>
        </div>
    </details>

    <details class="faq-item">
        <summary class="faq-q">
            <span class="faq-q-text">{{ $isFr ? 'Qu\'est-ce qu\'Opes Triage et comment fonctionne-t-il avec un logiciel existant ?' : 'What is Opes Triage and how does it work with existing software?' }}</span>
            <i data-lucide="chevron-down" class="faq-chevron" style="width:16px;height:16px"></i>
        </summary>
        <div class="faq-a">
            <p>{{ $isFr ? 'Opes Triage est notre système de triage autonome conçu pour réduire immédiatement les temps d\'attente aux urgences et en consultation. Il fonctionne indépendamment de tout autre logiciel — aucun remplacement de système existant n\'est nécessaire. Il peut être déployé en quelques jours et s\'intègre via des API ouvertes à votre SIH ou DME actuel si vous souhaitez une intégration plus poussée.' : 'Opes Triage is our standalone triage system designed to immediately reduce waiting times at emergency and outpatient departments. It works independently of any other software — no replacement of your existing system is required. It can be deployed within days and integrates via open APIs to your current HIS or EMR if you want deeper integration later.' }}</p>
        </div>
    </details>

    <details class="faq-item">
        <summary class="faq-q">
            <span class="faq-q-text">{{ $isFr ? 'Quels systèmes spécialisés sont disponibles ?' : 'What specialist systems are available?' }}</span>
            <i data-lucide="chevron-down" class="faq-chevron" style="width:16px;height:16px"></i>
        </summary>
        <div class="faq-a">
            <p>{{ $isFr ? 'OPES propose des systèmes d\'information spécialisés pour 9 disciplines :' : 'OPES offers specialist information systems for 9 disciplines:' }}</p>
            <ul>
                <li><strong>CARDIS</strong> — {{ $isFr ? 'Cardiologie' : 'Cardiology' }}</li>
                <li><strong>MHIS</strong> — {{ $isFr ? 'Santé mentale' : 'Mental Health' }}</li>
                <li><strong>DENTIS</strong> — {{ $isFr ? 'Dentisterie' : 'Dentistry' }}</li>
                <li><strong>PEDIS</strong> — {{ $isFr ? 'Pédiatrie' : 'Paediatrics' }}</li>
                <li><strong>GYNOBSIS</strong> — {{ $isFr ? 'Gynécologie & obstétrique' : 'Gynaecology & Obstetrics' }}</li>
                <li><strong>DERMIS</strong> — {{ $isFr ? 'Dermatologie' : 'Dermatology' }}</li>
                <li><strong>OPHIS</strong> — {{ $isFr ? 'Ophtalmologie' : 'Ophthalmology' }}</li>
                <li><strong>ORTIS</strong> — {{ $isFr ? 'Orthopédie' : 'Orthopaedics' }}</li>
                <li><strong>NEURIS</strong> — {{ $isFr ? 'Neurologie' : 'Neurology' }}</li>
            </ul>
            <p>{{ $isFr ? 'Chaque système spécialisé s\'intègre nativement avec OPES Hospital HIS et OPESCare.' : 'Every specialist system integrates natively with OPES Hospital HIS and OPESCare.' }}</p>
        </div>
    </details>

    <details class="faq-item">
        <summary class="faq-q">
            <span class="faq-q-text">{{ $isFr ? 'Qu\'est-ce qu\'OPES Intelligence ?' : 'What is OPES Intelligence?' }}</span>
            <i data-lucide="chevron-down" class="faq-chevron" style="width:16px;height:16px"></i>
        </summary>
        <div class="faq-a">
            <p>{{ $isFr ? 'OPES Intelligence est notre couche analytique et de décision clinique intégrée à la plateforme. Elle comprend :' : 'OPES Intelligence is our analytics and clinical decision layer built into the platform. It includes:' }}</p>
            <ul>
                @if($isFr)
                <li><strong>CDSS</strong> — Aide à la décision clinique (alertes médicamenteuses, recommandations diagnostiques)</li>
                <li><strong>Triage numérique</strong> — Évaluation automatisée de la gravité à l\'admission</li>
                <li><strong>Surveillance épidémiologique</strong> — Détection et alerte précoce de maladies</li>
                <li><strong>Analytique populationnelle</strong> — Tableaux de bord de santé publique pour les gestionnaires</li>
                <li><strong>Rapports exécutifs</strong> — KPI hospitaliers en temps réel pour la direction</li>
                @else
                <li><strong>CDSS</strong> — Clinical Decision Support System (drug alerts, diagnostic recommendations)</li>
                <li><strong>Digital Triage</strong> — Automated severity assessment at admission</li>
                <li><strong>Disease Surveillance</strong> — Early detection and outbreak alerting</li>
                <li><strong>Population Analytics</strong> — Public health dashboards for managers</li>
                <li><strong>Executive Reporting</strong> — Real-time hospital KPIs for leadership</li>
                @endif
            </ul>
        </div>
    </details>
</div>

{{-- ═══════════════════════════ 3. DEPLOYMENT ═══════════════════════════ --}}
<div class="faq-section" id="deployment">
    <div class="faq-section-header">
        <div class="faq-section-icon" style="background:rgba(0,200,150,0.1)">
            <i data-lucide="map" style="width:16px;height:16px;color:#00C896"></i>
        </div>
        <div class="faq-section-title">{{ $isFr ? 'Déploiement & implémentation' : 'Deployment & Implementation' }}</div>
    </div>

    <details class="faq-item">
        <summary class="faq-q">
            <span class="faq-q-text">{{ $isFr ? 'Combien de temps prend l\'implémentation ?' : 'How long does implementation take?' }}</span>
            <i data-lucide="chevron-down" class="faq-chevron" style="width:16px;height:16px"></i>
        </summary>
        <div class="faq-a">
            <p>{{ $isFr ? 'Le calendrier d\'implémentation varie selon la taille de l\'établissement et les modules choisis :' : 'Implementation timelines vary by facility size and modules chosen:' }}</p>
            <ul>
                @if($isFr)
                <li><strong>Opes Triage (autonome)</strong> — 3 à 7 jours</li>
                <li><strong>OPES EMR (clinique)</strong> — 2 à 4 semaines</li>
                <li><strong>OPES Lab ou PHARMIS</strong> — 2 à 6 semaines</li>
                <li><strong>OPES Hospital HIS complet</strong> — 60 à 90 jours (cadre standard)</li>
                <li><strong>Suite enterprise / Ministère</strong> — 3 à 12 mois selon la portée</li>
                @else
                <li><strong>Opes Triage (standalone)</strong> — 3 to 7 days</li>
                <li><strong>OPES EMR (clinic)</strong> — 2 to 4 weeks</li>
                <li><strong>OPES Lab or PHARMIS</strong> — 2 to 6 weeks</li>
                <li><strong>Full OPES Hospital HIS</strong> — 60 to 90 days (standard framework)</li>
                <li><strong>Enterprise suite / Ministry</strong> — 3 to 12 months depending on scope</li>
                @endif
            </ul>
            <p>{{ $isFr ? 'Notre cadre standard de déploiement en 90 jours comprend : migration des données, configuration, formation du personnel, tests d\'acceptation et go-live accompagné.' : 'Our standard 90-day deployment framework covers: data migration, configuration, staff training, acceptance testing, and accompanied go-live.' }}</p>
        </div>
    </details>

    <details class="faq-item">
        <summary class="faq-q">
            <span class="faq-q-text">{{ $isFr ? 'Quelles options de déploiement sont disponibles (cloud, on-premise, hybride) ?' : 'What deployment options are available — cloud, on-premise, or hybrid?' }}</span>
            <i data-lucide="chevron-down" class="faq-chevron" style="width:16px;height:16px"></i>
        </summary>
        <div class="faq-a">
            <p>{{ $isFr ? 'OPES supporte les trois modes de déploiement :' : 'OPES supports all three deployment modes:' }}</p>
            <ul>
                @if($isFr)
                <li><strong>Cloud hébergé</strong> — Infrastructure gérée par OPES, disponibilité cible 99,5 %, mises à jour automatiques. Idéal pour les cliniques et hôpitaux sans équipe IT.</li>
                <li><strong>On-premise</strong> — Le logiciel est installé sur vos propres serveurs. Les données de santé restent physiquement dans votre établissement. Recommandé pour les hôpitaux ayant des exigences strictes de souveraineté des données.</li>
                <li><strong>Hybride</strong> — Données cliniques sensibles on-premise, modules analytiques et de reporting dans le cloud. Solution équilibrée pour les hôpitaux généraux.</li>
                @else
                <li><strong>Cloud-hosted</strong> — Infrastructure managed by OPES, 99.5% target uptime, automatic updates. Ideal for clinics and hospitals without an IT team.</li>
                <li><strong>On-premise</strong> — Software installed on your own servers. Health data stays physically within your facility. Recommended for hospitals with strict data sovereignty requirements.</li>
                <li><strong>Hybrid</strong> — Sensitive clinical data on-premise, analytics and reporting modules in the cloud. Balanced solution for general hospitals.</li>
                @endif
            </ul>
        </div>
    </details>

    <details class="faq-item">
        <summary class="faq-q">
            <span class="faq-q-text">{{ $isFr ? 'OPES fournit-il la formation du personnel ?' : 'Does OPES provide staff training?' }}</span>
            <i data-lucide="chevron-down" class="faq-chevron" style="width:16px;height:16px"></i>
        </summary>
        <div class="faq-a">
            <p>{{ $isFr ? 'Oui. La formation fait partie intégrante de chaque déploiement et comprend :' : 'Yes. Training is a core part of every deployment and includes:' }}</p>
            <ul>
                @if($isFr)
                <li>Formation en présentiel sur site pour le personnel médical et administratif</li>
                <li>Sessions de formation par rôle (médecins, infirmiers, caissiers, administrateurs)</li>
                <li>Accès à OPES Academy — notre portail de formation en ligne avec certifications</li>
                <li>Manuels d\'utilisation en anglais et en français</li>
                <li>Sessions de rappel disponibles après le go-live</li>
                @else
                <li>On-site in-person training for medical and administrative staff</li>
                <li>Role-based training sessions (doctors, nurses, cashiers, administrators)</li>
                <li>Access to OPES Academy — our online training portal with certifications</li>
                <li>User manuals in English and French</li>
                <li>Refresher sessions available after go-live</li>
                @endif
            </ul>
        </div>
    </details>

    <details class="faq-item">
        <summary class="faq-q">
            <span class="faq-q-text">{{ $isFr ? 'Que se passe-t-il si la connexion internet est coupée ?' : 'What happens if the internet connection is lost?' }}</span>
            <i data-lucide="chevron-down" class="faq-chevron" style="width:16px;height:16px"></i>
        </summary>
        <div class="faq-a">
            <p>{{ $isFr ? 'OPES est conçu pour la réalité africaine — y compris les coupures de courant et d\'internet. Pour les déploiements cloud, un mode hors-ligne limité permet de continuer les consultations essentielles et de synchroniser les données à la reconnexion. Pour les déploiements on-premise, le système fonctionne entièrement en local — l\'internet n\'est nécessaire que pour les mises à jour et la synchronisation inter-sites.' : 'OPES is designed for African realities — including power and internet outages. For cloud deployments, a limited offline mode allows essential consultations to continue and syncs data when reconnected. For on-premise deployments, the system runs entirely locally — internet is only needed for updates and cross-site synchronization.' }}</p>
        </div>
    </details>

    <details class="faq-item">
        <summary class="faq-q">
            <span class="faq-q-text">{{ $isFr ? 'Peut-on migrer des données depuis un ancien système ?' : 'Can we migrate data from an existing system?' }}</span>
            <i data-lucide="chevron-down" class="faq-chevron" style="width:16px;height:16px"></i>
        </summary>
        <div class="faq-a">
            <p>{{ $isFr ? 'Oui. Notre équipe de migration de données prend en charge les imports depuis des systèmes existants (fichiers Excel, CSV, bases Access, d\'autres SIH ou DME) et depuis des dossiers papier numérisés. La qualité et l\'intégrité des données sont vérifiées avant le go-live. La migration est incluse dans les déploiements Silver et supérieurs.' : 'Yes. Our data migration team handles imports from existing systems (Excel files, CSV, Access databases, other HIS or EMR systems) and from digitised paper records. Data quality and integrity are verified before go-live. Migration is included in Silver-tier deployments and above.' }}</p>
        </div>
    </details>
</div>

{{-- ═══════════════════════════ 4. PRICING ═══════════════════════════ --}}
<div class="faq-section" id="pricing">
    <div class="faq-section-header">
        <div class="faq-section-icon" style="background:rgba(245,158,11,0.1)">
            <i data-lucide="tag" style="width:16px;height:16px;color:#F59E0B"></i>
        </div>
        <div class="faq-section-title">{{ $isFr ? 'Tarification & licences' : 'Pricing & Licensing' }}</div>
    </div>

    <details class="faq-item">
        <summary class="faq-q">
            <span class="faq-q-text">{{ $isFr ? 'Comment OPES est-il tarifé ?' : 'How is OPES priced?' }}</span>
            <i data-lucide="chevron-down" class="faq-chevron" style="width:16px;height:16px"></i>
        </summary>
        <div class="faq-a">
            @if($isFr)
            <p>OPES utilise un modèle de licence annuelle par établissement et par module. Les frais de licence couvrent l'utilisation du logiciel, les mises à jour, les correctifs de sécurité et le support de base. Les tarifs sont disponibles sur notre <a href="{{ url($locale.'/pricing') }}">page de tarification</a>. Pour les déploiements nationaux (Plateforme nationale, HIE, registres nationaux), veuillez nous contacter pour un devis — ces projets ont une portée trop variable pour être tarifés publiquement.</p>
            @else
            <p>OPES uses an annual licence model per facility and per module. Licence fees cover software usage, updates, security patches, and base support. Pricing is published on our <a href="{{ url($locale.'/pricing') }}">pricing page</a>. For national-scale deployments (National Platform, HIE, national registries), please contact us for a quotation — these projects vary too widely in scope for public pricing.</p>
            @endif
        </div>
    </details>

    <details class="faq-item">
        <summary class="faq-q">
            <span class="faq-q-text">{{ $isFr ? 'Y a-t-il une période d\'essai ou un pilote disponible ?' : 'Is there a trial period or pilot programme available?' }}</span>
            <i data-lucide="chevron-down" class="faq-chevron" style="width:16px;height:16px"></i>
        </summary>
        <div class="faq-a">
            <p>{{ $isFr ? 'Oui. Nous proposons un programme pilote structuré de 30 à 90 jours pour les hôpitaux et cliniques qualifiés. Durant la phase pilote, votre établissement déploie un ou plusieurs modules OPES dans un département sélectionné. Des indicateurs de performance clés sont suivis conjointement. À l\'issue du pilote, vous disposez de données réelles pour justifier un déploiement complet. Contactez notre équipe commerciale pour vérifier votre éligibilité.' : 'Yes. We offer a structured 30- to 90-day pilot programme for qualifying hospitals and clinics. During the pilot phase, your facility deploys one or more OPES modules in a selected department. Key performance indicators are tracked jointly. At the end of the pilot you have real data to justify full deployment. Contact our sales team to check your eligibility.' }}</p>
        </div>
    </details>

    <details class="faq-item">
        <summary class="faq-q">
            <span class="faq-q-text">{{ $isFr ? 'Quelle est la durée minimale d\'engagement ?' : 'What is the minimum contract term?' }}</span>
            <i data-lucide="chevron-down" class="faq-chevron" style="width:16px;height:16px"></i>
        </summary>
        <div class="faq-a">
            <p>{{ $isFr ? 'Les licences OPES sont annuelles, renouvelables chaque année. Des contrats pluriannuels (2–5 ans) sont disponibles avec des remises tarifaires. Il n\'y a pas de frais de résiliation anticipée après la première année contractuelle complète.' : 'OPES licences are annual, renewable each year. Multi-year contracts (2–5 years) are available with pricing discounts. There are no early termination fees after the first full contract year.' }}</p>
        </div>
    </details>

    <details class="faq-item">
        <summary class="faq-q">
            <span class="faq-q-text">{{ $isFr ? 'Les frais d\'implémentation et de formation sont-ils inclus dans la licence ?' : 'Are implementation and training fees included in the licence?' }}</span>
            <i data-lucide="chevron-down" class="faq-chevron" style="width:16px;height:16px"></i>
        </summary>
        <div class="faq-a">
            <p>{{ $isFr ? 'Les frais d\'implémentation (déploiement, configuration, migration de données, formation initiale) sont facturés séparément lors de la première année — c\'est ce qu\'on appelle les frais d\'installation (one-time setup fees). À partir de la deuxième année, seule la redevance annuelle de licence et de support s\'applique. Certains niveaux de support incluent des formations annuelles de rappel et des visites sur site.' : 'Implementation fees (deployment, configuration, data migration, initial training) are billed separately in the first year — these are one-time setup fees. From year two onwards, only the annual licence and support subscription applies. Certain support tiers include annual refresher training and on-site visits.' }}</p>
        </div>
    </details>

    <details class="faq-item">
        <summary class="faq-q">
            <span class="faq-q-text">{{ $isFr ? 'Des remises sont-elles disponibles pour les hôpitaux publics ou les ONG ?' : 'Are discounts available for public hospitals or NGOs?' }}</span>
            <i data-lucide="chevron-down" class="faq-chevron" style="width:16px;height:16px"></i>
        </summary>
        <div class="faq-a">
            <p>{{ $isFr ? 'Oui. OPES propose des tarifs préférentiels pour les hôpitaux du secteur public, les hôpitaux confessionnels à but non lucratif, les ONG de santé et les établissements dans les zones à ressources limitées. Des financements par des donateurs et des partenaires au développement peuvent également s\'appliquer. Contactez-nous pour discuter de votre situation spécifique.' : 'Yes. OPES offers preferential pricing for public sector hospitals, non-profit faith-based hospitals, health NGOs, and facilities in low-resource areas. Donor funding and development partner financing may also apply. Contact us to discuss your specific situation.' }}</p>
        </div>
    </details>
</div>

{{-- ═══════════════════════════ 5. INTEROPERABILITY ═══════════════════════════ --}}
<div class="faq-section" id="interoperability">
    <div class="faq-section-header">
        <div class="faq-section-icon" style="background:rgba(0,200,150,0.1)">
            <i data-lucide="share-2" style="width:16px;height:16px;color:#00C896"></i>
        </div>
        <div class="faq-section-title">{{ $isFr ? 'Interopérabilité & technique' : 'Interoperability & Technical' }}</div>
    </div>

    <details class="faq-item">
        <summary class="faq-q">
            <span class="faq-q-text">{{ $isFr ? 'OPES supporte-t-il les standards HL7 et FHIR ?' : 'Does OPES support HL7 and FHIR standards?' }}</span>
            <i data-lucide="chevron-down" class="faq-chevron" style="width:16px;height:16px"></i>
        </summary>
        <div class="faq-a">
            <p>{{ $isFr ? 'Oui. OPESCare et la couche d\'interopérabilité OPES sont construits sur les standards internationaux :' : 'Yes. OPESCare and the OPES interoperability layer are built on international standards:' }}</p>
            <ul>
                <li><strong>HL7 FHIR R4</strong> — {{ $isFr ? 'pour l\'échange de données cliniques entre systèmes' : 'for clinical data exchange between systems' }}</li>
                <li><strong>IHE Profiles</strong> — {{ $isFr ? 'PIX/PDQ pour l\'index des patients, MHD pour les documents' : 'PIX/PDQ for patient index, MHD for documents' }}</li>
                <li><strong>OpenHIE</strong> — {{ $isFr ? 'conformité avec l\'architecture de référence OpenHIE' : 'conformance with the OpenHIE reference architecture' }}</li>
                <li><strong>REST APIs</strong> — {{ $isFr ? 'API ouvertes documentées pour les intégrations tierces' : 'documented open APIs for third-party integrations' }}</li>
                <li><strong>SNOMED / ICD-10</strong> — {{ $isFr ? 'terminologies cliniques standardisées' : 'standardised clinical terminologies' }}</li>
            </ul>
        </div>
    </details>

    <details class="faq-item">
        <summary class="faq-q">
            <span class="faq-q-text">{{ $isFr ? 'OPES peut-il s\'intégrer avec des systèmes déjà en place ?' : 'Can OPES integrate with systems already in place?' }}</span>
            <i data-lucide="chevron-down" class="faq-chevron" style="width:16px;height:16px"></i>
        </summary>
        <div class="faq-a">
            <p>{{ $isFr ? 'Oui. OPESCare agit comme une couche d\'interopérabilité qui peut se connecter à des systèmes tiers via des APIs standardisées. Nous avons une expérience d\'intégration avec des SIH d\'autres éditeurs, des équipements médicaux (analyseurs de laboratoire, machines de radiologie), des systèmes de facturation tiers et des plateformes gouvernementales de rapportage. Contactez notre équipe technique pour évaluer la faisabilité d\'une intégration spécifique.' : 'Yes. OPESCare acts as an interoperability layer that can connect to third-party systems via standardised APIs. We have integration experience with HIS from other vendors, medical equipment (laboratory analysers, radiology machines), third-party billing systems, and government reporting platforms. Contact our technical team to assess the feasibility of a specific integration.' }}</p>
        </div>
    </details>

    <details class="faq-item">
        <summary class="faq-q">
            <span class="faq-q-text">{{ $isFr ? 'OPES peut-il alimenter les registres et rapports nationaux ?' : 'Can OPES feed into national registries and reports?' }}</span>
            <i data-lucide="chevron-down" class="faq-chevron" style="width:16px;height:16px"></i>
        </summary>
        <div class="faq-a">
            <p>{{ $isFr ? 'Oui. OPES Intelligence et OPESCare incluent des connecteurs pour les rapports vers les plateformes nationales de santé publique, les registres des maladies, les systèmes DHIS2 et les plateformes de surveillance épidémiologique. Les établissements utilisant OPES peuvent automatiser leur rapport mensuel obligatoire vers le Ministère de la Santé.' : 'Yes. OPES Intelligence and OPESCare include connectors for reporting to national public health platforms, disease registries, DHIS2 systems, and epidemiological surveillance platforms. Facilities using OPES can automate their mandatory monthly reporting to the Ministry of Health.' }}</p>
        </div>
    </details>

    <details class="faq-item">
        <summary class="faq-q">
            <span class="faq-q-text">{{ $isFr ? 'OPES fonctionne-t-il sur les appareils mobiles ?' : 'Does OPES work on mobile devices?' }}</span>
            <i data-lucide="chevron-down" class="faq-chevron" style="width:16px;height:16px"></i>
        </summary>
        <div class="faq-a">
            <p>{{ $isFr ? 'Les interfaces OPES sont conçues de manière responsive et fonctionnent sur tablettes et smartphones modernes via le navigateur web. Pour les agents de santé communautaires et les équipes mobiles, une application légère optimisée pour les faibles bandes passantes est disponible dans la suite OPES Intelligence. Les appareils recommandés sont les tablettes Android ou iPad pour le personnel clinique.' : 'OPES interfaces are designed responsively and work on modern tablets and smartphones via web browser. For community health workers and mobile teams, a lightweight application optimised for low bandwidth is available in the OPES Intelligence suite. Recommended devices are Android tablets or iPads for clinical staff.' }}</p>
        </div>
    </details>
</div>

{{-- ═══════════════════════════ 6. SUPPORT ═══════════════════════════ --}}
<div class="faq-section" id="support">
    <div class="faq-section-header">
        <div class="faq-section-icon" style="background:rgba(26,111,232,0.1)">
            <i data-lucide="headphones" style="width:16px;height:16px;color:#1A6FE8"></i>
        </div>
        <div class="faq-section-title">{{ $isFr ? 'Support & SLA' : 'Support & SLA' }}</div>
    </div>

    <details class="faq-item">
        <summary class="faq-q">
            <span class="faq-q-text">{{ $isFr ? 'Quels niveaux de support sont disponibles ?' : 'What support tiers are available?' }}</span>
            <i data-lucide="chevron-down" class="faq-chevron" style="width:16px;height:16px"></i>
        </summary>
        <div class="faq-a">
            <p>{{ $isFr ? 'OPES propose 4 niveaux de support :' : 'OPES offers 4 support tiers:' }}</p>
            <ul>
                @if($isFr)
                <li><strong>Bronze</strong> — E-mail, temps de réponse 72h, disponibilité cible 99,0 %. Idéal pour les petites cliniques.</li>
                <li><strong>Silver</strong> — E-mail + téléphone, réponse 24h, 99,5 %. Pour les hôpitaux de district.</li>
                <li><strong>Gold</strong> — Support prioritaire 24/7, réponse 4h, 99,9 %, gestionnaire de compte dédié. Pour les hôpitaux généraux.</li>
                <li><strong>Platinum</strong> — SLA personnalisé, ingénieur de support dédié, visites sur site mensuelles, disponibilité 99,95 %. Pour les déploiements nationaux et hospitalo-universitaires.</li>
                @else
                <li><strong>Bronze</strong> — Email, 72h response, 99.0% target uptime. Ideal for small clinics.</li>
                <li><strong>Silver</strong> — Email + phone, 24h response, 99.5%. For district hospitals.</li>
                <li><strong>Gold</strong> — 24/7 priority support, 4h response, 99.9%, dedicated account manager. For general hospitals.</li>
                <li><strong>Platinum</strong> — Custom SLA, dedicated support engineer, monthly on-site visits, 99.95% uptime. For national and teaching hospital deployments.</li>
                @endif
            </ul>
            <p><a href="{{ url($locale.'/support') }}">{{ $isFr ? 'Voir les détails complets des niveaux de support →' : 'View full support tier details →' }}</a></p>
        </div>
    </details>

    <details class="faq-item">
        <summary class="faq-q">
            <span class="faq-q-text">{{ $isFr ? 'Comment soumettre un ticket de support ?' : 'How do I submit a support ticket?' }}</span>
            <i data-lucide="chevron-down" class="faq-chevron" style="width:16px;height:16px"></i>
        </summary>
        <div class="faq-a">
            <p>{{ $isFr ? 'Les clients avec un compte actif peuvent soumettre des tickets depuis le portail client OPES (accès via votre identifiant fourni lors du déploiement). Les tickets peuvent aussi être envoyés par e-mail à <a href="mailto:support@opeshealthsystems.com">support@opeshealthsystems.com</a> ou par téléphone pour les niveaux Silver, Gold et Platinum. Les urgences critiques (système hors service) bénéficient d\'une ligne prioritaire disponible pour Gold et Platinum.' : 'Customers with an active account can submit tickets from the OPES customer portal (access via your login provided at deployment). Tickets can also be sent by email to <a href="mailto:support@opeshealthsystems.com">support@opeshealthsystems.com</a> or by phone for Silver, Gold, and Platinum tiers. Critical emergencies (system down) benefit from a priority hotline available for Gold and Platinum.' }}</p>
        </div>
    </details>

    <details class="faq-item">
        <summary class="faq-q">
            <span class="faq-q-text">{{ $isFr ? 'Les mises à jour logicielles sont-elles incluses ?' : 'Are software updates included?' }}</span>
            <i data-lucide="chevron-down" class="faq-chevron" style="width:16px;height:16px"></i>
        </summary>
        <div class="faq-a">
            <p>{{ $isFr ? 'Oui. Toutes les mises à jour de sécurité et correctifs critiques sont inclus dans tous les niveaux de support. Les mises à jour de fonctionnalités (nouvelles versions majeures) sont incluses à partir du niveau Silver. Pour les déploiements cloud, les mises à jour sont appliquées automatiquement pendant les fenêtres de maintenance planifiées. Pour les déploiements on-premise, les mises à jour sont packagées et livrées selon le calendrier convenu.' : 'Yes. All security updates and critical patches are included at all support tiers. Feature updates (new major versions) are included from Silver tier upwards. For cloud deployments, updates are applied automatically during scheduled maintenance windows. For on-premise deployments, updates are packaged and delivered on the agreed schedule.' }}</p>
        </div>
    </details>

    <details class="faq-item">
        <summary class="faq-q">
            <span class="faq-q-text">{{ $isFr ? 'OPES fournit-il un support pendant les jours fériés au Cameroun ?' : 'Does OPES provide support on Cameroonian public holidays?' }}</span>
            <i data-lucide="chevron-down" class="faq-chevron" style="width:16px;height:16px"></i>
        </summary>
        <div class="faq-a">
            <p>{{ $isFr ? 'Le support Bronze et Silver est disponible pendant les jours ouvrés camerounais (lundi–vendredi, 08h00–18h00 WAT, hors jours fériés). Le support Gold offre une couverture étendue incluant les samedis. Le support Platinum assure une couverture 24h/24, 7j/7, 365 jours par an, y compris tous les jours fériés.' : 'Bronze and Silver support is available during Cameroonian business days (Monday–Friday, 08:00–18:00 WAT, excluding public holidays). Gold support offers extended coverage including Saturdays. Platinum support provides 24/7/365 coverage including all public holidays.' }}</p>
        </div>
    </details>
</div>

{{-- ═══════════════════════════ 7. DATA & SECURITY ═══════════════════════════ --}}
<div class="faq-section" id="security">
    <div class="faq-section-header">
        <div class="faq-section-icon" style="background:rgba(168,85,247,0.1)">
            <i data-lucide="shield" style="width:16px;height:16px;color:#A855F7"></i>
        </div>
        <div class="faq-section-title">{{ $isFr ? 'Données & sécurité' : 'Data & Security' }}</div>
    </div>

    <details class="faq-item">
        <summary class="faq-q">
            <span class="faq-q-text">{{ $isFr ? 'Comment les données des patients sont-elles protégées ?' : 'How is patient data protected?' }}</span>
            <i data-lucide="chevron-down" class="faq-chevron" style="width:16px;height:16px"></i>
        </summary>
        <div class="faq-a">
            <p>{{ $isFr ? 'OPES applique des mesures de sécurité de niveau entreprise :' : 'OPES applies enterprise-grade security measures:' }}</p>
            <ul>
                @if($isFr)
                <li>Chiffrement en transit (TLS 1.3) et au repos (AES-256)</li>
                <li>Contrôle d\'accès basé sur les rôles (RBAC) — chaque utilisateur voit uniquement ce qu\'il est autorisé à voir</li>
                <li>Journaux d\'audit complets de toutes les actions sur les données de patients</li>
                <li>Authentification à deux facteurs (2FA) disponible pour tous les comptes</li>
                <li>Évaluations de sécurité régulières et tests de pénétration</li>
                <li>Sauvegardes automatiques quotidiennes avec rétention configurable</li>
                @else
                <li>Encryption in transit (TLS 1.3) and at rest (AES-256)</li>
                <li>Role-Based Access Control (RBAC) — each user sees only what they are authorised to see</li>
                <li>Complete audit logs of all actions on patient data</li>
                <li>Two-factor authentication (2FA) available for all accounts</li>
                <li>Regular security assessments and penetration testing</li>
                <li>Automatic daily backups with configurable retention</li>
                @endif
            </ul>
        </div>
    </details>

    <details class="faq-item">
        <summary class="faq-q">
            <span class="faq-q-text">{{ $isFr ? 'Qui est propriétaire des données de l\'établissement ?' : 'Who owns the facility\'s data?' }}</span>
            <i data-lucide="chevron-down" class="faq-chevron" style="width:16px;height:16px"></i>
        </summary>
        <div class="faq-a">
            <p>{{ $isFr ? 'Votre établissement est et reste l\'unique propriétaire de toutes ses données. OPES agit exclusivement en tant que sous-traitant traitant les données pour votre compte, selon vos instructions. En aucun cas OPES ne vend, partage à des fins commerciales ou exploite les données de patients de votre établissement. En cas de résiliation du contrat, vous recevez une exportation complète de vos données dans un format standard.' : 'Your facility is and remains the sole owner of all its data. OPES acts exclusively as a data processor handling data on your behalf, under your instructions. Under no circumstances does OPES sell, commercially share, or exploit patient data from your facility. In the event of contract termination, you receive a full export of your data in a standard format.' }}</p>
        </div>
    </details>

    <details class="faq-item">
        <summary class="faq-q">
            <span class="faq-q-text">{{ $isFr ? 'OPES est-il conforme aux réglementations locales sur les données de santé ?' : 'Is OPES compliant with local health data regulations?' }}</span>
            <i data-lucide="chevron-down" class="faq-chevron" style="width:16px;height:16px"></i>
        </summary>
        <div class="faq-a">
            <p>{{ $isFr ? 'Oui. OPES est conçu pour la conformité avec :' : 'Yes. OPES is designed for compliance with:' }}</p>
            <ul>
                @if($isFr)
                <li>La loi camerounaise n° 2010/012 relative à la cybersécurité et à la cybercriminalité</li>
                <li>Les obligations de confidentialité médicale selon le Code de déontologie médicale du Cameroun</li>
                <li>Les Actes uniformes OHADA régissant les données commerciales</li>
                <li>Les directives de l\'Union africaine sur la protection des données personnelles</li>
                @else
                <li>Cameroonian Law No. 2010/012 on Cybersecurity and Cybercrime</li>
                <li>Medical confidentiality obligations under the Cameroon Medical Code of Ethics</li>
                <li>OHADA Uniform Acts governing commercial data</li>
                <li>African Union guidelines on personal data protection</li>
                @endif
            </ul>
            <p><a href="{{ url($locale.'/compliance') }}">{{ $isFr ? 'Voir la page Conformité & confiance →' : 'View Compliance & Trust page →' }}</a></p>
        </div>
    </details>

    <details class="faq-item">
        <summary class="faq-q">
            <span class="faq-q-text">{{ $isFr ? 'OPES peut-il fonctionner sans stocker les données dans le cloud ?' : 'Can OPES operate without storing data in the cloud?' }}</span>
            <i data-lucide="chevron-down" class="faq-chevron" style="width:16px;height:16px"></i>
        </summary>
        <div class="faq-a">
            <p>{{ $isFr ? 'Oui. Notre option de déploiement on-premise permet à l\'ensemble du système de fonctionner sur vos propres serveurs, dans vos propres locaux. Aucune donnée de patient ne quitte votre réseau local. Cette option est particulièrement adaptée aux hôpitaux militaires, aux hôpitaux universitaires et aux établissements à exigences réglementaires strictes.' : 'Yes. Our on-premise deployment option allows the entire system to run on your own servers, in your own premises. No patient data ever leaves your local network. This option is particularly suitable for military hospitals, university hospitals, and facilities with strict regulatory requirements.' }}</p>
        </div>
    </details>
</div>

{{-- ═══════════════════════════ 8. PARTNERSHIP ═══════════════════════════ --}}
<div class="faq-section" id="partnership">
    <div class="faq-section-header">
        <div class="faq-section-icon" style="background:rgba(0,200,150,0.1)">
            <i data-lucide="handshake" style="width:16px;height:16px;color:#00C896"></i>
        </div>
        <div class="faq-section-title">{{ $isFr ? 'Partenariat & programme partenaires' : 'Partnership & Partner Programme' }}</div>
    </div>

    <details class="faq-item">
        <summary class="faq-q">
            <span class="faq-q-text">{{ $isFr ? 'Comment devenir partenaire OPES ?' : 'How do I become an OPES partner?' }}</span>
            <i data-lucide="chevron-down" class="faq-chevron" style="width:16px;height:16px"></i>
        </summary>
        <div class="faq-a">
            @if($isFr)
            <p>OPES accueille des partenaires dans plusieurs catégories : revendeurs et intégrateurs, partenaires hospitaliers cliniques, partenaires technologiques, établissements de recherche et académiques, ONG et organismes gouvernementaux. Visitez notre <a href="{{ url($locale.'/partner-program') }}">programme partenaires</a> pour postuler ou contacter notre équipe partenariale à <a href="mailto:partners@opeshealthsystems.com">partners@opeshealthsystems.com</a>.</p>
            @else
            <p>OPES welcomes partners across several categories: resellers and integrators, clinical hospital partners, technology partners, research and academic institutions, NGOs, and government bodies. Visit our <a href="{{ url($locale.'/partner-program') }}">partner programme page</a> to apply or contact our partnership team at <a href="mailto:partners@opeshealthsystems.com">partners@opeshealthsystems.com</a>.</p>
            @endif
        </div>
    </details>

    <details class="faq-item">
        <summary class="faq-q">
            <span class="faq-q-text">{{ $isFr ? 'Des cliniciens peuvent-ils tester OPES avant un déploiement officiel ?' : 'Can clinicians test OPES before a formal deployment?' }}</span>
            <i data-lucide="chevron-down" class="faq-chevron" style="width:16px;height:16px"></i>
        </summary>
        <div class="faq-a">
            @if($isFr)
            <p>Oui, via notre Programme praticien. Des médecins, infirmiers et professionnels de santé qualifiés peuvent accéder à des environnements de démonstration guidés, participer à des programmes de test structurés et soumettre des retours formels. Les praticiens participants reçoivent des certifications OPES et contribuent directement à l'amélioration des produits. Postulez sur notre <a href="{{ url($locale.'/practitioners') }}">page praticiens</a>.</p>
            @else
            <p>Yes, through our Practitioner Programme. Qualified doctors, nurses, and health professionals can access guided demo environments, participate in structured testing programmes, and submit formal feedback. Participating practitioners receive OPES certifications and directly contribute to product improvement. Apply on our <a href="{{ url($locale.'/practitioners') }}">practitioners page</a>.</p>
            @endif
        </div>
    </details>

    <details class="faq-item">
        <summary class="faq-q">
            <span class="faq-q-text">{{ $isFr ? 'OPES est-il ouvert à des investisseurs ?' : 'Is OPES open to investors?' }}</span>
            <i data-lucide="chevron-down" class="faq-chevron" style="width:16px;height:16px"></i>
        </summary>
        <div class="faq-a">
            <p>{{ $isFr ? 'Oui. OPES est activement à la recherche d\'investisseurs stratégiques alignés avec notre mission : construire l\'infrastructure de santé numérique de l\'Afrique. Nous accueillons les fonds de capital-risque orientés impact, les fonds de dotation, les investisseurs institutionnels et les partenaires de développement (Banque mondiale, AFD, USAID, etc.). Pour toute demande d\'investissement, veuillez contacter notre direction à <a href="mailto:invest@opeshealthsystems.com">invest@opeshealthsystems.com</a>.' : 'Yes. OPES is actively seeking strategic investors aligned with our mission: building Africa\'s digital health infrastructure. We welcome impact-oriented venture funds, endowment funds, institutional investors, and development partners (World Bank, AFD, USAID, etc.). For investment enquiries, please contact our leadership at <a href="mailto:invest@opeshealthsystems.com">invest@opeshealthsystems.com</a>.' }}</p>
        </div>
    </details>

    <details class="faq-item">
        <summary class="faq-q">
            <span class="faq-q-text">{{ $isFr ? 'OPES propose-t-il des API pour les développeurs tiers ?' : 'Does OPES offer APIs for third-party developers?' }}</span>
            <i data-lucide="chevron-down" class="faq-chevron" style="width:16px;height:16px"></i>
        </summary>
        <div class="faq-a">
            <p>{{ $isFr ? 'Oui. OPES expose un ensemble d\'API REST documentées permettant aux partenaires technologiques d\'intégrer leurs solutions à l\'écosystème OPES. Les cas d\'usage typiques incluent : équipements de diagnostic (analyseurs de labo, machines de radio), applications de télémedecine, portails d\'assurance et solutions gouvernementales. Rejoignez notre programme de partenariat technologique pour accéder à la documentation développeur et à l\'environnement sandbox.' : 'Yes. OPES exposes a set of documented REST APIs allowing technology partners to integrate their solutions into the OPES ecosystem. Typical use cases include: diagnostic equipment (lab analysers, radiology machines), telemedicine applications, insurance portals, and government solutions. Join our technology partner programme to access developer documentation and the sandbox environment.' }}</p>
        </div>
    </details>
</div>

{{-- ═══════════════════════════ CTA ═══════════════════════════ --}}
<div class="faq-cta">
    <i data-lucide="message-circle" style="width:32px;height:32px;color:#00C896;margin-bottom:12px"></i>
    <h2>{{ $isFr ? 'Votre question n\'est pas ici ?' : 'Your question isn\'t listed here?' }}</h2>
    <p>{{ $isFr ? 'Notre équipe répond à toutes les questions — sur les produits, les déploiements, les prix ou les partenariats. Écrivez-nous ou réservez une démonstration.' : 'Our team answers every question — about products, deployments, pricing, or partnerships. Write to us or book a demo.' }}</p>
    <div class="faq-cta-btns">
        <a href="{{ url($locale.'/contact') }}" class="btn-primary">
            <i data-lucide="mail" style="width:15px;height:15px"></i>
            {{ $isFr ? 'Contactez-nous' : 'Contact Us' }}
        </a>
        <a href="{{ url($locale.'/contact') }}" class="btn-secondary">
            <i data-lucide="calendar-check" style="width:15px;height:15px;color:#00C896"></i>
            {{ $isFr ? 'Réserver une démo' : 'Book a Demo' }}
        </a>
    </div>
</div>

</div>{{-- .faq-body --}}

</x-layouts.app>

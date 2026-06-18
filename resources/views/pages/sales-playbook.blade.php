@php $locale = app()->getLocale(); $isFr = $locale === 'fr'; @endphp

<x-layouts.app
    title="{{ $isFr ? 'Guide de vente enterprise — OPES Health Systems' : 'Enterprise Sales Playbook — OPES Health Systems' }}"
    description="{{ $isFr ? 'Méthodologie commerciale OPES : 7 étapes de vente, 6 segments clients, cadre de qualification, gestion des objections et stratégie de proposition.' : 'OPES commercial methodology: 7-stage sales process, 6 customer segments, qualification framework, objection handling, and proposal strategy.' }}">

{{-- ── HERO ──────────────────────────────────────────────────────── --}}
<div class="about-hero">
    <div class="section-label" style="justify-content:center;margin-bottom:16px">
        <i data-lucide="lock" style="width:12px;height:12px;color:#F59E0B"></i>
        {{ $isFr ? 'Document commercial confidentiel v1.0' : 'Confidential commercial document v1.0' }}
    </div>
    <h1 class="about-title">
        {{ $isFr ? 'Guide de vente' : 'Enterprise' }}
        <span class="gradient-text">{{ $isFr ? 'Enterprise OPES' : 'Sales Playbook' }}</span>
    </h1>
    <p class="about-sub" style="max-width:720px">
        {{ $isFr
            ? 'La méthodologie commerciale, le processus d\'engagement client, le cadre de qualification, le flux de proposition, l\'approche de négociation et le modèle de gestion de compte pour OPES Health Systems.'
            : 'The commercial strategy, sales methodology, customer engagement process, qualification framework, proposal workflow, negotiation approach, and account management model for OPES Health Systems.' }}
    </p>
    <div style="display:flex;gap:10px;justify-content:center;flex-wrap:wrap;margin-top:20px">
        @foreach($isFr
            ? ['Représentants commerciaux','Responsables de compte','Développement commercial','Partenaires','Consultants','Direction']
            : ['Sales Representatives','Account Managers','Business Development','Channel Partners','Consultants','Executive Leadership']
        as $chip)
        <span style="background:#F59E0B0D;border:1px solid #F59E0B20;border-radius:20px;padding:5px 12px;font-size:11px;color:#F59E0B">{{ $chip }}</span>
        @endforeach
    </div>
</div>

{{-- ── STATS ─────────────────────────────────────────────────────── --}}
<div class="section" style="text-align:center">
    <div class="stats-bar" style="max-width:960px;margin:0 auto">
        @foreach($isFr
            ? [['7','Étapes de vente'],['6','Segments clients'],['4','Objections gérées'],['7','KPIs commerciaux']]
            : [['7','Sales stages'],['6','Customer segments'],['4','Objection responses'],['7','Sales KPIs']]
        as $s)
        <div class="stat-item">
            <div class="stat-value">{{ $s[0] }}</div>
            <div class="stat-label">{{ $s[1] }}</div>
        </div>
        @endforeach
    </div>
</div>

<div class="divider"></div>

{{-- ── SALES PHILOSOPHY ─────────────────────────────────────────── --}}
<div class="section" style="max-width:960px;margin:0 auto">
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:40px;align-items:start">
        <div>
            <div class="section-label" style="margin-bottom:16px">
                <i data-lucide="compass" style="width:12px;height:12px"></i>
                {{ $isFr ? 'Philosophie commerciale' : 'Sales philosophy' }}
            </div>
            <h2 class="section-title" style="font-size:clamp(18px,2.5vw,26px)">{{ $isFr ? 'OPES ne vend pas de logiciels.' : 'OPES does not sell software.' }}</h2>
            <p style="font-size:13px;color:#64748b;line-height:1.7;margin-top:12px">
                {{ $isFr
                    ? 'Chaque conversation commerciale commence par la valeur patient et la transformation organisationnelle — pas par les fonctionnalités ou le prix.'
                    : 'Every commercial conversation starts with patient value and organisational transformation — not features or price.' }}
            </p>
        </div>
        <div>
            <div style="font-size:11px;font-weight:600;color:#64748b;text-transform:uppercase;letter-spacing:0.06em;margin-bottom:12px">{{ $isFr ? 'OPES vend :' : 'OPES sells:' }}</div>
            @foreach($isFr
                ? [['heart-pulse','#00C896','Meilleurs soins patients'],['trending-up','#1A6FE8','Efficacité opérationnelle'],['dollar-sign','#00C896','Optimisation des revenus'],['zap','#1A6FE8','Transformation numérique'],['share-2','#00C896','Interopérabilité'],['cpu','#1A6FE8','Intelligence en santé'],['building-2','#00C896','Infrastructure de santé nationale']]
                : [['heart-pulse','#00C896','Better patient care'],['trending-up','#1A6FE8','Operational efficiency'],['dollar-sign','#00C896','Revenue optimisation'],['zap','#1A6FE8','Digital transformation'],['share-2','#00C896','Interoperability'],['cpu','#1A6FE8','Healthcare intelligence'],['building-2','#00C896','National health infrastructure']]
            as $v)
            <div style="display:flex;align-items:center;gap:10px;padding:9px 12px;background:#0F172A;border-radius:8px;border:1px solid #1e293b;margin-bottom:6px">
                <i data-lucide="{{ $v[0] }}" style="width:13px;height:13px;color:{{ $v[1] }};flex-shrink:0"></i>
                <span style="font-size:12px;color:#e2e8f0;font-weight:600">{{ $v[2] }}</span>
            </div>
            @endforeach
        </div>
    </div>
</div>

<div class="divider"></div>

{{-- ── CUSTOMER SEGMENTS ─────────────────────────────────────────── --}}
<div class="section" style="max-width:960px;margin:0 auto">
    <div class="section-label" style="margin-bottom:16px">
        <i data-lucide="users" style="width:12px;height:12px"></i>
        {{ $isFr ? 'Segments clients' : 'Customer segments' }}
    </div>
    <h2 class="section-title">{{ $isFr ? 'Six segments cibles, une approche sur mesure' : 'Six target segments, one tailored approach' }}</h2>
    @php $segments = $isFr ? [
        ['activity','#00C896','1','Cliniques privées',
            ['Propriétaire','Directeur médical','Gestionnaire de cabinet'],
            ['DME','Facturation','Rendez-vous','Pharmacie'],
            ['OPES Clinic']],
        ['stethoscope','#1A6FE8','2','Centres médicaux',
            ['Directeur médical','Administrateur'],
            ['DME','Laboratoire','Pharmacie','Rapports'],
            ['OPES Clinic','OPES Lab IS','OPES Pharmacy IS']],
        ['hospital','#00C896','3','Hôpitaux',
            ['DG','DAF','Directeur médical'],
            ['HIS','DME','Facturation','Inventaire','Labo','Radiologie'],
            ['OPES Hospital','OPES Specialty Suite']],
        ['network','#1A6FE8','4','Groupes hospitaliers',
            ['PDG','DSI','Directeur médical'],
            ['Interopérabilité','Rapports centraux','Gestion multi-sites'],
            ['OPES Health OS','OPES Care']],
        ['shield-check','#00C896','5','Organisations d\'assurance',
            ['DG','Actuaire','DSI'],
            ['Sinistres','Gestion bénéficiaires','Analytique'],
            ['OPES Insurance','OPES Care']],
        ['building-2','#1A6FE8','6','Gouvernements',
            ['Ministère de la Santé','Agences nationales'],
            ['Health ID national','HIE','CSU','Registres','Surveillance'],
            ['OPES National Digital Health Platform']],
    ] : [
        ['activity','#00C896','1','Private Clinics',
            ['Owner','Medical Director','Practice Manager'],
            ['EMR','Billing','Appointments','Pharmacy'],
            ['OPES Clinic']],
        ['stethoscope','#1A6FE8','2','Medical Centers',
            ['Medical Director','Administrator'],
            ['EMR','Laboratory','Pharmacy','Reporting'],
            ['OPES Clinic','OPES Lab IS','OPES Pharmacy IS']],
        ['hospital','#00C896','3','Hospitals',
            ['CEO','CFO','Medical Director'],
            ['HIS','EMR','Billing','Inventory','Lab','Radiology'],
            ['OPES Hospital','OPES Specialty Suite']],
        ['network','#1A6FE8','4','Hospital Groups',
            ['CEO','CIO','Medical Director'],
            ['Interoperability','Central reporting','Multi-site management'],
            ['OPES Health OS','OPES Care']],
        ['shield-check','#00C896','5','Insurance Organisations',
            ['CEO','Actuary','CIO'],
            ['Claims','Beneficiary management','Analytics'],
            ['OPES Insurance','OPES Care']],
        ['building-2','#1A6FE8','6','Governments',
            ['Ministry of Health','National agencies'],
            ['National Health ID','HIE','UHC','Registries','Surveillance'],
            ['OPES National Digital Health Platform']],
    ]; @endphp
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:14px;margin-top:28px">
        @foreach($segments as $seg)
        <div style="background:#0F172A;border:1px solid #1e293b;border-radius:14px;padding:20px;position:relative;overflow:hidden">
            <div style="position:absolute;top:10px;right:14px;font-size:32px;font-weight:900;color:{{ $seg[2] === '1' || $seg[2] === '3' || $seg[2] === '5' ? '#00C896' : '#1A6FE8' }}06;line-height:1;pointer-events:none">{{ $seg[2] }}</div>
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:14px">
                <div style="width:34px;height:34px;border-radius:9px;background:{{ $seg[3] }}15;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    <i data-lucide="{{ $seg[0] }}" style="width:15px;height:15px;color:{{ $seg[3] }}"></i>
                </div>
                <div style="font-weight:700;color:#e2e8f0;font-size:13px">{{ $seg[4] }}</div>
            </div>
            <div style="font-size:10px;font-weight:600;color:#475569;text-transform:uppercase;letter-spacing:0.06em;margin-bottom:5px">{{ $isFr ? 'Décideurs' : 'Decision makers' }}</div>
            <div style="display:flex;flex-wrap:wrap;gap:4px;margin-bottom:12px">
                @foreach($seg[5] as $dm)
                <span style="background:#1e293b;border-radius:12px;padding:2px 8px;font-size:10px;color:#64748b">{{ $dm }}</span>
                @endforeach
            </div>
            <div style="font-size:10px;font-weight:600;color:#475569;text-transform:uppercase;letter-spacing:0.06em;margin-bottom:5px">{{ $isFr ? 'Besoins' : 'Primary needs' }}</div>
            <div style="display:flex;flex-wrap:wrap;gap:4px;margin-bottom:12px">
                @foreach($seg[6] as $need)
                <span style="background:{{ $seg[3] }}0D;border:1px solid {{ $seg[3] }}20;border-radius:12px;padding:2px 8px;font-size:10px;color:{{ $seg[3] }}">{{ $need }}</span>
                @endforeach
            </div>
            <div style="font-size:10px;font-weight:600;color:#475569;text-transform:uppercase;letter-spacing:0.06em;margin-bottom:5px">{{ $isFr ? 'Produits recommandés' : 'Recommended products' }}</div>
            @foreach($seg[7] as $prod)
            <div style="font-size:11px;color:#94a3b8;padding:2px 0">
                <i data-lucide="chevron-right" style="width:9px;height:9px;color:{{ $seg[3] }};margin-right:4px"></i>{{ $prod }}
            </div>
            @endforeach
        </div>
        @endforeach
    </div>
</div>

<div class="divider"></div>

{{-- ── 7-STAGE SALES PROCESS ────────────────────────────────────── --}}
<div class="section" style="max-width:960px;margin:0 auto">
    <div class="section-label" style="margin-bottom:16px">
        <i data-lucide="arrow-right-circle" style="width:12px;height:12px"></i>
        {{ $isFr ? 'Processus de vente' : 'Sales process' }}
    </div>
    <h2 class="section-title">{{ $isFr ? 'Méthodologie de vente en 7 étapes' : '7-stage sales methodology' }}</h2>

    {{-- Horizontal flow --}}
    <div style="position:relative;margin-top:36px;overflow-x:auto;padding-bottom:8px">
        <div style="display:flex;gap:0;min-width:700px;align-items:flex-start">
            @php $stages = $isFr ? [
                ['target','#00C896','1','Génération de leads','Site web · Référencements · Conférences · Visites terrain · Appels d\'offres · Partenaires'],
                ['filter','#1A6FE8','2','Qualification','Problème · Décideur · Budget · Délai · Systèmes existants · Parrainage exécutif'],
                ['search','#00C896','3','Découverte','Workflows cliniques · Défis opérationnels · Exigences de reporting · Infrastructure → Rapport de découverte'],
                ['monitor','#1A6FE8','4','Démonstration','Démontrer uniquement les flux pertinents. Ne pas montrer toutes les fonctionnalités.'],
                ['file-text','#00C896','5','Proposition','Résumé exécutif · Problème · Solution · Architecture · Tarification · Plan d\'implémentation · ROI'],
                ['scale','#1A6FE8','6','Négociation','Concentrer sur la valeur, non le prix. Sur les résultats, non les fonctionnalités. Sur la transformation.'],
                ['check-circle','#00C896','7','Contrat & Passation','Contrat signé · Charte projet · Plan d\'implémentation · Réunion de lancement'],
            ] : [
                ['target','#00C896','1','Lead Generation','Website · Referrals · Conferences · Hospital visits · Procurement notices · Partner referrals'],
                ['filter','#1A6FE8','2','Qualification','Problem · Decision maker · Budget · Timeline · Existing systems · Executive sponsorship'],
                ['search','#00C896','3','Discovery','Clinical workflows · Operational challenges · Reporting requirements · Infrastructure → Discovery Report'],
                ['monitor','#1A6FE8','4','Demonstration','Demonstrate only relevant workflows. Do not show every feature.'],
                ['file-text','#00C896','5','Proposal Development','Executive summary · Problem · Solution · Architecture · Pricing · Implementation plan · ROI'],
                ['scale','#1A6FE8','6','Negotiation','Focus on value, not price. On outcomes, not features. On transformation.'],
                ['check-circle','#00C896','7','Contract & Handover','Signed contract · Project charter · Implementation plan · Kickoff meeting'],
            ]; @endphp
            @foreach($stages as $idx => $st)
            <div style="display:flex;align-items:flex-start;flex:1;min-width:0">
                <div style="flex:1;min-width:0">
                    <div style="background:#0F172A;border:1px solid {{ $st[1] }}30;border-top:2px solid {{ $st[1] }};border-radius:10px;padding:14px 12px;text-align:center">
                        <div style="width:32px;height:32px;border-radius:50%;background:{{ $st[1] }}15;display:flex;align-items:center;justify-content:center;margin:0 auto 8px">
                            <i data-lucide="{{ $st[0] }}" style="width:14px;height:14px;color:{{ $st[1] }}"></i>
                        </div>
                        <div style="font-size:9px;font-weight:800;color:{{ $st[1] }};text-transform:uppercase;letter-spacing:0.08em;margin-bottom:3px">{{ $isFr ? 'Étape' : 'Stage' }} {{ $st[2] }}</div>
                        <div style="font-size:11px;font-weight:700;color:#e2e8f0;margin-bottom:6px;line-height:1.3">{{ $st[3] }}</div>
                        <div style="font-size:10px;color:#475569;line-height:1.5">{{ $st[4] }}</div>
                    </div>
                </div>
                @if($idx < count($stages) - 1)
                <div style="flex-shrink:0;display:flex;align-items:center;padding:0 2px;margin-top:40px">
                    <i data-lucide="chevron-right" style="width:12px;height:12px;color:#1e293b"></i>
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
</div>

<div class="divider"></div>

{{-- ── DEMO SCRIPTS + DISCOVERY QUESTIONNAIRE (2-COL) ─────────── --}}
<div class="section" style="max-width:960px;margin:0 auto">
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:40px">
        {{-- Demo scripts --}}
        <div>
            <div class="section-label" style="margin-bottom:16px">
                <i data-lucide="monitor" style="width:12px;height:12px"></i>
                {{ $isFr ? 'Scripts de démonstration' : 'Demonstration scripts' }}
            </div>
            <h3 style="font-size:15px;font-weight:700;color:#e2e8f0;margin-bottom:16px">{{ $isFr ? 'Adapter la démo au segment' : 'Adapt the demo to the segment' }}</h3>
            @foreach($isFr
                ? [['activity','#00C896','Démo Clinique',['Enregistrement','Consultation','Prescription','Facturation']],
                   ['hospital','#1A6FE8','Démo Hôpital',['Enregistrement','Admissions','Laboratoire','Pharmacie','Facturation','Reporting']],
                   ['building-2','#00C896','Démo Gouvernement',['Health ID','HIE','Registres','Analytique','Tableaux de bord']]]
                : [['activity','#00C896','Clinic Demo',['Registration','Consultation','Prescription','Billing']],
                   ['hospital','#1A6FE8','Hospital Demo',['Registration','Admissions','Laboratory','Pharmacy','Billing','Reporting']],
                   ['building-2','#00C896','Government Demo',['Health ID','HIE','Registries','Analytics','Dashboards']]]
            as $demo)
            <div style="background:#0F172A;border:1px solid #1e293b;border-radius:10px;padding:14px;margin-bottom:10px">
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:8px">
                    <i data-lucide="{{ $demo[0] }}" style="width:13px;height:13px;color:{{ $demo[1] }}"></i>
                    <span style="font-size:12px;font-weight:700;color:#e2e8f0">{{ $demo[2] }}</span>
                </div>
                <div style="display:flex;flex-wrap:wrap;gap:4px">
                    @foreach($demo[3] as $step)
                    <span style="background:{{ $demo[1] }}10;border:1px solid {{ $demo[1] }}20;border-radius:12px;padding:2px 8px;font-size:10px;color:{{ $demo[1] }}">{{ $step }}</span>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
        {{-- Discovery Questionnaire --}}
        <div>
            <div class="section-label" style="margin-bottom:16px">
                <i data-lucide="search" style="width:12px;height:12px"></i>
                {{ $isFr ? 'Questionnaire de découverte' : 'Discovery questionnaire' }}
            </div>
            <h3 style="font-size:15px;font-weight:700;color:#e2e8f0;margin-bottom:16px">{{ $isFr ? '4 dimensions clés à explorer' : '4 key dimensions to explore' }}</h3>
            @foreach($isFr
                ? [['heart-pulse','#00C896','Clinique',['Gestion des dossiers patients aujourd\'hui ?','Gestion des prescriptions ?','Livraison des résultats de labo ?','Gestion des référencements ?']],
                   ['trending-up','#1A6FE8','Opérationnel',['Patients vus par jour ?','Nombre de départements ?','Utilisateurs nécessitant un accès ?']],
                   ['dollar-sign','#00C896','Financier',['Budget technologique annuel ?','Gestion des sinistres ?','Suivi des paiements ?']],
                   ['server','#1A6FE8','Technique',['Disponibilité Internet ?','Disponibilité serveur ?','Systèmes logiciels actuels ?']]]
                : [['heart-pulse','#00C896','Clinical',['How are patient records managed today?','How are prescriptions managed?','How are lab results delivered?','How are referrals handled?']],
                   ['trending-up','#1A6FE8','Operational',['How many patients seen daily?','How many departments exist?','How many users require access?']],
                   ['dollar-sign','#00C896','Financial',['Annual technology budget?','How are claims managed?','How are payments tracked?']],
                   ['server','#1A6FE8','Technical',['Internet availability?','Server availability?','Current software systems?']]]
            as $q)
            <div style="margin-bottom:12px">
                <div style="display:flex;align-items:center;gap:7px;margin-bottom:6px">
                    <i data-lucide="{{ $q[0] }}" style="width:11px;height:11px;color:{{ $q[1] }}"></i>
                    <span style="font-size:11px;font-weight:700;color:{{ $q[1] }};text-transform:uppercase;letter-spacing:0.05em">{{ $q[2] }}</span>
                </div>
                @foreach($q[3] as $question)
                <div style="font-size:11px;color:#64748b;padding:3px 0 3px 18px;border-left:1px solid {{ $q[1] }}20;margin-left:5px">{{ $question }}</div>
                @endforeach
            </div>
            @endforeach
        </div>
    </div>
</div>

<div class="divider"></div>

{{-- ── ELEVATOR PITCH ────────────────────────────────────────────── --}}
<div class="section" style="max-width:760px;margin:0 auto;text-align:center">
    <div class="section-label" style="justify-content:center;margin-bottom:16px">
        <i data-lucide="mic" style="width:12px;height:12px"></i>
        {{ $isFr ? 'Pitch ascenseur' : 'Elevator pitch' }}
    </div>
    <h2 class="section-title">{{ $isFr ? '30 secondes pour convaincre' : '30 seconds to convince' }}</h2>
    <div style="background:#0F172A;border:1px solid #00C89630;border-radius:16px;padding:28px 32px;margin-top:24px;text-align:left">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px">
            <i data-lucide="quote" style="width:20px;height:20px;color:#00C896;opacity:0.5"></i>
            <span style="font-size:10px;font-weight:700;color:#00C896;text-transform:uppercase;letter-spacing:0.08em">30 {{ $isFr ? 'secondes' : 'seconds' }}</span>
        </div>
        <p style="font-size:15px;color:#e2e8f0;line-height:1.75;font-style:italic">
            {{ $isFr
                ? '"OPES Health Systems fournit une infrastructure de santé numérique interopérable qui permet aux prestataires de soins, hôpitaux, assureurs et gouvernements de numériser la prestation de soins, d\'améliorer les résultats patients, de renforcer l\'efficacité opérationnelle et de soutenir des écosystèmes de santé connectés grâce à OPES Health OS."'
                : '"OPES Health Systems provides interoperable digital health infrastructure that enables healthcare providers, hospitals, insurers, and governments to digitise healthcare delivery, improve patient outcomes, strengthen operational efficiency, and support connected healthcare ecosystems through OPES Health OS."' }}
        </p>
    </div>
</div>

<div class="divider"></div>

{{-- ── OBJECTION HANDLING ────────────────────────────────────────── --}}
<div class="section" style="max-width:960px;margin:0 auto">
    <div class="section-label" style="margin-bottom:16px">
        <i data-lucide="message-square" style="width:12px;height:12px"></i>
        {{ $isFr ? 'Gestion des objections' : 'Objection handling' }}
    </div>
    <h2 class="section-title">{{ $isFr ? 'Les 4 objections les plus courantes' : 'The 4 most common objections' }}</h2>
    <div style="display:flex;flex-direction:column;gap:12px;margin-top:28px">
        @foreach($isFr
            ? [['#F59E0B','Nous avons déjà un logiciel.','La plupart des établissements de santé ont un logiciel. La vraie question est de savoir si votre solution actuelle supporte l\'interopérabilité, l\'évolutivité, l\'intelligence clinique et les objectifs de transformation numérique futurs.'],
               ['#EF4444','C\'est trop cher.','La technologie de santé doit être évaluée sur les économies opérationnelles, la protection des revenus, l\'amélioration de la sécurité patient et la durabilité à long terme — et non sur le seul coût d\'acquisition.'],
               ['#8B5CF6','Notre personnel n\'est pas technique.','OPES Academy fournit des formations structurées, des certifications et des programmes de gestion du changement pour garantir une adoption réussie.'],
               ['#1A6FE8','Nous avons peur de l\'échec de l\'implémentation.','OPES suit une méthodologie d\'implémentation structurée : découverte, formation, déploiement pilote, support au go-live et optimisation post-implémentation.']]
            : [['#F59E0B','We already have software.','Most healthcare facilities have software. The question is whether your existing solution supports interoperability, scalability, clinical intelligence, and future digital transformation goals.'],
               ['#EF4444','It is expensive.','Healthcare technology should be evaluated based on operational savings, revenue protection, patient safety improvements, and long-term sustainability — not acquisition cost alone.'],
               ['#8B5CF6','Our staff are not technical.','OPES Academy provides structured training, certification, and change management programmes to ensure successful adoption.'],
               ['#1A6FE8','We are afraid of implementation failure.','OPES follows a structured implementation methodology: discovery, training, pilot deployment, go-live support, and post-implementation optimisation.']]
        as $obj)
        <div style="background:#0F172A;border:1px solid #1e293b;border-radius:12px;overflow:hidden">
            <div style="display:grid;grid-template-columns:1fr 2fr">
                <div style="padding:18px 20px;border-right:1px solid #1e293b;background:{{ $obj[0] }}08">
                    <div style="display:flex;align-items:flex-start;gap:8px">
                        <i data-lucide="alert-circle" style="width:14px;height:14px;color:{{ $obj[0] }};flex-shrink:0;margin-top:1px"></i>
                        <div style="font-size:12px;font-weight:700;color:#e2e8f0;line-height:1.4">{{ $obj[1] }}</div>
                    </div>
                </div>
                <div style="padding:18px 20px;display:flex;align-items:flex-start;gap:8px">
                    <i data-lucide="check-circle" style="width:14px;height:14px;color:#00C896;flex-shrink:0;margin-top:1px"></i>
                    <div style="font-size:12px;color:#64748b;line-height:1.6">{{ $obj[2] }}</div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<div class="divider"></div>

{{-- ── COMPETITIVE POSITIONING + PROPOSAL STRATEGY (2-COL) ────── --}}
<div class="section" style="max-width:960px;margin:0 auto">
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:40px">
        {{-- Competitive Positioning --}}
        <div>
            <div class="section-label" style="margin-bottom:16px">
                <i data-lucide="flag" style="width:12px;height:12px"></i>
                {{ $isFr ? 'Positionnement concurrentiel' : 'Competitive positioning' }}
            </div>
            <h3 style="font-size:15px;font-weight:700;color:#e2e8f0;margin-bottom:16px">{{ $isFr ? 'Comment se différencier' : 'How to differentiate' }}</h3>
            @foreach($isFr
                ? [['#00C896','vs DMEs génériques',['Intégré','Évolutif','Interopérable','Enterprise-ready']],
                   ['#1A6FE8','vs Systèmes labo standalone',['Connecté','Multi-département','Health ID activé']],
                   ['#F59E0B','vs Fournisseurs internationaux',['Support local','Contexte africain','Déploiement flexible','Rapport qualité-prix','Prêt au niveau national']]]
                : [['#00C896','vs Generic EMRs',['Integrated','Scalable','Interoperable','Enterprise-ready']],
                   ['#1A6FE8','vs Standalone lab systems',['Connected','Multi-department','Health ID enabled']],
                   ['#F59E0B','vs International vendors',['Local support','African context','Flexible deployment','Cost-effective','National readiness']]]
            as $comp)
            <div style="background:#0F172A;border:1px solid #1e293b;border-radius:10px;padding:14px 16px;margin-bottom:10px">
                <div style="font-size:11px;font-weight:700;color:{{ $comp[0] }};margin-bottom:8px">{{ $comp[1] }}</div>
                <div style="display:flex;flex-wrap:wrap;gap:5px">
                    @foreach($comp[2] as $pt)
                    <span style="background:{{ $comp[0] }}10;border:1px solid {{ $comp[0] }}20;border-radius:12px;padding:2px 9px;font-size:10px;color:{{ $comp[0] }}">✓ {{ $pt }}</span>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
        {{-- Proposal Strategy --}}
        <div>
            <div class="section-label" style="margin-bottom:16px">
                <i data-lucide="file-text" style="width:12px;height:12px"></i>
                {{ $isFr ? 'Stratégie de proposition' : 'Proposal strategy' }}
            </div>
            <h3 style="font-size:15px;font-weight:700;color:#e2e8f0;margin-bottom:16px">{{ $isFr ? 'Positionner OPES sur 3 niveaux' : 'Position OPES at three levels' }}</h3>
            @foreach($isFr
                ? [['#00C896','Bénéfices immédiats','Enregistrement plus rapide · Meilleure facturation · Meilleure documentation'],
                   ['#1A6FE8','Bénéfices à moyen terme','Reporting amélioré · Efficacité opérationnelle · Optimisation des revenus'],
                   ['#F59E0B','Bénéfices à long terme','Interopérabilité · Intelligence en santé · Connectivité nationale']]
                : [['#00C896','Immediate benefits','Faster registration · Better billing · Better documentation'],
                   ['#1A6FE8','Medium-term benefits','Improved reporting · Operational efficiency · Revenue optimisation'],
                   ['#F59E0B','Long-term benefits','Interoperability · Health intelligence · National connectivity']]
            as $level)
            <div style="padding:16px;background:#0F172A;border-radius:10px;border-left:3px solid {{ $level[0] }};margin-bottom:10px">
                <div style="font-size:12px;font-weight:700;color:{{ $level[0] }};margin-bottom:6px">{{ $level[1] }}</div>
                <div style="font-size:11px;color:#64748b;line-height:1.6">{{ $level[2] }}</div>
            </div>
            @endforeach
            <div style="background:#0F172A;border:1px solid #1e293b;border-radius:10px;padding:14px 16px;margin-top:6px">
                <div style="font-size:11px;font-weight:700;color:#e2e8f0;margin-bottom:8px">{{ $isFr ? 'Composants d\'une proposition' : 'Proposal components' }}</div>
                @foreach($isFr
                    ? ['Résumé exécutif','Énoncé du problème','Solution proposée','Architecture','Tarification','Plan d\'implémentation','Plan de support','ROI']
                    : ['Executive summary','Problem statement','Proposed solution','Architecture','Pricing','Implementation plan','Support plan','ROI']
                as $comp)
                <div style="font-size:11px;color:#475569;padding:2px 0">
                    <i data-lucide="chevron-right" style="width:9px;height:9px;color:#00C896;margin-right:4px"></i>{{ $comp }}
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<div class="divider"></div>

{{-- ── STRATEGIC ACCOUNTS + KPIs + POST-SALE (3-COL) ─────────── --}}
<div class="section" style="max-width:960px;margin:0 auto">
    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:28px">
        {{-- Strategic Accounts --}}
        <div>
            <div class="section-label" style="margin-bottom:14px">
                <i data-lucide="star" style="width:12px;height:12px"></i>
                {{ $isFr ? 'Comptes stratégiques' : 'Strategic accounts' }}
            </div>
            @foreach($isFr
                ? ['Ministères de la Santé','Hôpitaux régionaux','Caisses d\'assurance nationales','Programmes de donateurs','Hôpitaux universitaires','Réseaux hospitaliers']
                : ['Ministries of Health','Regional hospitals','National insurance funds','Donor programmes','University hospitals','Hospital networks']
            as $acct)
            <div style="display:flex;align-items:center;gap:8px;padding:8px 0;border-bottom:1px solid #0f172a">
                <i data-lucide="chevron-right" style="width:9px;height:9px;color:#00C896;flex-shrink:0"></i>
                <span style="font-size:12px;color:#94a3b8">{{ $acct }}</span>
            </div>
            @endforeach
        </div>
        {{-- KPIs --}}
        <div>
            <div class="section-label" style="margin-bottom:14px">
                <i data-lucide="bar-chart-2" style="width:12px;height:12px"></i>
                {{ $isFr ? 'KPIs commerciaux' : 'Sales KPIs' }}
            </div>
            @foreach($isFr
                ? ['Valeur du pipeline','Opportunités qualifiées','Taux de succès des propositions','Taille moyenne des contrats','Coût d\'acquisition client','Revenu par représentant commercial','Taux de fidélisation client']
                : ['Pipeline value','Qualified opportunities','Proposal win rate','Average deal size','Customer acquisition cost','Revenue per sales representative','Customer retention rate']
            as $kpi)
            <div style="display:flex;align-items:center;gap:8px;padding:8px 10px;background:#0F172A;border-radius:7px;border-left:2px solid #1A6FE8;margin-bottom:5px">
                <span style="font-size:11px;color:#94a3b8">{{ $kpi }}</span>
            </div>
            @endforeach
        </div>
        {{-- Post-Sale Expansion --}}
        <div>
            <div class="section-label" style="margin-bottom:14px">
                <i data-lucide="trending-up" style="width:12px;height:12px"></i>
                {{ $isFr ? 'Expansion post-vente' : 'Post-sale expansion' }}
            </div>
            <p style="font-size:11px;color:#475569;margin-bottom:12px">{{ $isFr ? 'Après l\'implémentation, identifier des opportunités pour :' : 'After implementation, identify opportunities for:' }}</p>
            @foreach($isFr
                ? ['Modules supplémentaires','Services d\'interopérabilité','Services Health ID','Analytique avancée','Programmes de formation','Services gérés']
                : ['Additional modules','Interoperability services','Health ID services','Advanced analytics','Training programmes','Managed services']
            as $exp)
            <div style="display:flex;align-items:center;gap:8px;padding:8px 10px;background:#0F172A;border-radius:7px;border-left:2px solid #00C896;margin-bottom:5px">
                <span style="font-size:11px;color:#94a3b8">{{ $exp }}</span>
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- ── CTA ──────────────────────────────────────────────────────── --}}
<div class="demo-section">
    <h2>{{ $isFr ? 'Prêt à engager le marché ?' : 'Ready to engage the market?' }}</h2>
    <p>{{ $isFr
        ? 'Utilisez ce guide pour qualifier les opportunités, structurer vos propositions et positionner OPES comme le partenaire technologique santé de référence pour chaque organisation.'
        : 'Use this playbook to qualify opportunities, structure proposals, and position OPES as the trusted healthcare technology partner for every organisation.' }}</p>
    <div style="display:flex;gap:14px;justify-content:center;flex-wrap:wrap;margin-top:12px">
        <a href="{{ url($locale.'/health-os') }}" class="btn-primary">
            {{ $isFr ? 'Catalogue produits' : 'Product catalog' }}
            <i data-lucide="arrow-right" style="width:15px;height:15px"></i>
        </a>
        <a href="{{ url($locale.'/contact') }}" class="btn-secondary">
            {{ $isFr ? 'Contacter l\'équipe commerciale' : 'Contact the commercial team' }}
            <i data-lucide="mail" style="width:15px;height:15px;color:#94a3b8"></i>
        </a>
    </div>
</div>

</x-layouts.app>

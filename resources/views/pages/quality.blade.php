@php $locale = app()->getLocale(); $isFr = $locale === 'fr'; @endphp

<x-layouts.app
    title="{{ $isFr ? 'Cadre de management de la qualité — OPES Health Systems' : 'Quality Management Framework — OPES Health Systems' }}"
    description="{{ $isFr ? 'Le cadre QMS d\'OPES : gouvernance de la qualité, 8 domaines, cycle de vie, KPIs, amélioration continue.' : 'OPES QMS: quality governance, 8 domains, lifecycle, KPIs, CAPA, and continuous improvement across all products and services.' }}">

{{-- ── HERO ─────────────────────────────────────────────────────── --}}
<div class="about-hero">
    <div class="section-label" style="justify-content:center;margin-bottom:16px">
        <i data-lucide="badge-check" style="width:12px;height:12px"></i>
        {{ $isFr ? 'Cadre QMS v1.0' : 'QMS Framework v1.0' }}
    </div>
    <h1 class="about-title">
        {{ $isFr ? 'Management de la qualité' : 'Quality management' }}
        <span class="gradient-text">{{ $isFr ? 'à chaque étape' : 'at every stage' }}</span>
    </h1>
    <p class="about-sub" style="max-width:720px">
        {{ $isFr
            ? 'Politiques, structures de gouvernance, processus, contrôles, indicateurs et mécanismes d\'amélioration continue garantissant que chaque produit, service et opération OPES répond aux objectifs de qualité définis.'
            : 'Policies, governance structures, processes, controls, KPIs, and continuous improvement mechanisms ensuring every OPES product, service, and operation consistently meets defined quality objectives.' }}
    </p>
</div>

{{-- ── STATS ────────────────────────────────────────────────────── --}}
<div class="section" style="text-align:center">
    <div class="stats-bar" style="max-width:960px;margin:0 auto">
        @foreach($isFr
            ? [['8','Domaines qualité'],['5','Comités de gouvernance'],['7','Étapes du cycle de vie'],['5','Niveaux de maturité']]
            : [['8','Quality domains'],['5','Governance committees'],['7','Lifecycle stages'],['5','Maturity levels']]
        as $s)
        <div class="stat-item">
            <div class="stat-value">{{ $s[0] }}</div>
            <div class="stat-label">{{ $s[1] }}</div>
        </div>
        @endforeach
    </div>
</div>

<div class="divider"></div>

{{-- ── QUALITY POLICY ───────────────────────────────────────────── --}}
<div class="section" style="max-width:960px;margin:0 auto">
    <div class="section-label" style="margin-bottom:16px">
        <i data-lucide="file-check" style="width:12px;height:12px"></i>
        {{ $isFr ? 'Politique qualité' : 'Quality policy' }}
    </div>
    <h2 class="section-title">{{ $isFr ? 'Sept engagements fondamentaux' : 'Seven foundational commitments' }}</h2>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:12px;margin-top:28px">
        @foreach($isFr
            ? [['shield-check','#00C896','Solutions fiables','Livraison de solutions de santé numérique fiables et sécurisées.'],['heart','#1A6FE8','Sécurité des patients','La sécurité des patients est au cœur de chaque décision.'],['scale','#00C896','Conformité réglementaire','Conformité aux cadres légaux et réglementaires applicables.'],['smile','#1A6FE8','Satisfaction client','Satisfaction et adoption des clients comme mesures clés.'],['arrow-left-right','#00C896','Interopérabilité','Échange fluide de données avec les systèmes partenaires.'],['trending-up','#1A6FE8','Amélioration continue','Amélioration permanente des produits et services.'],['settings','#00C896','Excellence opérationnelle','Efficacité et maîtrise des opérations internes.']]
            : [['shield-check','#00C896','Reliable solutions','Delivering reliable and secure healthcare technology solutions.'],['heart','#1A6FE8','Patient safety','Patient safety is at the centre of every decision.'],['scale','#00C896','Regulatory compliance','Compliance with applicable legal and regulatory frameworks.'],['smile','#1A6FE8','Customer satisfaction','Customer satisfaction and adoption as key quality metrics.'],['arrow-left-right','#00C896','Interoperability','Seamless data exchange with partner systems.'],['trending-up','#1A6FE8','Continuous improvement','Ongoing improvement of products and services.'],['settings','#00C896','Operational excellence','Efficiency and control of internal operations.']]
        as $p)
        <div style="background:#0F172A;border:1px solid #1e293b;border-radius:12px;padding:16px">
            <div style="display:flex;align-items:center;gap:8px;margin-bottom:8px">
                <i data-lucide="{{ $p[0] }}" style="width:14px;height:14px;color:{{ $p[1] }}"></i>
                <div style="font-weight:700;color:#e2e8f0;font-size:12px">{{ $p[2] }}</div>
            </div>
            <div style="font-size:11px;color:var(--text-muted);line-height:1.6">{{ $p[3] }}</div>
        </div>
        @endforeach
    </div>
</div>

<div class="divider"></div>

{{-- ── PRINCIPLES ───────────────────────────────────────────────── --}}
<div class="section" style="max-width:960px;margin:0 auto">
    <div class="section-label" style="margin-bottom:16px">
        <i data-lucide="compass" style="width:12px;height:12px"></i>
        {{ $isFr ? 'Principes directeurs' : 'Guiding principles' }}
    </div>
    <h2 class="section-title">{{ $isFr ? 'Six principes qui guident chaque décision qualité' : 'Six principles guiding every quality decision' }}</h2>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:14px;margin-top:28px">
        @foreach($isFr
            ? [['users','#00C896','Orientation client','Comprendre et anticiper les besoins des clients comme point de départ de toute démarche qualité.'],['heart-pulse','#1A6FE8','Sécurité clinique','Protéger les patients et les professionnels de santé dans chaque flux de travail.'],['award','#00C896','Engagement du leadership','La qualité est une responsabilité de la direction — pas uniquement de l\'équipe qualité.'],['bar-chart-2','#1A6FE8','Décisions basées sur les données','Piloter l\'amélioration par des informations mesurables et vérifiables.'],['git-branch','#00C896','Orientation processus','Gérer la qualité à travers des processus contrôlés et documentés.'],['refresh-cw','#1A6FE8','Amélioration continue','L\'amélioration de la qualité est un effort permanent, pas un projet ponctuel.']]
            : [['users','#00C896','Customer focus','Understand and anticipate customer needs as the starting point of every quality effort.'],['heart-pulse','#1A6FE8','Clinical safety','Protect patients and healthcare professionals in every workflow.'],['award','#00C896','Leadership commitment','Quality is a leadership responsibility — not only the quality team\'s.'],['bar-chart-2','#1A6FE8','Evidence-based decisions','Drive improvement through measurable, verifiable information.'],['git-branch','#00C896','Process orientation','Manage quality through controlled, documented processes.'],['refresh-cw','#1A6FE8','Continuous improvement','Quality improvement is an ongoing effort, not a one-time project.']]
        as $pr)
        <div style="background:#0F172A;border:1px solid #1e293b;border-radius:12px;padding:18px 16px">
            <div style="width:36px;height:36px;border-radius:9px;background:{{ $pr[1] }}15;display:flex;align-items:center;justify-content:center;margin-bottom:12px">
                <i data-lucide="{{ $pr[0] }}" style="width:16px;height:16px;color:{{ $pr[1] }}"></i>
            </div>
            <div style="font-weight:700;color:#e2e8f0;font-size:13px;margin-bottom:6px">{{ $pr[2] }}</div>
            <div style="font-size:12px;color:var(--text-muted);line-height:1.6">{{ $pr[3] }}</div>
        </div>
        @endforeach
    </div>
</div>

<div class="divider"></div>

{{-- ── GOVERNANCE ───────────────────────────────────────────────── --}}
<div class="section" style="max-width:960px;margin:0 auto">
    <div class="section-label" style="margin-bottom:16px">
        <i data-lucide="network" style="width:12px;height:12px"></i>
        {{ $isFr ? 'Structure de gouvernance' : 'Governance structure' }}
    </div>
    <h2 class="section-title">{{ $isFr ? 'Cinq comités, une vision qualité unifiée' : 'Five committees, one unified quality vision' }}</h2>
    <div class="pi-grid" style="max-width:960px;margin-top:28px">
        @php $comms = $isFr ? [
            ['layers','#00C896','Conseil de management qualité','Supervision stratégique : stratégie qualité, objectifs, revue de performance qualité.'],
            ['sliders','#1A6FE8','Comité de pilotage qualité','Planification, surveillance et amélioration de la qualité au niveau opérationnel.'],
            ['heart-pulse','#00C896','Comité de qualité clinique','Qualité clinique, sécurité des patients, audits cliniques.'],
            ['code','#1A6FE8','Comité de qualité produit','Standards produit, qualité des releases, revues produit.'],
            ['headphones','#00C896','Comité de qualité des services','Qualité de l\'implémentation, qualité du support, succès client.'],
        ] : [
            ['layers','#00C896','Quality Management Board','Strategic oversight: quality strategy, objectives, and performance review.'],
            ['sliders','#1A6FE8','Quality Steering Committee','Quality planning, monitoring, and improvement at the operational level.'],
            ['heart-pulse','#00C896','Clinical Quality Committee','Clinical quality, patient safety, and clinical audits.'],
            ['code','#1A6FE8','Product Quality Committee','Product standards, release quality, and product reviews.'],
            ['headphones','#00C896','Service Quality Committee','Implementation quality, support quality, and customer success.'],
        ]; @endphp
        @foreach($comms as $c)
        <div class="pi-card" style="flex-direction:column;align-items:flex-start">
            <div style="width:40px;height:40px;border-radius:10px;background:{{ $c[1] }}15;display:flex;align-items:center;justify-content:center;margin-bottom:12px">
                <i data-lucide="{{ $c[0] }}" style="width:18px;height:18px;color:{{ $c[1] }}"></i>
            </div>
            <div style="font-weight:700;color:#e2e8f0;font-size:13px;margin-bottom:6px">{{ $c[2] }}</div>
            <div style="font-size:12px;color:var(--text-muted);line-height:1.6">{{ $c[3] }}</div>
        </div>
        @endforeach
    </div>
</div>

<div class="divider"></div>

{{-- ── 8 QUALITY DOMAINS ────────────────────────────────────────── --}}
<div class="section" style="max-width:960px;margin:0 auto">
    <div class="section-label" style="margin-bottom:16px">
        <i data-lucide="grid" style="width:12px;height:12px"></i>
        {{ $isFr ? 'Domaines qualité' : 'Quality domains' }}
    </div>
    <h2 class="section-title">{{ $isFr ? 'Huit domaines couverts par le QMS' : 'Eight domains governed by the QMS' }}</h2>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:14px;margin-top:28px">
        @php $domains = $isFr ? [
            ['1','code','#00C896','Qualité produit','Fiabilité logicielle et performance',['Validation des exigences','Revues de conception','Revues de code','Tests','Approbation de release','Surveillance post-release']],
            ['2','heart-pulse','#1A6FE8','Qualité clinique','Sécurité des opérations cliniques',['Revues cliniques','Validation clinique','Évaluations de sécurité','Revues d\'incidents']],
            ['3','map','#00C896','Qualité de l\'implémentation','Réussite des déploiements',['Revues de projet','Évaluations de préparation','Revues de mise en service','Évaluations post-go-live']],
            ['4','headphones','#1A6FE8','Qualité du support','Services de support efficaces',['Surveillance SLA','Revues de tickets','Revues d\'escalade','Retours clients']],
            ['5','database','#00C896','Qualité de l\'information','Information de santé de haute qualité',['Validation des données','Audits qualité données','Détection des doublons','Surveillance de la complétude']],
            ['6','shield','#1A6FE8','Qualité sécurité','Opérations sécurisées',['Revues sécurité','Évaluations de vulnérabilité','Revues d\'incidents','Surveillance conformité']],
            ['7','graduation-cap','#00C896','Qualité formation','Transfert de connaissances efficace',['Revues de curriculum','Revues de certification','Évaluations formation','Validation des compétences']],
            ['8','handshake','#1A6FE8','Qualité partenaires','Performance des partenaires',['Certification partenaires','Audits partenaires','Revues de performance']],
        ] : [
            ['1','code','#00C896','Product quality','Software reliability & performance',['Requirements validation','Design reviews','Code reviews','Testing','Release approval','Post-release monitoring']],
            ['2','heart-pulse','#1A6FE8','Clinical quality','Safe clinical operations',['Clinical reviews','Clinical validation','Safety assessments','Incident reviews']],
            ['3','map','#00C896','Implementation quality','Successful deployments',['Project reviews','Readiness assessments','Go-live reviews','Post-go-live evaluations']],
            ['4','headphones','#1A6FE8','Support quality','Effective support services',['SLA monitoring','Ticket reviews','Escalation reviews','Customer feedback reviews']],
            ['5','database','#00C896','Information quality','High-quality healthcare information',['Data validation','Data quality audits','Duplicate detection','Completeness monitoring']],
            ['6','shield','#1A6FE8','Security quality','Secure operations',['Security reviews','Vulnerability assessments','Incident reviews','Compliance monitoring']],
            ['7','graduation-cap','#00C896','Training quality','Effective knowledge transfer',['Curriculum reviews','Certification reviews','Training assessments','Competency validation']],
            ['8','handshake','#1A6FE8','Partner quality','Partner performance management',['Partner certification','Partner audits','Performance reviews']],
        ]; @endphp
        @foreach($domains as $d)
        <div style="background:#0F172A;border:1px solid #1e293b;border-radius:12px;padding:16px">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:12px">
                <div style="width:32px;height:32px;border-radius:8px;background:{{ $d[2] }}15;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:10px;font-weight:800;color:{{ $d[2] }}">{{ $d[0] }}</div>
                <div>
                    <div style="font-weight:700;color:#e2e8f0;font-size:12px">{{ $d[3] }}</div>
                    <div style="font-size:10px;color:{{ $d[2] }};font-weight:600">{{ $d[4] }}</div>
                </div>
            </div>
            @foreach($d[5] as $act)
            <div style="display:flex;align-items:center;gap:6px;font-size:11px;color:var(--text-muted);padding:3px 0">
                <i data-lucide="chevron-right" style="width:10px;height:10px;color:{{ $d[2] }};flex-shrink:0"></i>{{ $act }}
            </div>
            @endforeach
        </div>
        @endforeach
    </div>
</div>

<div class="divider"></div>

{{-- ── LIFECYCLE + QA vs QC ─────────────────────────────────────── --}}
<div class="section" style="max-width:960px;margin:0 auto">
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:40px;align-items:start">

        {{-- Lifecycle --}}
        <div>
            <div class="section-label" style="margin-bottom:16px">
                <i data-lucide="refresh-cw" style="width:12px;height:12px"></i>
                {{ $isFr ? 'Cycle de vie qualité' : 'Quality lifecycle' }}
            </div>
            <h2 class="section-title" style="font-size:20px">{{ $isFr ? 'Sept étapes, zéro compromis' : 'Seven stages, no compromises' }}</h2>
            <div style="margin-top:20px">
                @foreach($isFr
                    ? [['pen-line','var(--text-faint)','Planifier','Définition des objectifs qualité et des standards.'],['drafting-compass','#1A6FE8','Concevoir','Conception orientée qualité avec revues intégrées.'],['hammer','#00C896','Construire','Développement conforme aux standards qualité.'],['check-square','#1A6FE8','Valider','Tests, revues cliniques et approbations de release.'],['rocket','#00C896','Déployer','Mise en production avec revue de préparation.'],['activity','#1A6FE8','Surveiller','Suivi continu des KPIs et incidents qualité.'],['trending-up','#00C896','Améliorer','Actions correctives et préventives, amélioration continue.']]
                    : [['pen-line','var(--text-faint)','Plan','Define quality objectives and standards.'],['drafting-compass','#1A6FE8','Design','Quality-oriented design with integrated reviews.'],['hammer','#00C896','Build','Development conforming to quality standards.'],['check-square','#1A6FE8','Validate','Testing, clinical reviews, and release approvals.'],['rocket','#00C896','Deploy','Production deployment with readiness review.'],['activity','#1A6FE8','Monitor','Continuous monitoring of KPIs and quality incidents.'],['trending-up','#00C896','Improve','Corrective and preventive actions, continuous improvement.']]
                as $idx => $step)
                <div style="display:flex;gap:12px">
                    <div style="display:flex;flex-direction:column;align-items:center">
                        <div style="width:32px;height:32px;border-radius:50%;background:{{ $step[1] }}20;border:1px solid {{ $step[1] }}40;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                            <i data-lucide="{{ $step[0] }}" style="width:13px;height:13px;color:{{ $step[1] }}"></i>
                        </div>
                        @if($idx < 6)<div style="width:1px;height:14px;background:#1e293b;margin:2px 0"></div>@endif
                    </div>
                    <div style="padding-top:5px;margin-bottom:{{ $idx < 6 ? '8px' : '0' }}">
                        <div style="font-weight:700;color:#e2e8f0;font-size:12px">{{ $step[2] }}</div>
                        <div style="font-size:11px;color:var(--text-muted);line-height:1.5">{{ $step[3] }}</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- QA vs QC --}}
        <div>
            <div class="section-label" style="margin-bottom:16px">
                <i data-lucide="split" style="width:12px;height:12px"></i>
                {{ $isFr ? 'Assurance vs contrôle' : 'Assurance vs control' }}
            </div>
            <h2 class="section-title" style="font-size:20px">{{ $isFr ? 'Prévenir et détecter' : 'Prevent and detect' }}</h2>
            <div style="margin-top:20px;display:flex;flex-direction:column;gap:16px">
                <div style="background:#0f1a2e;border:1px solid rgba(0,200,150,0.2);border-radius:12px;padding:18px">
                    <div style="font-size:10px;font-weight:800;color:#00C896;text-transform:uppercase;letter-spacing:0.08em;margin-bottom:8px">
                        {{ $isFr ? 'Assurance qualité (AQ) — Prévenir' : 'Quality Assurance (QA) — Prevent' }}
                    </div>
                    @foreach($isFr
                        ? ['Développement des standards','Revues de processus','Audits','Formation','Surveillance de conformité']
                        : ['Standards development','Process reviews','Audits','Training','Compliance monitoring']
                    as $item)
                    <div style="font-size:12px;color:var(--text-muted);padding:4px 0;border-bottom:1px solid #1e293b30">{{ $item }}</div>
                    @endforeach
                </div>
                <div style="background:#0f152e;border:1px solid rgba(26,111,232,0.2);border-radius:12px;padding:18px">
                    <div style="font-size:10px;font-weight:800;color:#1A6FE8;text-transform:uppercase;letter-spacing:0.08em;margin-bottom:8px">
                        {{ $isFr ? 'Contrôle qualité (CQ) — Détecter' : 'Quality Control (QC) — Detect' }}
                    </div>
                    @foreach($isFr
                        ? ['Tests','Revues','Inspections','Vérification']
                        : ['Testing','Reviews','Inspections','Verification']
                    as $item)
                    <div style="font-size:12px;color:var(--text-muted);padding:4px 0;border-bottom:1px solid #1e293b30">{{ $item }}</div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<div class="divider"></div>

{{-- ── CAPA ─────────────────────────────────────────────────────── --}}
<div class="section" style="max-width:960px;margin:0 auto">
    <div class="section-label" style="margin-bottom:16px">
        <i data-lucide="git-commit" style="width:12px;height:12px"></i>
        {{ $isFr ? 'Actions correctives & préventives (CAPA)' : 'Corrective & Preventive Action (CAPA)' }}
    </div>
    <h2 class="section-title">{{ $isFr ? 'Un processus structuré pour chaque incident significatif' : 'A structured process for every significant issue' }}</h2>
    <div style="display:flex;flex-wrap:wrap;justify-content:center;gap:0;margin-top:28px;max-width:720px;margin-left:auto;margin-right:auto">
        @foreach($isFr
            ? [['alert-circle','var(--text-muted)','Identification','Détection et enregistrement du problème.'],['search','#1A6FE8','Analyse','Identification de la cause racine.'],['tool','#00C896','Action corrective','Résolution de la cause immédiate.'],['shield-plus','#1A6FE8','Action préventive','Prévention de la récurrence.'],['check-circle','#00C896','Vérification','Confirmation que les actions sont efficaces.'],['archive','var(--text-muted)','Clôture','Documentation et clôture formelle.']]
            : [['alert-circle','var(--text-muted)','Identification','Issue detected and recorded.'],['search','#1A6FE8','Root cause analysis','Underlying cause identified.'],['tool','#00C896','Corrective action','Immediate cause resolved.'],['shield-plus','#1A6FE8','Preventive action','Recurrence prevention put in place.'],['check-circle','#00C896','Verification','Effectiveness of actions confirmed.'],['archive','var(--text-muted)','Closure','Documentation and formal closure.']]
        as $idx => $step)
        <div style="display:flex;align-items:center">
            <div style="text-align:center;width:110px;padding:12px 6px">
                <div style="width:40px;height:40px;border-radius:50%;background:{{ $step[1] }}20;border:1px solid {{ $step[1] }}40;display:flex;align-items:center;justify-content:center;margin:0 auto 8px">
                    <i data-lucide="{{ $step[0] }}" style="width:16px;height:16px;color:{{ $step[1] }}"></i>
                </div>
                <div style="font-weight:700;color:#e2e8f0;font-size:11px;margin-bottom:3px">{{ $step[2] }}</div>
                <div style="font-size:10px;color:var(--text-muted);line-height:1.4">{{ $step[3] }}</div>
            </div>
            @if($idx < 5)
            <i data-lucide="chevron-right" style="width:14px;height:14px;color:#334155;flex-shrink:0"></i>
            @endif
        </div>
        @endforeach
    </div>
</div>

<div class="divider"></div>

{{-- ── KPIs ─────────────────────────────────────────────────────── --}}
<div class="section" style="max-width:960px;margin:0 auto">
    <div class="section-label" style="margin-bottom:16px">
        <i data-lucide="bar-chart-2" style="width:12px;height:12px"></i>
        {{ $isFr ? 'Indicateurs de performance qualité' : 'Quality performance indicators' }}
    </div>
    <h2 class="section-title">{{ $isFr ? 'Mesurer pour améliorer' : 'Measure to improve' }}</h2>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:16px;margin-top:28px">
        @foreach($isFr
            ? [
                ['code','#00C896','KPIs Produit',['Densité de défauts','Stabilité des releases','Disponibilité des systèmes','MTTR des défauts']],
                ['heart-pulse','#1A6FE8','KPIs Cliniques',['Complétude de la documentation','Précision des alertes','Taux de doublons patients','Taux d\'incidents cliniques']],
                ['headphones','#00C896','KPIs Services',['Taux d\'atteinte des SLA','Résolution au premier contact','Satisfaction client','Taux de succès des déploiements']],
                ['graduation-cap','#1A6FE8','KPIs Formation',['Taux de réussite à la certification','Score de satisfaction formation','Taux d\'atteinte des compétences']],
            ] : [
                ['code','#00C896','Product KPIs',['Defect density','Release stability','System availability','Mean time to resolve defects']],
                ['heart-pulse','#1A6FE8','Clinical KPIs',['Documentation completeness','Alert accuracy','Duplicate patient rate','Clinical incident rate']],
                ['headphones','#00C896','Service KPIs',['SLA achievement rate','First contact resolution','Customer satisfaction','Deployment success rate']],
                ['graduation-cap','#1A6FE8','Training KPIs',['Certification pass rate','Training satisfaction score','Competency achievement rate']],
            ]
        as $cat)
        <div style="background:#0F172A;border:1px solid #1e293b;border-radius:12px;padding:18px 16px">
            <div style="display:flex;align-items:center;gap:8px;margin-bottom:14px">
                <i data-lucide="{{ $cat[0] }}" style="width:14px;height:14px;color:{{ $cat[1] }}"></i>
                <div style="font-weight:700;color:#e2e8f0;font-size:13px">{{ $cat[2] }}</div>
            </div>
            @foreach($cat[3] as $kpi)
            <div style="display:flex;align-items:center;gap:6px;font-size:11px;color:var(--text-muted);padding:5px 0;border-bottom:1px solid #1e293b40">
                <i data-lucide="chevron-right" style="width:10px;height:10px;color:{{ $cat[1] }};flex-shrink:0"></i>{{ $kpi }}
            </div>
            @endforeach
        </div>
        @endforeach
    </div>
</div>

<div class="divider"></div>

{{-- ── MATURITY MODEL ───────────────────────────────────────────── --}}
<div class="section" style="max-width:960px;margin:0 auto;text-align:center">
    <div class="section-label" style="justify-content:center;margin-bottom:16px">
        <i data-lucide="trending-up" style="width:12px;height:12px"></i>
        {{ $isFr ? 'Modèle de maturité qualité' : 'Quality maturity model' }}
    </div>
    <h2 class="section-title">{{ $isFr ? 'Cinq niveaux vers l\'excellence qualité' : 'Five levels toward quality excellence' }}</h2>
    <div style="display:flex;gap:0;justify-content:center;margin-top:28px;flex-wrap:wrap">
        @php
        $levels = $isFr
            ? [['1','Ad Hoc','Processus imprévisibles, réactifs.','#334155'],['2','Géré','Processus planifiés et suivis.','var(--text-faint)'],['3','Défini','Processus standardisés.','#1A6FE8'],['4','Mesuré','Qualité mesurée quantitativement.','#00A87B'],['5','Optimisé','Amélioration continue et innovation.','#00C896']]
            : [['1','Ad Hoc','Unpredictable, reactive processes.','#334155'],['2','Managed','Processes planned and tracked.','var(--text-faint)'],['3','Defined','Standardised processes.','#1A6FE8'],['4','Measured','Quality managed quantitatively.','#00A87B'],['5','Optimized','Continuous improvement & innovation.','#00C896']];
        @endphp
        @foreach($levels as $idx => $level)
        <div style="display:flex;align-items:center">
            <div style="text-align:center;width:130px;padding:16px 8px;background:#0F172A;border:1px solid #1e293b;border-radius:12px;{{ $level[0]==='5' ? 'border-color:#00C896;background:#0d1f19;' : '' }}">
                <div style="width:44px;height:44px;border-radius:50%;background:{{ $level[3] }}25;border:2px solid {{ $level[3] }}60;display:flex;align-items:center;justify-content:center;margin:0 auto 10px;font-size:18px;font-weight:800;color:{{ $level[3] }}">{{ $level[0] }}</div>
                <div style="font-weight:700;color:#e2e8f0;font-size:12px;margin-bottom:4px">{{ $level[1] }}</div>
                <div style="font-size:10px;color:var(--text-muted);line-height:1.4">{{ $level[2] }}</div>
            </div>
            @if($idx < 4)
            <i data-lucide="chevron-right" style="width:14px;height:14px;color:#334155;flex-shrink:0;margin:0 2px"></i>
            @endif
        </div>
        @endforeach
    </div>
</div>

{{-- ── CTA ──────────────────────────────────────────────────────── --}}
<div class="demo-section">
    <h2>{{ $isFr ? 'Découvrir notre démarche qualité ?' : 'Explore our quality approach?' }}</h2>
    <p>{{ $isFr
        ? 'Contactez notre équipe pour comprendre comment le cadre QMS OPES s\'applique concrètement à vos projets de déploiement.'
        : 'Contact our team to understand how the OPES QMS framework applies concretely to your deployment and implementation projects.' }}</p>
    <div style="display:flex;gap:14px;justify-content:center;flex-wrap:wrap;margin-top:12px">
        <a href="{{ url($locale.'/contact') }}" class="btn-primary">
            {{ $isFr ? 'Nous contacter' : 'Contact us' }}
            <i data-lucide="arrow-right" style="width:15px;height:15px"></i>
        </a>
        <a href="{{ url($locale.'/compliance') }}" class="btn-secondary">
            {{ $isFr ? 'Conformité & sécurité' : 'Compliance & trust' }}
            <i data-lucide="shield-check" style="width:15px;height:15px;color:var(--text-muted)"></i>
        </a>
    </div>
</div>

</x-layouts.app>

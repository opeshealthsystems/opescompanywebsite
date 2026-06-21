@php $locale = app()->getLocale(); $isFr = $locale === 'fr'; @endphp

<x-layouts.app
    title="{{ $isFr ? 'Gouvernance Clinique — OPES Health Systems' : 'Clinical Governance — OPES Health Systems' }}"
    description="{{ $isFr ? 'Cadre de gouvernance clinique et de sécurité des patients de la plateforme OPES.' : 'Clinical governance and patient safety framework governing all OPES clinical products and services.' }}">

{{-- ── HERO ─────────────────────────────────────────────────────── --}}
<div class="about-hero">
    <div class="section-label" style="justify-content:center;margin-bottom:16px">
        <i data-lucide="heart-pulse" style="width:12px;height:12px"></i>
        {{ $isFr ? 'Gouvernance clinique v1.0' : 'Clinical Governance v1.0' }}
    </div>
    <h1 class="about-title">
        {{ $isFr ? 'Cadre de gouvernance clinique' : 'Clinical Governance' }}
        <span class="gradient-text">{{ $isFr ? '& sécurité des patients' : '& Patient Safety' }}</span>
    </h1>
    <p class="about-sub" style="max-width:700px">
        {{ $isFr
            ? 'Structures, politiques, responsabilités, contrôles et mécanismes de supervision qui garantissent que toutes les fonctions cliniques de la plateforme OPES soutiennent une délivrance de soins sûre, efficace et centrée sur le patient.'
            : 'The structures, policies, responsibilities, controls, and oversight mechanisms ensuring every clinical function within the OPES Health Platform supports safe, effective, evidence-based, and patient-centred healthcare delivery.' }}
    </p>
</div>

{{-- ── HERO STATS ───────────────────────────────────────────────── --}}
<div class="section" style="text-align:center">
    <div class="stats-bar" style="max-width:960px;margin:0 auto">
        @foreach($isFr
            ? [['5','Comités de gouvernance'],['ICD-11 · SNOMED CT · LOINC','Terminologies cliniques'],['4 étapes','Validation avant déploiement'],['6','KPIs cliniques clés']]
            : [['5','Governance committees'],['ICD-11 · SNOMED CT · LOINC','Clinical terminologies'],['4-stage','Validation before deployment'],['6','Core clinical KPIs']]
        as $s)
        <div class="stat-item">
            <div class="stat-value" style="font-size:clamp(18px,2.5vw,28px)">{{ $s[0] }}</div>
            <div class="stat-label">{{ $s[1] }}</div>
        </div>
        @endforeach
    </div>
</div>

<div class="divider"></div>

{{-- ── PRINCIPLES ───────────────────────────────────────────────── --}}
<div class="section" style="max-width:960px;margin:0 auto">
    <div class="section-label" style="margin-bottom:16px">
        <i data-lucide="compass" style="width:12px;height:12px"></i>
        {{ $isFr ? 'Principes fondateurs' : 'Founding principles' }}
    </div>
    <h2 class="section-title">{{ $isFr ? 'Les cinq piliers de notre gouvernance clinique' : 'Five pillars of our clinical governance' }}</h2>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:14px;margin-top:32px">
        @foreach($isFr
            ? [
                ['heart','#00C896','Sécurité en premier','La sécurité du patient prime sur toute considération opérationnelle, technique ou commerciale.'],
                ['user-check','#1A6FE8','Responsabilité clinique','Les décisions cliniques restent la responsabilité des professionnels de santé agréés. OPES ne remplace pas le jugement clinique.'],
                ['book-open','#00C896','Pratique fondée sur les preuves','Le contenu clinique est basé sur des recommandations, des preuves publiées et des normes professionnelles reconnues.'],
                ['eye','#1A6FE8','Transparence','Les règles et recommandations cliniques sont traçables et révisables par les comités compétents.'],
                ['refresh-cw','#00C896','Amélioration continue','Le contenu clinique fait l\'objet d\'une revue et d\'un affinement permanents.'],
            ] : [
                ['heart','#00C896','Patient safety first','Patient welfare takes priority over operational, technical, or commercial considerations.'],
                ['user-check','#1A6FE8','Clinical accountability','Clinical decisions remain the responsibility of licensed healthcare professionals. OPES does not replace clinical judgment.'],
                ['book-open','#00C896','Evidence-based practice','Clinical content is based on guidelines, published evidence, and accepted professional standards.'],
                ['eye','#1A6FE8','Transparency','Clinical rules and recommendations are traceable and reviewable by the appropriate committees.'],
                ['refresh-cw','#00C896','Continuous improvement','Clinical content undergoes ongoing review and refinement throughout the platform lifecycle.'],
            ]
        as $p)
        <div style="background:#0F172A;border:1px solid #1e293b;border-radius:12px;padding:18px 16px">
            <div style="width:36px;height:36px;border-radius:9px;background:{{ $p[1] }}15;display:flex;align-items:center;justify-content:center;margin-bottom:12px">
                <i data-lucide="{{ $p[0] }}" style="width:16px;height:16px;color:{{ $p[1] }}"></i>
            </div>
            <div style="font-weight:700;color:#e2e8f0;font-size:13px;margin-bottom:6px">{{ $p[2] }}</div>
            <div style="font-size:var(--fs-xs);color:var(--text-muted);line-height:1.6">{{ $p[3] }}</div>
        </div>
        @endforeach
    </div>
</div>

<div class="divider"></div>

{{-- ── GOVERNANCE STRUCTURE ─────────────────────────────────────── --}}
<div class="section" style="max-width:960px;margin:0 auto">
    <div class="section-label" style="margin-bottom:16px">
        <i data-lucide="network" style="width:12px;height:12px"></i>
        {{ $isFr ? 'Structure de gouvernance' : 'Governance structure' }}
    </div>
    <h2 class="section-title">{{ $isFr ? 'Cinq comités, une mission : la sécurité du patient' : 'Five committees, one mission: patient safety' }}</h2>
    <p style="color:var(--text-muted);max-width:700px;font-size:14px;line-height:1.75;margin:12px 0 32px">
        {{ $isFr
            ? 'La gouvernance clinique OPES est structurée en cinq comités permanents, chacun doté d\'un mandat précis et d\'une composition multidisciplinaire, couvrant médecins, infirmiers, pharmaciens, biologistes et informaticiens cliniques.'
            : 'OPES clinical governance is structured across five standing committees, each with a defined mandate and multidisciplinary membership spanning physicians, nurses, pharmacists, lab specialists, and clinical informaticians.' }}
    </p>
    <div class="pi-grid" style="max-width:960px">
        @php
        $committees = $isFr ? [
            ['icon'=>'shield-check','color'=>'#00C896','title'=>'Conseil de gouvernance clinique',
             'role' => 'Supervision clinique générale',
             'resp' => ['Stratégie clinique','Supervision de la sécurité','Approbation des politiques cliniques','Supervision des risques cliniques']],
            ['icon'=>'alert-triangle','color'=>'#1A6FE8','title'=>'Comité de sécurité clinique',
             'role' => 'Sécurité & incidents',
             'resp' => ['Revues des risques cliniques','Évaluations de sécurité','Revues d\'incidents','Recommandations de sécurité']],
            ['icon'=>'file-text','color'=>'#00C896','title'=>'Comité de contenu clinique',
             'role' => 'Contenu & protocoles',
             'resp' => ['Modèles cliniques','Protocoles cliniques','Règles cliniques','Terminologies cliniques']],
            ['icon'=>'cpu','color'=>'#1A6FE8','title'=>'Comité de revue CDSS',
             'role' => 'Aide à la décision',
             'resp' => ['Validation des règles de décision','Revue des alertes','Gouvernance des règles cliniques','Contrôle de la fatigue d\'alerte']],
            ['icon'=>'users','color'=>'#00C896','title'=>'Panels consultatifs de spécialité',
             'role' => 'Cardiologie · Labo · Pharma · Radio · Obstétrique · Pédiatrie',
             'resp' => ['Validation spécialisée des protocoles','Revue des cas d\'usage spécifiques','Recommandations par discipline','Support aux déploiements nationaux']],
        ] : [
            ['icon'=>'shield-check','color'=>'#00C896','title'=>'Clinical Governance Board',
             'role' => 'Overall clinical oversight',
             'resp' => ['Clinical strategy','Patient safety oversight','Clinical policy approval','Clinical risk oversight']],
            ['icon'=>'alert-triangle','color'=>'#1A6FE8','title'=>'Clinical Safety Committee',
             'role' => 'Safety & incidents',
             'resp' => ['Clinical risk reviews','Safety assessments','Incident reviews','Safety recommendations']],
            ['icon'=>'file-text','color'=>'#00C896','title'=>'Clinical Content Committee',
             'role' => 'Content & protocols',
             'resp' => ['Clinical templates','Clinical protocols','Clinical rules','Clinical terminologies']],
            ['icon'=>'cpu','color'=>'#1A6FE8','title'=>'CDSS Review Committee',
             'role' => 'Decision support',
             'resp' => ['Decision support validation','Alert reviews','Clinical rule governance','Alert fatigue control']],
            ['icon'=>'users','color'=>'#00C896','title'=>'Specialty Advisory Panels',
             'role' => 'Cardiology · Lab · Pharmacy · Radiology · Obstetrics · Paediatrics',
             'resp' => ['Specialty protocol validation','Specific use-case review','Discipline-level recommendations','National deployment advisory']],
        ];
        @endphp
        @foreach($committees as $c)
        <div class="pi-card" style="flex-direction:column;align-items:flex-start">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:12px">
                <div style="width:38px;height:38px;border-radius:10px;background:{{ $c['color'] }}15;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    <i data-lucide="{{ $c['icon'] }}" style="width:17px;height:17px;color:{{ $c['color'] }}"></i>
                </div>
                <div>
                    <div style="font-weight:700;color:#e2e8f0;font-size:13px">{{ $c['title'] }}</div>
                    <div style="font-size:var(--fs-2xs);color:{{ $c['color'] }};font-weight:600;text-transform:uppercase;letter-spacing:0.06em">{{ $c['role'] }}</div>
                </div>
            </div>
            <ul style="list-style:none;padding:0;margin:0;display:flex;flex-direction:column;gap:6px">
                @foreach($c['resp'] as $r)
                <li style="display:flex;align-items:flex-start;gap:6px;font-size:var(--fs-xs);color:var(--text-muted)">
                    <i data-lucide="chevron-right" style="width:11px;height:11px;color:{{ $c['color'] }};flex-shrink:0;margin-top:2px"></i>{{ $r }}
                </li>
                @endforeach
            </ul>
        </div>
        @endforeach
    </div>
</div>

<div class="divider"></div>

{{-- ── CLINICAL RISK CATEGORIES ─────────────────────────────────── --}}
<div class="section" style="max-width:960px;margin:0 auto">
    <div class="section-label" style="margin-bottom:16px">
        <i data-lucide="shield-alert" style="width:12px;height:12px"></i>
        {{ $isFr ? 'Risques cliniques' : 'Clinical risk categories' }}
    </div>
    <h2 class="section-title">{{ $isFr ? 'Quatre catégories de risques, des contrôles pour chacune' : 'Four risk categories, controls for each' }}</h2>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:16px;margin-top:32px">
        @php
        $risks = $isFr ? [
            ['icon'=>'fingerprint','color'=>'#1A6FE8','cat'=>'Identification des patients',
             'examples'=>['Patients en doublon','Mauvaise correspondance de dossiers'],
             'controls'=>['Identifiant de santé OPESCare','Annuaire des patients (MPI)','Processus de vérification']],
            ['icon'=>'pill','color'=>'#00C896','cat'=>'Médicaments',
             'examples'=>['Interactions médicamenteuses','Allergies','Médicaments en double'],
             'controls'=>['Aide à la décision clinique','Vérification des allergies','Règles d\'interaction médicamenteuse']],
            ['icon'=>'microscope','color'=>'#1A6FE8','cat'=>'Diagnostics',
             'examples'=>['Résultats manquants','Résultats retardés','Mapping de résultats incorrect'],
             'controls'=>['Validation des workflows','Pistes d\'audit','Systèmes de notification']],
            ['icon'=>'clipboard','color'=>'#00C896','cat'=>'Documentation',
             'examples'=>['Dossiers incomplets','Informations manquantes'],
             'controls'=>['Champs obligatoires','Règles de validation','Audits qualité']],
        ] : [
            ['icon'=>'fingerprint','color'=>'#1A6FE8','cat'=>'Patient identification',
             'examples'=>['Duplicate patients','Incorrect patient matching'],
             'controls'=>['OPESCare Health ID','Master Patient Index (MPI)','Verification processes']],
            ['icon'=>'pill','color'=>'#00C896','cat'=>'Medication',
             'examples'=>['Drug interactions','Allergies','Duplicate medications'],
             'controls'=>['Clinical Decision Support','Allergy checks','Drug interaction rules']],
            ['icon'=>'microscope','color'=>'#1A6FE8','cat'=>'Diagnostics',
             'examples'=>['Missing results','Delayed results','Incorrect result mapping'],
             'controls'=>['Workflow validation','Audit trails','Notification systems']],
            ['icon'=>'clipboard','color'=>'#00C896','cat'=>'Documentation',
             'examples'=>['Incomplete records','Missing information'],
             'controls'=>['Mandatory fields','Validation rules','Quality audits']],
        ];
        @endphp
        @foreach($risks as $r)
        <div style="background:#0F172A;border:1px solid #1e293b;border-radius:12px;padding:18px 16px">
            <div style="display:flex;align-items:center;gap:8px;margin-bottom:14px">
                <div style="width:34px;height:34px;border-radius:9px;background:{{ $r['color'] }}15;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    <i data-lucide="{{ $r['icon'] }}" style="width:15px;height:15px;color:{{ $r['color'] }}"></i>
                </div>
                <div style="font-weight:700;color:#e2e8f0;font-size:13px">{{ $r['cat'] }}</div>
            </div>
            <div style="font-size:var(--fs-2xs);color:var(--text-faint);text-transform:uppercase;letter-spacing:0.07em;font-weight:700;margin-bottom:6px">{{ $isFr ? 'Exemples' : 'Examples' }}</div>
            @foreach($r['examples'] as $ex)
            <div style="font-size:var(--fs-xs);color:var(--text-muted);padding:3px 0;border-bottom:1px solid #1e293b20">{{ $ex }}</div>
            @endforeach
            <div style="font-size:var(--fs-2xs);color:var(--text-faint);text-transform:uppercase;letter-spacing:0.07em;font-weight:700;margin:10px 0 6px">{{ $isFr ? 'Contrôles' : 'Controls' }}</div>
            @foreach($r['controls'] as $ctrl)
            <div style="display:flex;align-items:center;gap:5px;font-size:var(--fs-xs);color:var(--text-muted);padding:3px 0">
                <i data-lucide="check" style="width:10px;height:10px;color:{{ $r['color'] }};flex-shrink:0"></i>{{ $ctrl }}
            </div>
            @endforeach
        </div>
        @endforeach
    </div>
</div>

<div class="divider"></div>

{{-- ── CDSS GOVERNANCE ──────────────────────────────────────────── --}}
<div class="section" style="max-width:960px;margin:0 auto">
    <div class="section-label" style="margin-bottom:16px">
        <i data-lucide="brain" style="width:12px;height:12px"></i>
        {{ $isFr ? 'Gouvernance du CDSS' : 'CDSS governance' }}
    </div>
    <h2 class="section-title">{{ $isFr ? 'Aide à la décision clinique gouvernée à chaque étape' : 'Clinical Decision Support governed at every stage' }}</h2>
    <p style="color:var(--text-muted);max-width:700px;font-size:14px;line-height:1.75;margin:12px 0 32px">
        {{ $isFr
            ? 'Le cadre s\'applique à OPES CDSS, OPES Triage et OPES Clinical Intelligence. Aucune règle clinique n\'est déployée sans avoir suivi un processus d\'approbation formel, versionnée et tracée.'
            : 'The framework applies to OPES CDSS, OPES Triage, and OPES Clinical Intelligence. No clinical rule is deployed without a formal approval process, version control, and full traceability.' }}
    </p>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:32px;align-items:start">
        {{-- Rule approval flow --}}
        <div>
            <div style="font-weight:700;color:#e2e8f0;font-size:13px;margin-bottom:20px">
                {{ $isFr ? 'Processus d\'approbation des règles' : 'Rule approval process' }}
            </div>
            @foreach($isFr
                ? [['pen-line','var(--text-faint)','Auteur','Rédaction de la règle clinique'],['search','#00C896','Revue clinique','Examen par le comité de contenu'],['check-circle','#1A6FE8','Validation','Tests fonctionnels et cliniques'],['badge-check','#00C896','Approbation','Signature du comité CDSS'],['rocket','#1A6FE8','Déploiement','Mise en production versionnée']]
                : [['pen-line','var(--text-faint)','Author','Clinical rule authored'],['search','#00C896','Clinical review','Content committee examination'],['check-circle','#1A6FE8','Validation','Functional and clinical testing'],['badge-check','#00C896','Approval','CDSS committee sign-off'],['rocket','#1A6FE8','Deployment','Versioned production release']]
            as $idx => $step)
            <div style="display:flex;align-items:flex-start;gap:12px;margin-bottom:{{ $idx < 4 ? '0' : '0' }}">
                <div style="display:flex;flex-direction:column;align-items:center">
                    <div style="width:34px;height:34px;border-radius:50%;background:{{ $step[1] }}20;border:1px solid {{ $step[1] }}40;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                        <i data-lucide="{{ $step[0] }}" style="width:14px;height:14px;color:{{ $step[1] }}"></i>
                    </div>
                    @if($idx < 4)
                    <div style="width:1px;height:20px;background:#1e293b;margin:3px 0"></div>
                    @endif
                </div>
                <div style="padding-top:6px;margin-bottom:{{ $idx < 4 ? '0' : '0' }}">
                    <div style="font-weight:700;color:#e2e8f0;font-size:var(--fs-xs)">{{ $step[2] }}</div>
                    <div style="font-size:var(--fs-xs);color:var(--text-muted)">{{ $step[3] }}</div>
                </div>
            </div>
            @endforeach
        </div>
        {{-- Alert governance --}}
        <div>
            <div style="font-weight:700;color:#e2e8f0;font-size:13px;margin-bottom:14px">
                {{ $isFr ? 'Gouvernance des alertes' : 'Alert governance' }}
            </div>
            <p style="font-size:var(--fs-xs);color:var(--text-muted);line-height:1.65;margin-bottom:14px">
                {{ $isFr
                    ? 'La fatigue d\'alerte réduit la sécurité des patients. La plateforme régit la priorité, la fréquence, l\'escalade et la révision de chaque alerte.'
                    : 'Alert fatigue reduces patient safety. The platform governs priority, frequency, escalation, and review of every alert.' }}
            </p>
            @foreach($isFr
                ? [['#ef4444','Critique','Risque vital immédiat — interruption obligatoire'],['#f97316','Élevée','Risque clinique significatif — action immédiate'],['#eab308','Moyenne','Attention requise — action dans les 2h'],['var(--text-muted)','Informative','Information contextuelle — aucune action immédiate']]
                : [['#ef4444','Critical','Immediate life risk — mandatory interrupt'],['#f97316','High','Significant clinical risk — immediate action'],['#eab308','Medium','Attention required — action within 2 h'],['var(--text-muted)','Informational','Contextual information — no immediate action']]
            as $al)
            <div style="display:flex;align-items:center;gap:10px;padding:9px 12px;background:#0F172A;border-radius:8px;margin-bottom:6px;border-left:3px solid {{ $al[0] }}">
                <div style="width:8px;height:8px;border-radius:50%;background:{{ $al[0] }};flex-shrink:0"></div>
                <div>
                    <div style="font-weight:700;font-size:var(--fs-xs);color:#e2e8f0">{{ $al[1] }}</div>
                    <div style="font-size:var(--fs-xs);color:var(--text-muted)">{{ $al[2] }}</div>
                </div>
            </div>
            @endforeach
            <div style="margin-top:14px;padding:12px;background:#0f1a2e;border:1px solid rgba(0,200,150,0.15);border-radius:8px">
                <div style="font-size:var(--fs-xs);color:var(--text-muted);line-height:1.6">
                    <i data-lucide="info" style="width:11px;height:11px;color:#00C896;margin-right:4px;vertical-align:middle"></i>
                    {{ $isFr
                        ? 'Toutes les règles cliniques maintiennent un numéro de version, un auteur, une date d\'approbation et un historique complet des modifications.'
                        : 'All clinical rules maintain a version number, author, approval date, and complete change history.' }}
                </div>
            </div>
        </div>
    </div>
</div>

<div class="divider"></div>

{{-- ── VALIDATION FRAMEWORK ─────────────────────────────────────── --}}
<div class="section" style="max-width:960px;margin:0 auto">
    <div class="section-label" style="margin-bottom:16px">
        <i data-lucide="check-square" style="width:12px;height:12px"></i>
        {{ $isFr ? 'Validation clinique' : 'Clinical validation' }}
    </div>
    <h2 class="section-title">{{ $isFr ? 'Validation en 4 étapes avant tout déploiement' : '4-stage validation before every deployment' }}</h2>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:16px;margin-top:32px">
        @foreach($isFr
            ? [['1','#00C896','Validation fonctionnelle','Les fonctions se comportent comme spécifié. Tests automatisés et manuels sur tous les modules cliniques.'],['2','#1A6FE8','Validation clinique','Les praticiens valident que le comportement clinique est correct, sûr et conforme aux bonnes pratiques.'],['3','#00C896','Validation des workflows','Les flux de travail cliniques complets sont examinés pour la sécurité, l\'efficacité et l\'utilisabilité.'],['4','#1A6FE8','Recette utilisateur (UAT)','Les utilisateurs finaux confirment que le système répond à leurs besoins opérationnels réels.']]
            : [['1','#00C896','Functional validation','Features behave as specified. Automated and manual testing across all clinical modules.'],['2','#1A6FE8','Clinical validation','Practitioners confirm that clinical behaviour is correct, safe, and compliant with best practice.'],['3','#00C896','Workflow validation','Full clinical workflows are reviewed for safety, efficiency, and usability.'],['4','#1A6FE8','User acceptance testing','End users confirm the system meets their real operational needs.']]
        as $v)
        <div style="background:#0F172A;border:1px solid {{ $v[1] }}30;border-radius:12px;padding:20px 16px;position:relative">
            <div style="font-size:28px;font-weight:900;color:{{ $v[1] }}18;position:absolute;top:12px;right:16px;line-height:1">{{ $v[0] }}</div>
            <div style="font-size:var(--fs-2xs);color:{{ $v[1] }};font-weight:800;letter-spacing:0.1em;text-transform:uppercase;margin-bottom:6px">{{ $isFr ? 'Étape' : 'Stage' }} {{ $v[0] }}</div>
            <div style="font-weight:700;color:#e2e8f0;font-size:13px;margin-bottom:8px">{{ $v[2] }}</div>
            <div style="font-size:var(--fs-xs);color:var(--text-muted);line-height:1.6">{{ $v[3] }}</div>
        </div>
        @endforeach
    </div>
</div>

<div class="divider"></div>

{{-- ── INCIDENT MANAGEMENT ──────────────────────────────────────── --}}
<div class="section" style="max-width:960px;margin:0 auto">
    <div class="section-label" style="margin-bottom:16px">
        <i data-lucide="siren" style="width:12px;height:12px"></i>
        {{ $isFr ? 'Gestion des incidents cliniques' : 'Clinical incident management' }}
    </div>
    <h2 class="section-title">{{ $isFr ? 'De la détection à l\'action corrective' : 'From detection to corrective action' }}</h2>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:40px;margin-top:32px;align-items:start">
        <div>
            <p style="font-size:14px;color:var(--text-muted);line-height:1.75;margin-bottom:24px">
                {{ $isFr
                    ? 'Tout incident clinique — alerte manquante, calcul incorrect, défaillance de workflow, erreur de documentation — est soumis à un processus de revue formel en cinq étapes.'
                    : 'Every clinical incident — missing alert, incorrect calculation, workflow failure, documentation error — is subject to a five-step formal review process.' }}
            </p>
            @foreach($isFr
                ? [['search','#00C896','Détection','Identification de l\'incident par les utilisateurs, les audits ou les systèmes automatisés.'],['file-search','#1A6FE8','Investigation','Analyse détaillée par le Comité de sécurité clinique.'],['git-branch','#00C896','Analyse des causes','Identification de la cause profonde et des facteurs contributifs.'],['wrench','#1A6FE8','Action corrective','Mise en œuvre des corrections et améliorations.'],['activity','#00C896','Surveillance','Suivi de l\'efficacité des actions correctives.']]
                : [['search','#00C896','Detection','Incident identified by users, audits, or automated systems.'],['file-search','#1A6FE8','Investigation','Detailed analysis by the Clinical Safety Committee.'],['git-branch','#00C896','Root cause analysis','Identification of root cause and contributing factors.'],['wrench','#1A6FE8','Corrective action','Implementation of fixes and improvements.'],['activity','#00C896','Monitoring','Tracking the effectiveness of corrective actions.']]
            as $idx => $step)
            <div style="display:flex;gap:12px;margin-bottom:{{ $idx < 4 ? '0' : '0' }}">
                <div style="display:flex;flex-direction:column;align-items:center">
                    <div style="width:32px;height:32px;border-radius:50%;background:{{ $step[1] }}20;border:1px solid {{ $step[1] }}40;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                        <i data-lucide="{{ $step[0] }}" style="width:13px;height:13px;color:{{ $step[1] }}"></i>
                    </div>
                    @if($idx < 4)<div style="width:1px;height:18px;background:#1e293b;margin:2px 0"></div>@endif
                </div>
                <div style="padding-top:5px;margin-bottom:{{ $idx < 4 ? '8px' : '0' }}">
                    <div style="font-weight:700;color:#e2e8f0;font-size:var(--fs-xs)">{{ $step[2] }}</div>
                    <div style="font-size:var(--fs-xs);color:var(--text-muted);line-height:1.5">{{ $step[3] }}</div>
                </div>
            </div>
            @endforeach
        </div>
        <div>
            <div style="background:#0F172A;border:1px solid #1e293b;border-radius:12px;padding:20px 18px;margin-bottom:16px">
                <div style="font-weight:700;color:#e2e8f0;font-size:13px;margin-bottom:14px">
                    {{ $isFr ? 'KPIs cliniques suivis' : 'Clinical KPIs tracked' }}
                </div>
                @foreach($isFr
                    ? [['Taux d\'erreur de médication','#00C896'],['Complétude de la documentation','#1A6FE8'],['Conformité aux alertes d\'allergie','#00C896'],['Taux de doublons de patients','#1A6FE8'],['Taux d\'acceptation des alertes','#00C896'],['Taux de complétion des workflows','#1A6FE8']]
                    : [['Medication error rate','#00C896'],['Documentation completeness','#1A6FE8'],['Allergy alert compliance','#00C896'],['Duplicate patient rate','#1A6FE8'],['Clinical alert acceptance rate','#00C896'],['Clinical workflow completion rate','#1A6FE8']]
                as $kpi)
                <div style="display:flex;align-items:center;gap:8px;padding:8px 0;border-bottom:1px solid #1e293b">
                    <div style="width:6px;height:6px;border-radius:50%;background:{{ $kpi[1] }};flex-shrink:0"></div>
                    <span style="font-size:var(--fs-xs);color:var(--text-muted)">{{ $kpi[0] }}</span>
                </div>
                @endforeach
            </div>
            <div style="background:#0f1a2e;border:1px solid rgba(0,200,150,0.15);border-radius:12px;padding:16px 18px">
                <div style="font-weight:700;color:#00C896;font-size:var(--fs-xs);margin-bottom:8px">
                    {{ $isFr ? 'Terminologies cliniques supportées' : 'Supported clinical terminologies' }}
                </div>
                @foreach([['ICD-11','WHO International Classification of Diseases'],['SNOMED CT','Systematized Nomenclature of Medicine'],['LOINC','Logical Observation Identifiers Names and Codes']] as $term)
                <div style="display:flex;align-items:flex-start;gap:8px;padding:7px 0;border-bottom:1px solid #1e293b20">
                    <div style="font-size:var(--fs-xs);font-weight:700;color:#e2e8f0;white-space:nowrap;min-width:80px">{{ $term[0] }}</div>
                    <div style="font-size:var(--fs-xs);color:var(--text-faint)">{{ $term[1] }}</div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<div class="divider"></div>

{{-- ── SCOPE ────────────────────────────────────────────────────── --}}
<div class="section" style="max-width:960px;margin:0 auto;text-align:center">
    <div class="section-label" style="justify-content:center;margin-bottom:16px">
        <i data-lucide="layers" style="width:12px;height:12px"></i>
        {{ $isFr ? 'Périmètre d\'application' : 'Scope of application' }}
    </div>
    <h2 class="section-title">{{ $isFr ? 'Ce cadre s\'applique à toute la plateforme OPES Health OS' : 'This framework applies across the entire OPES Health OS platform' }}</h2>
    <div style="display:flex;flex-wrap:wrap;justify-content:center;gap:10px;margin-top:24px;max-width:700px;margin-left:auto;margin-right:auto">
        @foreach(['OPES Health OS','OPES Clinic','OPES Hospital','OPES Specialty Suite','OPES Care','OPES Clinical Intelligence','OPES Academy','Customer Deployments'] as $scope)
        <div style="background:#0F172A;border:1px solid #1e293b;border-radius:20px;padding:6px 14px;font-size:var(--fs-xs);color:var(--text-muted);display:flex;align-items:center;gap:5px">
            <i data-lucide="check" style="width:10px;height:10px;color:#00C896"></i>{{ $scope }}
        </div>
        @endforeach
    </div>
</div>

{{-- ── CTA ──────────────────────────────────────────────────────── --}}
<div class="demo-section">
    <h2>{{ $isFr ? 'Vous souhaitez en savoir plus sur notre approche clinique ?' : 'Want to learn more about our clinical approach?' }}</h2>
    <p>{{ $isFr
        ? 'Notre équipe clinique peut vous présenter le cadre de gouvernance, les comités en place et les processus de validation en détail.'
        : 'Our clinical team can walk you through the governance framework, standing committees, and validation processes in detail.' }}</p>
    <div style="display:flex;gap:14px;justify-content:center;flex-wrap:wrap;margin-top:12px">
        <a href="{{ url($locale.'/contact') }}" class="btn-primary">
            {{ $isFr ? 'Parler à l\'équipe clinique' : 'Talk to the clinical team' }}
            <i data-lucide="arrow-right" style="width:15px;height:15px"></i>
        </a>
        <a href="{{ url($locale.'/compliance') }}" class="btn-secondary">
            {{ $isFr ? 'Conformité & sécurité' : 'Compliance & Trust' }}
            <i data-lucide="shield-check" style="width:15px;height:15px;color:var(--text-muted)"></i>
        </a>
    </div>
</div>

</x-layouts.app>

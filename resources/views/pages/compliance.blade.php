@php $locale = app()->getLocale(); $isFr = $locale === 'fr'; @endphp

<x-layouts.app
    title="{{ $isFr ? 'Conformité & Sécurité — OPES Health Systems' : 'Compliance & Trust — OPES Health Systems' }}"
    description="{{ $isFr
        ? 'Conformité OHADA, réglementation camerounaise des données de santé, cybersécurité, continuité de service et interopérabilité HL7 FHIR.'
        : 'OHADA compliance, Cameroonian health data regulations, cybersecurity framework, business continuity, and HL7 FHIR interoperability.' }}">

{{-- HERO --}}
<div class="pricing-hero">
    <div class="section-label" style="justify-content:center;margin-bottom:16px">
        <i data-lucide="shield-check" style="width:12px;height:12px"></i>
        {{ $isFr ? 'Conformité & Sécurité' : 'Compliance & Trust' }}
    </div>
    <h1>
        {{ $isFr ? 'Conçu pour la confiance.' : 'Built for trust.' }}
        <span class="gradient-text">{{ $isFr ? 'Certifié pour l\'Afrique.' : 'Certified for Africa.' }}</span>
    </h1>
    <p>
        {{ $isFr
            ? 'OPES Health Systems opère selon les normes les plus strictes de conformité réglementaire, de sécurité des données et de continuité de service — pour que vos données de santé restent protégées, souveraines et disponibles en permanence.'
            : 'OPES Health Systems operates to the highest standards of regulatory compliance, data security, and service continuity — so your health data stays protected, sovereign, and always available.' }}
    </p>
</div>

{{-- TRUST STATS --}}
<div class="section" style="max-width:900px;margin:0 auto;text-align:center">
    <div class="stats-bar">
        @foreach($isFr
            ? [['OHADA','Conforme'],['AES-256','Chiffrement'],['99,9 %','Disponibilité SLA'],['FHIR R4','Interopérabilité']]
            : [['OHADA','Compliant'],['AES-256','Encryption'],['99.9%','Uptime SLA'],['FHIR R4','Interoperable']]
        as $s)
        <div class="stat-item">
            <div class="stat-value">{{ $s[0] }}</div>
            <div class="stat-label">{{ $s[1] }}</div>
        </div>
        @endforeach
    </div>
</div>

<div class="divider"></div>

{{-- REGULATORY COMPLIANCE --}}
<div class="section" style="max-width:860px;margin:0 auto">
    <div class="section-label" style="margin-bottom:16px">
        <i data-lucide="scale" style="width:12px;height:12px"></i>
        {{ $isFr ? 'Cadre réglementaire' : 'Regulatory framework' }}
    </div>
    <h2 class="section-title">{{ $isFr ? 'Conformité légale & réglementaire' : 'Legal & regulatory compliance' }}</h2>

    <div class="blog-art-body prose" style="margin-top:28px">

        <h3>{{ $isFr ? '1. Cadre juridique OHADA' : '1. OHADA Legal Framework' }}</h3>
        <p>{{ $isFr
            ? 'OPES Health Systems SARL est constituée et opère sous l\'Organisation pour l\'Harmonisation en Afrique du Droit des Affaires (OHADA). Nos contrats commerciaux, accords de traitement des données et gouvernance d\'entreprise sont conformes à l\'Acte Uniforme OHADA relatif au Droit Commercial Général (AUDCG) et à l\'Acte Uniforme relatif aux Sociétés Commerciales (AUSC).'
            : 'OPES Health Systems SARL is incorporated and operates under the Organisation pour l\'Harmonisation en Afrique du Droit des Affaires (OHADA) Uniform Acts. Our commercial contracts, data processing agreements, and corporate governance comply with the OHADA Uniform Act on General Commercial Law (AUDCG) and the Uniform Act on Commercial Companies (AUSC).' }}</p>

        <h3>{{ $isFr ? '2. Réglementation camerounaise des données de santé' : '2. Cameroonian Health Data Regulations' }}</h3>
        <p>{{ $isFr
            ? 'Les données de santé des patients traitées via la plateforme OPES sont gérées conformément à la Loi n° 2010/013 du 21 décembre 2010 sur les Communications Électroniques et la Loi n° 2010/021 du 21 décembre 2010 sur le Commerce Électronique au Cameroun. OPES s\'engage à aligner ses pratiques de traitement des données dans les 12 mois suivant l\'entrée en vigueur de toute nouvelle norme sectorielle.'
            : 'Patient health data processed through the OPES Platform is handled in accordance with Law No. 2010/013 of 21 December 2010 on Electronic Communications and Law No. 2010/021 of 21 December 2010 on Electronic Commerce in Cameroon. OPES commits to align Platform data handling practices within 12 months of any new sector-specific standard.' }}</p>

        <h3>{{ $isFr ? '3. Alignement Stratégie Numérique MoH 2026–2030' : '3. Ministry of Health 2026–2030 Alignment' }}</h3>
        <p>{{ $isFr
            ? 'Tous les produits OPES sont conçus pour s\'intégrer dans la Stratégie de Santé Numérique du Ministère de la Santé du Cameroun 2026–2030, notamment :'
            : 'All OPES products are designed to operate within the Cameroon Ministry of Health Digital Health Strategy 2026–2030, including:' }}</p>
        <ul>
            @foreach($isFr
                ? ['Structures de reporting compatibles DHIS2','Formats de données de surveillance des maladies alignés sur les exigences nationales','Soutien aux programmes de santé nationaux (VIH/SIDA, paludisme, tuberculose, vaccination)','Classification des établissements et taxonomie des services conformes aux normes MoH']
                : ['Reporting structures compatible with the DHIS2 national health information system','Disease surveillance data formats aligned with national reporting requirements','Support for national health programmes (HIV/AIDS, malaria, tuberculosis, vaccination)','Facility classification and service taxonomy consistent with MoH standards']
            as $item)
            <li>{{ $item }}</li>
            @endforeach
        </ul>

        <h3>{{ $isFr ? '4. Interopérabilité HL7 FHIR' : '4. HL7 FHIR Interoperability' }}</h3>
        <p>{{ $isFr
            ? 'OPES implémente HL7 FHIR R4 comme standard d\'interopérabilité dans tous les modules de la plateforme. Cela garantit que les données patients peuvent être partagées entre systèmes OPES, avec les registres nationaux et les institutions partenaires dans un format standardisé et interopérable, sans enfermement propriétaire.'
            : 'OPES implements HL7 FHIR R4 as the interoperability standard across all Platform modules. This ensures patient data can be shared between OPES systems, with national registries, and with partner health institutions in a standardised, machine-readable format without proprietary lock-in.' }}</p>

        <h3>{{ $isFr ? '5. Conformité bilingue' : '5. Bilingual Compliance' }}</h3>
        <p>{{ $isFr
            ? 'Conformément à la Constitution de la République du Cameroun qui reconnaît le français et l\'anglais comme langues officielles, tous les modules de la plateforme OPES sont entièrement bilingues. Les dossiers patients, formulaires cliniques, rapports et interfaces utilisateurs sont disponibles en français et en anglais.'
            : 'In conformity with the Constitution of the Republic of Cameroon, which recognises French and English as official languages, all OPES Platform modules are fully bilingual. Patient records, clinical forms, reports, and user interfaces are available in both French and English.' }}</p>

        <h3>{{ $isFr ? '6. Souveraineté des données' : '6. Data Sovereignty' }}</h3>
        <p>{{ $isFr
            ? 'Des options de déploiement on-premise sont disponibles pour tous les modules OPES, garantissant que les données restent au Cameroun ou dans la juridiction du client. Les services hébergés dans le cloud utilisent des datacenters situés dans la région CEMAC ou bénéficient de garanties contractuelles de localisation des données.'
            : 'On-premise deployment options are available for all OPES modules, ensuring that health facility data remains within Cameroon or within the customer\'s jurisdiction. Cloud-hosted services use data centres within the CEMAC region or with contractual data localisation guarantees.' }}</p>

        <h3>{{ $isFr ? '7. Piste d\'audit & responsabilité' : '7. Audit Trail & Accountability' }}</h3>
        <p>{{ $isFr
            ? 'La plateforme maintient des journaux d\'audit complets de toutes les actions utilisateurs, accès aux données et décisions cliniques. Les journaux sont infalsifiables et conservés conformément aux obligations légales des établissements de santé. Les administrateurs système de votre établissement ont un accès complet aux pistes d\'audit.'
            : 'The Platform maintains comprehensive audit logs of all user actions, data access, and clinical decisions. Logs are tamper-evident and retained in accordance with health facility statutory obligations. System administrators at your facility retain full access to audit trails.' }}</p>

    </div>
</div>

<div class="divider"></div>

{{-- CYBERSECURITY --}}
<div class="section" style="max-width:960px;margin:0 auto">
    <div class="section-label" style="margin-bottom:16px">
        <i data-lucide="lock" style="width:12px;height:12px"></i>
        {{ $isFr ? 'Cybersécurité' : 'Cybersecurity' }}
    </div>
    <h2 class="section-title">{{ $isFr ? 'Cadre de sécurité de l\'information' : 'Information security framework' }}</h2>
    <p style="color:var(--text-muted);max-width:720px;font-size:15px;line-height:1.75;margin-bottom:36px">
        {{ $isFr
            ? 'OPES Health Systems opère selon un cadre de cybersécurité multicouches qui protège les données de santé à chaque niveau — de l\'identité au réseau, du stockage à la transmission.'
            : 'OPES Health Systems operates a multi-layered cybersecurity framework that protects health data at every level — from identity to network, from storage to transmission.' }}
    </p>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:16px">
        @php
        $secSections = $isFr ? [
            ['icon'=>'users',          'color'=>'#00C896','title'=>'Gestion des identités (IAM)',    'desc'=>'Authentification multi-facteurs (MFA), Single Sign-On (SSO), gestion des sessions, révocation d\'accès immédiate, et principe du moindre privilège pour chaque rôle.'],
            ['icon'=>'eye',            'color'=>'#1A6FE8','title'=>'Surveillance & détection',       'desc'=>'Surveillance continue des événements de sécurité (SIEM), détection des anomalies, alertes en temps réel, et journalisation centralisée avec rétention 24 mois.'],
            ['icon'=>'alert-triangle', 'color'=>'#00C896','title'=>'Réponse aux incidents',          'desc'=>'Procédures de réponse aux incidents documentées, équipe d\'astreinte 24/7, notification sous 72h en cas de violation de données, et forensique post-incident.'],
            ['icon'=>'lock',           'color'=>'#1A6FE8','title'=>'Chiffrement',                    'desc'=>'AES-256 pour les données au repos, TLS 1.3 pour les données en transit, gestion des clés par module avec rotation automatique.'],
            ['icon'=>'code-2',         'color'=>'#00C896','title'=>'Sécurité du développement',      'desc'=>'Analyse statique du code (SAST), tests de pénétration trimestriels, revues de sécurité avant chaque déploiement, et formation des développeurs à l\'OWASP Top 10.'],
            ['icon'=>'shield',         'color'=>'#1A6FE8','title'=>'Sécurité du réseau',             'desc'=>'Segmentation réseau, pare-feux applicatifs (WAF), protection DDoS, VPN obligatoire pour les accès administrateurs distants, et isolation des environnements.'],
        ] : [
            ['icon'=>'users',          'color'=>'#00C896','title'=>'Identity & Access Management',   'desc'=>'Multi-factor authentication (MFA), Single Sign-On (SSO), session management, immediate access revocation, and least-privilege principle per role.'],
            ['icon'=>'eye',            'color'=>'#1A6FE8','title'=>'Monitoring & Detection',         'desc'=>'Continuous security event monitoring (SIEM), anomaly detection, real-time alerts, and centralised logging with 24-month retention.'],
            ['icon'=>'alert-triangle', 'color'=>'#00C896','title'=>'Incident Response',              'desc'=>'Documented incident response procedures, 24/7 on-call team, 72-hour data breach notification, and post-incident forensics.'],
            ['icon'=>'lock',           'color'=>'#1A6FE8','title'=>'Encryption',                     'desc'=>'AES-256 for data at rest, TLS 1.3 for data in transit, per-module key management with automatic rotation.'],
            ['icon'=>'code-2',         'color'=>'#00C896','title'=>'Secure Development',             'desc'=>'Static code analysis (SAST), quarterly penetration testing, security reviews before each release, and OWASP Top 10 developer training.'],
            ['icon'=>'shield',         'color'=>'#1A6FE8','title'=>'Network Security',               'desc'=>'Network segmentation, web application firewall (WAF), DDoS protection, mandatory VPN for remote admin access, and environment isolation.'],
        ];
        @endphp
        @foreach($secSections as $sec)
        <div class="pi-card" style="flex-direction:column;align-items:flex-start">
            <div style="width:40px;height:40px;border-radius:10px;background:{{ $sec['color'] }}15;display:flex;align-items:center;justify-content:center;margin-bottom:12px">
                <i data-lucide="{{ $sec['icon'] }}" style="width:18px;height:18px;color:{{ $sec['color'] }}"></i>
            </div>
            <div style="font-weight:700;color:#e2e8f0;font-size:14px;margin-bottom:6px">{{ $sec['title'] }}</div>
            <div style="font-size:13px;color:var(--text-muted);line-height:1.6">{{ $sec['desc'] }}</div>
        </div>
        @endforeach
    </div>
</div>

<div class="divider"></div>

{{-- BUSINESS CONTINUITY --}}
<div class="section" style="max-width:960px;margin:0 auto">
    <div class="section-label" style="margin-bottom:16px">
        <i data-lucide="refresh-cw" style="width:12px;height:12px"></i>
        {{ $isFr ? 'Continuité d\'activité' : 'Business continuity' }}
    </div>
    <h2 class="section-title">{{ $isFr ? 'Continuité & reprise après sinistre' : 'Business continuity & disaster recovery' }}</h2>
    <p style="color:var(--text-muted);max-width:720px;font-size:15px;line-height:1.75;margin-bottom:36px">
        {{ $isFr
            ? 'Dans le secteur de la santé, l\'indisponibilité d\'un système peut avoir des conséquences directes sur la vie des patients. Notre architecture de continuité est conçue pour garantir que les données de santé restent accessibles, même en cas d\'incident majeur.'
            : 'In healthcare, system downtime can have direct consequences on patient lives. Our continuity architecture is designed to ensure health data remains accessible, even during a major incident.' }}
    </p>

    {{-- RPO/RTO --}}
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:16px;margin-bottom:36px">
        @foreach($isFr
            ? [['clock','#00C896','RPO','Objectif de point de reprise','< 1 heure','Perte de données maximale acceptable'],['zap','#1A6FE8','RTO','Objectif de temps de reprise','< 4 heures','Temps de reprise opérationnelle maximal'],['activity','#00C896','HA','Haute disponibilité','99,9 %','Disponibilité garantie sur les services critiques'],['server','#1A6FE8','DR','Reprise après sinistre','Site secondaire','Réplication des données en temps réel']]
            : [['clock','#00C896','RPO','Recovery Point Objective','< 1 hour','Maximum acceptable data loss'],['zap','#1A6FE8','RTO','Recovery Time Objective','< 4 hours','Maximum operational recovery time'],['activity','#00C896','HA','High Availability','99.9%','Guaranteed uptime on critical services'],['server','#1A6FE8','DR','Disaster Recovery','Secondary site','Real-time data replication']]
        as $metric)
        <div style="background:#0F172A;border:1px solid {{ $metric[1] }}25;border-radius:12px;padding:20px 16px;text-align:center">
            <i data-lucide="{{ $metric[0] }}" style="width:22px;height:22px;color:{{ $metric[1] }};margin-bottom:10px"></i>
            <div style="font-weight:800;color:{{ $metric[1] }};font-size:22px;margin-bottom:2px">{{ $metric[2] }}</div>
            <div style="font-weight:700;color:#e2e8f0;font-size:var(--fs-xs);margin-bottom:4px">{{ $metric[3] }}</div>
            <div style="font-size:16px;font-weight:800;color:#e2e8f0;margin:8px 0 4px">{{ $metric[4] }}</div>
            <div style="font-size:var(--fs-xs);color:var(--text-muted)">{{ $metric[5] }}</div>
        </div>
        @endforeach
    </div>

    <div class="blog-art-body prose">
        <h3>{{ $isFr ? 'Architecture haute disponibilité' : 'High availability architecture' }}</h3>
        <p>{{ $isFr
            ? 'Les services critiques d\'OPES Health OS sont déployés en configuration actif-actif avec répartition de charge automatique. En cas de défaillance d\'un nœud, le trafic est redirigé automatiquement vers les nœuds sains sans interruption de service perceptible pour les utilisateurs cliniques.'
            : 'OPES Health OS critical services are deployed in active-active configuration with automatic load balancing. If a node fails, traffic is automatically redirected to healthy nodes with no perceptible service interruption for clinical users.' }}</p>

        <h3>{{ $isFr ? 'Sauvegardes & réplication' : 'Backups & replication' }}</h3>
        <p>{{ $isFr
            ? 'Les données sont sauvegardées toutes les heures avec rétention de 30 jours pour les sauvegardes quotidiennes, 12 mois pour les sauvegardes mensuelles. La réplication des données vers le site de reprise est continue, avec un RPO inférieur à 1 heure. Les sauvegardes sont chiffrées et testées mensuellement.'
            : 'Data is backed up hourly with 30-day retention for daily backups and 12-month retention for monthly backups. Data replication to the recovery site is continuous, with RPO under 1 hour. Backups are encrypted and tested monthly.' }}</p>

        <h3>{{ $isFr ? 'Mode hors ligne & dégradé' : 'Offline & degraded mode' }}</h3>
        <p>{{ $isFr
            ? 'OPES Health OS supporte un mode hors ligne pour les établissements avec une connectivité Internet intermittente. Les données cliniques critiques sont synchronisées localement et répliquées vers le serveur central dès la reconnexion — permettant aux cliniciens de continuer à travailler même sans connexion Internet.'
            : 'OPES Health OS supports an offline mode for facilities with intermittent internet connectivity. Critical clinical data is synchronised locally and replicated to the central server upon reconnection — allowing clinicians to continue working even without an internet connection.' }}</p>

        <h3>{{ $isFr ? 'Plan de reprise & tests' : 'Recovery plan & testing' }}</h3>
        <p>{{ $isFr
            ? 'Un Plan de Reprise d\'Activité (PRA) documenté est maintenu et testé deux fois par an. Les tests incluent des exercices de basculement complet, validation des sauvegardes, et simulation d\'incidents. Les résultats sont communiqués aux clients concernés dans le cadre de leur SLA.'
            : 'A documented Disaster Recovery Plan (DRP) is maintained and tested twice yearly. Tests include full failover exercises, backup validation, and incident simulation. Results are communicated to relevant customers as part of their SLA.' }}</p>
    </div>
</div>

<div class="divider"></div>

{{-- COMPLIANCE ENQUIRIES --}}
<div class="section" style="max-width:860px;margin:0 auto">
    <div class="blog-art-body prose">
        <h2>{{ $isFr ? 'Demandes de conformité' : 'Compliance Enquiries' }}</h2>
        <p>{{ $isFr
            ? 'Pour toute demande réglementaire, de conformité ou juridique, contactez :'
            : 'For regulatory, compliance, or legal enquiries, contact:' }}<br>
        OPES Health Systems SARL<br>
        {{ config('company.address') }}<br>
        {{ $isFr ? 'E-mail' : 'Email' }}: <a href="mailto:compliance@opeshealthsystems.com" style="color:#00C896">compliance@opeshealthsystems.com</a><br>
        {{ $isFr ? 'Tél' : 'Phone' }}: <a href="tel:{{ config('company.phone') }}" style="color:#00C896">{{ config('company.phone') }}</a></p>
    </div>
</div>

</x-layouts.app>

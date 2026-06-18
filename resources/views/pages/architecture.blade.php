@php $locale = app()->getLocale(); $isFr = $locale === 'fr'; @endphp

<x-layouts.app
    title="{{ $isFr ? 'Architecture de référence — OPES Health Systems' : 'Reference Architecture — OPES Health Systems' }}"
    description="{{ $isFr
        ? 'Architecture enterprise d\'OPES Health OS : interopérabilité HL7 FHIR, identité patient, sécurité et analytique.'
        : 'OPES Health OS enterprise architecture: HL7 FHIR interoperability, patient identity, security, and analytics layers.' }}">

{{-- HERO --}}
<div class="pricing-hero">
    <div class="section-label" style="justify-content:center;margin-bottom:16px">
        <i data-lucide="cpu" style="width:12px;height:12px"></i>
        {{ $isFr ? 'Architecture de référence' : 'Reference Architecture' }}
    </div>
    <h1>
        {{ $isFr ? 'Conçu pour' : 'Built for' }}
        <span class="gradient-text">{{ $isFr ? 'l\'échelle et l\'interopérabilité' : 'scale and interoperability' }}</span>
    </h1>
    <p>
        {{ $isFr
            ? 'OPES Health OS repose sur une architecture en couches alliant sécurité, interopérabilité mondiale et souveraineté des données locales — conçue pour les établissements de santé africains.'
            : 'OPES Health OS is built on a layered architecture combining security, global interoperability, and local data sovereignty — engineered for African health facilities.' }}
    </p>
</div>

{{-- ARCHITECTURE DIAGRAM SUMMARY --}}
<div class="section" style="max-width:960px;margin:0 auto">
    <div style="background:#0f1a2e;border:1px solid #1e293b;border-radius:16px;padding:40px 32px">
        <div style="text-align:center;margin-bottom:32px">
            <div class="section-label" style="justify-content:center;margin-bottom:12px">
                <i data-lucide="layers" style="width:12px;height:12px"></i>
                {{ $isFr ? 'Modèle en couches' : 'Layered model' }}
            </div>
            <h2 class="section-title" style="font-size:clamp(18px,2.5vw,24px)">OPES Health OS</h2>
        </div>

        @php
        $layers = $isFr ? [
            ['label'=>'Couche Présentation',     'items'=>'Portails web · Application mobile · Tableaux de bord analytiques · API tierces',           'color'=>'#1A6FE8','icon'=>'monitor'],
            ['label'=>'Couche Services Métier',  'items'=>'Dossier patient · SIH hospitalier · Laboratoire · Pharmacie · Radiologie · Maternité',     'color'=>'#00C896','icon'=>'layers'],
            ['label'=>'Couche Interopérabilité', 'items'=>'HL7 FHIR R4 · SNOMED CT · ICD-10/11 · DICOM · CDA · API ouverte REST/JSON',               'color'=>'#1A6FE8','icon'=>'share-2'],
            ['label'=>'Couche Identité Santé',   'items'=>'OPESCare Health ID · Master Patient Index · CRVS · Correspondance multi-établissements',   'color'=>'#00C896','icon'=>'fingerprint'],
            ['label'=>'Couche Données & IA',     'items'=>'Entrepôt de données cliniques · Analytique HMIS · IA décision clinique · Rapports MoH',    'color'=>'#1A6FE8','icon'=>'brain'],
            ['label'=>'Couche Infrastructure',   'items'=>'On-premise · Cloud privé · Déploiement hybride · Datacenter Cameroun',                     'color'=>'#475569','icon'=>'server'],
        ] : [
            ['label'=>'Presentation Layer',     'items'=>'Web portals · Mobile app · Analytics dashboards · Third-party integrations',               'color'=>'#1A6FE8','icon'=>'monitor'],
            ['label'=>'Business Services Layer','items'=>'Patient record · Hospital HIS · Laboratory · Pharmacy · Radiology · Maternity',            'color'=>'#00C896','icon'=>'layers'],
            ['label'=>'Interoperability Layer', 'items'=>'HL7 FHIR R4 · SNOMED CT · ICD-10/11 · DICOM · CDA · Open REST/JSON API',                  'color'=>'#1A6FE8','icon'=>'share-2'],
            ['label'=>'Health Identity Layer',  'items'=>'OPESCare Health ID · Master Patient Index · CRVS · Cross-facility record matching',        'color'=>'#00C896','icon'=>'fingerprint'],
            ['label'=>'Data & Intelligence Layer','items'=>'Clinical data warehouse · HMIS analytics · AI clinical decision support · MoH reporting','color'=>'#1A6FE8','icon'=>'brain'],
            ['label'=>'Infrastructure Layer',   'items'=>'On-premise · Private cloud · Hybrid deployment · Cameroon data centres',                   'color'=>'#475569','icon'=>'server'],
        ];
        @endphp

        <div style="display:flex;flex-direction:column;gap:12px">
            @foreach($layers as $i => $layer)
            <div style="background:#0F172A;border:1px solid {{ $layer['color'] }}30;border-radius:10px;padding:16px 20px;display:flex;align-items:center;gap:16px">
                <div style="width:36px;height:36px;border-radius:8px;background:{{ $layer['color'] }}15;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    <i data-lucide="{{ $layer['icon'] }}" style="width:16px;height:16px;color:{{ $layer['color'] }}"></i>
                </div>
                <div style="flex:1;min-width:0">
                    <div style="font-weight:700;color:#e2e8f0;font-size:13px;margin-bottom:3px">{{ $layer['label'] }}</div>
                    <div style="color:#64748b;font-size:12px;line-height:1.5">{{ $layer['items'] }}</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<div class="divider"></div>

{{-- INTEROPERABILITY --}}
<div class="section" style="max-width:960px;margin:0 auto">
    <div class="section-label" style="margin-bottom:16px">
        <i data-lucide="share-2" style="width:12px;height:12px"></i>
        {{ $isFr ? 'Interopérabilité' : 'Interoperability' }}
    </div>
    <h2 class="section-title">{{ $isFr ? 'Connecté aux standards mondiaux' : 'Connected to global standards' }}</h2>
    <p style="color:#64748b;max-width:680px;font-size:15px;line-height:1.75;margin-bottom:32px">
        {{ $isFr
            ? 'OPES Health OS implémente les standards internationaux d\'interopérabilité en santé, permettant l\'échange sécurisé de données entre établissements, partenaires et systèmes gouvernementaux.'
            : 'OPES Health OS implements international health interoperability standards, enabling secure data exchange between facilities, partners, and government systems.' }}
    </p>
    <div class="pi-grid" style="max-width:960px">
        @php
        $standards = $isFr ? [
            ['icon'=>'share-2','color'=>'#00C896','title'=>'HL7 FHIR R4','desc'=>'API FHIR native pour l\'échange de ressources cliniques — patients, observations, prescriptions et comptes rendus.'],
            ['icon'=>'file-text','color'=>'#1A6FE8','title'=>'ICD-10 / ICD-11','desc'=>'Codification diagnostique conforme OMS, avec transition automatisée vers ICD-11 prête à l\'emploi.'],
            ['icon'=>'image','color'=>'#00C896','title'=>'DICOM','desc'=>'Gestion des images médicales (radiologie, échographie, scanner) avec stockage PACS intégré.'],
            ['icon'=>'code-2','color'=>'#1A6FE8','title'=>'API REST ouverte','desc'=>'API documentée (OpenAPI 3.0) pour connecter des systèmes tiers, portails gouvernementaux et applications de télémédecine.'],
            ['icon'=>'database','color'=>'#00C896','title'=>'SNOMED CT','desc'=>'Terminologie clinique normalisée pour les diagnostics, procédures et observations.'],
            ['icon'=>'building','color'=>'#1A6FE8','title'=>'HMIS / DHIS2','desc'=>'Exports DHIS2-compatibles pour le reporting vers le Ministère de la Santé et les partenaires de développement.'],
        ] : [
            ['icon'=>'share-2','color'=>'#00C896','title'=>'HL7 FHIR R4','desc'=>'Native FHIR API for clinical resource exchange — patients, observations, prescriptions, and reports.'],
            ['icon'=>'file-text','color'=>'#1A6FE8','title'=>'ICD-10 / ICD-11','desc'=>'WHO-compliant diagnostic coding with automated ICD-11 transition pathways built in.'],
            ['icon'=>'image','color'=>'#00C896','title'=>'DICOM','desc'=>'Medical imaging management (radiology, ultrasound, CT) with integrated PACS-compatible storage.'],
            ['icon'=>'code-2','color'=>'#1A6FE8','title'=>'Open REST API','desc'=>'Documented API (OpenAPI 3.0) for connecting third-party systems, government portals, and telemedicine apps.'],
            ['icon'=>'database','color'=>'#00C896','title'=>'SNOMED CT','desc'=>'Standardised clinical terminology for diagnoses, procedures, and observations.'],
            ['icon'=>'building','color'=>'#1A6FE8','title'=>'HMIS / DHIS2','desc'=>'DHIS2-compatible exports for reporting to the Ministry of Health and development partners.'],
        ];
        @endphp
        @foreach($standards as $s)
        <div class="pi-card" style="flex-direction:column;align-items:flex-start">
            <div style="width:40px;height:40px;border-radius:10px;background:rgba(255,255,255,0.05);display:flex;align-items:center;justify-content:center;margin-bottom:12px">
                <i data-lucide="{{ $s['icon'] }}" style="width:18px;height:18px;color:{{ $s['color'] }}"></i>
            </div>
            <div style="font-weight:700;color:#e2e8f0;font-size:14px;margin-bottom:6px">{{ $s['title'] }}</div>
            <div style="font-size:13px;color:#64748b;line-height:1.6">{{ $s['desc'] }}</div>
        </div>
        @endforeach
    </div>
</div>

<div class="divider"></div>

{{-- HEALTH IDENTITY --}}
<div class="section" style="max-width:960px;margin:0 auto">
    <div class="opescare-spotlight">
        <div class="section-label" style="margin-bottom:14px">
            <i data-lucide="fingerprint" style="width:12px;height:12px"></i>
            {{ $isFr ? 'Identité santé' : 'Health identity' }}
        </div>
        <h2 style="font-size:clamp(20px,3vw,28px);font-weight:700;color:#e2e8f0;margin-bottom:14px;line-height:1.3">
            {{ $isFr ? 'Un identifiant patient universel pour toute l\'Afrique' : 'One universal patient identifier across Africa' }}
        </h2>
        <p style="color:#94a3b8;max-width:700px;line-height:1.75;margin-bottom:16px;font-size:15px">
            {{ $isFr
                ? 'La couche OPESCare assigne à chaque patient un identifiant numérique unique dès son premier contact avec le système de santé. Cet identifiant persiste à travers les consultations, les transferts inter-établissements, et les années — éliminant la duplication des dossiers et les erreurs d\'identification.'
                : 'The OPESCare layer assigns every patient a unique digital identifier at their first point of contact with the health system. This identifier persists across consultations, inter-facility transfers, and years — eliminating duplicate records and identification errors.' }}
        </p>
        <div style="display:flex;gap:24px;flex-wrap:wrap;margin-top:24px">
            @foreach($isFr
                ? ['Indexe patient maître (MPI)','Correspondance multi-établissements','Conformité CRVS','ID hors ligne & mode dégradé']
                : ['Master Patient Index (MPI)','Cross-facility record matching','CRVS-compliant','Offline ID & degraded mode'] as $f)
            <div style="display:flex;align-items:center;gap:8px">
                <i data-lucide="check-circle" style="width:15px;height:15px;color:#00C896;flex-shrink:0"></i>
                <span style="color:#94a3b8;font-size:13px">{{ $f }}</span>
            </div>
            @endforeach
        </div>
    </div>
</div>

<div class="divider"></div>

{{-- SECURITY --}}
<div class="section" style="max-width:960px;margin:0 auto;text-align:center">
    <div class="section-label" style="justify-content:center;margin-bottom:16px">
        <i data-lucide="shield-check" style="width:12px;height:12px"></i>
        {{ $isFr ? 'Sécurité' : 'Security' }}
    </div>
    <h2 class="section-title">{{ $isFr ? 'Sécurité conçue dès la fondation' : 'Security designed from the foundation' }}</h2>
    <p style="color:#64748b;max-width:660px;margin:12px auto 36px;font-size:15px;line-height:1.75">
        {{ $isFr
            ? 'Chaque composant d\'OPES Health OS est conçu selon les principes de sécurité dès la conception (Security by Design), avec chiffrement de bout en bout, contrôle d\'accès granulaire et audit complet.'
            : 'Every component of OPES Health OS is designed on Security by Design principles — end-to-end encryption, granular access control, and full audit trails.' }}
    </p>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:16px">
        @php
        $secItems = $isFr ? [
            ['icon'=>'lock','title'=>'Chiffrement AES-256','desc'=>'Données au repos et en transit chiffrées avec AES-256 et TLS 1.3.'],
            ['icon'=>'users','title'=>'Contrôle d\'accès RBAC','desc'=>'Permissions granulaires basées sur les rôles : clinicien, administrateur, infirmier, technicien.'],
            ['icon'=>'file-clock','title'=>'Piste d\'audit complète','desc'=>'Journalisation immuable de toute action — qui a accédé à quoi et quand.'],
            ['icon'=>'shield','title'=>'Conformité HIPAA/GDPR','desc'=>'Architecture alignée sur les référentiels HIPAA, GDPR et la loi camerounaise sur les données de santé.'],
        ] : [
            ['icon'=>'lock','title'=>'AES-256 Encryption','desc'=>'Data at rest and in transit encrypted with AES-256 and TLS 1.3.'],
            ['icon'=>'users','title'=>'RBAC Access Control','desc'=>'Granular role-based permissions: clinician, admin, nurse, technician, auditor.'],
            ['icon'=>'file-clock','title'=>'Full Audit Trail','desc'=>'Immutable logging of every action — who accessed what and when.'],
            ['icon'=>'shield','title'=>'HIPAA/GDPR-aligned','desc'=>'Architecture aligned with HIPAA, GDPR, and Cameroonian health data law.'],
        ];
        @endphp
        @foreach($secItems as $item)
        <div class="pi-card" style="flex-direction:column;align-items:flex-start;text-align:left">
            <div style="width:40px;height:40px;border-radius:10px;background:rgba(0,200,150,0.08);display:flex;align-items:center;justify-content:center;margin-bottom:12px">
                <i data-lucide="{{ $item['icon'] }}" style="width:18px;height:18px;color:#00C896"></i>
            </div>
            <div style="font-weight:700;color:#e2e8f0;font-size:13px;margin-bottom:6px">{{ $item['title'] }}</div>
            <div style="font-size:12px;color:#64748b;line-height:1.6">{{ $item['desc'] }}</div>
        </div>
        @endforeach
    </div>
</div>

<div class="divider"></div>

{{-- DEPLOYMENT --}}
<div class="section" style="max-width:960px;margin:0 auto">
    <div class="section-label" style="margin-bottom:16px">
        <i data-lucide="server" style="width:12px;height:12px"></i>
        {{ $isFr ? 'Options de déploiement' : 'Deployment options' }}
    </div>
    <h2 class="section-title">{{ $isFr ? 'Déployez là où vos données doivent rester' : 'Deploy where your data must stay' }}</h2>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:20px;margin-top:32px">
        @php
        $deployOptions = $isFr ? [
            ['icon'=>'server',    'color'=>'#00C896','title'=>'On-Premise',           'desc'=>'Hébergement sur vos propres serveurs, dans votre établissement ou datacenter. Contrôle total des données, idéal pour les hôpitaux généraux et les réseaux publics.'],
            ['icon'=>'cloud',     'color'=>'#1A6FE8','title'=>'Cloud Privé Cameroun', 'desc'=>'Hébergement managé dans nos datacenters au Cameroun. Données en souveraineté nationale, conformes aux exigences du Ministère de la Santé.'],
            ['icon'=>'git-merge', 'color'=>'#00C896','title'=>'Hybride',              'desc'=>'Combinaison on-premise et cloud pour les réseaux multi-sites : données sensibles locales, agrégation analytique dans le cloud.'],
        ] : [
            ['icon'=>'server',    'color'=>'#00C896','title'=>'On-Premise',           'desc'=>'Hosted on your own servers, within your facility or data centre. Full data control — ideal for general hospitals and public health networks.'],
            ['icon'=>'cloud',     'color'=>'#1A6FE8','title'=>'Private Cloud Cameroon','desc'=>'Managed hosting in our Cameroon data centres. Data remains in-country, compliant with Ministry of Health requirements.'],
            ['icon'=>'git-merge', 'color'=>'#00C896','title'=>'Hybrid',               'desc'=>'Combination of on-premise and cloud for multi-site networks — sensitive data stays local, analytics aggregated in the cloud.'],
        ];
        @endphp
        @foreach($deployOptions as $d)
        <div class="pi-card" style="flex-direction:column;align-items:flex-start">
            <div style="width:44px;height:44px;border-radius:12px;background:{{ $d['color'] }}15;display:flex;align-items:center;justify-content:center;margin-bottom:16px">
                <i data-lucide="{{ $d['icon'] }}" style="width:20px;height:20px;color:{{ $d['color'] }}"></i>
            </div>
            <div style="font-weight:700;color:#e2e8f0;font-size:15px;margin-bottom:8px">{{ $d['title'] }}</div>
            <div style="font-size:13px;color:#64748b;line-height:1.65">{{ $d['desc'] }}</div>
        </div>
        @endforeach
    </div>
</div>

{{-- CTA --}}
<div class="demo-section">
    <h2>{{ $isFr ? 'Besoin d\'un aperçu technique détaillé ?' : 'Need a detailed technical overview?' }}</h2>
    <p>{{ $isFr
        ? 'Nos architectes solutions peuvent vous présenter l\'architecture complète d\'OPES Health OS et répondre à vos questions techniques.'
        : 'Our solutions architects can walk you through the full OPES Health OS architecture and answer your technical questions.' }}</p>
    <div style="display:flex;gap:14px;justify-content:center;flex-wrap:wrap;margin-top:12px">
        <a href="{{ url($locale.'/contact') }}" class="btn-primary">
            {{ $isFr ? 'Parler à un architecte' : 'Talk to an architect' }} <i data-lucide="arrow-right" style="width:15px;height:15px"></i>
        </a>
        <a href="{{ url($locale.'/compliance') }}" class="btn-secondary">
            {{ $isFr ? 'Sécurité & conformité' : 'Security & compliance' }} <i data-lucide="shield" style="width:15px;height:15px;color:#94a3b8"></i>
        </a>
    </div>
</div>

</x-layouts.app>

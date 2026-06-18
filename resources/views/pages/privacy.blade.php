@php $locale = app()->getLocale(); $isFr = $locale === 'fr'; @endphp

<x-layouts.app
    title="{{ $isFr ? 'Politique de confidentialité — OPES Health Systems' : 'Privacy Policy — OPES Health Systems' }}"
    description="{{ $isFr ? 'Politique de confidentialité d\'OPES Health Systems : comment nous collectons, utilisons et protégeons vos données personnelles conformément au droit camerounais et aux normes internationales.' : 'OPES Health Systems privacy policy: how we collect, use and protect your personal data in accordance with Cameroonian law and international standards.' }}">

<div class="pd-breadcrumb">
    <a href="{{ url($locale) }}">{{ $isFr ? 'Accueil' : 'Home' }}</a>
    <span>›</span>
    <span class="pd-breadcrumb-current">{{ $isFr ? 'Politique de confidentialité' : 'Privacy Policy' }}</span>
</div>

<article class="section" style="max-width:860px;margin:0 auto">

    <div style="margin-bottom:40px">
        <div class="section-label" style="margin-bottom:12px">
            <i data-lucide="shield" style="width:12px;height:12px"></i>
            {{ $isFr ? 'Juridique' : 'Legal' }}
        </div>
        <h1 class="section-title">{{ $isFr ? 'Politique de confidentialité' : 'Privacy Policy' }}</h1>
        <p class="section-sub">{{ $isFr ? 'Date d\'effet : 1er janvier 2025 &nbsp;·&nbsp; OPES Health Systems SARL' : 'Effective date: 1 January 2025 &nbsp;·&nbsp; OPES Health Systems SARL' }}</p>
    </div>

    <div class="blog-art-body prose">

        @if($isFr)
        <h2>1. Qui sommes-nous</h2>
        <p>OPES Health Systems SARL (« OPES », « nous ») est une entreprise technologique de santé enregistrée sous le droit OHADA, dont le siège social est situé à Bonamousadi, Douala, Cameroun. Nous développons et exploitons des logiciels intégrés de gestion de la santé (collectivement, la « Plateforme ») pour les hôpitaux, cliniques, laboratoires, pharmacies et ministères de la santé de la région CEMAC.</p>

        <h2>2. Données collectées</h2>
        <p>Nous collectons les informations que vous nous fournissez directement (nom, e-mail, téléphone, type d'établissement) lors de la soumission d'une demande de démo ou d'un formulaire de contact. Pour les utilisateurs agréés de la Plateforme, nous pouvons traiter des identifiants patients, des dossiers cliniques et des données administratives pour le compte de votre établissement — dans ce cas, OPES agit en tant que sous-traitant et votre établissement est le responsable du traitement.</p>

        <h2>3. Utilisation de vos données</h2>
        <ul>
            <li>Pour répondre aux demandes de démonstration et aux renseignements</li>
            <li>Pour fournir et maintenir la Plateforme dans le cadre de votre contrat de licence</li>
            <li>Pour envoyer des mises à jour produit et des informations pertinentes (avec votre consentement)</li>
            <li>Pour nous conformer au droit camerounais, aux obligations OHADA et aux réglementations régionales applicables</li>
        </ul>

        <h2>4. Partage des données</h2>
        <p>Nous ne vendons pas de données personnelles. Nous partageons les données uniquement avec les prestataires de services nécessaires à l'exploitation de la Plateforme (hébergement cloud, envoi d'e-mails) dans le cadre d'accords de confidentialité, ou lorsque la loi l'exige.</p>

        <h2>5. Conservation des données</h2>
        <p>Les données de contact sont conservées pendant 3 ans maximum. Les données de la Plateforme sont conservées pendant la durée de votre licence, plus toute période de conservation légale requise par la réglementation camerounaise en matière de santé.</p>

        <h2>6. Sécurité</h2>
        <p>Nous mettons en œuvre des mesures techniques et organisationnelles conformes aux normes de l'industrie, notamment le chiffrement des transmissions de données (TLS 1.3), le contrôle d'accès basé sur les rôles, les journaux d'audit et les évaluations régulières de sécurité. Pour les déploiements cliniques, les données peuvent être stockées sur site dans votre établissement.</p>

        <h2>7. Vos droits</h2>
        <p>Sous réserve du droit applicable, vous avez le droit d'accéder à vos données personnelles, de les corriger ou d'en demander la suppression. Pour exercer ces droits, contactez-nous à l'adresse <a href="mailto:privacy@opeshealthsystems.com" style="color:#00C896">privacy@opeshealthsystems.com</a>.</p>

        <h2>8. Cookies</h2>
        <p>Notre site web de marketing utilise uniquement des cookies de session essentiels. Aucun cookie de suivi ou de publicité tiers n'est utilisé.</p>

        <h2>9. Contact</h2>
        <p>OPES Health Systems SARL<br>
        Bonamousadi, Douala, Cameroun<br>
        E-mail : <a href="mailto:privacy@opeshealthsystems.com" style="color:#00C896">privacy@opeshealthsystems.com</a></p>

        @else
        <h2>1. Who We Are</h2>
        <p>OPES Health Systems SARL ("OPES", "we", "us") is a healthcare technology company registered under OHADA law, headquartered at Bonamousadi, Douala, Cameroon. We develop and operate integrated healthcare management software (collectively, the "Platform") for hospitals, clinics, laboratories, pharmacies and health ministries across the CEMAC region.</p>

        <h2>2. Data We Collect</h2>
        <p>We collect information you provide directly (name, email, phone, facility type) when submitting a demo request or contact form. For licensed Platform users, we may process patient identifiers, clinical records and administrative data on behalf of your facility — in that capacity OPES acts as a data processor and your facility is the data controller.</p>

        <h2>3. How We Use Your Data</h2>
        <ul>
            <li>To respond to demo requests and enquiries</li>
            <li>To deliver and support the Platform under your licence agreement</li>
            <li>To send product updates and relevant information (with your consent)</li>
            <li>To comply with Cameroonian law, OHADA obligations and applicable regional regulations</li>
        </ul>

        <h2>4. Data Sharing</h2>
        <p>We do not sell personal data. We share data only with service providers necessary to operate the Platform (cloud hosting, email delivery) under confidentiality agreements, or where required by law.</p>

        <h2>5. Data Retention</h2>
        <p>Contact enquiry data is retained for up to 3 years. Platform data is retained for the duration of your licence plus any statutory retention period required by Cameroonian health regulations.</p>

        <h2>6. Security</h2>
        <p>We implement industry-standard technical and organisational measures including encrypted data transmission (TLS 1.3), role-based access control, audit logs and regular security assessments. For clinical deployments, data may be stored on-premise at your facility.</p>

        <h2>7. Your Rights</h2>
        <p>Subject to applicable law, you have the right to access, correct or request deletion of your personal data. To exercise these rights, contact us at <a href="mailto:privacy@opeshealthsystems.com" style="color:#00C896">privacy@opeshealthsystems.com</a>.</p>

        <h2>8. Cookies</h2>
        <p>Our marketing website uses only essential session cookies. No third-party tracking or advertising cookies are used.</p>

        <h2>9. Contact</h2>
        <p>OPES Health Systems SARL<br>
        Bonamousadi, Douala, Cameroon<br>
        Email: <a href="mailto:privacy@opeshealthsystems.com" style="color:#00C896">privacy@opeshealthsystems.com</a></p>
        @endif

    </div>

</article>

</x-layouts.app>

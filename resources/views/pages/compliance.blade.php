@php $isFr = app()->getLocale() === 'fr'; @endphp
<x-layouts.app>
<section class="section" style="max-width:900px;margin:auto">
    <h1 class="section-title">{{ $isFr ? 'Sécurité et vérification des projets' : 'Security and project assurance' }}</h1>
    <p class="section-sub">{{ $isFr ? 'Les exigences dépendent du produit, du pays, de l’hébergement et du contrat de votre établissement.' : 'Requirements depend on the product, country, hosting arrangement and your facility’s agreement.' }}</p>
    <p class="brand-notice">{{ $isFr ? 'Ce site ne constitue pas une attestation de certification, d’approbation gouvernementale ou de conformité réglementaire. Demandez les documents applicables à votre projet.' : 'This website is not evidence of certification, government endorsement or regulatory compliance. Request the documents applicable to your project.' }}</p>
    <h2>{{ $isFr ? 'Référence sur la protection des données' : 'Data protection reference' }}</h2>
    <p>{{ $isFr ? 'La loi camerounaise n° 2024/017 du 23 décembre 2024 porte sur la protection des données à caractère personnel. Consultez le texte officiel et faites examiner les obligations de votre projet par votre conseil juridique. La présence de cette référence ne constitue pas une attestation de conformité OPES.' : 'Cameroon’s Law No. 2024/017 of 23 December 2024 concerns personal data protection. Consult the official text and have your project’s obligations reviewed by legal counsel. Referencing the law does not establish OPES compliance.' }} <a href="https://www.prc.cm/files/9b/df/2c/e818fedc7d5568778f884ea2886bea7d.pdf">{{ $isFr ? 'Texte officiel de la Présidence' : 'Official Presidency text' }}</a></p>
    @foreach($isFr ? [
        ['Documents de vérification', 'Demandez le périmètre du produit, les résultats de tests disponibles, les responsabilités des parties et les éventuelles attestations indépendantes. Vérifiez les dates, le périmètre et l’organisme émetteur de toute attestation.'],
        ['Confidentialité et données de santé', 'Définissez les finalités du traitement, les accès, le lieu d’hébergement, les durées de conservation, les sauvegardes et la gestion des incidents dans les accords du projet. Ne transmettez pas de données de patients dans les formulaires publics.'],
        ['Hébergement et continuité', 'Confirmez les lieux de stockage, les mesures de protection, les objectifs de reprise et les modalités de support. Toute garantie de disponibilité doit figurer dans un contrat signé.'],
        ['Interopérabilité', 'Un standard cité dans une présentation ne prouve pas qu’une interface est disponible ou testée. Confirmez les versions, les connecteurs et les tests nécessaires pour les systèmes de votre établissement.'],
    ] : [
        ['Evidence to request', 'Ask for product scope, available test results, responsibilities and any applicable independent assessments. Check the dates, scope and issuer of any certificate.'],
        ['Privacy and health data', 'Establish processing purposes, access permissions, hosting location, retention, backups and incident handling in the project agreements. Do not submit patient information through public forms.'],
        ['Hosting and continuity', 'Confirm storage locations, protection measures, recovery objectives and support arrangements. Any availability guarantee must be recorded in a signed agreement.'],
        ['Interoperability', 'A standard mentioned in a product overview does not prove that an interface is available or tested. Confirm versions, connectors and testing for your facility’s actual systems.'],
    ] as [$heading, $text])
        <h2 style="font-size:22px;margin:28px 0 10px">{{ $heading }}</h2><p style="color:var(--text-2)">{{ $text }}</p>
    @endforeach
    <a class="btn-primary" style="margin-top:28px" href="{{ route('contact') }}">{{ $isFr ? 'Discuter de votre projet' : 'Discuss your project' }}</a>
</section>
</x-layouts.app>

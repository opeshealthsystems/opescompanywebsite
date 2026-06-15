@php $locale = app()->getLocale(); @endphp

<x-layouts.app title="OHADA Compliance — OPES Health Systems" description="OPES Health Systems compliance commitments: OHADA law, Cameroonian health data regulations, HL7 FHIR interoperability, and Ministry of Health 2026–2030 digitalization alignment.">

<div class="pd-breadcrumb">
    <a href="{{ url($locale) }}">Home</a>
    <span>›</span>
    <span class="pd-breadcrumb-current">OHADA Compliance</span>
</div>

<article class="section" style="max-width:860px;margin:0 auto">

    <div style="margin-bottom:40px">
        <div class="section-label" style="margin-bottom:12px">
            <i data-lucide="scale" style="width:12px;height:12px"></i>
            Legal
        </div>
        <h1 class="section-title">OHADA Compliance</h1>
        <p class="section-sub">Regulatory alignment for the CEMAC region &nbsp;·&nbsp; OPES Health Systems SARL</p>
    </div>

    <div class="blog-art-body prose">

        <h2>1. OHADA Legal Framework</h2>
        <p>OPES Health Systems SARL is incorporated and operates under the Organisation pour l'Harmonisation en Afrique du Droit des Affaires (OHADA) Uniform Acts. Our commercial contracts, data processing agreements and corporate governance comply with the OHADA Uniform Act on General Commercial Law (AUDCG) and the Uniform Act on Commercial Companies (AUSC).</p>

        <h2>2. Cameroonian Health Data Regulations</h2>
        <p>Patient health data processed through the OPES Platform is handled in accordance with Law No. 2010/013 of 21 December 2010 on Electronic Communications and Law No. 2010/021 of 21 December 2010 on Electronic Commerce in Cameroon. Where sector-specific health data protection standards are enacted, OPES commits to align Platform data handling practices within 12 months of enactment.</p>

        <h2>3. Ministry of Health 2026–2030 Alignment</h2>
        <p>All OPES products are designed to support and operate within the Cameroon Ministry of Health (MoH) Digital Health Strategy 2026–2030. This includes:</p>
        <ul>
            <li>Reporting structures compatible with DHIS2 national health information system</li>
            <li>Disease surveillance data formats aligned with national reporting requirements</li>
            <li>Support for national health programmes (HIV/AIDS, malaria, tuberculosis, vaccination)</li>
            <li>Facility classification and service taxonomy consistent with MoH standards</li>
        </ul>

        <h2>4. HL7 FHIR Interoperability</h2>
        <p>OPES implements HL7 FHIR R4 as the interoperability standard across all Platform modules. This ensures patient data can be shared between OPES systems, with national registries and with partner health institutions in a standardised, machine-readable format without proprietary lock-in.</p>

        <h2>5. Bilingual Compliance</h2>
        <p>In conformity with the Constitution of the Republic of Cameroon, which recognises French and English as official languages, all OPES Platform modules are fully bilingual. Patient records, clinical forms, reports and user interfaces are available in both French and English.</p>

        <h2>6. Data Sovereignty</h2>
        <p>On-premise deployment options are available for all OPES modules, ensuring that health facility data remains within Cameroon or within the customer's jurisdiction. Cloud-hosted services use data centres within the CEMAC region or with contractual data localisation guarantees.</p>

        <h2>7. Audit and Accountability</h2>
        <p>The Platform maintains comprehensive audit logs of all user actions, data access and clinical decisions. Logs are tamper-evident and retained in accordance with health facility statutory obligations. System administrators at your facility retain full access to audit trails.</p>

        <h2>8. Compliance Enquiries</h2>
        <p>For regulatory, compliance or legal enquiries, contact:<br>
        OPES Health Systems SARL<br>
        Bonamousadi, Douala, Cameroon<br>
        Email: <a href="mailto:compliance@opeshealthsystems.com" style="color:#00C896">compliance@opeshealthsystems.com</a></p>

    </div>

</article>

</x-layouts.app>

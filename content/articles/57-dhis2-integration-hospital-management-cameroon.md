# DHIS2 Integration: How Hospital Management Systems Feed Cameroon's National Health Database

**Meta Description:** Understand how HMS-to-DHIS2 integration works in Cameroon, what health indicators are reported, how MINSANTE uses the data, and how automated reporting replaces manual monthly returns.

**Target Keywords:** DHIS2 integration Cameroon, hospital management system DHIS2, MINSANTE health data reporting, DHIS2 API HMS, health information system Cameroon, national health database Africa

---

## What Is DHIS2?

DHIS2 — the District Health Information Software 2 — is an open-source health information platform developed by the HISP (Health Information Systems Programme) network, based at the University of Oslo. It is the world's largest health management information system, deployed in more than 100 countries to aggregate, analyse, and visualise routine health facility data at district, regional, and national levels.

DHIS2 is not a clinical system: it does not store individual patient records. Instead, it aggregates data into summary indicators — monthly counts of outpatient visits, antenatal care attendances, deliveries, immunisations administered, malaria diagnoses, and similar metrics — that allow health authorities to monitor population health trends, track programme performance, and allocate resources. Its web-based interface, customisable indicator library, and open API have made it the standard platform for routine health information management across sub-Saharan Africa.

## Cameroon's National DHIS2 Deployment Under MINSANTE

Cameroon's Ministry of Public Health (Ministère de la Santé Publique — MINSANTE) adopted DHIS2 as its national Health Management Information System (HMIS) platform with support from WHO, PEPFAR, and the Global Fund in the early 2010s. The system is accessible through the national health information portal and is used by district health offices (DSP — Délégations de Santé de Province) and regional health delegations (DRS) to compile monthly facility reports submitted by health facilities across the country.

Under Cameroon's mandatory reporting framework, every accredited health facility — public district hospital, private clinic, confessional health centre, or mission hospital — is required to submit a monthly aggregate return covering a defined set of health indicators to its supervising district health office. These returns form the input data for DHIS2. The aggregate district-level data is then used by MINSANTE to produce national health statistics, inform the national health plan, and report to international donors and partners.

## How Manual Reporting to DHIS2 Fails

In the current standard operating model, a data focal point at each health facility manually counts tally marks from paper registers — the OPD register, the ANC register, the delivery register, the immunisation card — at the end of each month, transcribes the counts onto the monthly reporting form (Rapport Mensuel d'Activités — RMA), and either submits the paper form to the district health office or enters the figures directly into DHIS2 via the facility's data entry form.

This manual process generates well-documented problems. Transcription errors occur at every step — from tally to form, and from form to DHIS2 data entry. Facilities with high patient volumes find it practically impossible to count accurately from tally registers, especially if register management has been inconsistent during the month. Data submission is frequently late: MINSANTE and its partners routinely report on-time reporting completeness rates of 60 to 80 percent nationally, with rural facilities often below 50 percent. Retrospective correction of submitted data is cumbersome, so errors persist in the national database.

Perhaps most significantly, the manual process severs the connection between the individual clinical record and the aggregate indicator. If a patient's diagnosis is not recorded on the OPD register, or the register uses a non-standard diagnostic category, the case may not be counted in the right DHIS2 indicator — or may not be counted at all. Data quality suffers systematically in high-volume facilities where register management is challenging.

## What Automated HMS-to-DHIS2 Integration Means

Automated integration means the Hospital Management System maintains a live database of individual patient encounters — each coded by diagnosis, service type, patient demographics, and date — and, at the end of each reporting period, automatically aggregates those records into the exact indicator counts that DHIS2 requires, then submits them through the DHIS2 API without manual transcription.

The result is a reporting chain where: a patient attends the outpatient department → the clinician records the encounter in the HMS with a diagnosis code → the HMS counts that encounter in the relevant DHIS2 indicator category → at month end, the aggregated counts are pushed to DHIS2 automatically. No tallying. No transcription. No manually typed RMA form. The facility's DHIS2 submission is generated directly from the same clinical records that drive billing, pharmacy, and patient care — a single source of truth.

## DHIS2 API: How an HMS Connects

DHIS2 provides a well-documented REST API that allows external systems to read and write data programmatically. The API uses standard HTTPS requests with JSON payloads. An HMS connecting to DHIS2 must:

1. **Authenticate** using facility-specific credentials provided by the district health office or MINSANTE
2. **Map local data elements** — the HMS's internal service and diagnosis codes — to the corresponding DHIS2 data element UIDs for the national instance
3. **Aggregate records** over the reporting period into the counts required for each data element and category option combination
4. **POST the data values** to the DHIS2 `/api/dataValueSets` endpoint in the correct organisational unit (the facility's assigned DHIS2 UID) and period format (e.g., `202501` for January 2025)
5. **Handle the response** — DHIS2 returns an import summary indicating success, conflicts, or validation errors, which the HMS should log for audit purposes

The technical requirements are well within the capability of any modern HMS platform. The principal implementation challenge is the mapping step: ensuring that local clinical codes correspond correctly to the national DHIS2 data elements, which may be updated when MINSANTE revises the national indicator list.

## Which Indicators Are Exported to DHIS2

Cameroon's national DHIS2 instance collects indicators across multiple health programme areas. The standard minimum dataset for a first-level health facility includes:

| Programme Area | Key DHIS2 Indicators |
|---|---|
| Outpatient services | Total OPD attendances (new + revisit), by age group and sex |
| Maternal health | ANC first visits, ANC 4th visit, supervised deliveries, postnatal consultations |
| Child health | Under-5 OPD consultations, growth monitoring attendances |
| Immunisation | Doses administered by antigen (BCG, Penta, Measles, Yellow Fever, etc.) |
| Malaria | Confirmed malaria cases (RDT positive + microscopy positive), treated cases |
| HIV/AIDS | HIV tests performed, positive results, patients initiated on ART |
| Tuberculosis | TB cases notified (new pulmonary, retreatment, extra-pulmonary) |
| Family planning | FP clients (new + continuing), by method |
| Hospitalisation | Admissions, bed-days, deaths, by ward |

Vertical programme reporting — for PEPFAR-funded HIV programmes, Global Fund malaria and TB programmes — may require additional data elements beyond the standard MINSANTE dataset, submitted to separate DHIS2 instances or supplementary platforms.

## Data Aggregation from Individual Records

The power of HMS-to-DHIS2 integration lies in aggregating structured individual records. An HMS that captures each patient's age, sex, diagnosis (ICD-10 coded), service type, and visit date can automatically count, for example, the number of female patients aged 15 to 49 who had a first ANC visit in a given month — a DHIS2 indicator that would otherwise require manual tallying from the ANC register.

This aggregation must be configured carefully: the ICD-10 diagnosis codes used in the HMS must be mapped to the DHIS2 indicator definitions. Malaria confirmed by RDT (ICD-10 B54 or more specific codes) must be distinguished from clinically diagnosed malaria if the DHIS2 indicator makes that distinction. Maternal deaths must be flagged at the time of recording to appear in the maternal mortality count. These mappings are established during HMS implementation and validated against historical manual returns.

## Scheduled vs Real-Time Submission

Two architectural approaches exist for HMS-to-DHIS2 data submission:

**Scheduled batch submission** aggregates and submits data on a defined schedule — typically monthly, aligned with the national reporting deadline. This is the most common approach and aligns with the monthly RMA reporting cycle. Some implementations submit weekly summary data to enable early warning of disease outbreaks.

**Near-real-time submission** pushes individual-level aggregate updates to DHIS2 daily or more frequently. This is less common in Cameroon but is increasingly relevant for disease surveillance programmes where early outbreak detection is a priority. It requires a stable internet connection at the facility and a DHIS2 instance configured to accept frequent updates.

## LMIS Integration: Commodity Reporting

The Logistics Management Information System (LMIS) function within DHIS2 — or integrated with it — handles commodity consumption reporting: how many doses of each vaccine were used, how many malaria RDTs were consumed, how many bed nets distributed. In Cameroon, LMIS data feeds into the supply chain managed by the Central Procurement and Essential Medicines Store (CENAME) and informs procurement planning.

An HMS with pharmacy and stock management modules can generate LMIS reports automatically from dispensing records — capturing actual commodity consumption against stock balances and generating the facility-level stock report that feeds into the national supply chain. This closes the loop between clinical service data and supply chain data within a single integrated system.

## Benefits for National Health Data Quality

The shift from manual to automated HMS-to-DHIS2 reporting has well-documented benefits for national health data quality in countries that have made the transition. Reporting completeness increases because the monthly submission is generated automatically rather than depending on a staff member finding time to complete a paper form. Timeliness improves for the same reason. Data accuracy improves because the source data is the same structured clinical record used for billing — not a tally mark re-counted at month end.

For MINSANTE, higher-quality facility data improves the reliability of national health statistics, strengthens the evidence base for policy decisions, and improves the quality of reports submitted to international bodies including WHO, the African Union, and bilateral and multilateral donors.

## How OPES HMS Enables DHIS2 Integration

OPES Health Management System is built with Cameroon's national reporting requirements as a core design consideration. The OPES clinical module enforces ICD-10 diagnosis coding at the point of care and captures the patient demographic data — age, sex, pregnancy status — needed to disaggregate DHIS2 indicators correctly. The OPES reporting module includes a DHIS2 export function that maps local records to national data elements and generates the monthly aggregate dataset ready for submission.

Where the facility has internet connectivity, OPES can submit data directly to DHIS2 via the API, generating an import summary log that serves as the official submission record. Where connectivity is unreliable — common in Cameroon's rural and peri-urban areas — OPES generates the DHIS2-formatted data file for offline submission by the data focal point when connectivity is available. The result is a facility that meets its MINSANTE reporting obligations accurately and on time, without adding to the workload of clinical or administrative staff — transforming compliance from a burden into a by-product of normal operations.

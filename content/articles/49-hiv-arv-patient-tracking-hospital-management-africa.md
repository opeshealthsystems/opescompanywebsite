# HIV and ARV Patient Tracking: Managing Long-Term Care in African Health Facilities

**Meta Description:** How African hospitals track HIV patients, manage ARV regimens, monitor CD4 and viral load, and meet PEPFAR/DATIM reporting requirements using a hospital management system.

**Target Keywords:** HIV patient tracking system Africa, ARV management software Cameroon, PEPFAR DATIM reporting HMS, CD4 viral load tracking EHR, PLHIV longitudinal records, HIV programme management Cameroon, antiretroviral therapy software CEMAC

---

## What Is the HIV Burden in Cameroon?

Cameroon carries one of the heaviest HIV burdens in West and Central Africa, with an estimated adult prevalence of approximately 3.4% — translating to roughly 540,000 people living with HIV (PLHIV) as of the most recent UNAIDS estimates. Prevalence varies substantially by region, reaching over 8% in some southern and eastern areas, and is higher among female sex workers, men who have sex with men, and people who inject drugs.

The national AIDS control programme (CNLS — Comité National de Lutte contre le SIDA) coordinates antiretroviral therapy (ART) services through a network of approved treatment centres (centres de traitement agréés, CTAs). Managing thousands of patients on lifelong treatment, each with unique regimen histories, CD4 trajectories, and adherence profiles, is impossible without a robust digital records system. Paper-based patient files — the current reality in many Cameroonian facilities — create dangerous gaps: missed viral load results, undetected treatment failures, and patients who default without any active follow-up.

---

## Why HIV Requires a Longitudinal Electronic Health Record

HIV care is fundamentally different from acute illness management. A patient diagnosed today may remain on treatment for the next 40 years, accumulating dozens of clinic visits, multiple regimen changes, opportunistic infection episodes, and pregnancy events. Each data point matters — and the relationship between data points matters even more.

A longitudinal electronic health record (EHR) for HIV care must:

- Maintain a complete, time-stamped clinical history across all visits
- Track laboratory results (CD4 count, viral load, full blood count, liver function tests) as a trend rather than isolated values
- Link laboratory results to regimen decisions, so that a documented virological failure is visibly associated with the subsequent regimen switch
- Flag data inconsistencies, such as a viral load result that is never followed by a documented clinical decision
- Maintain confidentiality through strict role-based access controls

Without these capabilities, clinicians reviewing a returning patient have no reliable baseline. With them, the entire care history is visible at a glance.

---

## CD4 Count and Viral Load Tracking Over Time

CD4 cell count and HIV viral load are the two principal biomarkers guiding HIV treatment decisions in sub-Saharan Africa. An HMS HIV module must present these not as single values but as trends visualised over the patient's full care history.

### CD4 Count

CD4 count, expressed as cells per cubic millimetre, indicates immune system status. A falling CD4 count in a patient on ART suggests treatment failure or adherence problems. The HMS should display CD4 results in a chronological chart, with alert flags when the count drops below key clinical thresholds (200 cells/mm³ indicates severe immunosuppression; 100 cells/mm³ triggers urgent review).

### Viral Load

Viral load monitoring is the gold standard for detecting treatment failure. WHO guidelines recommend viral load testing at 6 months and 12 months after ART initiation, then annually. A detectable viral load (above 1,000 copies/mL) triggers an enhanced adherence counselling (EAC) session before any regimen switch decision. The HMS must capture viral load results, link them to the EAC workflow, and document the eventual clinical decision — switch or continue with adherence support.

---

## ARV Regimen Management: First, Second, and Third Line

Cameroon's national HIV treatment guidelines, aligned with WHO recommendations, define a structured regimen hierarchy:

| Line | Standard Regimen | Trigger for Switch |
|------|-----------------|-------------------|
| First-line | TDF + 3TC + DTG | Confirmed virological failure after EAC |
| Second-line | AZT or ABC + 3TC + LPV/r or ATV/r | Confirmed second-line failure |
| Third-line | Individualised with DST guidance | Second-line failure documented |

A hospital management system must record the complete regimen history for each patient, including the start date, any modifications (dose adjustments, substitutions for toxicity), and the clinical rationale for each change. When a patient presents at a facility other than their home site — as frequently happens with mobile workers or displaced persons — the digital record ensures continuity of care without reliance on the patient carrying their paper card.

---

## Adherence Monitoring and Appointment Tracking

Adherence to ART is the single most important factor determining treatment success. A patient who takes less than 95% of doses as prescribed risks virological failure and the development of drug resistance. Digital adherence monitoring in an HMS tracks two key proxies for adherence: pharmacy refill punctuality and clinic appointment attendance.

When a patient is due to collect their ARV supply and does not appear within a defined window (typically three days), the system generates a defaulter alert. The healthcare team — nurse, adherence counsellor, or community health worker — can then initiate active tracing: a phone call, a home visit, or a message through a peer support network. Early tracing has been shown to dramatically reduce loss to follow-up rates compared to passive systems where defaults are only noticed when the patient returns — often months later with a much higher viral load.

Appointment adherence data, aggregated across the patient panel, also gives clinic managers a forward view of workload — showing which days will have high patient volumes — enabling staffing adjustments.

---

## PEPFAR and USAID Reporting: DATIM Indicators

Cameroon's HIV programme receives substantial support from PEPFAR (the US President's Emergency Plan for AIDS Relief) and other international partners. PEPFAR-funded facilities are required to report quarterly against a standardised set of indicators known as DATIM (Data for Accountability, Transparency, and Impact Monitoring).

Key DATIM indicators include:

- **TX_CURR**: Number of adults and children currently receiving ART
- **TX_NEW**: Number of adults and children newly enrolled on ART in the reporting period
- **TX_PVLS**: Percentage of ART patients with a documented viral load result below 1,000 copies/mL
- **HTS_TST**: Number of individuals who received HIV testing services
- **PMTCT_STAT**: Number of pregnant women with known HIV status

Generating these indicators manually from paper registers requires hours of data clerks' time each quarter and introduces significant counting errors. An HMS that maps its data schema to DATIM indicator definitions can generate the quarterly report in minutes, with drill-down capability to verify individual patient counts — greatly reducing the risk of audit findings and funding consequences.

---

## TB/HIV Co-infection Management

Tuberculosis is the leading cause of death among PLHIV globally. In Cameroon, TB/HIV co-infection rates are high — facilities managing HIV patients will routinely encounter patients who are simultaneously on anti-TB therapy. Managing both conditions digitally requires careful integration: TB treatment regimens interact with certain ARVs (rifampicin substantially reduces the plasma levels of many protease inhibitors), and clinical decision support should flag these interactions when both regimens are active in the same patient record.

The HMS must also track TB screening at every HIV clinic visit, document the result, and initiate isoniazid preventive therapy (IPT) records for eligible patients. WHO recommends routine TB symptom screening at every ART visit — a workflow that can be embedded as a mandatory field in the HMS encounter form.

---

## PMTCT and Option B+

Preventing mother-to-child transmission (PMTCT) of HIV requires identifying HIV-positive pregnant women as early as possible and initiating lifelong ART (the Option B+ approach). An HMS PMTCT module must:

- Link the antenatal care record to the HIV care record for HIV-positive mothers
- Trigger infant prophylaxis prescriptions (nevirapine syrup) linked to the mother's record
- Schedule infant HIV testing at 4–6 weeks (early infant diagnosis, EID) and at 18 months
- Capture infant test results and link them to the correct mother record

Without digital linkage, infants born to HIV-positive mothers frequently fall through the cracks — neither their prophylaxis completion nor their final HIV status is confirmed in a systematic way.

---

## Confidentiality and Access Controls in HIV Records

HIV-related health information is among the most sensitive data a health facility holds. Stigma remains high in Cameroon and across the CEMAC region, and inadvertent disclosure of a patient's HIV status can have serious social, employment, and safety consequences. An HMS HIV module must implement strict access controls:

- Only clinicians assigned to the HIV programme should access HIV-specific records
- The patient's HIV status must not be visible on general ward dashboards or printed summary sheets
- Audit logs must record every access event — who viewed the record, when, and from which device
- Patients must be able to choose whether their HIV status is shared with other departments within the same facility

These controls must be configurable by facility administrators and enforced at the database level, not merely the interface level.

---

## How OPES Health Systems' HMS HIV Module Meets PEPFAR Requirements

OPES Health Systems has designed its HIV care module to meet the full requirements of PEPFAR-funded facilities in Cameroon and the CEMAC region. The module provides longitudinal patient records with CD4 and viral load trend charts, structured ARV regimen management with clinical decision support for drug interactions, defaulter tracing workflows, and automated DATIM indicator report generation in the standard format required for quarterly submission.

Confidentiality is enforced through a dedicated HIV programme access group — a separate permission tier within the OPES role-based access control framework that prevents HIV data from appearing on general clinical views. All access events are logged and retrievable for audit purposes.

For facilities managing PMTCT, the module includes full Option B+ support: ANC-to-ART record linkage, infant prophylaxis tracking, EID scheduling, and infant outcome recording. OPES's implementation team works directly with facility HIV coordinators and PEPFAR site representatives to configure indicator mappings before go-live, ensuring that the first quarterly DATIM submission is accurate and on time.

---

## Conclusion

Long-term HIV care management is one of the most data-intensive tasks in any health facility. The volume of patients, the complexity of individual regimen histories, the requirements of international donors, and the critical importance of confidentiality all demand a purpose-built digital system. For Cameroonian facilities committed to achieving the UNAIDS 95-95-95 targets, a robust HMS HIV module is not optional — it is the operational foundation on which programme success depends.

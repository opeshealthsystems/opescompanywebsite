# Mutual Health Organisations and Mutuelles Billing: Managing Community Insurance in Cameroon

**Meta Description:** Discover how Cameroon hospitals manage billing for mutual health organisations (mutuelles de santé), including capitation models, claim reconciliation, CHRAFON rules, and multi-MHO HMS management.

**Target Keywords:** mutual health organisations Cameroon, mutuelles de santé billing, MHO hospital billing Cameroon, community health insurance Africa, HMS mutuelles integration, CHRAFON Cameroon

---

## What Are Mutual Health Organisations in Cameroon?

A mutual health organisation (MHO) — known in French as a mutuelle de santé — is a community-based, non-profit health financing scheme in which members pool contributions to cover the medical costs of the group. Unlike CNPS, which is employer-funded and mandatory for formal-sector workers, mutuelles are voluntary, member-governed, and typically rooted in a specific community: a village, a parish, a professional association, a trade union, or a women's cooperative.

Cameroon has one of the highest concentrations of mutuelles in Central Africa, with an estimated 300 to 500 active MHOs depending on the source and definition used. Membership sizes range from as few as 50 households in a rural tontine-linked scheme to several thousand members in larger urban professional mutuelles. Combined, they represent an important but fragmented layer of health coverage for populations outside formal employment — covering an estimated 3 to 7 percent of the population, with concentration in the Adamawa, West, North-West, and South-West regions.

## How Mutuelles Differ from CNPS

The structural differences between MHOs and CNPS shape everything about how hospitals must handle billing for each:

| Dimension | CNPS | Mutuelle de Santé |
|---|---|---|
| Governance | State institution | Member-elected committee |
| Funding source | Employer + employee contributions | Member premiums (often annual) |
| Membership | Mandatory for formal sector | Voluntary |
| Benefit standardisation | National tariff schedule | Set by each MHO individually |
| Claims processing | Centralised (regional offices) | Decentralised (MHO committee or focal point) |
| Payment reliability | Government-backed | Dependent on contribution rates and reserves |
| Regulatory oversight | CNPS Act | CHRAFON (since 2002) |

The critical implication for hospital billing is heterogeneity: each mutuelle defines its own benefit package, its own ceiling per episode, its own pre-authorisation rules, and its own reimbursement timelines. A hospital contracted with ten different MHOs is, in effect, managing ten different insurance schemes simultaneously.

## How Mutuelles Fund Healthcare: Capitation vs Fee-for-Service

MHOs use one of two principal payment models when contracting with healthcare providers:

### Fee-for-Service (Acte par Acte)
Under fee-for-service, the hospital bills the MHO for each service delivered to a member, up to the MHO's approved tariff schedule. This is the more common arrangement for smaller or newer mutuelles. It is administratively straightforward but exposes the MHO to financial risk if utilisation is higher than anticipated — and exposes the hospital to slow payment if the MHO's reserves are depleted mid-year.

### Capitation (Forfait par Tête)
Under capitation, the MHO pays the hospital a fixed monthly or annual amount per enrolled member, regardless of how many services each member consumes. The hospital bears the utilisation risk but gains predictable, advance cash flow. Capitation arrangements require the HMS to track MHO enrolment numbers precisely and to produce utilisation reports that inform future rate negotiations.

In practice, many Cameroonian MHOs combine elements of both: a capitation payment for primary care consultations and a fee-for-service arrangement for hospitalisation, surgery, or laboratory investigations above a threshold value.

## Benefit Limits, Co-Payments, and Pre-Authorisation

Each MHO defines its own benefit limits — the maximum the organisation will cover per consultation, per hospitalisation episode, per year, or per family. Common structures include:

- An annual ceiling per member (e.g., 150,000 FCFA total benefit per member per year)
- A per-episode hospitalisation limit (e.g., 50,000 FCFA per admission)
- A consultation co-payment (e.g., member pays 500 FCFA per visit, MHO covers the rest up to the tariff)
- Exclusions for specific conditions (some MHOs exclude pre-existing conditions, maternity, or HIV-related care in their initial years of operation)

Pre-authorisation for expensive procedures — surgery, imaging, specialist referral — is common in larger MHOs. The hospital must obtain written or phone authorisation from the MHO focal point before proceeding, or the claim will be refused. Managing these pre-authorisation requirements across multiple MHOs, each with different procedures and contact points, is a significant administrative challenge without a structured HMS.

## The Challenge of Heterogeneous MHO Rules

The fundamental billing challenge for hospitals working with multiple mutuelles is that there is no standardised benefit schedule, no common claim form, and no shared electronic platform. A hospital in Bafoussam or Ngaoundéré may hold contracts with five to twenty different MHOs, each with:

- A different tariff schedule (or no tariff schedule — merely verbal agreement)
- A different claim form template
- A different set of approved services and exclusions
- A different payment focal point and process
- A different response time (ranging from 30 days to 6 months or more)

The practical result, without an HMS, is that billing staff maintain a paper dossier or spreadsheet per MHO, manually cross-referencing patient memberships, calculating covered amounts, preparing claim files, and following up by telephone. Errors are frequent, claims are often under-priced (leaving revenue on the table) or over-priced (triggering rejection), and the reconciliation of payments received against claims submitted is rarely done systematically.

## CHRAFON: The Regulatory Body for Mutuelles in Cameroon

CHRAFON — the Cellule d'Appui et de Renforcement aux mutuelles de santé et autres Formes d'Organisations Non lucratives — is the Cameroonian government body responsible for registering, regulating, and providing technical support to mutual health organisations. Established under the Ministry of Public Health, CHRAFON maintains a national registry of recognised mutuelles and sets minimum governance and financial standards for MHO operation.

For hospitals, CHRAFON registration is a useful signal that an MHO has at least basic governance in place and is likely to honour its contractual obligations. Before signing a contract with a mutuelle, hospitals should verify CHRAFON registration status. CHRAFON also facilitates the negotiation of provider-MHO contracts (conventions de partenariat), and its standard contract template — while not mandatory — provides a useful baseline from which both parties can negotiate.

## What an HMS Needs to Manage Multiple MHO Contracts

A Hospital Management System serving a multi-MHO environment must handle several distinct requirements:

- **MHO contract repository** — the system must store each MHO's tariff schedule, benefit ceilings, co-payment rules, pre-authorisation triggers, and contract validity period
- **Member identification** — at registration, the patient's MHO membership number and card must be captured and validated against the stored contract
- **Automatic tariff application** — when a service is ordered, the HMS should retrieve the applicable MHO tariff and calculate the split between MHO liability and patient co-payment automatically
- **Pre-authorisation workflow** — for services requiring prior approval, the HMS should flag the requirement and record authorisation status before the service proceeds
- **Claim generation** — the system should produce a completed claim document in the format each MHO accepts, populated from the clinical record
- **Claims ledger and ageing** — submitted claims should be tracked by MHO, submission date, and outstanding balance, with alerts for overdue payments
- **Reconciliation module** — payments received from MHOs should be matched against submitted claims, with discrepancies flagged for follow-up

## Late Payment Management and MHO Solvency Risk

Mutuelles de santé face a structural solvency risk: if contribution collection rates fall — which happens frequently during agricultural lean seasons or economic downturns — the MHO may lack the reserves to pay providers on time. Hospitals that are heavily dependent on one or two MHOs for a large share of revenue are particularly exposed.

Best practice for hospitals includes: setting a maximum credit limit per MHO before requiring cash payment from members, maintaining an aged debtors report for MHO receivables, and including a payment schedule clause in the provider-MHO convention that specifies consequences for late settlement. An HMS that tracks MHO receivables in real time enables the hospital director to make informed decisions about credit extension — rather than discovering the exposure only at the annual audit.

## Claims Tracking and Reconciliation in Practice

Effective MHO claims management requires two parallel processes that many hospitals currently handle manually:

**Claim tracking** monitors each submitted claim from the date of submission through to payment receipt, flagging claims that exceed the expected response time for follow-up contact with the MHO focal point.

**Payment reconciliation** matches each payment received from an MHO against the specific claims it covers. MHOs often pay in bulk — a single transfer covering multiple claims — without providing a detailed remittance advice. Without systematic reconciliation, hospitals cannot determine which claims have been paid, which are partially paid, and which are still outstanding.

Both processes are labour-intensive when done manually. Integrated in an HMS, they become reports and dashboards — accessible to the billing manager, the finance director, and the hospital director — providing real-time visibility into insurance receivables.

## How OPES HMS Manages Multiple MHO Billing Simultaneously

OPES Health Management System is designed for the Cameroonian reality of multiple, heterogeneous insurance relationships operating in parallel. The OPES insurance module stores an unlimited number of MHO contracts, each with its own tariff schedule, benefit rules, co-payment structure, and pre-authorisation triggers. When a patient is registered as an MHO member, the system retrieves the relevant contract parameters automatically.

At billing, OPES applies the correct MHO tariff and generates a claim document that meets the MHO's format requirements — whether that is a standardised CHRAFON-aligned form or a custom template negotiated with the specific mutuelle. The claims ledger provides a real-time ageing report across all MHOs, enabling the finance team to prioritise follow-up on the highest-value and most overdue claims. Reconciliation is supported through a payment matching function that links bulk MHO payments to individual claim records, producing the audit trail that Cameroonian health facilities increasingly require for donor reporting and financial accountability. For hospital directors managing mixed-payer environments across Cameroon's regions, OPES HMS reduces the administrative overhead of multi-MHO billing to a manageable, structured workflow.

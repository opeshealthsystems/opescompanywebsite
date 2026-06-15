# Health Technology in the Central African Republic: Challenges and Opportunities for HMS in Post-Conflict Settings

**Meta Description:** An honest assessment of digital health in the Central African Republic — the challenges of fragile-state HMS deployment, NGO-led pilots, and the long road to health system recovery.

**Target Keywords:** hospital management system Central African Republic, digital health CAR Bangui, HMS fragile states Africa, health technology CAR, OpenMRS Central Africa, CEMAC health software post-conflict, santé numérique Centrafrique

---

## The Central African Republic: Healthcare in One of the World's Most Fragile States

The Central African Republic (CAR) presents the most challenging health system environment in the CEMAC region and one of the most difficult in the world. With a population of approximately 5 million people, CAR has endured decades of cyclical political violence, coups, and armed conflict that have systematically destroyed health infrastructure, displaced qualified health workers, and severed supply chains for medicines and equipment. The consequences for population health are severe and measurable.

Maternal mortality in CAR stands at approximately 829 per 100,000 live births — one of the highest rates in sub-Saharan Africa. Under-five mortality exceeds 110 per 1,000 live births. Physician density is fewer than 0.1 per 1,000 inhabitants, meaning that the majority of CAR's population has no realistic access to a qualified physician. The country consistently ranks at the very bottom of the UNDP Human Development Index — in 2023 it occupied 188th place out of 193 countries — reflecting the combined weight of health, education, and income deprivation.

The violence that erupted following the 2013 coup and continued through the activities of armed groups including the Séléka coalition and Anti-Balaka militias resulted in the deliberate targeting of health facilities in some regions, widespread looting of medical equipment and medicines, and the exodus of health personnel from most of the country outside Bangui. Many districts that once had functioning health centres now have none. Rebuilt facilities are frequently attacked or occupied again during subsequent phases of conflict. This context is the essential backdrop against which any discussion of health technology in CAR must be situated.

## Health Facility Infrastructure: What Exists and What Has Been Destroyed

Prior to the 2013 crisis, CAR's health facility network was already thin relative to the country's geographic scale. The country covers 623,000 square kilometres — comparable in size to France and Germany combined — with a road network that is largely unpaved and impassable in the rainy season. The Hôpital de l'Amitié (the main reference hospital in Bangui, also known as the Community Hospital) and the Hôpital de Pédiatrie de Bangui are the country's principal public referral institutions, supplemented by a small number of private clinics and mission hospitals in the capital.

Outside Bangui, the health facility landscape is dominated by prefectural hospitals, district hospitals, and rural health posts — many of which were damaged, looted, or abandoned during conflict. The humanitarian response to the 2013–2016 crisis and its aftermath saw international NGOs effectively become the primary healthcare providers across large portions of CAR's territory. MSF, International Medical Corps (IMC), the International Rescue Committee (IRC), ALIMA, and UNFPA among others have maintained clinical operations in contexts where government facility function was wholly absent.

The consequence is a bifurcated health system: a small, partially functional government structure in Bangui and a handful of larger provincial towns, and an NGO-managed network providing the majority of actual clinical services across the rest of the country. This bifurcation has profound implications for health technology adoption, since NGOs and government facilities operate under very different procurement frameworks, accountability structures, and technical capabilities.

## International Dependency and the Role of MSF, IRC, and WHO

Healthcare delivery in CAR depends to an extraordinary degree on international humanitarian actors. MSF alone operates multiple projects across CAR, including emergency surgery, paediatric care, obstetrics, and HIV treatment — in contexts where no other provider is present. The IRC, Médecins du Monde, ALIMA, and numerous other organisations collectively provide clinical services to a large share of the population outside Bangui.

WHO's country office in Bangui plays a coordination role, supporting the Ministry of Public Health in strategic planning, supply chain management for essential medicines, and disease surveillance. UNICEF manages immunisation cold chains and nutrition programmes. UNFPA supports reproductive health services. USAID, through implementing partners, funds specific health programmes including malaria control and health system strengthening in accessible areas.

For digital health, this humanitarian dependency has a specific implication: the most practically significant entry points for health technology adoption in CAR are not government public facilities (which lack the capacity and resources to drive digitisation) but NGO-managed or NGO-supported facilities where international implementing organisations bring their own technical standards and tool preferences. Understanding what digital platforms NGOs already use in CAR — and building compatibility with those platforms — is more strategically useful for an HMS vendor than focusing exclusively on government procurement.

## Open-Source and Donor-Funded HMS Options in Fragile Settings

The humanitarian and donor community operating in CAR has, over time, converged on a small number of digital health tools that have been validated in resource-constrained and fragile-state environments. OpenMRS is the most widely used open-source electronic medical record platform in the NGO-managed health sector across sub-Saharan Africa, including in CAR. Its modular architecture, active developer community, and no-licence-cost model make it accessible to implementing organisations operating under tight humanitarian budgets.

OpenClinic, another open-source platform, has been used in some mission hospital and NGO facility contexts in central Africa, particularly for outpatient registration and basic pharmacy management. DHIS2 serves as the national aggregate health information platform, with WHO and donor support enabling district-level data reporting in accessible areas of the country — though coverage remains incomplete given the security situation.

These open-source tools share characteristics that make them appropriate for fragile settings: they can be deployed on low-cost hardware, function in offline or low-connectivity environments, and do not require ongoing commercial licensing commitments that humanitarian actors may not be able to sustain through funding cycles. Their limitations are also real — they typically require significant technical configuration expertise to deploy well, and their user interfaces are not always optimised for low-literacy or low-digital-literacy clinical staff.

## What Minimal Digital Health Looks Like in Fragile States

In settings like CAR, "digital health" looks quite different from HMS deployments in stable middle-income countries. The realistic baseline for most functional facilities in CAR is:

- **Basic patient registration** using a simple database or spreadsheet maintained on a single laptop with battery backup
- **Disease surveillance reporting** via mobile phone using SMS-based reporting tools or WhatsApp, aggregating into DHIS2 at district level
- **Stock management** for essential medicines using paper-based stock cards, supplemented in some NGO facilities by supply chain software such as CommCare or ODK-based inventory tools
- **Individual patient case tracking** for priority programmes (HIV, TB, malaria, nutrition) using OpenMRS modules configured for those specific conditions
- **Aggregate reporting** to Ministry of Health and donor programme managers via standardised paper forms or DHIS2 mobile data entry

Comprehensive hospital management — covering registration, EMR, pharmacy, billing, laboratory, appointments, and reporting in an integrated platform — is essentially not yet a realistic operational objective for most CAR facilities outside Bangui. The infrastructure prerequisites (reliable power, connectivity, maintained hardware, trained staff) do not yet exist at scale. Acknowledging this honestly is important both for setting appropriate expectations and for identifying the specific facilities and contexts where more ambitious digital tools can realistically be deployed.

## CAR's Plan National de Santé: Long-Term Vision

CAR's Ministry of Public Health has articulated a Plan National de Santé (PNS) that sets out a long-term vision for health system reconstruction. The PNS acknowledges the extraordinary challenges posed by ongoing insecurity, weak governance, and extreme resource scarcity, while identifying strategic priorities including rebuilding health infrastructure, restoring basic service delivery in conflict-affected prefectures, and progressively strengthening health information systems.

The PNS explicitly recognises that digital health will play a role in the health system of a rebuilt CAR, even if the timeline for meaningful implementation is necessarily uncertain. Health information systems are identified as a priority for investment, and the plan expresses commitment to aligning with WHO's Digital Health Strategic Framework for the African Region. For donors and implementing partners engaged in the long-term reconstruction of CAR's health system, the PNS provides a framework within which digital health investments can be anchored.

## Mobile Network Coverage: Bangui vs Rural CAR

Mobile network coverage in CAR is sharply geographically concentrated. Bangui has relatively good mobile coverage, with both 3G and in some areas 4G connectivity available. The main Bangui operators — Moov Africa and Telecel (formerly Orange Centrafrique) — provide urban coverage that makes mobile-based digital health tools feasible in the capital. Mobile money services, including Moov Money, are available in Bangui and offer a potential pathway for digital payment tracking in facilities that serve fee-paying patients.

Outside Bangui, mobile coverage drops precipitously. Most rural prefectures — Haute-Kotto, Basse-Kotto, Haut-Mbomou, Vakaga — have minimal or no mobile coverage. Satellite connectivity (VSAT) is used by some NGO facilities for communication and data reporting, but bandwidth constraints and cost make it unsuitable for data-intensive HMS applications. Any digital health tool intended for use beyond Bangui must be designed as an offline-first system, with data synchronisation occurring only when connectivity is available, however infrequently.

## NGO-Managed Facilities as Entry Points for HMS

The most realistic near-term pathway for HMS adoption in CAR runs through NGO-managed or co-managed facilities rather than purely government institutions. This is for several reasons:

**Procurement capacity**: NGOs operating in CAR have access to international funding streams and procurement systems that can support digital tool investment where government budget cannot.

**Technical capacity**: International NGOs bring technical staff who can configure, train, and maintain digital tools — capacity that is largely absent in government facilities outside Bangui.

**Operational stability**: NGO-managed facilities, while not immune to security incidents, generally maintain more consistent operations than government facilities in insecure areas.

**Accountability requirements**: Donor reporting requirements for NGO programmes create incentives for accurate, timely, and complete data — the same incentives that make HMS adoption valuable in more stable contexts.

For HMS vendors considering CAR as a future market, engagement with international NGO headquarters and regional offices — in Nairobi, Paris, Geneva, or Washington — rather than in-country government procurement is the more productive near-term commercial strategy.

## Recommendations for Health Technology Investment in Fragile Settings

Organisations considering health technology investment in CAR or similar fragile contexts should approach the process with specific principles:

- **Start with data, not software**: The prerequisite for successful HMS deployment is a minimum level of data culture and recording discipline. Investing in data quality improvement — accurate registers, complete patient records, consistent reporting — before introducing digital tools substantially increases the likelihood of successful adoption.
- **Choose offline-first tools**: Any system that requires continuous internet connectivity will fail in most CAR contexts. Offline-first architecture is non-negotiable.
- **Prioritise open-source compatibility**: Aligning with platforms already familiar to implementing partners — OpenMRS, DHIS2 — reduces the training burden and eases system integration.
- **Minimise hardware complexity**: Solutions requiring specialist hardware maintenance are unsustainable in contexts where IT technicians are scarce. Android-tablet-based or cloud-synced solutions using commodity hardware are preferable.
- **Plan for staff turnover**: Systems must be simple enough that newly recruited staff can achieve basic competence within days, not weeks.
- **Build in flexibility for scale-down**: In fragile settings, a facility may need to temporarily suspend digital operations due to security incidents or power failure. Systems should support graceful downgrade to paper-based backup and clean data recovery when operations resume.

## OPES Health Systems: Positioning for CAR When Conditions Allow

OPES Health Systems does not currently position itself as a full HMS provider for active conflict zones or the most severely fragile settings — the honest assessment is that the prerequisites for comprehensive HMS deployment do not yet exist across most of CAR outside Bangui. This intellectual honesty is itself a mark of responsible market positioning.

However, OPES recognises that CAR is on a long and difficult trajectory toward health system reconstruction, and that the CEMAC regional context — in which Bangui sits — means that cross-border flows between CAR and Cameroon will remain clinically significant for the foreseeable future. CAR patients who travel to Cameroonian facilities in Bertoua or Yaoundé for specialist care are part of the same CEMAC patient population that OPES serves.

For NGO-managed facilities in Bangui — those with stable operations, adequate power, and international technical support — OPES HMS is a credible option for administrative and clinical digitisation. The platform's offline-first capability, French-language interface, and modular architecture allow implementation at whatever scope is appropriate to the facility's current capacity, with expansion as conditions permit.

OPES Health Systems welcomes conversations with NGOs, mission hospitals, and development partners operating in CAR about how its platform can be configured for fragile-setting deployment and what support models make sense for the Bangui context.

## A Long View on Digital Health in the Central African Republic

The Central African Republic's path to functioning digital health systems is long, non-linear, and dependent on conditions — security, governance, economic recovery — that lie outside the health sector's control. Realistic optimism requires acknowledging both the genuine severity of current constraints and the real progress that determined actors have made even in extraordinarily difficult circumstances.

The facilities, NGOs, and development partners that invest now in the foundations of digital health in CAR — the data culture, the basic recording disciplines, the open-source tools in Bangui's more stable facilities — are building the base on which more comprehensive systems can eventually stand. The opportunity is not immediate and not uncomplicated. But it is real, and it belongs to those willing to engage with it honestly and patiently.

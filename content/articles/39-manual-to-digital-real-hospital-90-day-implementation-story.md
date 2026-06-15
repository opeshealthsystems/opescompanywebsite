# From Manual to Digital: A Real Hospital's 90-Day Implementation Story

**Meta Description:** A day-by-day account of how a Cameroonian hospital moved from paper-based operations to a full digital HMS in 90 days — the preparation, the go-live, the challenges, and the outcomes.

**Target Keywords:** hospital digitisation Cameroon, implement hospital management system Africa, HMS implementation story Cameroon, going digital hospital Cameroon, health software implementation 90 days Africa

---

## Introduction: What 90 Days of Digital Transformation Looks Like

Most health technology discussions focus on outcomes — wait times reduced, revenue recovered, errors prevented. Fewer address the question that hospital administrators actually need answered before they commit: what does the process actually look like, day by day, over the weeks it takes to go from paper to digital?

This article answers that question through the story of a Cameroonian hospital's 90-day transformation from a fully paper-based operation to a fully digital facility.

The hospital is a 48-bed private facility serving urban and peri-urban patients. At implementation start, all patient records were maintained on paper, appointments were managed by a receptionist with a paper calendar, billing was calculated manually, and the pharmacy tracked inventory with a handwritten ledger.

Ninety days later, every one of those processes was digital.

---

## Days 1–14: The Preparation Phase

### Day 1–3: Baseline Assessment

Before any software was installed, the implementation team spent three days observing operations. They watched the registration process, followed patients through the consulting rooms, observed pharmacy dispensing, and sat with the billing team. They counted how many steps each process required, how long each step took, and where the failures were.

This was the foundation for configuration. You cannot configure a hospital management system correctly without understanding what the hospital actually does — not what the administrator thinks it does, but what actually happens at 9:00 AM when fifteen patients arrive and three are emergencies.

The baseline data: average registration time 22 minutes, average wait from registration to consultation 47 minutes, monthly revenue leakage estimated at 18% of billable services, pharmacy stockout events averaging 3.4 per month.

### Day 4–7: Hardware and Network Setup

Six computers were installed: two at registration, one in each of three consulting rooms, one at the pharmacy dispensing window, and one at the billing desk. A local area network was configured. A local server was installed in a secure room — this would host the HMS database locally, ensuring the system functioned even when internet was unavailable.

Internet connectivity was provided by the facility's existing connection, with a backup 4G modem for failover. The implementation team tested both paths.

### Day 8–14: System Configuration

The implementation team configured the HMS for this specific facility:
- Patient registration form fields mapped to the facility's existing intake form
- Consultation room templates created for the facility's main consultation types
- Medicine inventory database populated with the facility's 847 stock-keeping units
- Insurance schemes configured: CNPS direct billing, three private insurers, and self-pay
- Price list entered: 312 services, procedures, and medicine prices
- User accounts created for all 34 staff members requiring system access
- Role-based access controls configured: reception, clinician, pharmacist, billing, management

Configuration took seven working days. The implementation team worked with the head of each department to validate their module before training began.

---

## Days 15–28: Training Phase

### The Training Approach

Every staff member who would use the system received role-specific training. The implementation team resisted the temptation to do one large group session — instead, they trained in small groups of four to six people, by role, with hands-on practice on the actual system.

Training sessions were two to three hours, spread over three days per role group. After the initial training session, staff had access to the system in a test environment — a separate database loaded with dummy patient data — where they could practice without affecting real records.

**Week 3 Training Schedule:**
- Day 15–16: Registration team (5 staff, 3 sessions)
- Day 17: Management and reporting (3 staff, 1 session)
- Day 18–19: Billing team (4 staff, 2 sessions)

**Week 4 Training Schedule:**
- Day 20–21: Clinical team — consulting physicians (6 doctors, 4 sessions)
- Day 22–23: Nursing team (9 nurses, 5 sessions)
- Day 24–25: Pharmacy team (4 pharmacists, 2 sessions)
- Day 26–27: IT support and system administration (2 staff, 2 sessions)
- Day 28: Refresher and questions from all teams

### The Challenges of Training

The most significant training challenge was not technical skill — it was speed. The system is fast for experienced users, but during training, staff were slower than with their familiar paper processes. Some staff became anxious: "If I am slower with this than with paper, won't the queue get longer?"

The answer required evidence. The implementation team showed them timing data from other facilities: after two weeks of daily use, most staff were faster in the system than they had been on paper. The anxiety was real, but the resolution was in the data.

The second challenge was the oldest member of the reception team — a 54-year-old woman who had worked in the registration room for eleven years. She had been managing the paper system with great skill and feared she would be left behind. The implementation team invested extra time with her, working through the registration process step by step. By the end of training week, she had become an informal champion of the new system among her colleagues.

---

## Days 29–42: Go-Live Phase

### Day 29: Go-Live

Go-live happened on a Monday. The implementation team was on site from 6:30 AM. Three implementation support staff were stationed at the registration desk, consulting rooms, and pharmacy respectively.

The first two hours were the most challenging. Patient volume at opening was high, staff were operating more slowly than usual while the new system was new, and there was one moment — at 8:47 AM — when the registration computer showed an error message that no one had seen before. The implementation team's on-site support resolved it in four minutes, and the queue continued moving.

By 10:00 AM, the pace was approaching normal. By 11:30 AM, some staff were moving faster in the digital system than they had on paper the previous Friday.

### Days 30–42: Parallel Operations

For the first two weeks of go-live, the hospital maintained parallel paper and digital processes. Every patient registration was done both digitally and on paper. Every prescription was written both in the system and on paper.

This doubled the workload for two weeks. But it provided a safety net that proved important twice — once when a consulting room computer had a software issue on day 34, and once when a nurse forgot her login password on day 38.

Both times, paper was available. Both times, the issue was resolved the same morning. Both times, the existence of the parallel process prevented the issue from becoming a crisis.

By day 42, management made the call: the parallel paper process was retired. From that point, the HMS was the system of record.

---

## Days 43–70: Stabilisation Phase

### What Stabilisation Actually Looks Like

Stabilisation is the phase that does not get written about enough. It is not dramatic. It is the accumulation of small corrections, configuration adjustments, and process refinements that convert a system that basically works into a system that works well.

During this phase:
- Six medicine entries had to be corrected (wrong pack sizes entered during configuration)
- Two consulting room templates were adjusted based on feedback from doctors
- The pharmacy's dispensing workflow was modified after the head pharmacist identified a more efficient sequence
- The billing module's insurance calculation was reconfigured for one insurer whose fee schedule had changed
- Two staff members required additional one-on-one support — one receptionist who struggled with the computer interface, and one doctor who was initially resistant to digital prescription entry

None of these were crises. All were resolved within the stabilisation period.

### The First Month-End Close

The first month-end close in the new system was a milestone. Previously, the billing team had spent three days manually compiling monthly revenue figures from paper invoices. In the new system, the month-end report was generated in twenty minutes.

The report showed total revenue 16% higher than the comparable month in the previous year — driven partly by the period being seasonally busier, but primarily by the elimination of billing leakage. Every service had been invoiced. Every invoice had been tracked.

---

## Days 71–90: Optimisation and Results

### Measuring Against Baseline

At day 90, the implementation team re-ran the baseline assessment — the same timing exercise, the same process observation, the same measurement of revenue and pharmacy performance.

**Results:**

| Metric | Day 1 | Day 90 | Change |
|---|---|---|---|
| Average registration time | 22 min | 5 min | -77% |
| Wait from registration to consultation | 47 min | 12 min | -74% |
| Total patient journey time (average) | 138 min | 55 min | -60% |
| Monthly revenue leakage | ~18% | ~3% | -83% |
| Pharmacy stockout events | 3.4/month | 0.2/month | -94% |
| CNPS claim rejection rate | 24% | 5% | -79% |

### The Director's Assessment

The hospital director, reflecting on the 90 days, identified three things that had made the difference:

**First, involvement.** Staff had been part of the configuration process from the start. When the system went live, it felt like their system, not a system that had been imposed on them.

**Second, honesty.** The implementation team had been honest about what the transition period would look like — more work, more stress, some mistakes — and that had prepared staff mentally. When the hard days came, no one was surprised.

**Third, presence.** The implementation team was physically in the building for every day of the go-live week. That presence was not about technical support alone. It was a signal that the vendor was invested in the outcome.

---

## What This Implementation Did Not Do

This account would be incomplete without noting what the implementation did not achieve:

It did not eliminate all manual processes on day one. Some clinical documentation — particularly specialist referral letters and complex clinical assessments — continued on paper for the first six weeks while clinical templates were refined.

It did not immediately change staff culture. Two doctors initially continued to write paper prescriptions even after digital prescribing was live. It took a direct conversation with the hospital director and a demonstration of the time savings to shift their practice.

It did not deliver the full revenue improvement in the first thirty days. The full effect of digital billing was visible at sixty days, not thirty, because some services (particularly CNPS reimbursements) operate on delayed payment cycles.

Realistic expectations are part of trust. The 90-day transformation is real. The outcomes are real. The path has genuine challenges.

---

## Frequently Asked Questions

**What is the minimum preparation time before go-live?**
For a facility of this size (48 beds, 34 system users), the preparation phase was 28 days. Smaller facilities with fewer staff and simpler processes can go live in as few as 14 days. Larger or more complex facilities may require 6–8 weeks of preparation.

**What happens if a key staff member is absent during training?**
Training should be completed before go-live for all staff who will use the system. If unavoidable absences occur, one-on-one catch-up training should be provided before the absent staff member begins using the live system.

**Is 90 days a realistic timeline for any facility in Cameroon?**
For a typical private clinic or small hospital implementing a comprehensive HMS for the first time, 60–90 days from contract to full digital operations is realistic. Very small facilities (under 10 staff, single consulting room) can achieve this in 30–45 days.

---

## Conclusion: The 91st Day

The 91st day was a normal day. Staff came in, logged into their accounts, registered patients, saw consultations, filled prescriptions, issued invoices, and went home. The system was just how things worked now.

That is the quiet outcome of 90 days of transformation. Not a dramatic event. Just a better normal — faster, more accurate, more financially sound — that the facility cannot imagine going back from.

---

*OPES Health Systems provides implementation support through every phase of the journey described in this article. Contact us to discuss what a 90-day implementation roadmap would look like for your facility.*

# OPES Health Systems — Pre-Launch Content Sign-Off Review (2026-06-21)

> Outward-facing content review run as the content half of the go-live checklist
> (`go-live-runbook.md` §0 "Content sign-off"). An adversarial multi-reviewer pass over the
> highest-risk public articles — competitor comparisons (legal/brand), clinical product
> articles (medical accuracy / efficacy overclaim), and French translations — followed by a
> surgical fix pass applying only the reviewers' specific recommended rewordings.

## Method

- **Review:** 15 independent reviewers across 3 dimensions (legal/brand, clinical/factual,
  French quality), each returning a structured verdict (`pass` / `fix_recommended` / `needs_human`)
  with quoted issues and suggested rewordings.
- **Fix:** 12 surgical editors (one per flagged article, EN + FR together) applying **only** the
  reviewers' recommended changes — no other sentences touched. Result: **125 line-level
  replacements, perfectly balanced (no net add/remove)**, frontmatter skeletons verified intact,
  blog reseeded (113 posts), Blog/Readability/Market guards green (25 tests, 1977 assertions).
- Commits: `250ccde` (clinical), `6937c9e` (competitor), `455bb06` (French + gov hedging).

## The one systemic finding (now fixed)

The "Problems & Solutions" clinical series repeatedly framed OPES — **administrative
records/workflow software, not a regulated medical device** — as the agent that itself
*protects / fixes / prevents / catches / detects* disease. Every independent clinical reviewer
flagged this as **medical-device / clinical-efficacy overclaim** (a real regulatory + liability
exposure for an unregulated product). All flagged instances were reworded so the software
**supports clinicians** (helps them track, structure, document, be reminded, surface data)
rather than producing the clinical outcome. Persuasive structure and all clinical facts were
preserved.

| Article | Product | Highest-risk item (before) | Now |
|---|---|---|---|
| 97 | GYNOBSIS | "digital partogram that raises **automatic alerts on abnormal labour**" (reads as clinical decision-support) | charting tool that highlights when clinician-plotted readings cross the alert/action lines |
| 98 | PAEDIS | H1 "How PAEDIS **Protects Every Child**"; IMCI "**classify-and-treat… referral decision support**" | "Helps Clinics Keep Every Child's… Record Complete"; documentation aid supporting clinician-led IMCI |
| 99 | ENDOIS | H1 "…**Fixes It**"; "**prevents complications**" (title/meta/CTA/body) | "…Helps Clinicians Track It"; "helps structure complication screening" |
| 100 | MHIS | "MHIS **solves** this… answers each gap" | "helps teams reduce these risks by…" |
| 104 | OPHIS | H1 "…**Fixes It**"; "**genuine blindness prevention**" | "…Helps Clinics Catch It Earlier"; "underpin blindness-prevention programmes" |
| 105 | NDIS | "**so no at-risk patient is missed**" (zero-miss guarantee); "makes nutrition care safer" | "so fewer at-risk patients are missed"; "supports clinicians in delivering nutrition care" |
| 108 | SLTIS | software "**protects patients from aspiration**"; "the **safety core**" | "makes safe-diet decisions visible across the care team"; "the dysphagia documentation module" |

> **Deliberately retained:** the "…and How X Fixes It" formula remains on 97 / 100 / 108 (and
> "Closes the Gap" on 105) because there the "fix" refers to fixing the **paper-records/workflow
> problem** — which is accurate — not a clinical outcome. Reviewers flagged the formula only on
> 99 and 104, where the body crossed into clinical-prevention claims. The edits track the
> evidence rather than blanket-rewriting the brand voice.

## Competitor comparisons (110–113) — legal/brand

The set is unusually careful for comparative marketing (strong "based on publicly available
vendor info as of 2026 — verify with each provider" disclaimers, respectful framing, explicit
"there is no single best"). **No item rose to genuine legal jeopardy.** Fixes applied (pure
risk reduction, OPES's own positioning untouched):

- **110 (hub):** dropped subjective "prestigious"/"narrower"; anchored GESMEDIC's scope to its
  own self-description; removed an OPES-supplied adoption quantifier.
- **111 (vs Evolucare):** **removed named non-consenting client hospitals** (Polyclinique Farah,
  Hôpital Mère-Enfant Dominique Ouattara, Centre Médical la Cathédrale) **and partner companies**
  (Altea, Eurosys, Glem Solutions); generalised + attributed to Evolucare's published materials.
  *(Keyword line left as-is — "WebHospital" is Evolucare's own product, discussed in the body;
  ProsoftAfrica/GESMEDIC do not appear in 111's keywords.)*
- **113 (vs ProsoftAfrica):** multi-country footprint + feature set now attributed to
  ProsoftAfrica's publicly available materials at the point of claim; dropped open-ended "and beyond".
- **112 (vs GESMEDIC):** **passed the review unchanged** — model handling of capability gaps
  ("Not in stated scope" / "Not specified publicly").

## French quality

All French reviewers returned `pass` / `fix_recommended`; the translations read as natively
authored, with correct medical/technical terminology. One genuine factual slip fixed:

- **09.fr:** "demandes à **la CNPS**" — the CNPS is Cameroon's social-security/pensions body, not
  a hospital-insurance claims payer → "aux **organismes payeurs**"; also fixed an English-calque
  closing line.

Remaining French notes were cosmetic (EHR abbreviation consistency DSE/DME/DPI across the
competitor series; minor register choices) and are non-blocking polish.

---

## ⚠ Outstanding for HUMAN sign-off (cannot be resolved without external sources)

1. **Article 86 — government statistics need a source-check.** The figures are now hedged
   ("reported/estimated/reportedly") so they no longer read as OPES's own verified assertions,
   but they should be confirmed against primary sources before launch:
   - the **~US$51.3M** health-digitization plan figure (verify it refers specifically to
     digitization, not a broader World Bank/IDA health-financing envelope);
   - the **~5M CSU enrolment** count (cite source + date — politically sensitive, changes over time);
   - the named plan partners (**I-TECH/UW, Johns Hopkins Cameroon Program, CDC-PEPFAR**) and the
     **"2025 evaluation"** of the 2020–2024 plan;
   - **~4% of GDP** government health spending (attribute to WHO GHED / year; "% of GDP" vs
     "% of government budget" are commonly conflated).

2. **Substantiate OPES's own self-claims (company-only knowledge).** Across 110/112 the reviewers
   noted that, in a comparative page, OPES must be able to back its own differentiators. Confirm
   these are **shipping, not roadmap**, before go-live and keep the evidence on file:
   - "22 integrated systems" all live;
   - **HL7 FHIR R4** interoperability actually implemented;
   - **in-country data-residency** option available;
   - full **EN/FR bilingual on every screen** (mid-session switch);
   - offline tolerance.
   Marketing should also not lift the scoped "strongest fit for a facility that wants…" line as a
   bare superlative ("OPES is the strongest HMS") in ads/meta without the qualifying clause.

3. **Optional final legal pass on 111's competitor facts.** The named third parties are now
   generalised, but if any doubt remains about Evolucare's current African deployments/AGFA
   relationship, an in-house/external counsel skim before publishing is prudent.

## Verdict

The outward-facing content is **launch-ready once items 1–2 above are confirmed by a human**.
All reviewer-flagged wording risks (medical-device/efficacy overclaim, competitor attribution,
the CNPS factual slip) have been fixed and committed. The only true blockers are facts that
require company/primary-source knowledge I cannot verify — they are listed explicitly above so
the sign-off is a quick confirmation rather than a re-review.

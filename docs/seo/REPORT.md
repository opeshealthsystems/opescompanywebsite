# OPES live content, search and account remediation

30 October 2026 · https://opeshealthsystems.com

## Result

Internal evidence-based SEO checklist: **19/100 before, 88/100 after**. This is the skill's weighted ten-check rubric, not a Google score, ranking forecast, certification or independent audit. The weakest remaining areas are evidence for broader product capability claims and performance/field measurement. Raw checks and scoring output are in `evidence/before/` and `evidence/after/`.

The scoped changes are deployed. The main release copied 123 source/build files with verified hashes; a final two-file correction fixed title branding and reset-email failure handling. Backups are outside the public web root in `/home/u117099711/releases/ohs-content-auth-20261030/`. No existing customer records or historical articles were deleted.

## Findings and changes

| Priority | Issue / impact | Live evidence before | Fix and verification |
| --- | --- | --- | --- |
| High | Broken internal destinations prevent users and crawlers reaching content | 14 failed destinations in a 134-URL crawl: locale login/registration routes and missing country articles | Added legacy redirects; only link to published country articles. New crawl: 124 URLs, zero failures. The URL count fell because sample courses were unpublished and missing article links were removed. |
| High | Blog advertised articles that did not exist | Zero published posts; three static homepage previews pointed to the empty index | Added three original bilingual planning/evaluation articles with working individual routes and primary references. Homepage cards now come from published records. Existing historical content was not bulk-seeded. |
| High | Repeated/default metadata and inconsistent origin | Home/blog/directory shared titles; eight pages repeated the company name; generic/overlong descriptions | Public landing-page metadata register, localized product titles, normalized descriptions, one brand suffix, canonical origin and reciprocal language links. Final 15-route check passed. `www` now returns 301 to the apex domain. |
| High | Unsupported certification and contradictory service promises | “Certified for Africa”; uptime figures of 99.5%, 99.9%, 99.95% and 99.99% across public pages | Replaced trust/support/FAQ assertions with project-specific verification and signed-agreement requirements; removed the fixed uptime promise from terms and added indicative-pricing wording. No certification or legal sign-off is asserted. |
| High | Misleading sample content and anonymous endorsement | Five partners with example.com websites; three courses using an entertainment-video URL; anonymous medical-director quotation | Backed up and unpublished the five partner and three course records; removed the anonymous quotation. Production demo seeding is disabled. Demo/test identities are excluded from public testimonials/directories. |
| High | Duplicate login screens and embedded shared credentials | Public login and separate Filament login exposed demo-login mechanisms | `/admin/login` redirects to `/login`; locale login aliases redirect there too. Removed demo passwords/buttons and the callable demo-login method. Inactive users cannot access the admin panel. Login and reset pages are noindex. |
| High | Owner needed an admin account and password reset | Requested admin email was absent | Created active `admin@opeshealthsystems.com` with the admin role and 23 permissions, using an undisclosed random initial password. Password-reset integration tested. Live SMTP rejected the reset email; an owner-only one-time reset link was generated and handed over separately. No reset token is stored in this report. |
| Medium | Incomplete sitemap and stale modification dates | Seven static sitemap routes; reading an article could update its editorial modification date | Expanded sitemap to useful public landing pages, markets, published posts and products; excludes empty catalogues and confidential/auth routes. Scheduled articles stay private. View increments no longer change editorial timestamps. |
| Medium | FAQ not suitable as reliable answer content | Broad compatibility, endorsement and SLA claims lacked evidence | Ten concise EN/FR answers now use the same data as their FAQ structured data. Added consistent WebPage/WebSite/Organization entities and the approved logo. Validated JSON-LD syntax and `@context`. |
| Medium | Outdated legal references and inaccurate cookie description | Trust copy relied on older laws; privacy said session-only cookies despite a persistent locale cookie | Added the official reference to Cameroon Law 2024/017, corrected cookie/retention wording, and removed claims that this reference establishes compliance. Counsel review remains required. |
| Medium | DrugTrace absent | Existing catalogue contained 22 products | Owner confirmed DrugTrace is released and available. Added an EN/FR product page and Medicine Safety category, homepage spotlight and demo selection. Total is now 23: the existing 22 plus DrugTrace. No unverified feature list or certification was invented. |
| Medium | Navigation and typography acceptance not evidenced | Broad primary navigation; no retained computed-style checks for requested sizes | Six primary links; Inter headings, 56px desktop/34px mobile hero, 18px desktop/16px mobile paragraphs. Thirty live page/width combinations passed overflow checks at 360, 390, 768, 1024 and 1440px. Twelve live axe scans detected no WCAG A/AA violations on representative pages. |
| Medium | Official seal absent | Generic marks used for website identity | Created a transparent cutout from the supplied official seal. Applied to header, footer, company page, login, admin branding, favicon, social metadata and reset-mail header. Final browser check: corner alpha 0, centre alpha 253; proportional 1254×1254 asset. |

## Validation and scope

- Production build passed.
- Targeted regression suite: **70 tests, 2,246 assertions passed**. Final title/reset checks: **11 tests, 114 assertions passed**; these overlap the regression suite and are not additive.
- Before crawl: `evidence/before/crawl.json`. After crawl: `evidence/after/crawl.json`. Final corrected-title/login/host/logo checks: `evidence/after/final-check.json`.
- Actual full-page screenshots and computed styles at the five requested widths: `evidence/live/`. The rendered report includes 30 combinations and 12 axe scans with zero detected violations. These are automated checks and representative visual evidence, not certification of every page or every interaction.
- New editorial publication and DrugTrace seeding are idempotent. A targeted row snapshot was retained before sample-content unpublication and new records were created. No schema migration was needed; DrugTrace uses the existing product-category enum internally and a dedicated public Medicine Safety group.
- Login/reset behaviour was exercised with isolated test accounts. The owner's real password was not set or exposed, and the final live account was not signed into using a shared temporary password.
- Search/AEO helper reports are retained as diagnostics only. Content accessibility and structured data do not guarantee indexing, rankings, rich results or AI citations.

## Sources and implementation guidance

- [Google: AI features and your website](https://developers.google.com/search/docs/appearance/ai-features) — conventional search fundamentals, crawl access, useful text and structured data matching visible content remain relevant; special AI markup or guaranteed inclusion is not claimed.
- [Google: localized versions](https://developers.google.com/search/docs/specialty/international/localized-versions) — reciprocal fully qualified alternates, self-references and genuine language content.
- [Cameroon Presidency: Law 2024/017](https://www.prc.cm/files/9b/df/2c/e818fedc7d5568778f884ea2886bea7d.pdf) — primary legal reference; obligations and actual implementation status require counsel review.
- [HL7 FHIR overview](https://www.hl7.org/fhir/overview.html) and [WHO DIIG](https://www.who.int/publications/i/item/9789240010567) — primary references for the newly written engineering/planning articles. Neither is presented as approval of an OPES product.

## Prioritized remaining actions

1. **SMTP follow-up: transport restored.** Hostinger implicit TLS SMTP on port 465 was configured with owner-supplied credentials. A fresh admin reset request returned `passwords.sent`. The owner should confirm inbox/spam placement. Credentials are kept only in the protected live environment configuration.
2. **Obtain counsel and product evidence.** Review privacy, contractual terms, hosting/retention practices and detailed product capability claims. Named leadership, approved customer case studies, real product interfaces and independent certification documents cannot be fabricated.
3. **Complete French technical product bodies.** All 23 catalogue-card subtitles/summaries and homepage product captions now use a French register. Shared chrome, metadata, FAQs, DrugTrace and three articles are bilingual. Older detailed module descriptions and some directory/training labels still use English. See [the final design report](../design/README.md) for direct French retrieval and six-width rendering evidence.
4. **Measure and improve live performance.** Previous local Lighthouse results did not meet all performance targets. This release does not claim field CWV success; the full-resolution seal also needs a future lossless/owner-approved web-asset optimisation pass. Keep CLS below 0.1 and compare lab runs under matching conditions; do not allow more than 10% LCP/TBT regression without review.
5. **Connect Search Console/Bing and measure outcomes.** Submit the sitemap in verified webmaster accounts; inspect actual indexation and queries, and measure referrals/citations rather than promising rankings or AI visibility. No webmaster submission or external account messaging was performed.
6. **Broaden manual coverage.** Keyboard/mobile checks and axe scans are retained, but NVDA/VoiceOver, physical-device checks, authenticated portal-wide review and exhaustive external-link verification remain unperformed.

# Public branding changes — 29 October 2026

Deployment status: **deployed on 29 October 2026 at 20:31 WAT** to https://opeshealthsystems.com. The 96 deployed source/build files passed hash verification. Views were cleared/rebuilt and the queue restart signal was sent. The original release archive remains an immutable record of the pre-deployment package.

## Changes

- Solid navy/teal primary actions; brighter blue text and headline gradient; visible control borders and keyboard focus; reduced-motion support.
- OPES Health Systems spelling in shared navigation/footer, consistent OPESCare in the main hero copy, localized default metadata without unsupported superlatives or government-alignment language.
- Contact labels, autocomplete, validation summary, patient-data caution, localized receipt feedback without a promised response SLA. Product demo submissions now send the product identifier expected by the controller.
- Keyboard skip link, hidden dropdown focus protection, Escape dismissal, language disclosure semantics, linked category navigation, larger slider controls, corrected heading levels. Hero slides no longer advance automatically.
- Corrected 320px layout, including an implicit second grid column in the ecosystem graphic. Counts use the rendered catalogue. The public catalogue falls back to configured product definitions only when the products table is empty; existing disabled products remain disabled. Cards without configured detail pages are excluded.
- Product catalogue/detail notices distinguish marketing descriptions from verified availability, deployment or certification. Existing repo identity retained; no replacement logo invented.

## Verification

Commands executed from the repository root:

```powershell
npm run build
& 'C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe' artisan test --compact --filter='PublicBrandingTest|ReadabilityTokensTest|PublicFormMarkupTest|ProductPageTest|MarketPageTest|SitemapTest|BlogValidationTest|N4QuoteDemoNotificationsTest'
```

Build passed. Targeted regression suite: **39 tests, 2,080 assertions passed**. Includes English/French contact validation, persisted test leads in isolated SQLite, catalogue fallback, disabled products, and configured product detail routes. It does not send real production email.

Evidence in `evidence/`:

| Check | Result | Evidence |
| --- | --- | --- |
| Browser routes and labels | 24 combinations: EN/FR home, catalogue and contact at 320, 360, 768 and 1440px; HTTP 200, no viewport overflow or unnamed form controls | browser-report.json |
| Keyboard/mobile | Skip link focuses main; mobile disclosure expands | browser-report.json |
| Internal public destinations | 114 links returned success | internal-links.json |
| axe WCAG 2 A/AA and 2.1 AA | 16 scans, zero detected violations on EN/FR home, catalogue, OPESCare and contact at 320/1440px | axe-report.json |
| Design-token contrast | Nine recorded pairs pass their text/non-text threshold; primary action text 7.59:1, hover 10.16:1 | token-contrast.json |
| Local mobile Lighthouse EN | Performance 58, accessibility 100; FCP 2.4s, LCP 4.8s, CLS 0, TBT 690ms | lighthouse-en.json |
| Local mobile Lighthouse FR | Performance 76, accessibility 100; FCP 2.0s, LCP 4.1s, CLS 0, TBT 340ms | lighthouse-fr.json |
| Actual implementation screenshots | Full-page home/catalogue/contact EN/FR at 320 and 1440px | *.png |
| Live deployment smoke test | 12 public/auth-entry routes returned success; 8 EN/FR home/contact scans at 360/1440px detected zero WCAG A/AA violations; no viewport overflow; mobile open/Escape close and contact required-field validation passed | live/report.json and live/*.png |

These Lighthouse values are local lab measurements, not live-server Core Web Vitals or a performance pass. axe also records cases requiring human inspection; automated results do not establish full accessibility compliance. New performance regression budget: no more than 10% deterioration in LCP/TBT compared with these reports under comparable conditions; CLS must remain below 0.1. Current LCP and TBT require further performance work.

For browser reruns, start the app on `http://127.0.0.1:8765`, install `playwright`, `@axe-core/playwright` and `lighthouse` into `storage/branding-tools`, and set `PLAYWRIGHT_MODULE` to the absolute Playwright package directory. Run the four `*-qa.cjs` scripts from the repo root. QA dependencies are isolated from the application lockfile.

## Release procedure when SSH is supplied

1. Confirm the correct application path, runtime, deployed source revision and web root. Back up the current changed source files and `public/build` and retain a rollback copy outside the web root.
2. Deploy only this change set and its compiled `public/build` output, or deploy the reviewed commit and run `npm ci` plus `npm run build` on the server. Do not overwrite `.env`, uploads or database content. No migration or seeding is needed for these fixes; the existing generic `deploy.sh` reseeds content and should not be used for this scoped release.
3. Run `php artisan view:clear` then `php artisan view:cache` in the live application root. Check ownership and confirm the built manifest/assets return HTTP 200.
4. Verify live EN/FR home, products, product details, contact, book-demo, sitemap, mobile navigation, language switching and validation. Submit an explicitly marked test enquiry only with a verified delivery destination. Check server logs.
5. Roll back changed files and the matching build directory, then refresh compiled views if a regression appears.

## Open items / limits

- Deployed over the owner-authorized SSH connection. Rollback archive: `/home/u117099711/releases/ohs-branding-20261029/backup-reviewed.tar.gz`, outside the web root. No database migration or reseeding was performed. Live browser evidence is recorded separately in `evidence/live/`.
- Official master/product logo approval and registered spellings beyond the existing repo identity require owner evidence. Existing assets/text retained.
- No legal/clinical review, certification, regulator approval, uptime SLA, government relationship, pilot/deployment evidence or patient-privacy approval is asserted. Prominent unsupported claims were removed/qualified, but historical articles and all product capability statements still need owner review.
- Product detail bodies come from existing English configuration even on French URLs; complete French product content remains outstanding. Shared chrome and revised copy/metadata support both languages. Existing routes and canonical/hreflang logic were preserved; there was no URL migration.
- No physical mobile, NVDA/VoiceOver, production SMTP, external-link sweep, authenticated portal-wide audit or field CWV validation was performed.

This is a scoped public branding/accessibility remediation, not a sign-off of every gate in the pasted launch programme.

# Seal-based platform palette

30 October 2026 · deployed to https://opeshealthsystems.com

The corporate colours from the supplied specification are navy **#073656** and blue **#1664D4**. The supplied seal also informs the cyan accent and silver/white neutrals. The seal artwork is unchanged.

## Combinations

| Role | Background | Foreground / accent | Use |
| --- | --- | --- | --- |
| Corporate header/footer | #073656 navy | #F1F5F9 white, restrained cyan | Identity and navigation |
| Dark public/portal canvas | #081B30, #102A43, #16354F | #F1F5F9, #CBD5E1, #A7B8CC | Body, cards and supporting copy |
| Primary action | #1664D4 blue | #FFFFFF | Buttons; 5.52:1 text contrast |
| Hover action | #104784 | #FFFFFF | Buttons; 9.31:1 text contrast |
| Dark accent | Navy surfaces | #69D5F5 cyan / #8AC5ED blue-silver | Links, icons and limited highlights |
| Light admin workspace | #F3F6FA silver / #FFFFFF | #102A43 navy, #425A70 secondary, #075985 links | Admin light mode |
| Status | Appropriate surface | Green, amber or red with text labels | Success, warnings and errors only |

The full token source is `resources/css/seal-tokens.css`; existing components retain compatible variable names. A standalone visual preview is in `palette.html`. Fourteen core text/control contrast pairs pass their defined thresholds in `evidence/contrast.json`.

## Changes

- Removed authored interface gradients, glow layers, coloured shadows, text shadows and blur/drop-shadow effects. Solid colours and outlines carry interaction states.
- Flattened decorative treatments in public pages, login, shared portal components and custom admin styling. The admin provider defines matching light/dark surface combinations and blue primary actions.
- Standardised decorative product/family/market colours to the seal palette instead of separate green/purple/rainbow identities. Status semantics remain distinguishable by wording and context.
- Restored formerly gradient-clipped text to ordinary visible text, including statistics and login branding.
- Corrected low-contrast blog date/headline numbers and pricing/About labels. Replaced the public article paginator with readable, labelled previous/next controls. Named the demo form’s selects and date field.
- French catalogue cards now have explicit French subtitles and summaries for all 23 products; French homepage product captions use the same register. Removed unsupported adoption copy and affected stale 22-system references.

## Evidence

- Production build passed. Initial palette regression suite: **68 tests, 2,263 assertions passed**. Final targeted colour/French checks: **26 tests, 2,166 assertions passed**, overlapping the first suite.
- `evidence/live/report.json`: 26 live route/width combinations and 26 axe WCAG 2/2.1 A/AA scans; zero detected violations, no overflow and no gradients/shadows/filters on the checked visible controls. Normal, hover and focus states are recorded.
- `evidence/final/report.json`: the final corporate colours and French cards checked at 360, 390, 430, 768, 1024 and 1440px. Includes WCAG 2.2 AA-tagged checks at representative narrow/wide widths and direct French blog/compliance checks.
- Final public buttons, including the mobile menu and carousel controls, have a 44px minimum target. The final rendered report checks actual target dimensions alongside overflow and decorative effects.
- Screenshots are actual rendered pages, not design mock-ups. The public hero retains Inter, 34px at phone widths and 56px on desktop.
- Main palette release: `/home/u117099711/releases/ohs-seal-palette-20261030/`; 147 deployed files passed hash verification. Final exact-colour/French polish and its backup: `/home/u117099711/releases/ohs-fr-colour-polish-20261030/`. No account or database changes were required for these visual releases.

These results establish the recorded combinations and sampled public/auth interfaces. They are not independent certification of every authenticated screen, every device or all regulatory requirements. Full authenticated portal visual regression remains an explicit follow-up; the shared tokens/admin theme are applied but those screens were not signed into with the owner’s password for this review.

## Mail and content follow-up

Hostinger SMTP was configured on the live server using the owner-supplied mailbox credentials, implicit TLS on port 465. The application returned `passwords.sent` for a fresh reset email to the requested admin address. This confirms transport acceptance; inbox placement is for the owner to confirm. Credentials were not added to repository files or this report.

Direct live retrieval shows three French blog articles and current French trust/privacy wording. Old search/retrieval snapshots should not be treated as the live release. Older product-detail technical bodies are still partly English; catalogue-card translation does not imply that every module description has been translated. Named leadership, approved deployment evidence, legal sign-off and field performance measurement remain outside these verified visual changes.

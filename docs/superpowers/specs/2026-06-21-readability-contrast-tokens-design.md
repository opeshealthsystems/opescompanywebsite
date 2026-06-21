# Readability & Contrast — Design-Token Overhaul (Design Spec)

**Goal:** Make every surface readable without strain by (1) introducing a CSS design-token system (colors + type scale) and (2) eliminating sub-12px text and faint gray-on-dark text, while preserving the existing look and brand.

**Architecture:** A `:root` token block in `resources/css/app.css` defines an AA-compliant color scale and a type scale with a readable floor. `app.css` classes are refactored to reference the tokens. The many hardcoded literals in the Blade views are remediated by controlled, scripted global replacements (faint colors → `var(--text-*)`, small font sizes bumped per a fixed map). Same design, just legible.

**Tech stack:** Laravel 13.8 Blade + a single hand-written `resources/css/app.css` (compiled via Vite), inline `style="…"` attributes throughout the views, and a separate Filament v3 admin theme. PHP binary `C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe`.

---

## Why (the audit, June 2026)

`app.css` declares only two custom properties (font families) — **every color and font-size is hardcoded** in `app.css` *and* across ~54 Blade view files. The result is a low-contrast, too-small scale repeated literally everywhere:

- **Faint text on the dark `#0F172A` background (fails WCAG AA 4.5:1):**
  - `#475569` (~2.3:1, near-invisible) — ~176 uses (145 views + 31 css)
  - `#64748b` (~3.5:1) — ~376 uses (318 views + 58 css)
  - `#94a3b8` (~4.7:1, borderline) — ~298 uses
- **Tiny fonts:** 477 inline occurrences ≤12px across 54 files (175×11px, 160×10px, 124×12px), plus 9px/9.5px cases and small `app.css` classes. Body base is 14px.
- **Worst offenders combine both** (e.g. `font-size:10px;color:#475569`): nav tagline (9px `#475569`), blog meta (10px `#475569`), card subtitles (10px `#64748b`).

This is systemic, not a few bugs.

## Program decomposition (3 sub-projects)

This spec covers **SP1 only**. SP2 and SP3 get their own spec → plan → build cycles, reusing SP1's tokens.

- **SP1 — Token foundation + public marketing site.** Define the tokens in `app.css`; refactor `app.css`; remediate the public pages (`resources/views/pages/*`) and shared public chrome (`resources/views/components/{navbar,footer,language-switcher}.blade.php`, `components/layouts/app.blade.php`). **(this spec)**
- **SP2 — Portal layouts.** Remediate the 7 portal layouts (`components/layouts/{customer,practitioner,tester,support,hr,manager,accountant}.blade.php`) + their views (`resources/views/{customer,practitioner,tester,support,hr,manager,accountant}/*`). Tokens already defined by SP1; this is remediation only.
- **SP3 — Filament admin theme.** Filament loads its own CSS (not `app.css`), so tokens are re-declared in a Filament custom theme / CSS override; audit + lift only where Filament's defaults fall short.

---

## SP1 design

### 1. The token system (`:root` in `resources/css/app.css`)

All text tokens verified ≥4.5:1 on `--bg` (`#0F172A`).

```css
:root {
    /* fonts (existing) */
    --font-sans: 'Inter', ui-sans-serif, system-ui, sans-serif;
    --font-display: 'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif;

    /* surfaces */
    --bg: #0F172A;          /* page background */
    --surface: #131d31;     /* cards / raised panels */
    --surface-2: #1a2438;   /* nested / hover */
    --border: #243149;      /* hairline borders (was #1e293b) */

    /* text — all AA-compliant on --bg */
    --text: #e8edf5;        /* primary (~14:1) */
    --text-2: #c2cde0;      /* secondary (~10:1) */
    --text-muted: #9fb0c9;  /* muted (~6:1) — REPLACES #64748b and #94a3b8 */
    --text-faint: #8696b4;  /* faintest (~5:1) — REPLACES #475569 */

    /* brand */
    --green: #00C896;
    --blue: #2f7df0;        /* slightly brighter than #1A6FE8 for contrast on dark */
    --purple: #A855F7;

    /* type scale — readable floor (nothing below 12px) */
    --fs-2xs: 12px;   /* was 9–10px micro-labels */
    --fs-xs: 13px;    /* was 11px */
    --fs-sm: 14px;    /* was 12px */
    --fs-base: 15px;  /* body (was 14px) */
    --fs-md: 17px;
    --fs-lg: 20px;
    /* display sizes (h2/h3/hero) keep their current px values — they are not the problem */
}
```

Notes:
- `--blue` shifts `#1A6FE8 → #2f7df0` (brighter) only where it is used as **text/icon on dark**; large brand fills can keep `#1A6FE8`. Implementation keeps brand fills as-is and only swaps blue used for small text/icons if any fail contrast. Default: leave `#1A6FE8` fills untouched; introduce `--blue` for text use.
- `#94a3b8` (borderline) is folded into `--text-muted` (`#9fb0c9`) for a comfortable margin.

### 2. `app.css` refactor

Replace hardcoded values in `app.css` rules with the tokens:
- `color:#475569` → `var(--text-faint)`; `color:#64748b` → `var(--text-muted)`; `color:#94a3b8` → `var(--text-muted)`.
- `color:#e2e8f0` → `var(--text)`; `color:#cbd5e1` → `var(--text-2)`.
- `border…#1e293b` → `var(--border)`.
- Font sizes per the size map below.
- Brand `#00C896`/`#A855F7` → `var(--green)`/`var(--purple)`; `#1A6FE8` text → `var(--blue)`.

The named classes most affected (from the audit): `.nav-logo-tagline` (9px `#475569`), `.eco-desc`/`.p-cat` (10px `#64748b`), `.blog-meta` (10px `#475569`), `.trust-item .label`, `.triage-stat-label`, `.footer-bottom p`/`.footer-lang a` (`#475569`), `.section-label`, `.stat-label`, `.eco-spoke-name`, etc.

### 3. View remediation (scripted global replacements, scoped to SP1 dirs)

Scope: `resources/views/pages/**`, `resources/views/components/{navbar,footer,language-switcher}.blade.php`, `resources/views/components/layouts/app.blade.php`. **Do not touch** portal layouts/views (SP2) or anything Filament (SP3).

**Color map (in `style="…"` attributes):**
| From | To |
|---|---|
| `#475569` | `var(--text-faint)` |
| `#64748b` | `var(--text-muted)` |
| `#94a3b8` | `var(--text-muted)` |

(Leave `#e2e8f0`/`#cbd5e1` as-is or tokenize opportunistically; they already pass. Leave brand fills `#00C896`/`#1A6FE8`/`#A855F7` and surface/border hexes alone unless they are faint text.)

**Font-size map (in `style="…"` attributes):**
| From | To |
|---|---|
| `font-size:9px`, `9.5px`, `10px` | `font-size:12px` |
| `font-size:11px` | `font-size:13px` |
| `font-size:12px`, `12.5px` | `font-size:14px` |
| `font-size:13px`, `13.5px` | `font-size:14px` |
| ≥14px | unchanged |

Implementation does these as precise, reviewable replacements (one literal at a time, `replace_all` within the scoped files, or a small sed pass limited to SP1 paths), **not** a blind repo-wide sed. Hero/heading sizes (50/38/36/30/26/24/22/20/18px) are untouched.

### 4. Verification

- **Visual, page-by-page** via the preview server (`preview_start "ohs"`): home, products, products-index, markets hub + a country page, blog index + a post, solutions, pricing, about, contact, navbar dropdowns, footer. Confirm nothing is clipped/overflowing from the size bumps and all text is legible. Screenshot key pages.
- **Automated:** existing suite stays green (esp. `MarketPageTest`, `ProductPageTest`, `BlogValidationTest`, home render). Add a guard test asserting `app.css` contains the `:root` tokens and no `#475569`/`#64748b` remains as a text color in the SP1-scoped views (a grep-style assertion).
- **Contrast spot-check:** confirm `--text-muted`/`--text-faint` compute ≥4.5:1 on `--bg`.

### 5. Risks & mitigations

- **Layout shift from size bumps** (12→14 etc. can widen/wrap elements). → Verify visually per page; the bumps are small (1–4px) and the design uses flexible fl/grid layouts. Roll back any element that breaks by pinning it explicitly.
- **Over-replacement** (a hex used as a non-text color, e.g. a border or icon, getting swapped). → The color map targets `color:` usages; surface/border hexes (`#1e293b`, `#243149`) are separate literals and are not in the text-color map. Review the diff.
- **Brand drift.** → Brand fills and accents are left as-is; only faint *text* colors and small *font sizes* change.
- **Filament & portals out of scope here** — explicitly deferred to SP2/SP3 so SP1 stays shippable.

### 6. Definition of done (SP1)

- `:root` token block added to `app.css`; `app.css` rules reference tokens; no faint `#475569`/`#64748b`/`#94a3b8` text colors and no sub-12px font sizes remain in `app.css` or the SP1-scoped views.
- Public pages render correctly (visually verified, screenshots captured); existing tests green; new token/grep guard test green.
- Committed; both branches synced. SP2/SP3 tracked as follow-ups.

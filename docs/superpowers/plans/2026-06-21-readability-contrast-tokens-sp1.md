# Readability & Contrast Tokens — SP1 Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Introduce a `:root` design-token system (AA-compliant colors + a readable type floor) and remediate the public marketing site + shared chrome so no text is faint-on-dark or under 12px — same look, readable without strain.

**Architecture:** Add tokens to `resources/css/app.css`; refactor `app.css` rules to reference them; then scripted, scoped replacements swap faint color literals → `var(--text-*)` and bump small `font-size` literals → `var(--fs-*)` across the public views only. A PHPUnit guard test enforces that no banned literals remain.

**Tech Stack:** Laravel 13.8 Blade, hand-written `resources/css/app.css` (Vite), inline `style="…"` attributes. Spec: `docs/superpowers/specs/2026-06-21-readability-contrast-tokens-design.md`. PHP: `C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe`. Tests: `php artisan test --filter=<Class>`.

**Finalized maps (authoritative — refine the spec's draft for lower layout risk; same DoD "nothing under 12px, no faint text"):**

Colors (in `app.css` + scoped views): `#475569 → var(--text-faint)`, `#64748b → var(--text-muted)`, `#94a3b8 → var(--text-muted)`.

Font sizes: `9 / 9.5 / 10px → var(--fs-2xs)` (12px) · `11 / 12 / 12.5px → var(--fs-xs)` (13px) · `13px+` unchanged.

**SP1 file scope** (a shell variable reused below):
```bash
SP1=$(find resources/views/pages -name '*.blade.php'); SP1="$SP1 resources/views/components/navbar.blade.php resources/views/components/footer.blade.php resources/views/components/language-switcher.blade.php resources/views/components/layouts/app.blade.php"
```
Out of scope: portal layouts/views (SP2), Filament (SP3).

---

### Task 1: Define the `:root` tokens + guard test

**Files:**
- Modify: `resources/css/app.css` (add `:root` block near the top, replacing the existing 2-property `:root`)
- Create: `tests/Feature/ReadabilityTokensTest.php`

- [ ] **Step 1: Write the failing test**

```php
<?php

namespace Tests\Feature;

use Tests\TestCase;

class ReadabilityTokensTest extends TestCase
{
    private function appCss(): string
    {
        return file_get_contents(base_path('resources/css/app.css'));
    }

    /** SP1-scoped Blade files (public pages + shared public chrome). */
    private function sp1Files(): array
    {
        $files = array_merge(
            glob(base_path('resources/views/pages/*.blade.php')) ?: [],
            glob(base_path('resources/views/pages/markets/*.blade.php')) ?: [],
            [
                base_path('resources/views/components/navbar.blade.php'),
                base_path('resources/views/components/footer.blade.php'),
                base_path('resources/views/components/language-switcher.blade.php'),
                base_path('resources/views/components/layouts/app.blade.php'),
            ],
        );
        return array_values(array_filter($files, 'is_file'));
    }

    public function test_root_tokens_are_defined(): void
    {
        $css = $this->appCss();
        foreach ([
            '--text: #e8edf5', '--text-2: #c2cde0', '--text-muted: #9fb0c9', '--text-faint: #8696b4',
            '--bg: #0F172A', '--border: #243149',
            '--green: #00C896', '--blue: #2f7df0',
            '--fs-2xs: 12px', '--fs-xs: 13px', '--fs-sm: 14px',
        ] as $token) {
            $this->assertStringContainsString($token, $css, "Missing token: {$token}");
        }
    }
}
```

- [ ] **Step 2: Run it to verify it fails**

Run: `C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan test --filter=ReadabilityTokensTest`
Expected: FAIL on `test_root_tokens_are_defined` (tokens not yet present).

- [ ] **Step 3: Add the `:root` block to `app.css`**

Replace the existing `:root { --font-sans…; --font-display…; }` with:

```css
:root {
    --font-sans: 'Inter', ui-sans-serif, system-ui, sans-serif;
    --font-display: 'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif;

    /* surfaces */
    --bg: #0F172A;
    --surface: #131d31;
    --surface-2: #1a2438;
    --border: #243149;

    /* text — all ≥4.5:1 on --bg */
    --text: #e8edf5;
    --text-2: #c2cde0;
    --text-muted: #9fb0c9;   /* replaces #64748b and #94a3b8 */
    --text-faint: #8696b4;   /* replaces #475569 */

    /* brand */
    --green: #00C896;
    --blue: #2f7df0;
    --purple: #A855F7;

    /* type scale — readable floor */
    --fs-2xs: 12px;
    --fs-xs: 13px;
    --fs-sm: 14px;
}
```

- [ ] **Step 4: Run it to verify it passes**

Run: `…php.exe artisan test --filter=ReadabilityTokensTest`
Expected: PASS.

- [ ] **Step 5: Commit**

```bash
git add resources/css/app.css tests/Feature/ReadabilityTokensTest.php
git commit -m "feat(css): add readability design tokens (:root) + guard test"
```

---

### Task 2: Refactor `app.css` to tokens (colors + small sizes)

**Files:**
- Modify: `resources/css/app.css`
- Modify: `tests/Feature/ReadabilityTokensTest.php` (add app.css cleanliness assertions)

- [ ] **Step 1: Add failing assertions**

Append to `ReadabilityTokensTest`:

```php
    public function test_app_css_has_no_faint_text_hexes(): void
    {
        $css = $this->appCss();
        foreach (['#475569', '#64748b', '#94a3b8'] as $hex) {
            $this->assertStringNotContainsString($hex, $css, "Faint hex still in app.css: {$hex}");
        }
    }

    public function test_app_css_has_no_sub_13_font_sizes(): void
    {
        $css = $this->appCss();
        foreach (['font-size: 9px', 'font-size: 9.5px', 'font-size: 10px', 'font-size: 11px', 'font-size: 12px', 'font-size: 12.5px'] as $bad) {
            $this->assertStringNotContainsString($bad, $css, "Tiny size still in app.css: {$bad}");
        }
    }
```

- [ ] **Step 2: Run to verify fail**

Run: `…php.exe artisan test --filter=ReadabilityTokensTest`
Expected: FAIL on the two new tests (literals still present).

- [ ] **Step 3: Apply scoped replacements to `app.css`**

```bash
cd /c/laragon/www/ohs
# colours → tokens (the :root token *values* #8696b4/#9fb0c9 are NOT among these, so they survive)
sed -i 's/#475569/var(--text-faint)/g; s/#64748b/var(--text-muted)/g; s/#94a3b8/var(--text-muted)/g' resources/css/app.css
# small sizes → tokens (note the SPACE after the colon in app.css; decimals before integers)
sed -i -E 's/font-size: 9\.5px/font-size: var(--fs-2xs)/g; s/font-size: 10px/font-size: var(--fs-2xs)/g; s/font-size: 9px/font-size: var(--fs-2xs)/g; s/font-size: 12\.5px/font-size: var(--fs-xs)/g; s/font-size: 11px/font-size: var(--fs-xs)/g; s/font-size: 12px/font-size: var(--fs-xs)/g' resources/css/app.css
```

Then manually verify the `:root` token definitions (`--text-faint: #8696b4`, `--fs-2xs: 12px` …) were not altered (they use different property names, so they are safe) by re-reading the `:root` block.

- [ ] **Step 4: Run to verify pass**

Run: `…php.exe artisan test --filter=ReadabilityTokensTest`
Expected: PASS (all 3 tests).

- [ ] **Step 5: Commit**

```bash
git add resources/css/app.css tests/Feature/ReadabilityTokensTest.php
git commit -m "refactor(css): app.css colors + small sizes → readability tokens"
```

---

### Task 3: Remediate public views — colors

**Files:**
- Modify: all SP1-scoped Blade files (see `$SP1`)
- Modify: `tests/Feature/ReadabilityTokensTest.php` (add views color assertion)

- [ ] **Step 1: Add failing assertion**

```php
    public function test_sp1_views_have_no_faint_text_hexes(): void
    {
        foreach ($this->sp1Files() as $file) {
            $c = file_get_contents($file);
            foreach (['#475569', '#64748b', '#94a3b8'] as $hex) {
                $this->assertStringNotContainsString($hex, $c, basename($file)." still uses {$hex}");
            }
        }
    }
```

- [ ] **Step 2: Run to verify fail** — `…--filter=ReadabilityTokensTest` → FAIL on the new test.

- [ ] **Step 3: Apply scoped color replacement**

```bash
cd /c/laragon/www/ohs
SP1=$(find resources/views/pages -name '*.blade.php'); SP1="$SP1 resources/views/components/navbar.blade.php resources/views/components/footer.blade.php resources/views/components/language-switcher.blade.php resources/views/components/layouts/app.blade.php"
sed -i 's/#475569/var(--text-faint)/g; s/#64748b/var(--text-muted)/g; s/#94a3b8/var(--text-muted)/g' $SP1
```

- [ ] **Step 4: Run to verify pass** — `…--filter=ReadabilityTokensTest` → PASS.

- [ ] **Step 5: Commit**

```bash
git add resources/views tests/Feature/ReadabilityTokensTest.php
git commit -m "refactor(views): public pages faint text colors → tokens"
```

---

### Task 4: Remediate public views — font sizes

**Files:**
- Modify: all SP1-scoped Blade files
- Modify: `tests/Feature/ReadabilityTokensTest.php` (add views size assertion)

- [ ] **Step 1: Add failing assertion**

```php
    public function test_sp1_views_have_no_sub_13_font_sizes(): void
    {
        foreach ($this->sp1Files() as $file) {
            $c = file_get_contents($file);
            foreach (['font-size:9px', 'font-size:9.5px', 'font-size:10px', 'font-size:11px', 'font-size:12px', 'font-size:12.5px'] as $bad) {
                $this->assertStringNotContainsString($bad, $c, basename($file)." still uses {$bad}");
            }
        }
    }
```

- [ ] **Step 2: Run to verify fail** — FAIL on the new test.

- [ ] **Step 3: Apply scoped size replacement** (no space after colon in inline styles; decimals before integers to avoid partial matches)

```bash
cd /c/laragon/www/ohs
SP1=$(find resources/views/pages -name '*.blade.php'); SP1="$SP1 resources/views/components/navbar.blade.php resources/views/components/footer.blade.php resources/views/components/language-switcher.blade.php resources/views/components/layouts/app.blade.php"
sed -i -E 's/font-size:9\.5px/font-size:var(--fs-2xs)/g; s/font-size:10px/font-size:var(--fs-2xs)/g; s/font-size:9px/font-size:var(--fs-2xs)/g; s/font-size:12\.5px/font-size:var(--fs-xs)/g; s/font-size:11px/font-size:var(--fs-xs)/g; s/font-size:12px/font-size:var(--fs-xs)/g' $SP1
```

- [ ] **Step 4: Run to verify pass** — `…--filter=ReadabilityTokensTest` → PASS (all 5 tests).

- [ ] **Step 5: Commit**

```bash
git add resources/views tests/Feature/ReadabilityTokensTest.php
git commit -m "refactor(views): public pages small font sizes → tokens"
```

---

### Task 5: Verify (visual + full suite) and finalize

**Files:** none (verification + push)

- [ ] **Step 1: Full test suite green**

Run: `C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe artisan test`
Expected: 0 failures (esp. `ReadabilityTokensTest`, `MarketPageTest`, `ProductPageTest`, `BlogValidationTest`, home render). Fix any regression before proceeding.

- [ ] **Step 2: Visual check via preview**

`preview_start "ohs"`, then load and screenshot: `/en` (home), `/en/products`, `/en/markets`, `/en/markets/gabon`, `/en/blog`, a blog post, `/en/solutions`, `/en/pricing`, `/en/about`, `/en/contact`; open the navbar dropdowns; check the footer. Confirm: (a) all text legible (no faint gray, nothing < 12px), (b) no clipped/overflowing elements from the size bumps. If an element breaks, pin its size explicitly and re-verify.

- [ ] **Step 3: Build assets (so prod build picks up the CSS) — optional dev check**

Run: `npm run build` (only if the environment builds assets; otherwise Vite dev/`public/build` already serves). Confirm no build error.

- [ ] **Step 4: Commit any visual fixes + push both branches**

```bash
git add -- resources/   # only if Step 2 required fixes; stage explicit paths
git commit -m "fix(css): pin sizes where the readability bump shifted layout" || true
git push origin main
git branch -f master main && git push origin master:master
```

- [ ] **Step 5: Record follow-ups**

Note SP2 (portals) and SP3 (Filament admin) as the next sub-projects, reusing these tokens.

---

## Self-Review

**Spec coverage:** tokens (Task 1 ✓), app.css refactor (Task 2 ✓), view color remediation (Task 3 ✓), view size remediation (Task 4 ✓), verification incl. tests + visual + contrast-by-construction (Task 5 ✓), SP2/SP3 deferral (noted ✓). The finalized size map (11/12→13 rather than the spec draft's →14) is a documented, lower-risk refinement meeting the same DoD.

**Placeholder scan:** none — every step has the exact test code, `sed` command, or run command.

**Consistency:** token names (`--text-muted`, `--text-faint`, `--fs-2xs`, `--fs-xs`) and the SP1 file glob are identical across Tasks 1–4 and the test. Color/size maps identical in `app.css` (Task 2) and views (Tasks 3–4) modulo the space-after-colon difference (app.css has the space; inline styles don't) — handled explicitly.

# Foundation & Bilingual Scaffolding Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Stand up the OPES website foundation — brand design tokens, fonts, and a fully bilingual (English/French) `/en/` `/fr/` URL structure with a styled layout (navbar + footer + language switcher) and a homepage hero that renders in both languages.

**Architecture:** Server-side rendered Blade with a `{locale}` route prefix group guarded by a `SetLocale` middleware. Design tokens and fonts are defined in Tailwind v4's CSS-first `@theme` block. All visible strings come from `lang/en` and `lang/fr` translation files via `__()`. A language switcher swaps the first URL path segment, preserving the current page.

**Tech Stack:** Laravel 13, PHP 8.3, Blade + Alpine.js, **Tailwind CSS v4** (`@tailwindcss/vite`, CSS-first `@theme`), Vite 8, MySQL 8 (runtime), in-memory SQLite (tests), PHPUnit.

---

## Reconciliations vs. the design spec

The spec (`docs/superpowers/specs/2026-06-13-opes-website-design.md`) was written before scaffolding. Two details changed against reality and this plan follows reality:

1. **Tailwind v4, not v3.** The Laravel starter installed Tailwind v4, which uses a CSS-first `@theme` block in `resources/css/app.css` instead of a `tailwind.config.js`. Design tokens are defined there. (Spec section 3 should be read as "Tailwind v4".)
2. **Fonts via the Vite bunny() helper**, not a Google Fonts `<link>`. Same families (Plus Jakarta Sans + Inter); they are self-hosted by `laravel-vite-plugin/fonts` for performance.

## Roadmap (this plan is #1 of 5)

1. **Foundation & Bilingual Scaffolding** ← this plan
2. Products subsystem — `Product` model/migration/seeder (22 products), filterable products index, product detail template
3. Admin panel — Filament v3 install + `ProductResource`, `LeadResource`, etc.
4. Content & leads — blog, demo-request form + `Lead`, partnerships, testimonials, ministry page
5. SEO finalization — sitemap.xml, JSON-LD schemas, hreflang/canonical polish across all page types

---

## Environment notes (this machine)

PHP and npm are **not on the global PATH**. Prefix shell sessions:

- PHP / artisan: PowerShell — `$env:PATH = "C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64;$env:PATH"` then `php artisan ...`
- npm: `C:\laragon\bin\nodejs\node-v22\npm.cmd`
- Working directory for all commands: `C:\laragon\www\ohs`

`php artisan test` runs on in-memory SQLite (per `phpunit.xml`), so tests never touch the `ohs` MySQL database.

---

## File Structure

| File | Responsibility |
|---|---|
| `config/locale.php` | Single source of truth for default + supported locales |
| `app/Http/Middleware/SetLocale.php` | Validate `{locale}`, set app locale + URL default |
| `bootstrap/app.php` | Register the `setlocale` middleware alias (modify) |
| `routes/web.php` | Root redirect + `{locale}` prefixed route group (replace) |
| `app/Http/Controllers/HomeController.php` | Serve the homepage |
| `resources/css/app.css` | Tailwind `@theme` design tokens (modify) |
| `vite.config.js` | Swap fonts to Plus Jakarta Sans + Inter (modify) |
| `resources/views/layouts/app.blade.php` | Master layout: head, fonts, navbar, footer, Lucide |
| `resources/views/partials/seo-meta.blade.php` | `<title>`, description, canonical, hreflang |
| `resources/views/components/navbar.blade.php` | Top navigation + language switcher |
| `resources/views/components/footer.blade.php` | Footer |
| `resources/views/components/language-switcher.blade.php` | EN/FR toggle (swaps path segment) |
| `resources/views/pages/home.blade.php` | Homepage hero shell |
| `lang/{en,fr}/nav.php` | Navigation strings |
| `lang/{en,fr}/home.php` | Homepage strings |
| `lang/{en,fr}/common.php` | Shared strings (company name, tagline) |
| `tests/Feature/LocaleRoutingTest.php` | Locale routing + redirect + 404 |
| `tests/Feature/HomePageTest.php` | Homepage renders EN/FR + hreflang + nav |

---

### Task 0: Initialize git repository

**Files:**
- Use existing: `.gitignore` (Laravel ships one)

- [ ] **Step 1: Initialize the repo and make the baseline commit**

The directory is not yet a git repo. Laravel's `.gitignore` already excludes `/vendor`, `/node_modules`, `.env`, etc.

Run (PowerShell, from `C:\laragon\www\ohs`):
```powershell
git init
git add .
git commit -m "chore: initial Laravel scaffold + design spec"
```
Expected: a commit is created. `git status` shows a clean tree.

- [ ] **Step 2: Confirm `.env` is ignored**

Run: `git status --porcelain | Select-String ".env"`
Expected: **no output** (only `.env.example` is tracked; real `.env` is ignored).

---

### Task 1: Design tokens & fonts

**Files:**
- Modify: `resources/css/app.css`
- Modify: `vite.config.js`

- [ ] **Step 1: Replace `resources/css/app.css` with brand tokens**

```css
@import 'tailwindcss';

@source '../../vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php';
@source '../../storage/framework/views/*.php';
@source '../views';

@theme {
    /* Brand colors — generate bg-*, text-*, border-* utilities */
    --color-teal: #007A87;
    --color-teal-light: #009DAD;
    --color-teal-dark: #005F6B;
    --color-gold: #C8962E;
    --color-gold-light: #E8B84B;
    --color-navy: #0F2B4C;
    --color-navy-light: #1A3D6B;
    --color-ink: #1A2332;
    --color-muted: #6B7A8D;
    --color-soft: #F8FAFB;

    /* Typography */
    --font-sans: 'Inter', ui-sans-serif, system-ui, sans-serif;
    --font-display: 'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif;
}
```

- [ ] **Step 2: Swap the Vite fonts to Plus Jakarta Sans + Inter**

In `vite.config.js`, replace the single `bunny('Instrument Sans', ...)` entry inside the `fonts` array with both brand families:

```js
            fonts: [
                bunny('Plus Jakarta Sans', {
                    weights: [700, 800],
                }),
                bunny('Inter', {
                    weights: [400, 500, 600],
                }),
            ],
```

- [ ] **Step 3: Install JS deps and build assets**

Run (PowerShell, from `C:\laragon\www\ohs`):
```powershell
& "C:\laragon\bin\nodejs\node-v22\npm.cmd" install
& "C:\laragon\bin\nodejs\node-v22\npm.cmd" run build
```
Expected: `public/build/manifest.json` is created and the build reports the compiled CSS/JS bundles without errors.

- [ ] **Step 4: Commit**

```powershell
git add resources/css/app.css vite.config.js package-lock.json public/build
git commit -m "feat: brand design tokens and fonts (Tailwind v4 @theme)"
```

---

### Task 2: Locale config & SetLocale middleware

**Files:**
- Create: `config/locale.php`
- Create: `app/Http/Middleware/SetLocale.php`
- Modify: `bootstrap/app.php`
- Test: `tests/Feature/LocaleRoutingTest.php` (created here, expanded in Task 3)

- [ ] **Step 1: Create `config/locale.php`**

```php
<?php

return [
    'default' => 'en',
    'supported' => ['en', 'fr'],
];
```

- [ ] **Step 2: Write the failing test for the middleware behavior**

Create `tests/Feature/LocaleRoutingTest.php`:

```php
<?php

namespace Tests\Feature;

use Tests\TestCase;

class LocaleRoutingTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_unsupported_locale_returns_404(): void
    {
        $this->get('/es')->assertNotFound();
    }
}
```

- [ ] **Step 3: Run the test to verify it fails**

Run: `php artisan test --filter=test_unsupported_locale_returns_404`
Expected: FAIL — no route is defined for `/es` yet it currently 404s for the wrong reason (no routes). This still passes accidentally; to make the test meaningful, Task 3 adds the `{locale}` group so `/en` works while `/es` 404s. Proceed — this test is finalized in Task 3 Step 2.

- [ ] **Step 4: Create the `SetLocale` middleware**

Create `app/Http/Middleware/SetLocale.php`:

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->route('locale');

        if (! in_array($locale, config('locale.supported'), true)) {
            abort(404);
        }

        app()->setLocale($locale);
        URL::defaults(['locale' => $locale]);

        return $next($request);
    }
}
```

- [ ] **Step 5: Register the middleware alias in `bootstrap/app.php`**

Replace the empty `->withMiddleware(function (Middleware $middleware): void { // })` body with:

```php
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'setlocale' => \App\Http\Middleware\SetLocale::class,
        ]);
    })
```

- [ ] **Step 6: Commit**

```powershell
git add config/locale.php app/Http/Middleware/SetLocale.php bootstrap/app.php tests/Feature/LocaleRoutingTest.php
git commit -m "feat: locale config and SetLocale middleware"
```

---

### Task 3: Bilingual route structure & homepage controller

**Files:**
- Replace: `routes/web.php`
- Create: `app/Http/Controllers/HomeController.php`
- Create: `resources/views/pages/home.blade.php` (placeholder here, styled in Task 6)
- Modify: `tests/Feature/LocaleRoutingTest.php`

- [ ] **Step 1: Create a minimal `HomeController`**

Create `app/Http/Controllers/HomeController.php`:

```php
<?php

namespace App\Http\Controllers;

class HomeController extends Controller
{
    public function index()
    {
        return view('pages.home');
    }
}
```

- [ ] **Step 2: Create a placeholder homepage view**

Create `resources/views/pages/home.blade.php` (replaced with the real hero in Task 6):

```blade
<x-layouts.app>
    <h1>{{ __('home.hero_title') }}</h1>
</x-layouts.app>
```

> Note: `<x-layouts.app>` and the `home.*` translations are created in Tasks 4–5. Implement Tasks 4 and 5 before running the full homepage tests. The routing tests below do not depend on translation content.

- [ ] **Step 3: Replace `routes/web.php`**

```php
<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

// Send bare root to the default locale.
Route::get('/', fn () => redirect('/'.config('locale.default')));

Route::prefix('{locale}')
    ->where(['locale' => implode('|', config('locale.supported'))])
    ->middleware('setlocale')
    ->group(function () {
        Route::get('/', [HomeController::class, 'index'])->name('home');
    });
```

- [ ] **Step 4: Expand the routing test**

Replace the body of `tests/Feature/LocaleRoutingTest.php` with:

```php
<?php

namespace Tests\Feature;

use Tests\TestCase;

class LocaleRoutingTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_root_redirects_to_default_locale(): void
    {
        $this->get('/')->assertRedirect('/en');
    }

    public function test_english_homepage_loads(): void
    {
        $this->get('/en')->assertOk();
    }

    public function test_french_homepage_loads(): void
    {
        $this->get('/fr')->assertOk();
    }

    public function test_unsupported_locale_returns_404(): void
    {
        $this->get('/es')->assertNotFound();
    }

    public function test_locale_is_applied_to_the_app(): void
    {
        $this->get('/fr');
        $this->assertSame('fr', app()->getLocale());
    }
}
```

- [ ] **Step 5: Run the routing tests**

Run: `php artisan test --filter=LocaleRoutingTest`
Expected: PASS (5 tests). If `test_english_homepage_loads`/`test_french_homepage_loads` fail because the layout/translations aren't built yet, complete Tasks 4–5 then re-run — they will pass.

- [ ] **Step 6: Commit**

```powershell
git add routes/web.php app/Http/Controllers/HomeController.php resources/views/pages/home.blade.php tests/Feature/LocaleRoutingTest.php
git commit -m "feat: bilingual route group and homepage controller"
```

---

### Task 4: Translation files

**Files:**
- Create: `lang/en/nav.php`, `lang/fr/nav.php`
- Create: `lang/en/home.php`, `lang/fr/home.php`
- Create: `lang/en/common.php`, `lang/fr/common.php`

- [ ] **Step 1: Create `lang/en/nav.php`**

```php
<?php

return [
    'products' => 'Products',
    'solutions' => 'Solutions',
    'partnerships' => 'Partnerships',
    'blog' => 'Blog',
    'about' => 'About',
    'book_demo' => 'Book a Demo',
];
```

- [ ] **Step 2: Create `lang/fr/nav.php`**

```php
<?php

return [
    'products' => 'Produits',
    'solutions' => 'Solutions',
    'partnerships' => 'Partenariats',
    'blog' => 'Blog',
    'about' => 'À propos',
    'book_demo' => 'Demander une démo',
];
```

- [ ] **Step 3: Create `lang/en/home.php`**

```php
<?php

return [
    'hero_eyebrow' => 'Health technology, built for Africa',
    'hero_title' => 'Software that powers African healthcare',
    'hero_tagline' => '22 bilingual, interoperable systems for hospitals, clinics, and health systems across Cameroon, CEMAC, and Africa.',
    'cta_demo' => 'Book a Free Demo',
    'cta_explore' => 'Explore Products',
];
```

- [ ] **Step 4: Create `lang/fr/home.php`**

```php
<?php

return [
    'hero_eyebrow' => 'La technologie de santé, conçue pour l’Afrique',
    'hero_title' => 'Des logiciels au service de la santé africaine',
    'hero_tagline' => '22 systèmes bilingues et interopérables pour les hôpitaux, cliniques et systèmes de santé au Cameroun, en CEMAC et en Afrique.',
    'cta_demo' => 'Demander une démo gratuite',
    'cta_explore' => 'Découvrir les produits',
];
```

- [ ] **Step 5: Create `lang/en/common.php`**

```php
<?php

return [
    'company' => 'OPES Health Systems',
    'tagline_short' => 'Bilingual. Built for Africa. Fully interoperable.',
    'all_rights' => 'All rights reserved.',
];
```

- [ ] **Step 6: Create `lang/fr/common.php`**

```php
<?php

return [
    'company' => 'OPES Health Systems',
    'tagline_short' => 'Bilingue. Conçu pour l’Afrique. Totalement interopérable.',
    'all_rights' => 'Tous droits réservés.',
];
```

- [ ] **Step 7: Commit**

```powershell
git add lang
git commit -m "feat: EN/FR translation files for nav, home, common"
```

---

### Task 5: Layout, navbar, footer, language switcher

**Files:**
- Create: `resources/views/layouts/app.blade.php`
- Create: `resources/views/partials/seo-meta.blade.php`
- Create: `resources/views/components/navbar.blade.php`
- Create: `resources/views/components/footer.blade.php`
- Create: `resources/views/components/language-switcher.blade.php`

- [ ] **Step 1: Create the SEO meta partial**

Create `resources/views/partials/seo-meta.blade.php`. It builds EN/FR alternates by swapping the first path segment of the current request.

```blade
@php
    $segments = request()->segments();
    if (empty($segments)) { $segments = [app()->getLocale()]; }
    $enSegments = $segments; $enSegments[0] = 'en';
    $frSegments = $segments; $frSegments[0] = 'fr';
    $enUrl = url(implode('/', $enSegments));
    $frUrl = url(implode('/', $frSegments));
    $canonical = url(implode('/', $segments));
    $pageTitle = ($title ?? __('home.hero_title')).' — '.__('common.company');
    $pageDescription = $description ?? __('home.hero_tagline');
@endphp

<title>{{ $pageTitle }}</title>
<meta name="description" content="{{ $pageDescription }}">
<link rel="canonical" href="{{ $canonical }}">
<link rel="alternate" hreflang="en" href="{{ $enUrl }}">
<link rel="alternate" hreflang="fr" href="{{ $frUrl }}">
<link rel="alternate" hreflang="x-default" href="{{ $enUrl }}">

<meta property="og:type" content="website">
<meta property="og:title" content="{{ $pageTitle }}">
<meta property="og:description" content="{{ $pageDescription }}">
<meta property="og:url" content="{{ $canonical }}">
```

- [ ] **Step 2: Create the language switcher component**

Create `resources/views/components/language-switcher.blade.php`:

```blade
@php
    $segments = request()->segments();
    if (empty($segments)) { $segments = [app()->getLocale()]; }
    $toEn = $segments; $toEn[0] = 'en';
    $toFr = $segments; $toFr[0] = 'fr';
    $current = app()->getLocale();
@endphp

<div class="flex items-center gap-1 text-sm font-semibold">
    <a href="{{ url(implode('/', $toEn)) }}"
       class="px-2 py-1 rounded {{ $current === 'en' ? 'bg-teal text-white' : 'text-muted hover:text-teal' }}">EN</a>
    <span class="text-muted">/</span>
    <a href="{{ url(implode('/', $toFr)) }}"
       class="px-2 py-1 rounded {{ $current === 'fr' ? 'bg-teal text-white' : 'text-muted hover:text-teal' }}">FR</a>
</div>
```

- [ ] **Step 3: Create the navbar component**

Create `resources/views/components/navbar.blade.php`:

```blade
@php $locale = app()->getLocale(); @endphp
<nav class="sticky top-0 z-50 bg-white/95 backdrop-blur border-b border-gray-100">
    <div class="mx-auto max-w-7xl px-6 h-16 flex items-center justify-between">
        <a href="{{ url($locale) }}" class="font-display font-extrabold text-lg text-teal">
            {{ __('common.company') }}
        </a>

        <div class="hidden md:flex items-center gap-7 text-sm font-medium text-muted">
            <a href="{{ url($locale.'/products') }}" class="hover:text-teal">{{ __('nav.products') }}</a>
            <a href="{{ url($locale.'/solutions') }}" class="hover:text-teal">{{ __('nav.solutions') }}</a>
            <a href="{{ url($locale.'/partnerships') }}" class="hover:text-teal">{{ __('nav.partnerships') }}</a>
            <a href="{{ url($locale.'/blog') }}" class="hover:text-teal">{{ __('nav.blog') }}</a>
            <a href="{{ url($locale.'/about') }}" class="hover:text-teal">{{ __('nav.about') }}</a>
        </div>

        <div class="flex items-center gap-4">
            <x-language-switcher />
            <a href="{{ url($locale.'/contact') }}"
               class="hidden sm:inline-flex bg-teal text-white rounded-lg px-4 py-2 text-sm font-semibold hover:bg-teal-dark">
                {{ __('nav.book_demo') }}
            </a>
        </div>
    </div>
</nav>
```

- [ ] **Step 4: Create the footer component**

Create `resources/views/components/footer.blade.php`:

```blade
@php $locale = app()->getLocale(); @endphp
<footer class="bg-navy text-white/70 mt-24">
    <div class="mx-auto max-w-7xl px-6 py-14 grid gap-8 md:grid-cols-4">
        <div>
            <div class="font-display font-extrabold text-white text-lg">{{ __('common.company') }}</div>
            <p class="mt-2 text-sm">{{ __('common.tagline_short') }}</p>
            <div class="mt-4"><x-language-switcher /></div>
        </div>
        <div>
            <div class="text-white font-semibold text-sm mb-3">{{ __('nav.products') }}</div>
            <a href="{{ url($locale.'/products') }}" class="block text-sm hover:text-white py-1">{{ __('nav.products') }}</a>
        </div>
        <div>
            <div class="text-white font-semibold text-sm mb-3">{{ __('nav.about') }}</div>
            <a href="{{ url($locale.'/about') }}" class="block text-sm hover:text-white py-1">{{ __('nav.about') }}</a>
            <a href="{{ url($locale.'/blog') }}" class="block text-sm hover:text-white py-1">{{ __('nav.blog') }}</a>
            <a href="{{ url($locale.'/partnerships') }}" class="block text-sm hover:text-white py-1">{{ __('nav.partnerships') }}</a>
        </div>
        <div>
            <div class="text-white font-semibold text-sm mb-3">Douala, Cameroun</div>
            <a href="{{ url($locale.'/contact') }}"
               class="inline-flex bg-gold text-white rounded-lg px-4 py-2 text-sm font-semibold hover:bg-gold-light">
                {{ __('nav.book_demo') }}
            </a>
        </div>
    </div>
    <div class="border-t border-white/10">
        <div class="mx-auto max-w-7xl px-6 py-5 text-xs">
            © {{ date('Y') }} {{ __('common.company') }} SARL. {{ __('common.all_rights') }}
        </div>
    </div>
</footer>
```

- [ ] **Step 5: Create the master layout**

Create `resources/views/layouts/app.blade.php`:

```blade
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @include('partials.seo-meta')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans text-ink antialiased bg-white">
    <x-navbar />

    <main>
        {{ $slot }}
    </main>

    <x-footer />

    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
    <script>lucide.createIcons();</script>
</body>
</html>
```

- [ ] **Step 6: Run the routing tests again (layout now exists)**

Run: `php artisan test --filter=LocaleRoutingTest`
Expected: PASS (5 tests). `/en` and `/fr` now render the full layout.

- [ ] **Step 7: Commit**

```powershell
git add resources/views/layouts resources/views/partials resources/views/components
git commit -m "feat: master layout, navbar, footer, language switcher, SEO meta"
```

---

### Task 6: Homepage hero (styled, bilingual)

**Files:**
- Replace: `resources/views/pages/home.blade.php`
- Test: `tests/Feature/HomePageTest.php`

- [ ] **Step 1: Write the failing homepage test**

Create `tests/Feature/HomePageTest.php`:

```php
<?php

namespace Tests\Feature;

use Tests\TestCase;

class HomePageTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_english_hero_renders(): void
    {
        $this->get('/en')
            ->assertOk()
            ->assertSee('Software that powers African healthcare')
            ->assertSee('Products'); // nav in English
    }

    public function test_french_hero_renders(): void
    {
        $this->get('/fr')
            ->assertOk()
            ->assertSee('Des logiciels au service de la santé africaine', false)
            ->assertSee('Produits'); // nav in French
    }

    public function test_homepage_has_hreflang_alternates(): void
    {
        $this->get('/en')
            ->assertSee('hreflang="en"', false)
            ->assertSee('hreflang="fr"', false)
            ->assertSee(url('/fr'), false);
    }
}
```

- [ ] **Step 2: Run the test to verify it fails**

Run: `php artisan test --filter=HomePageTest`
Expected: FAIL — the placeholder home view has no hero markup / French copy / hreflang assertions all-present yet (hreflang passes, hero copy fails on the styled assertions).

- [ ] **Step 3: Replace the homepage view with the styled hero**

Replace `resources/views/pages/home.blade.php`:

```blade
@php $locale = app()->getLocale(); @endphp
<x-layouts.app>
    <section class="relative overflow-hidden bg-gradient-to-br from-navy via-navy-light to-teal-dark">
        <div class="absolute -top-24 -right-24 w-[480px] h-[480px] rounded-full bg-teal-light/10 blur-2xl"></div>

        <div class="relative mx-auto max-w-7xl px-6 py-24 md:py-32">
            <span class="inline-flex items-center gap-2 rounded-full border border-teal-light/40 bg-teal-light/10 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-teal-light">
                {{ __('home.hero_eyebrow') }}
            </span>

            <h1 class="mt-5 font-display text-4xl md:text-6xl font-extrabold leading-tight text-white max-w-3xl">
                {{ __('home.hero_title') }}
            </h1>

            <p class="mt-5 max-w-xl text-lg text-white/70">
                {{ __('home.hero_tagline') }}
            </p>

            <div class="mt-9 flex flex-wrap gap-3">
                <a href="{{ url($locale.'/contact') }}"
                   class="inline-flex items-center gap-2 rounded-xl bg-gold px-6 py-3 font-display font-bold text-white hover:bg-gold-light">
                    {{ __('home.cta_demo') }}
                </a>
                <a href="{{ url($locale.'/products') }}"
                   class="inline-flex items-center gap-2 rounded-xl border border-white/30 px-6 py-3 font-display font-semibold text-white hover:bg-white/10">
                    {{ __('home.cta_explore') }}
                </a>
            </div>
        </div>
    </section>
</x-layouts.app>
```

- [ ] **Step 4: Run the homepage tests to verify they pass**

Run: `php artisan test --filter=HomePageTest`
Expected: PASS (3 tests).

- [ ] **Step 5: Rebuild assets so the hero styles compile**

Run:
```powershell
& "C:\laragon\bin\nodejs\node-v22\npm.cmd" run build
```
Expected: build succeeds; `public/build/manifest.json` updated.

- [ ] **Step 6: Commit**

```powershell
git add resources/views/pages/home.blade.php tests/Feature/HomePageTest.php public/build
git commit -m "feat: styled bilingual homepage hero"
```

---

### Task 7: Full suite green + manual smoke check

**Files:** none (verification only)

- [ ] **Step 1: Run the entire test suite**

Run: `php artisan test`
Expected: PASS — all Feature tests (LocaleRouting + HomePage) plus the two scaffold ExampleTests. No failures.

- [ ] **Step 2: Manual smoke check in the browser**

Visit each URL and confirm:
- `http://ohs.test/` → redirects to `http://ohs.test/en`
- `http://ohs.test/en` → English hero, navbar reads "Products / Solutions …"
- `http://ohs.test/fr` → French hero, navbar reads "Produits / Solutions …"
- Click the **FR** switcher on `/en` → lands on `/fr` (same page), and vice versa
- `http://ohs.test/es` → 404

If `ohs.test` shows stale assets, run the build (Task 6 Step 5) and hard-refresh (Ctrl+Shift+R).

- [ ] **Step 3: Final commit (if any uncommitted changes remain)**

```powershell
git add -A
git commit -m "chore: foundation scaffolding complete"
```

---

## Self-Review

- **Spec coverage (foundation slice):** bilingual `/en/` `/fr/` routing ✅ (Tasks 2–3), hreflang ✅ (Task 5 partial), design tokens + fonts ✅ (Task 1), navbar/footer/layout ✅ (Task 5), homepage hero ✅ (Task 6). Product grid, blog, admin, leads, full SEO/JSON-LD are intentionally deferred to plans 2–5.
- **Placeholder scan:** every code step contains complete, runnable content. The only forward references (`<x-layouts.app>`, `home.*` strings) are explicitly called out in Task 3 Step 2 with ordering guidance.
- **Type/name consistency:** middleware alias `setlocale` matches route group usage; config keys `locale.default` / `locale.supported` match middleware + routes; component tags `<x-navbar>`, `<x-footer>`, `<x-language-switcher>`, `<x-layouts.app>` match their file paths; translation namespaces `nav.*`, `home.*`, `common.*` match file names.

# OPES Health Systems — Company Website Design Spec
**Date:** 2026-06-13  
**Project:** opeshealthsystems.com  
**Stack:** Laravel 13 · Filament v3 · Blade + Alpine.js · Tailwind CSS v4 · MySQL  
**Domain:** opeshealthsystems.com  
**Company:** Opes Health Systems SARL, Bonamousadi, Douala, Cameroon

---

## 1. Project Overview

### Purpose
Build the primary company website for Opes Health Systems SARL — a Cameroonian health technology company offering 22 interoperable software products for hospitals, clinics, and health systems across Cameroon, the CEMAC region, and Africa.

### Goals (in priority order)
1. Showcase and sell 22 software products
2. Get found first on Google / AI answer engines via SEO, AEO, GEO
3. Attract hospital, NGO, pharma, insurance, and investor partnerships
4. Publish content about every product and medical software topics
5. Capture demo leads (primary conversion action)
6. Align publicly with the Cameroon Ministry of Health Digital Health Strategy 2026–2030

### Success Criteria
- Every product has a standalone detail page indexed by Google
- Homepage loads under 2 seconds (LCP)
- Contact/demo form captures name, facility type, country, email
- All pages serve correctly in English (/en/) and French (/fr/)
- JSON-LD structured data passes Google Rich Results Test
- Sitemap.xml auto-generated and submitted to Search Console

---

## 2. Brand & Design System

### Brand Direction
**Premium Hybrid** — authoritative like enterprise software, warm like African healthcare. Not clinical white-on-blue. Not startup neon. Confident, trustworthy, modern.

### Color Palette
| Token | Hex | Usage |
|---|---|---|
| `--teal` | `#007A87` | Primary brand, CTAs, links, accents |
| `--teal-light` | `#009DAD` | Hover states, gradients |
| `--teal-dark` | `#005F6B` | Dark backgrounds, footer |
| `--gold` | `#C8962E` | Secondary accent, primary CTA buttons |
| `--gold-light` | `#E8B84B` | Hero highlights, hero text accents |
| `--navy` | `#0F2B4C` | Section backgrounds, hero overlays |
| `--navy-light` | `#1A3D6B` | Gradient pairs |
| `--text` | `#1A2332` | Body text |
| `--text-muted` | `#6B7A8D` | Secondary text, descriptions |
| `--bg-soft` | `#F8FAFB` | Alternating section backgrounds |
| `--border` | `#E2E8F0` | Card borders, dividers |

### Typography
| Role | Font | Weight | Size range |
|---|---|---|---|
| Headlines | Plus Jakarta Sans | 800 | 30px–56px |
| Subheadings | Plus Jakarta Sans | 700 | 18px–24px |
| Body | Inter | 400/500 | 14px–16px |
| Labels / caps | Inter | 600–700 | 11px–13px, uppercase |

Load via Google Fonts CDN: `Plus Jakarta Sans` (400,500,600,700,800) + `Inter` (400,500,600).

### Icons
Lucide icons via CDN: `https://unpkg.com/lucide@latest/dist/umd/lucide.min.js`  
Usage: `<i data-lucide="icon-name"></i>` + `lucide.createIcons()` in Blade.  
For SSR consistency, inline SVG paths in Blade components where icon is critical to layout.

### Design Tokens (CSS custom properties)
All tokens defined in `resources/css/app.css` `:root {}` block. Tailwind extended to reference them via `tailwind.config.js`.

---

## 3. Tech Stack

| Layer | Choice | Notes |
|---|---|---|
| Framework | Laravel 13 | PHP 8.3 via Laragon |
| Admin / CMS | Filament v3 | Product management, blog, leads, partnerships |
| Frontend | Blade + Alpine.js | SSR for SEO; Alpine for interactive UI only |
| CSS | Tailwind CSS v4 | Design tokens in CSS-first `@theme` (resources/css/app.css) |
| Database | MySQL 8 | Laragon, port 3306 — DO NOT CHANGE |
| Web server | Apache | Ports 80/443 — DO NOT CHANGE |
| Icons | Lucide (CDN) | Inline SVG fallback for LCP-critical icons |
| Fonts | Google Fonts CDN | Plus Jakarta Sans + Inter |
| Search | Laravel Scout + Meilisearch | Product and blog search |
| Sitemap | spatie/laravel-sitemap | Auto-generated, submitted to GSC |
| SEO | ralphjsmit/laravel-seo | Meta tags, OG, JSON-LD per page |
| Image optimisation | spatie/laravel-medialibrary | Responsive images, WebP conversion |

### Infrastructure Constraints (from AI_GUIDELINES.md)
- Apache: ports 80 and 443 — immutable
- MySQL: port 3306 — immutable
- Never `taskkill mysqld.exe` or `httpd.exe`
- Use isolated DB user for this project: `ohs_user`
- Register static port in `C:\laragon\www\PORT_REGISTRY.md` (web on 80/443, no custom port needed)

---

## 4. Bilingual Architecture

### Routing Strategy
**URL prefix approach** — all routes prefixed with locale:
```
/en/                    → Homepage (English)
/fr/                    → Homepage (French)
/en/products/opes-emr   → Product detail (EN)
/fr/products/opes-emr   → Product detail (FR)
/en/blog/               → Blog index (EN)
/fr/blog/               → Blog index (FR)
```

Root `/` redirects to `/en/` (browser `Accept-Language` detection, default EN).

### Implementation
- Laravel `Route::prefix('{locale}')` group with `SetLocale` middleware
- Middleware validates locale against `['en', 'fr']`, aborts 404 otherwise
- `App::setLocale($locale)` in middleware; all strings via `__('key')` / `trans()`
- Translation files: `lang/en/*.php` and `lang/fr/*.php`
- Language switcher in navbar updates URL prefix, preserves current page path

### SEO for Bilingual
Every page includes in `<head>`:
```html
<link rel="alternate" hreflang="en" href="https://opeshealthsystems.com/en/..." />
<link rel="alternate" hreflang="fr" href="https://opeshealthsystems.com/fr/..." />
<link rel="alternate" hreflang="x-default" href="https://opeshealthsystems.com/en/..." />
```

---

## 5. Site Architecture

### Pages

#### Public
| Route | Description |
|---|---|
| `/{locale}` | Homepage |
| `/{locale}/products` | Products index (filterable icon grid) |
| `/{locale}/products/{slug}` | Product detail page (×22) |
| `/{locale}/solutions` | Solutions by facility type / use case |
| `/{locale}/partnerships` | Partnership tracks (hospital, NGO, pharma, insurance, investor) |
| `/{locale}/blog` | Blog / content hub index |
| `/{locale}/blog/{slug}` | Blog article |
| `/{locale}/about` | About OPES, team, mission, Articles alignment |
| `/{locale}/contact` | Contact + demo request form |
| `/{locale}/ministry-alignment` | MoH Digital Health Strategy 2026–2030 alignment page |

#### System
| Route | Description |
|---|---|
| `/sitemap.xml` | Auto-generated sitemap (all locales) |
| `/robots.txt` | Crawl directives |
| `/{locale}/privacy` | Privacy policy |
| `/{locale}/terms` | Terms of use |

#### Admin (Filament)
| Route | Description |
|---|---|
| `/admin` | Filament dashboard |
| `/admin/products` | CRUD for all 22 products |
| `/admin/blog-posts` | Blog post management |
| `/admin/leads` | Demo request leads |
| `/admin/partnerships` | Partnership enquiry CRM |

### URL Slugs for 22 Products
| Product | Slug |
|---|---|
| OPESCare Health ID | `opescare` |
| OPES EMR | `opes-emr` |
| Hospital HIS | `hospital-his` |
| UHC IS | `uhc-is` |
| Opes Triage | `opes-triage` |
| OPES Lab | `opes-lab` |
| PHARMIS | `pharmis` |
| RADIS | `radis` |
| OPES CDMS | `opes-cdms` |
| RCMIS | `rcmis` |
| CARDIS | `cardis` |
| DENTIS | `dentis` |
| DERMIS | `dermis` |
| ENDOIS | `endois` |
| GYNOBSIS | `gynobsis` |
| MHIS | `mhis` |
| NDIS | `ndis` |
| OPHIS | `ophis` |
| ORTHOIS | `orthois` |
| PAEDIS | `paedis` |
| REHABIS | `rehabis` |
| SLTIS | `sltis` |

---

## 6. Homepage Design

### Layout: Split Hero + Category Tabs

**Section order (top to bottom):**
1. Navbar (sticky)
2. Hero — split layout: left text + right product category tabs + product icon grid
3. Ministry Alignment Banner
4. Why OPES? — 3 value pillars (Bilingual · Built for Africa · Fully Interoperable)
5. OPES Ecosystem Diagram — OPESCare at center, 22 systems connected
6. Featured Products Showcase — 3–4 highlighted products with UI previews
7. Opes Triage Standalone Callout
8. Impact / Stats Section (animated on scroll)
9. Demo Request CTA (mid-page)
10. Partnerships Section
11. Blog / Content Hub Preview (latest 3 articles)
12. Testimonials / Early Adopters
13. Footer

### Hero
- **Left:** OPES headline, tagline in EN/FR, two CTAs (Book Demo — gold; Explore Products — outline)
- **Right:** Category filter tabs (Clinical · Administrative · Financial · Specialist) above a 4-column icon grid of products; tab click filters the visible products
- Background: navy-to-teal-dark gradient with subtle radial glow
- Typography: Plus Jakarta Sans 800 for headline, Inter for tagline

### Product Icon Grid (Filterable)
- 22 product cards in a responsive grid (4 cols desktop, 2 cols mobile)
- Each card: icon, product name, one-line description
- Alpine.js `x-show` filter by category — zero JavaScript framework dependency
- Hover: card lifts, teal border, arrow appears
- Click: navigates to `/{locale}/products/{slug}`

### Category Taxonomy
| Category | Products |
|---|---|
| Clinical | OPESCare, OPES EMR, Hospital HIS, UHC IS, Opes Triage, OPES Lab, RADIS, OPES CDMS |
| Administrative | Hospital HIS, RCMIS, PHARMIS |
| Specialist | CARDIS, DENTIS, DERMIS, ENDOIS, GYNOBSIS, MHIS, NDIS, OPHIS, ORTHOIS, PAEDIS, REHABIS, SLTIS |
| Financial | RCMIS, PHARMIS |

---

## 7. Product Detail Page Design

Every product has a standalone detail page at `/{locale}/products/{slug}`.  
The template is identical across all 22 products; content is driven from the database.

### Page Sections
1. **Breadcrumb** — Home › Products › {Category} › {Product Name}
2. **Product Hero**
   - Left: category badge, product icon (64px), product name (h1), tagline, two CTAs
   - Right: "At a glance" stats box (module count, language, interoperability standard, ecosystem connections)
   - Background: navy gradient matching homepage hero
3. **Tab Navigation** (sticky below navbar)
   - Overview · Modules · Workflow · Integrations · Technical Specs
4. **Overview Section**
   - Left: descriptive copy (3 paragraphs), target audience checklist
   - Right: "Problems this product solves" numbered card
5. **Modules Section**
   - 3-column grid of module cards
   - Each card: icon, module name, description, 4 feature bullets
   - Cards can be activated independently as a facility grows
6. **Workflow Section**
   - Visual step-flow diagram (horizontal, 6–8 steps)
   - Below: 4 expanded detail cards explaining key workflow stages
7. **Benefits Section**
   - 4-column grid: speed, connectivity, compliance, bilingual
8. **OPES Ecosystem / Integrations**
   - Hub diagram: product at center, connected systems as chips
   - 3 deep-dive integration cards for the most critical connections
9. **Technical Specifications**
   - 4 tables: Deployment, Interoperability, Localisation, Security
10. **Related Products** — 3 sibling product cards with links
11. **Demo CTA** — full-width dark section, two buttons

### Database Model: `products`
```
id, slug, category, icon_lucide (icon name string),
name_en, name_fr,
tagline_en, tagline_fr,
overview_en, overview_fr,
target_audience (JSON),
problems_solved (JSON),
modules (JSON),           // array of {icon, name_en, name_fr, desc_en, desc_fr, features[]}
workflow_steps (JSON),    // array of {step, icon, label_en, label_fr, desc_en, desc_fr}
integrations (JSON),      // array of related product slugs
tech_specs (JSON),        // key-value pairs by category
stats (JSON),             // {module_count, standard, ecosystem_count, ...}
meta_title_en, meta_title_fr,
meta_description_en, meta_description_fr,
published_at, created_at, updated_at
```

---

## 8. SEO / AEO / GEO Architecture

### Per-Page Meta
Every Blade layout includes:
```html
<title>{{ $seo->title }}</title>
<meta name="description" content="{{ $seo->description }}">
<meta property="og:title" content="{{ $seo->title }}">
<meta property="og:description" content="{{ $seo->description }}">
<meta property="og:image" content="{{ $seo->image }}">
<link rel="canonical" href="{{ $seo->canonical }}">
```

### JSON-LD Structured Data
**Homepage:** `Organization` schema  
```json
{
  "@type": "Organization",
  "name": "Opes Health Systems SARL",
  "url": "https://opeshealthsystems.com",
  "logo": "...",
  "address": { "@type": "PostalAddress", "addressLocality": "Douala", "addressCountry": "CM" },
  "sameAs": ["https://linkedin.com/company/opes-health-systems"]
}
```

**Product pages:** `SoftwareApplication` schema  
```json
{
  "@type": "SoftwareApplication",
  "name": "OPES EMR",
  "applicationCategory": "HealthApplication",
  "operatingSystem": "Web",
  "offers": { "@type": "Offer", "priceCurrency": "XAF" },
  "description": "...",
  "provider": { "@type": "Organization", "name": "Opes Health Systems SARL" }
}
```

**Blog posts:** `Article` + `FAQPage` schemas where applicable.

### Sitemap
`spatie/laravel-sitemap` generates `/sitemap.xml` including:
- All product pages in EN and FR
- All blog posts in EN and FR
- All static pages in EN and FR
- Updated `lastmod` from `updated_at` timestamps
- Priority: homepage 1.0, products 0.9, blog 0.7, static 0.5

### AEO / GEO Tactics
- Every product page includes an FAQ section (collapsible, `FAQPage` JSON-LD)
- Blog articles target "what is X", "how does X work in Africa" queries
- Structured content written to be quoted by AI answer engines (Claude, ChatGPT, Gemini, Perplexity)
- `<meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large">` on all indexable pages

---

## 9. Admin Panel (Filament v3)

### Resources
| Resource | Purpose |
|---|---|
| `ProductResource` | CRUD for all 22 products; JSON fields edited via repeater fields |
| `BlogPostResource` | Title (EN/FR), body (EN/FR), slug, category, published_at, featured image |
| `LeadResource` | Demo request submissions; name, facility, country, email, message, status |
| `PartnershipResource` | Partnership enquiry management; type (hospital/NGO/pharma/insurance/investor) |
| `TestimonialResource` | Testimonial quotes; facility name, person, role, quote (EN/FR), photo |

### Admin Auth
- Separate `AdminUser` model (or gate-guarded `User` with `is_admin` flag)
- Filament default auth guard
- No public registration — seeded admin user only

---

## 10. Blog / Content Hub

### Purpose
Drive SEO, AEO, and GEO traffic. Every article targets a specific search query.

### Content Pillars
1. **Product deep-dives** — "Everything you need to know about OPES EMR"
2. **African digital health** — "State of EHR adoption in Cameroon 2026"
3. **Ministry of Health updates** — "What the MoH Digital Health Strategy means for hospitals"
4. **Medical informatics education** — "What is HL7 FHIR and why it matters in Africa"
5. **Clinical workflow guides** — "How to digitise your hospital's triage process"

### Blog Post Model: `blog_posts`
```
id, slug, category_id,
title_en, title_fr,
excerpt_en, excerpt_fr,
body_en, body_fr,        // Markdown stored, rendered to HTML
featured_image,
author_id,
reading_time_minutes,
meta_title_en, meta_title_fr,
meta_description_en, meta_description_fr,
published_at, created_at, updated_at
```

---

## 11. Lead Capture & Demo Request

### Primary CTA
"Book a Free Demo" — appears in:
- Navbar (button)
- Hero section
- Mid-page Demo CTA banner
- Every product detail page (hero + bottom CTA)
- Footer

### Demo Request Form Fields
| Field | Type | Required |
|---|---|---|
| Full name | text | yes |
| Organisation / facility name | text | yes |
| Facility type | select (Hospital / Clinic / NGO / Government / Insurance / Other) | yes |
| Country | select (Cameroon default, then CEMAC, then all Africa) | yes |
| Email | email | yes |
| Phone (WhatsApp preferred) | tel | no |
| Product(s) of interest | multiselect from 22 products | no |
| Message | textarea | no |

### Lead Storage
- Stored in `leads` table via `LeadResource` in Filament
- Email notification to `exerateanalytical@gmail.com` on new submission
- No third-party CRM in phase 1; Filament admin is the CRM

---

## 12. Partnerships Section

### Partnership Tracks (5 tracks)
| Track | Target | Value proposition |
|---|---|---|
| Hospital / Clinic | Facilities wanting to deploy OPES products | Software licensing, implementation, training |
| NGO / International | Global health orgs operating in CEMAC | Interoperable, MoH-aligned, bilingual |
| Pharma | Drug companies wanting pharmacy data reach | PHARMIS network data (anonymised, aggregated) |
| Insurance / Mutual | Health insurers | RCMIS integration, claims automation |
| Investor | VCs, DFIs, angels | Africa health tech growth, 22-product moat |

### Page Design
- Intro section: "Partner with OPES"
- 5 track cards (icon, name, who it's for, what you get, CTA button)
- Partnership enquiry form (name, organisation, track, email, message)
- Stored in `partnerships` Filament resource

---

## 13. Ministry Alignment Page

### Purpose
Credibility signal for government procurement, public tenders, and international partnerships.

### Content
- Banner: "Aligned with Cameroon Ministry of Health Digital Health Strategy 2026–2030"
- Which MoH goals each OPES product addresses (table)
- HL7 FHIR interoperability statement
- Data sovereignty section (in-country deployment option)
- Contact for government / institutional procurement

---

## 14. Navigation

### Primary Navbar
```
Logo | Products | Solutions | Partnerships | Blog | About | [EN/FR toggle] | [Book Demo — CTA]
```

### Products Mega-Menu (hover/click)
```
Clinical Systems          Administrative Systems     Specialist Systems
-----------------         ----------------------     -----------------
OPESCare Health ID        Hospital HIS               CARDIS
OPES EMR                  RCMIS                      DENTIS
Opes Triage               PHARMIS                    DERMIS
OPES Lab                  UHC IS                     ENDOIS
RADIS                                                GYNOBSIS
OPES CDMS                                            MHIS
OPES CDSS                                            NDIS
                                                     OPHIS / ORTHOIS / PAEDIS / REHABIS / SLTIS
[ View all 22 products → ]
```

### Footer
```
Column 1: Logo, tagline, EN/FR, social links
Column 2: Products (top 8)
Column 3: Company (About, Blog, Partnerships, Ministry Alignment)
Column 4: Contact (address Douala, email, demo CTA)
Bottom bar: © 2026 Opes Health Systems SARL · Privacy · Terms · Bilingual badge
```

---

## 15. Performance & Accessibility

- Server-side rendered Blade — no client-side routing; full HTML on first byte
- Tailwind CSS purged in production — target < 15 kB CSS
- Images: WebP via `spatie/laravel-medialibrary`, lazy-loaded, explicit width/height
- LCP target: < 2.5 s on 4G
- Alpine.js loaded only on pages that need it (deferred)
- Lucide icons: CDN for non-critical icons, inline SVG for LCP-critical (hero icon)
- WCAG 2.1 AA minimum: 4.5:1 contrast ratios, keyboard navigation, `aria-label` on icon-only buttons

---

## 16. File / Folder Structure

```
ohs/
├── app/
│   ├── Filament/Resources/          # ProductResource, BlogPostResource, LeadResource, etc.
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── HomeController.php
│   │   │   ├── ProductController.php
│   │   │   ├── BlogController.php
│   │   │   ├── PartnershipController.php
│   │   │   └── LeadController.php
│   │   └── Middleware/
│   │       └── SetLocale.php
│   └── Models/
│       ├── Product.php
│       ├── BlogPost.php
│       ├── Lead.php
│       └── Partnership.php
├── database/migrations/             # products, blog_posts, leads, partnerships, testimonials
├── resources/
│   ├── views/
│   │   ├── layouts/
│   │   │   └── app.blade.php        # master layout: navbar, footer, meta, JSON-LD slot
│   │   ├── pages/
│   │   │   ├── home.blade.php
│   │   │   ├── products/
│   │   │   │   ├── index.blade.php  # filterable icon grid
│   │   │   │   └── show.blade.php   # product detail (tabs, modules, workflow, specs)
│   │   │   ├── blog/
│   │   │   │   ├── index.blade.php
│   │   │   │   └── show.blade.php
│   │   │   ├── partnerships.blade.php
│   │   │   ├── about.blade.php
│   │   │   ├── contact.blade.php
│   │   │   └── ministry-alignment.blade.php
│   │   └── components/
│   │       ├── navbar.blade.php
│   │       ├── footer.blade.php
│   │       ├── product-card.blade.php
│   │       ├── module-card.blade.php
│   │       ├── workflow-step.blade.php
│   │       ├── demo-cta.blade.php
│   │       └── hero.blade.php
│   ├── css/app.css                  # CSS custom properties, Tailwind directives
│   └── js/app.js                    # Alpine.js initialisation, Lucide createIcons()
├── lang/
│   ├── en/                          # English translation strings
│   └── fr/                          # French translation strings
├── routes/web.php                   # locale-prefixed route groups
├── config/seo.php                   # site-wide SEO defaults
└── docs/superpowers/specs/
    └── 2026-06-13-opes-website-design.md  # this file
```

---

## 17. Database Migrations

### Priority order for implementation
1. `create_products_table` — core content
2. `create_leads_table` — revenue-critical (demo capture)
3. `create_blog_posts_table` — SEO content
4. `create_partnerships_table` — partnership enquiries
5. `create_testimonials_table` — social proof

---

## 18. Seed Data

The 22 products must be seeded in `database/seeders/ProductSeeder.php` with:
- English and French names, taglines, and overview paragraphs
- Module lists per product
- Workflow steps per product
- Integration links (which other OPES products connect)
- Lucide icon name per product (see icon mapping in section 19)

---

## 19. Product Icon Mapping (Lucide)

| Product | Lucide Icon | Rationale |
|---|---|---|
| OPESCare Health ID | `fingerprint` | Unique patient identity |
| OPES EMR | `stethoscope` | Clinical examination / records |
| Hospital HIS | `hospital` | Hospital building |
| UHC IS | `users` | Population-level universal coverage |
| Opes Triage | `timer` | Time-critical wait reduction |
| OPES Lab | `microscope` | Laboratory microscopy |
| PHARMIS | `pill` | Pharmacy / medication |
| RADIS | `image-up` | Radiological imaging / X-ray upload |
| OPES CDMS | `folder-open` | Document management |
| RCMIS | `receipt` | Revenue cycle / billing receipts |
| CARDIS | `activity` | ECG / cardiac monitoring |
| DENTIS | `smile` | Oral / dental |
| DERMIS | `layers` | Skin layers |
| ENDOIS | `gauge` | Hormone / glucose level monitoring |
| GYNOBSIS | `baby` | Obstetrics / maternity |
| MHIS | `brain` | Mental health |
| NDIS | `apple` | Nutrition / diet |
| OPHIS | `eye` | Ophthalmology |
| ORTHOIS | `accessibility` | Mobility / orthotics / prosthetics |
| PAEDIS | `shield-heart` | Protecting children's health |
| REHABIS | `dumbbell` | Physical therapy / rehabilitation |
| SLTIS | `waves` | Sound waves / speech |

---

## 20. Out of Scope (Phase 1)

- Patient-facing portal / login
- Online payment processing
- E-commerce / direct software purchase
- Live chat widget
- Multi-tenant SaaS provisioning
- Third-party CRM integration (HubSpot, Salesforce)
- Mobile app
- Video hosting / product demo videos (placeholder sections only)

---

## 21. Open Questions (Resolved)

| Question | Decision |
|---|---|
| Language routing strategy | URL prefix: /en/ and /fr/ |
| Admin / CMS approach | Filament v3 (built-in Laravel panel) |
| Frontend framework | Blade + Alpine.js (SSR for SEO, no Vue/React) |
| Primary CTA | "Book a Free Demo" (lead capture priority) |
| Brand direction | Premium Hybrid (navy + teal + gold) |
| Homepage layout | Split Hero + Category Tabs |
| Product navigation | Filterable Icon Grid |
| Typography | Plus Jakarta Sans 800 + Inter 400/500 |
| Homepage sections | All 10 sections included |
| Product detail page | Full profile: modules, workflow, integrations, tech specs |

# OPES Health Systems — Production Go-Live Runbook

> Practical, ordered steps to deploy to the live server. Tick each box. Commands run **on the production server**, in the app directory, unless noted. PHP 8.3, Laravel 13.8, MySQL 8.x, Node/Vite, Filament v3.

---

## 0. Pre-flight (do BEFORE touching prod)

- [ ] **DB backup taken** (`mysqldump` of the prod DB, stored off-server).
- [ ] **`.env` on prod is correct:**
  - `APP_ENV=production`
  - `APP_DEBUG=false`  ← critical (don't leak stack traces)
  - `APP_KEY=` set (run `php artisan key:generate` once if blank)
  - `APP_URL=https://opeshealthsystems.com` (your real domain)
  - `DB_*` → prod MySQL credentials
  - `MAIL_*` → a real mail driver (SMTP/SES), **not** `log` — the notification system + password reset + demo confirmations need it
  - `QUEUE_CONNECTION=database` (or redis) and a **queue worker running** (notifications are queued)
  - `SESSION_SECURE_COOKIE=true`, `SESSION_DOMAIN` set
  - `CACHE_STORE`/`REDIS_*` if used
- [ ] **HTTPS** cert valid; HTTP→HTTPS redirect in the web server.
- [ ] **Content sign-off (outward-facing — review before public):**
  - [ ] Competitor pages **110–113** (name ProsoftAfrica / Evolucare / GESMEDIC) — legal/brand OK.
  - [ ] Clinical/government content — article **86** + the **"Problems & Solutions" series (87–109)** cite mortality/blindness/govt figures; and the **63 French translations** from this batch — a domain + native-French read.

---

## 1. Deploy (in order)

```bash
php artisan down --render="errors::503"          # maintenance mode

git fetch origin
git checkout main && git pull origin main         # (master is kept in sync with main)

composer install --no-dev --optimize-autoloader

npm ci
npm run build                                     # builds public/build — REQUIRED (CSS tokens, flag SVGs, JS).
                                                  # public/build is gitignored, so this MUST run on every deploy.

php artisan migrate --force                       # apply new migrations

# Seed content — without these, /blog and /products are empty / 500 (the bug we hit on dev)
php artisan db:seed --class=BlogPostSeeder --force   # 109 bilingual articles (truncates+reseeds blog_posts)
php artisan db:seed --class=ProductSeeder --force    # 22 products

php artisan storage:link                          # if the symlink isn't already present
php artisan optimize                              # config + route + view + event cache
php artisan queue:restart                         # if running queue workers (supervisor)

php artisan up                                    # exit maintenance
```

> **Note on seeders:** `BlogPostSeeder` truncates `blog_posts` then reinserts from `content/articles/*.md` — safe to re-run, it's the source of truth. `ProductSeeder` is additive. Run both on a fresh DB; re-run `BlogPostSeeder` whenever articles change.

---

## 2. Post-deploy smoke test (under 2 minutes)

Replace `https://opeshealthsystems.com` below. Expect `200`/`3xx`:

```bash
for u in /en /en/products /en/products/pharmis /en/markets /en/markets/gabon \
         /en/blog "/en/blog?category=HMS%20Solutions" \
         /en/blog/109-real-cost-paper-based-healthcare-why-digitalise \
         /fr /fr/markets/gabon /en/book-demo /login; do
  echo "$(curl -s -o /dev/null -w '%{http_code}' https://opeshealthsystems.com$u)  $u"
done
```

Then manually:
- [ ] Home renders with styles (CSS built) and the **Markets** nav dropdown shows flags.
- [ ] `/en/products` lists 22 products (seeded).
- [ ] `/en/blog` lists articles; open one; switch EN/FR.
- [ ] **Admin login** works; a Filament page (e.g. a dashboard) loads.
- [ ] **Send one real email** (e.g. trigger a password reset) — confirms the mail driver + queue worker.
- [ ] Tail logs for 5 min: `tail -f storage/logs/laravel.log` — watch for any strict-MySQL 500 in authenticated flows (those weren't covered by the public sweep).

---

## 3. Rollback (if something breaks)

```bash
php artisan down
git checkout <previous-good-commit-or-tag>
composer install --no-dev --optimize-autoloader
npm ci && npm run build
php artisan optimize:clear && php artisan optimize
# only if a NEW migration is the culprit: php artisan migrate:rollback --step=1 --force
php artisan up
```
Restore the DB backup from step 0 if data was affected.

---

## 4. Known limitations / watch-items (honest list)

- **Tests run on SQLite; prod is strict MySQL.** The full suite is **500/500 green** and the **public surface was swept on real MySQL (83 routes, 0 errors)** — but **authenticated portal + Filament-admin flows were not swept** (they need a login). Watch `laravel.log` after go-live.
- **`public/build` is gitignored** — the build (CSS readability tokens, flag SVGs, JS) exists only after `npm run build`. Skipping it = unstyled/broken pages.
- **Filament admin** uses direct AA color values (not the `app.css` tokens) because it loads its own CSS — verified by the `ReadabilityTokensTest` guard.
- **First deploy only:** ensure the web root points at `public/`, and file permissions on `storage/` + `bootstrap/cache/` are writable by the web user.

---

## 5. Regression guards already in place

- `php artisan test` → **500 tests** (run in CI before each deploy).
- `ReadabilityTokensTest` fails the build if anyone reintroduces faint `#475569`/`#64748b`/`#94a3b8` text or sub-12px fonts (public + portals + Filament).
- `BlogValidationTest` guards the blog DISTINCT/FAQ-schema behavior; `MarketPageTest` guards the country pages + the home-page-still-renders + `/products`-no-500 cases.

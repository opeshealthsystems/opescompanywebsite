#!/usr/bin/env bash
#
# OPES Health Systems — production deploy.
# Run from the application root ON THE LIVE SERVER:  bash deploy.sh
# Mirrors docs/deploy/go-live-runbook.md. Idempotent — safe to re-run.
#
set -euo pipefail

echo "==> [1/9] Maintenance mode"
php artisan down --render="errors::503" || true

echo "==> [2/9] Pull latest (main)"
git fetch origin
git checkout main
git pull origin main

echo "==> [3/9] PHP dependencies (production, no dev)"
composer install --no-dev --optimize-autoloader

echo "==> [4/9] Build front-end assets  (REQUIRED — public/build is gitignored)"
npm ci
npm run build

echo "==> [5/9] Database migrations"
php artisan migrate --force

echo "==> [6/9] Seed content (blog + products) — without this /blog and /products are empty/500"
php artisan db:seed --class=BlogPostSeeder --force
php artisan db:seed --class=ProductSeeder --force

echo "==> [7/9] Storage symlink"
php artisan storage:link || true

echo "==> [8/9] Optimize caches (config + route + view + events)"
php artisan optimize

echo "==> [9/9] Restart queue workers + exit maintenance"
php artisan queue:restart || true
php artisan up

echo ""
echo "✓ Deploy complete. Now run the smoke test in docs/deploy/go-live-runbook.md §2,"
echo "  and tail storage/logs/laravel.log for ~5 min (authenticated flows weren't pre-swept)."

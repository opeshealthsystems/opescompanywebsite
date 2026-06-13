# OPES Health Systems — Deployment Checklist

## Server Requirements
- PHP 8.3+ with extensions: pdo, pdo_mysql, mbstring, openssl, tokenizer, xml, ctype, json, bcmath, fileinfo, gd
- MySQL 8.0+
- Apache 2.4+ with mod_rewrite (DocumentRoot must point to `public/`)
- Composer 2.x
- Node.js 20+ (build step only)

## Initial Deployment

```bash
# 1. Clone repository
git clone <repo-url> /var/www/ohs

# 2. Install PHP dependencies
composer install --no-dev --optimize-autoloader

# 3. Build frontend assets
npm ci && npm run build

# 4. Copy and configure .env
cp .env.example .env
php artisan key:generate

# 5. Set database credentials in .env, then migrate
php artisan migrate --force
php artisan db:seed --force   # creates admin user

# 6. Create storage symlink
php artisan storage:link

# 7. Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 8. Set file permissions
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
```

## Apache VirtualHost
```apache
<VirtualHost *:80>
    ServerName opeshealthsystems.com
    ServerAlias www.opeshealthsystems.com
    DocumentRoot /var/www/ohs/public
    <Directory /var/www/ohs/public>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```
Enable SSL and redirect HTTP→HTTPS with Certbot: `certbot --apache -d opeshealthsystems.com`

## Post-Deployment Checks
- [ ] `https://opeshealthsystems.com/en` loads home page
- [ ] `https://opeshealthsystems.com/fr` loads French home page
- [ ] `https://opeshealthsystems.com/en/products/opescare` loads product detail
- [ ] `https://opeshealthsystems.com/en/contact` form submits (creates lead in DB)
- [ ] `https://opeshealthsystems.com/admin` Filament admin login works
- [ ] `https://opeshealthsystems.com/sitemap.xml` returns valid XML

## Admin Credentials (change immediately after first login)
- URL: `https://opeshealthsystems.com/admin`
- Email: `admin@opeshealthsystems.com`
- Password: `OPESadmin2026!` — **CHANGE THIS ON FIRST LOGIN**

## Updating
```bash
git pull
composer install --no-dev --optimize-autoloader
npm ci && npm run build
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

# Deployment — rt2.mluthfirr.id

Aplikasi ini berjalan di server `103.247.11.186` (Ubuntu 24.04) dan diakses
publik melalui **https://rt2.mluthfirr.id** (di belakang Cloudflare).

## Auto-deploy (server menarik dari GitHub)

Deploy otomatis memakai **cron-pull**: server memeriksa GitHub tiap menit dan
men-deploy sendiri bila ada commit baru di branch `main`.

> Kenapa bukan GitHub Actions (SSH push)? Server memakai `fail2ban`, dan runner
> GitHub Actions berbagi kolam IP yang sering sudah masuk daftar ban server
> (akibat brute-force penyewa lain), sehingga koneksi SSH dari Actions sering
> timeout. Menarik dari sisi server (outbound) tidak tersentuh masalah ini.

**Alur:** `git push` ke `main` → dalam ≤1 menit server menjalankan
`/usr/local/bin/rt2-deploy.sh` → live.

Isi `rt2-deploy.sh` (di server):

```bash
cd /var/www/rt2_dukacita
git fetch origin main
# jika HEAD != origin/main:
git reset --hard origin/main
composer install --no-dev --optimize-autoloader --no-interaction
npm ci --no-audit --no-fund
npm run build
php artisan migrate --force
php artisan config:cache && php artisan route:cache && php artisan view:cache
chown -R www-data:www-data /var/www/rt2_dukacita
chmod -R 775 storage bootstrap/cache
```

Cron: `* * * * * flock -n /tmp/rt2-deploy.lock /usr/local/bin/rt2-deploy.sh >> /var/log/rt2-deploy.log 2>&1`

Log deploy: `/var/log/rt2-deploy.log`

## Deploy manual (opsional, tanpa menunggu cron)

```bash
ssh root@103.247.11.186 /usr/local/bin/rt2-deploy.sh
```

## Stack server

- PHP 8.3 (FPM: `/run/php/php8.3-fpm.sock`), Composer, Node 20
- PostgreSQL 16 — database `renijaya_dukacita`, user `renijaya`
- nginx — vhost `/etc/nginx/sites-available/rt2.mluthfirr.id`, root `/var/www/rt2_dukacita/public`
- `.env` produksi hanya ada di server (tidak di-commit)

## Akun awal aplikasi

- Email: `pengurus@rt2renijaya.id`
- Sandi: `dukacita2025` (segera ganti setelah live)

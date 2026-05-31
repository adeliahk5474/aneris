#!/bin/sh
set -e

echo "==> Starting Aneris on Render..."

# ── Generate app key kalau belum ada ──
if [ -z "$APP_KEY" ]; then
    echo "==> Generating APP_KEY..."
    php artisan key:generate --force
fi

# ── Clear & rebuild cache dengan env dari Render ──
echo "==> Caching config, routes, views..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# ── Jalankan migration ──
echo "==> Running migrations..."
php artisan migrate --force --no-interaction

# ── Storage link (kalau pakai local fallback) ──
php artisan storage:link --force 2>/dev/null || true

# ── Buat log directory supervisor ──
mkdir -p /var/log/supervisor

echo "==> Starting supervisord (nginx + php-fpm + queue)..."
exec supervisord -c /etc/supervisor/conf.d/supervisord.conf

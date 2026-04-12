#!/usr/bin/env bash
set -euo pipefail
# Support for dynamic PaaS ports (like Railway)
if [ -n "${PORT:-}" ]; then
  sed -i "s/listen 80;/listen ${PORT};/g" /etc/nginx/conf.d/default.conf
fi

# If APP_KEY empty, warn (avoid regenerating every deploy to keep sessions valid)
if [ -z "${APP_KEY:-}" ]; then
  echo "[entrypoint] WARNING: APP_KEY is empty. Set APP_KEY in Render env vars before caching config." >&2
else
  mkdir -p storage/framework/views storage/framework/cache/data storage/framework/sessions
  php artisan config:cache || true
  php artisan route:cache || true
  php artisan view:cache || true
fi

# Fix permissions (in case of volume changes)
chown -R www-data:www-data storage bootstrap/cache || true
chmod -R 775 storage bootstrap/cache || true

exec "$@"

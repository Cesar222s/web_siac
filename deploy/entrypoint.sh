#!/usr/bin/env bash
set -euo pipefail

# If APP_KEY empty, warn (avoid regenerating every deploy to keep sessions valid)
if [ -z "${APP_KEY:-}" ]; then
  echo "[entrypoint] WARNING: APP_KEY is empty. Set APP_KEY in Render env vars before caching config." >&2
else
  php artisan config:cache || true
  php artisan route:cache || true
  php artisan view:cache || true
fi

# Optimize autoloader
composer dump-autoload --optimize --no-dev || true

# Fix permissions (in case of volume changes)
chown -R www-data:www-data storage bootstrap/cache || true
chmod -R 775 storage bootstrap/cache || true

exec "$@"

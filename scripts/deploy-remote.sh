#!/usr/bin/env bash
# Runs on the VPS after a release tarball is uploaded.
# Usage: deploy-remote.sh /path/to/release-dir

set -euo pipefail

RELEASE_DIR=${1:?release directory required}
APP_DIR=${APP_DIR:-/var/www/carwise.ai}
SHARED="$APP_DIR/shared"
CURRENT="$APP_DIR/current"

cd "$RELEASE_DIR"

if [[ ! -f "$SHARED/.env" ]]; then
  echo "Missing $SHARED/.env — create it once on the server before deploying."
  exit 1
fi

ln -sfn "$SHARED/.env" "$RELEASE_DIR/.env"
rm -rf "$RELEASE_DIR/storage"
ln -sfn "$SHARED/storage" "$RELEASE_DIR/storage"

composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

php artisan migrate --force
php artisan storage:link || true
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache || true

ln -sfn "$RELEASE_DIR" "$CURRENT"
chown -R deploy:www-data "$RELEASE_DIR" "$CURRENT" || true

if command -v php-fpm8.2 >/dev/null 2>&1; then
  sudo systemctl reload php8.2-fpm || true
fi
sudo systemctl reload nginx || true

# Keep the newest 5 releases
ls -1dt "$APP_DIR"/releases/* 2>/dev/null | tail -n +6 | xargs -r rm -rf

echo "Deployed $RELEASE_DIR"

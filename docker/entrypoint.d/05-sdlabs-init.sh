#!/bin/sh
#
# First-boot bootstrap. serversideup runs every /etc/entrypoint.d/*.sh on start;
# the low number makes this run early. Idempotent — safe on every boot, and does
# the whole app setup itself so ordering never depends on other automations.
set -e

cd /var/www/html

# 1. Ensure an .env exists (dev bind-mounts may start without one).
if [ ! -f .env ]; then
    echo "[init] creating .env from .env.example"
    cp .env.example .env
fi

# 2. Install PHP deps if they aren't baked in (dev bind-mount over the app).
if [ ! -f vendor/autoload.php ]; then
    echo "[init] installing composer dependencies"
    composer install --no-interaction --prefer-dist
fi

# 3. Generate an app key if one hasn't been set.
if ! grep -q '^APP_KEY=base64:' .env; then
    echo "[init] generating APP_KEY"
    php artisan key:generate --force
fi

# 4. Ensure the sqlite database file exists (when using sqlite).
if grep -q '^DB_CONNECTION=sqlite' .env; then
    [ -f database/database.sqlite ] || touch database/database.sqlite
fi

# 5. Link public storage and run migrations (both idempotent).
php artisan storage:link 2>/dev/null || true
php artisan migrate --force || echo "[init] migrate skipped/failed — check DB config"

# 6. Make sure the web user owns the writable paths.
chown -R www-data:www-data storage bootstrap/cache database .env 2>/dev/null || true

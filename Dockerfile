# syntax=docker/dockerfile:1
#
# Portable, self-contained image for SDLabs. Builds anywhere with just Docker —
# no host PHP/Node/Composer required. Nothing machine-specific lives here;
# all configuration comes from the environment / .env at runtime.
#
#   docker build -t sdlabs .
#
# Uses the serversideup/php runtime (Apache + PHP-FPM). An early entrypoint
# script (docker/entrypoint.d) bootstraps Laravel on boot: APP_KEY, sqlite db,
# storage:link and migrations.

ARG PHP_VERSION=8.4

# ---------------------------------------------------------------------------
# Stage 1 — PHP dependencies. Built on the same PHP as the runtime so the
# platform requirements match. Scripts are deferred until the full app is
# present (package discovery needs artisan).
# ---------------------------------------------------------------------------
FROM serversideup/php:${PHP_VERSION}-cli AS vendor

USER root
RUN install-php-extensions gd exif intl pcntl bcmath zip
WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --no-interaction --prefer-dist

COPY . .
RUN composer dump-autoload --no-dev --optimize --no-interaction

# ---------------------------------------------------------------------------
# Stage 2 — front-end assets. Tailwind v4 scans maryUI's PHP component files
# (see resources/css/app.css @source), so vendor/ must be present here.
# Debian (glibc), not Alpine (musl): the pinned optional deps are *-gnu builds.
# ---------------------------------------------------------------------------
FROM node:22-bookworm-slim AS assets

WORKDIR /app
COPY package.json package-lock.json .npmrc ./
RUN npm ci
COPY vite.config.js ./
COPY resources ./resources
COPY --from=vendor /app/vendor ./vendor
RUN npm run build

# ---------------------------------------------------------------------------
# Stage 3 — runtime.
# ---------------------------------------------------------------------------
FROM serversideup/php:${PHP_VERSION}-fpm-apache AS app

USER root
RUN install-php-extensions gd exif intl pcntl bcmath zip

WORKDIR /var/www/html

# Application code + built dependencies/assets.
COPY --chown=www-data:www-data . .
COPY --chown=www-data:www-data --from=vendor /app/vendor ./vendor
COPY --chown=www-data:www-data --from=assets /app/public/build ./public/build

# First-boot bootstrap (idempotent): .env, APP_KEY, sqlite file.
COPY docker/entrypoint.d/ /etc/entrypoint.d/
RUN sed -i 's/\r$//' /etc/entrypoint.d/*.sh \
    && chmod +x /etc/entrypoint.d/*.sh \
    && chown -R www-data:www-data /var/www/html

USER www-data

# App bootstrap (key, db, storage:link, migrations) is handled by the early
# entrypoint script above so it stays deterministic. AUTORUN_ENABLED just keeps
# serversideup's entrypoint machinery active.
ENV AUTORUN_ENABLED=true \
    PHP_OPCACHE_ENABLE=1

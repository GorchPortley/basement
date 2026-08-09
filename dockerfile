############################################
# Base Image
############################################
FROM serversideup/php:8.5-fpm-nginx-bookworm AS base

USER root
RUN install-php-extensions intl exif gd
USER www-data

############################################
# Development Image
############################################
FROM base AS development

# Switch to root so we can do rooty things
USER root
ARG USER_ID
ARG GROUP_ID
RUN docker-php-serversideup-set-id www-data $USER_ID:$GROUP_ID && \
    \
    # Update the file permissions to match the new UID/GID
    docker-php-serversideup-set-file-permissions --owner $USER_ID:$GROUP_ID
USER www-data

############################################
# Production Image
############################################

FROM base AS production

COPY --chown=www-data:www-data . /var/www/html

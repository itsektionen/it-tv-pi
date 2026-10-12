FROM serversideup/php:8.4-fpm-nginx

ENV PHP_OPCACHE_ENABLE=1

WORKDIR /var/www/html

COPY --chown=www-data:www-data composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --no-scripts --no-autoloader --prefer-dist

COPY --chown=www-data:www-data . .

ARG VITE_REVERB_APP_KEY=it-tv-pi
ARG VITE_REVERB_HOST=tv-dev.kth.it
ARG VITE_REVERB_PORT=443
ARG VITE_REVERB_SCHEME=https

USER root

RUN apt-get update \
    && apt-get install -y --no-install-recommends nodejs npm php-gd \
    && npm install -g pnpm@10 \
    && pnpm install --frozen-lockfile \
    && pnpm run build \
    && chown -R www-data:www-data public/build \
    && rm -rf node_modules \
    && npm uninstall -g pnpm \
    && apt-get purge -y --auto-remove nodejs npm \
    && rm -rf /var/lib/apt/lists/* /root/.npm /root/.cache /root/.local/share/pnpm

USER www-data

RUN composer dump-autoload --no-dev --optimize \
    && mkdir -p \
    bootstrap/cache \
    storage/app/private/images \
    storage/app/public \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs

FROM php:8.4.27-apache

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

RUN install-php-extensions gd opcache pcntl pdo_mysql redis zip

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" \
    && printf '%s\n' 'memory_limit=256M' 'upload_max_filesize=12M' 'post_max_size=16M' > "$PHP_INI_DIR/conf.d/zz-app.ini" \
    && sed -ri 's!^Listen 80$!Listen 8080!' /etc/apache2/ports.conf \
    && sed -ri 's!<VirtualHost \*:80>!<VirtualHost *:8080>!; s!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf \
    && a2enmod rewrite

ENV COMPOSER_HOME=/tmp/composer

WORKDIR /var/www/html

USER www-data

COPY --chown=www-data:www-data composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --no-scripts --no-autoloader --prefer-dist

COPY --chown=www-data:www-data . .

ARG VITE_REVERB_APP_KEY=it-tv-pi
ARG VITE_REVERB_HOST=tv-dev.kth.it
ARG VITE_REVERB_PORT=443
ARG VITE_REVERB_SCHEME=https

USER root

RUN apt-get update \
    && apt-get install -y --no-install-recommends nodejs npm \
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

EXPOSE 8080

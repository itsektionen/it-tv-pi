FROM debian:trixie-slim

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
    ca-certificates apache2 libapache2-mod-php8.4 php8.4-cli php8.4-opcache \
    php8.4-bcmath php8.4-curl php8.4-gd php8.4-intl php8.4-mbstring php8.4-mysql php8.4-xml php8.4-zip php-redis \
    && rm -rf /var/lib/apt/lists/* \
    && php -r 'exit(PHP_VERSION_ID >= 80427 ? 0 : 1);'

RUN printf '%s\n' 'memory_limit=256M' 'upload_max_filesize=12M' 'post_max_size=16M' \
    | tee /etc/php/8.4/apache2/conf.d/99-app.ini > /etc/php/8.4/cli/conf.d/99-app.ini \
    && sed -ri 's!^Listen 80$!Listen 8080!' /etc/apache2/ports.conf \
    && sed -ri 's!<VirtualHost \*:80>!<VirtualHost *:8080>!; s!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf \
    && sed -ri '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf \
    && a2enmod rewrite \
    && ln -sfT /dev/stderr /var/log/apache2/error.log \
    && ln -sfT /dev/stdout /var/log/apache2/access.log \
    && ln -sfT /dev/stdout /var/log/apache2/other_vhosts_access.log \
    && mkdir -p /var/run/apache2 /var/lock/apache2 \
    && chown -R www-data:www-data /var/run/apache2 /var/lock/apache2 /var/log/apache2 \
    && rm -rf /var/www/html && mkdir /var/www/html && chown www-data:www-data /var/www/html

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

CMD ["sh", "-c", ". /etc/apache2/envvars && exec apache2 -D FOREGROUND"]

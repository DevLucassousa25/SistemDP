# docker/php/Dockerfile
FROM php:8.3-fpm-alpine AS base

RUN apk add --no-cache \
    bash curl git \
    freetype-dev libjpeg-turbo-dev libpng-dev \
    libzip-dev oniguruma-dev zip unzip \
    postgresql-dev

RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_pgsql mbstring zip exif pcntl gd bcmath

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# ---------- Estágio de DESENVOLVIMENTO ----------
FROM base AS development
RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS linux-headers \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && apk del .build-deps
COPY docker/php/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini
CMD ["php-fpm"]

# ---------- Estágio de PRODUÇÃO ----------
FROM base AS production
COPY . .
RUN composer install --no-dev --optimize-autoloader --no-interaction \
    && php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
CMD ["php-fpm"]

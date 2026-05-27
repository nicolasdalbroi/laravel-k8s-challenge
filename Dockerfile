# Build phase
FROM php:8.2-fpm-alpine AS builder
WORKDIR /app

RUN apk add --no-cache \
    curl \
    unzip \
    libpq-dev \
    && docker-php-ext-install pdo pdo_mysql bcmath opcache

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY composer.json composer.lock ./
RUN rm -rf vendor/barryvdh/laravel-debugbar \
    && rm -rf vendor/barryvdh/laravel-ide-helper \
    && rm -f bootstrap/cache/packages.php \
    && rm -f bootstrap/cache/services.php
COPY . .
RUN composer dump-autoload \
    --optimize \
    --no-dev \
    --no-scripts

# Runtime
FROM php:8.2-fpm-alpine AS runtime
WORKDIR /var/www/html

RUN apk add --no-cache libpq \
    && docker-php-ext-install pdo pdo_mysql bcmath opcache \
    && rm -rf /tmp/*

COPY --from=builder /app .

RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache \
    && mkdir -p /var/www/html/storage/framework/cache \
    && mkdir -p /var/www/html/storage/framework/sessions \
    && mkdir -p /var/www/html/storage/framework/views \
    && mkdir -p /var/www/html/bootstrap/cache

USER www-data
EXPOSE 9000

HEALTHCHECK --interval=30s --timeout=5s --start-period=10s --retries=3 \
    CMD php-fpm -t || exit 1

CMD ["php-fpm"]
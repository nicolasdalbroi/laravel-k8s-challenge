# Build phase
FROM php:8.2-fpm-alpine AS builder

WORKDIR /app

# Install build dependencies
RUN apk add --no-cache \
    curl \
    unzip \
    libpq-dev \
    && docker-php-ext-install pdo pdo_mysql bcmath opcache

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy dependency files first (layer caching)
COPY composer.json composer.lock ./

# Install dependencies without dev packages
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

# Copy full source
COPY . .

# Generate optimized autoloader
RUN composer dump-autoload --optimize --no-dev

# Running app

FROM php:8.2-fpm-alpine AS runtime

WORKDIR /var/www/html

# Install only runtime PHP extensions
RUN apk add --no-cache libpq \
    && docker-php-ext-install pdo pdo_mysql \
    && rm -rf /tmp/*

# Copy app from builder
COPY --from=builder /app .

# Set correct permissions for Laravel writable dirs
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Switch to non-root user
USER www-data

EXPOSE 9000

HEALTHCHECK --interval=30s --timeout=5s --start-period=10s --retries=3 \
    CMD php-fpm -t || exit 1

CMD ["php-fpm"]
# Multi-stage Dockerfile for SIAC (Laravel)
# Stage 1: Frontend build (Vite assets)
FROM node:20-alpine AS frontend
WORKDIR /app
COPY package.json package-lock.json* yarn.lock* ./
RUN if [ -f package-lock.json ]; then npm ci; elif [ -f yarn.lock ]; then yarn install --frozen-lockfile; else npm install; fi
COPY resources/ ./resources/
COPY vite.config.js ./
RUN npm run build || echo "Skipping asset build (check Vite config)"

# Stage 2: Composer dependencies
FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --no-progress --no-scripts --no-interaction

# Stage 3: Runtime (PHP-FPM + Nginx + Supervisor)
FROM php:8.2-fpm-alpine AS runtime
WORKDIR /var/www/html

# Install system packages and PHP extensions
RUN apk add --no-cache \
    nginx supervisor bash git icu-dev libzip-dev oniguruma-dev libpng-dev libjpeg-turbo-dev freetype-dev \
    shadow openssl curl

# PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install pdo pdo_mysql mbstring zip gd intl opcache
# Optional MongoDB extension (comment out if not needed)
RUN pecl install mongodb && docker-php-ext-enable mongodb || echo "MongoDB ext optional"

# Create non-root user (optional; keeping www-data)
RUN usermod -u 1000 www-data && groupmod -g 1000 www-data || true

# Copy application source
COPY . ./
# Remove local vendor if present (we will use the vendor stage)
RUN rm -rf vendor
# Copy vendor from composer stage
COPY --from=vendor /app/vendor ./vendor
# Copy built assets
COPY --from=frontend /app/dist ./public/build

# Nginx configuration
RUN mkdir -p /run/nginx
COPY ./deploy/nginx.conf /etc/nginx/conf.d/default.conf

# Supervisor configuration
COPY ./deploy/supervisord.conf /etc/supervisord.conf

# Entrypoint script
COPY ./deploy/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

# Ensure storage & cache writable
RUN chown -R www-data:www-data storage bootstrap/cache && \
    chmod -R 775 storage bootstrap/cache

ENV APP_ENV=production \
    PHP_OPCACHE_VALIDATE_TIMESTAMPS=0 \
    PHP_OPCACHE_MAX_ACCELERATED_FILES=20000 \
    PHP_OPCACHE_MEMORY_CONSUMPTION=192 \
    PHP_OPCACHE_INTERNED_STRINGS_BUFFER=16

EXPOSE 80
USER www-data
ENTRYPOINT ["/entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-n", "-c", "/etc/supervisord.conf"]

# Healthcheck (simple HTTP test)
HEALTHCHECK --interval=30s --timeout=5s --start-period=20s --retries=3 \
    CMD wget -qO- http://127.0.0.1/ || exit 1

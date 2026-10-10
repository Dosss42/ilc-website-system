# Use official PHP 8.3 with FPM
FROM php:8.3-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    nginx \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    nodejs \
    npm \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy project files
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Install Node dependencies and build Vite assets
RUN npm install && npm run build

# Create Laravel's runtime folders. Git does not keep empty folders, and
# storage/framework/views is gitignored, so it is missing after COPY . . —
# without it `php artisan view:cache` fails with "View path not found".
RUN mkdir -p storage/framework/views storage/framework/cache/data \
        storage/framework/sessions storage/framework/testing \
        storage/logs storage/app/public bootstrap/cache

# Copy nginx config
COPY docker/nginx.conf /etc/nginx/sites-enabled/default

# Copy PHP-FPM pool config — the base image's default (pm.max_children = 5)
# caps the whole app at 5 concurrent requests; see docker/www.conf.
COPY docker/www.conf /usr/local/etc/php-fpm.d/www.conf

# Cache routes/views for production (config:cache is done at container
# start in entrypoint.sh, since env vars aren't available at build time)
RUN php artisan route:cache \
    && php artisan view:cache

# Set permissions after the caches are built, so the compiled views written
# above are owned by www-data too (PHP-FPM runs as www-data)
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache \
    && chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Expose port 80
EXPOSE 80

# Copy and set up entrypoint
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

CMD ["/entrypoint.sh"]

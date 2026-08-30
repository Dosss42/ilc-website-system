#!/bin/bash
set -e

# Generate app key if not set
if [ -z "$APP_KEY" ]; then
    php artisan key:generate --force
fi

# Run migrations
php artisan migrate --force

# Create storage symlink
php artisan storage:link || true

# Start PHP-FPM in background
php-fpm -D

# Start Nginx in foreground
nginx -g 'daemon off;'

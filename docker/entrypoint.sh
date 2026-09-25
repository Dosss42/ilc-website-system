#!/bin/bash
set -e

# Generate app key if not set
if [ -z "$APP_KEY" ]; then
    php artisan key:generate --force
fi

# Cache config now that real runtime env vars are available
php artisan config:clear
php artisan config:cache

# Run migrations
php artisan migrate --force

# Create storage symlink
php artisan storage:link || true

# Start a supervised queue worker in the background — auto-restarts if it
# ever exits, so a single failed/killed job doesn't leave mail (and any
# other queued work) stuck forever. Mail is dispatched to the real queue
# (see EnrollmentController) instead of afterResponse(), since that relied
# on the same PHP-FPM worker surviving past the HTTP response with no
# supervision or retry if that assumption ever broke.
(
    while true; do
        php artisan queue:work --queue=default --tries=3 --backoff=10 --max-time=3600
        sleep 2
    done
) &

# Start PHP-FPM in background
php-fpm -D

# Start Nginx in foreground
nginx -g 'daemon off;'

#!/bin/bash
set -e

# Remove the build-time .env so Laravel reads from actual environment variables
# Render injects env vars directly into the process environment
rm -f /var/www/html/.env

# Clear any cached config from build time (uses stale values)
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Re-cache with runtime environment variables
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run database migrations
php artisan migrate --force

# Link storage (ignore error if already linked)
php artisan storage:link || true

# Start Apache in foreground
apache2-foreground

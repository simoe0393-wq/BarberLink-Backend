#!/bin/bash

# Cache configuration, routes, and views for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run database migrations
php artisan migrate --force

# Link storage
php artisan storage:link || true

# Start Apache in foreground
apache2-foreground

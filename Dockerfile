FROM php:8.3-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    curl \
    libpq-dev \
    libzip-dev \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions for PostgreSQL and Laravel
RUN docker-php-ext-install pdo_pgsql mbstring exif pcntl bcmath gd zip

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy application source (vendor/ and node_modules/ excluded via .dockerignore)
COPY . /var/www/html

# Step 1: Install packages WITHOUT running post-install scripts (avoids artisan hook)
#         and WITHOUT generating autoload (we do that after .env is ready)
RUN composer install --no-interaction --no-scripts --no-autoloader --no-dev

# Step 2: Now that vendor/ exists, set up a temporary .env so artisan can bootstrap
RUN cp .env.example .env \
    && php artisan key:generate --ansi

# Step 3: Generate optimized autoloader, then run post-autoload-dump scripts
#         (this runs php artisan package:discover with a working .env)
RUN composer dump-autoload --optimize \
    && php artisan package:discover --ansi

# Set correct permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Configure Apache document root to serve from /public
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Enable Apache mod_rewrite for Laravel routing
RUN a2enmod rewrite

# Copy and make the startup script executable
COPY start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh

EXPOSE 80

CMD ["/usr/local/bin/start.sh"]

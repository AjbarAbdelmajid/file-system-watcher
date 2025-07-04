FROM php:8.2-cli

# Install system dependencies & PHP extensions
RUN apt-get update && \
    apt-get install -y --no-install-recommends \
        inotify-tools zip unzip libzip-dev libpng-dev libwebp-dev && \
    docker-php-ext-install zip gd && \
    rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Copy application code & install PHP deps
COPY . .
RUN composer install --no-dev --optimize-autoloader

ENTRYPOINT ["php", "bin/console"]

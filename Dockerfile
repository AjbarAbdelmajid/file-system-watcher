FROM php:8.2-cli

# Install system dependencies
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
         inotify-tools zip unzip libzip-dev libpng-dev libwebp-dev \
    && docker-php-ext-install zip gd

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# 1) Copy all application code (including bin/console)
COPY . .

# 2) Install PHP dependencies without running post-install scripts
RUN composer install --no-dev --optimize-autoloader --no-scripts

# Default entrypoint: start the watcher
ENTRYPOINT ["php", "bin/console", "file:watch"]

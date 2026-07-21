FROM php:8.2-cli

# System dependencies (mirrors docker/app/Dockerfile, plus Node for the asset build)
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    curl \
    libzip-dev \
    libpq-dev \
    git \
    ca-certificates \
    gnupg \
    && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && rm -rf /var/lib/apt/lists/*

# PHP extensions
RUN docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd

WORKDIR /var/www

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install PHP deps first so this layer only rebuilds when composer.* changes
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-scripts --no-interaction

# Install JS deps and build Vite assets (required for @vite() in layouts/app.blade.php)
COPY package.json package-lock.json ./
RUN npm ci

COPY . .

RUN npm run build && rm -rf node_modules

RUN composer dump-autoload --optimize \
    && chown -R www-data:www-data storage bootstrap/cache

COPY docker/render/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# NOTE: no EXPOSE here — Render assigns $PORT at runtime and detects it by
# probing the container's network, it doesn't read EXPOSE from the Dockerfile.

ENTRYPOINT ["entrypoint.sh"]

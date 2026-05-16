FROM php:8.4-cli

WORKDIR /var/www

RUN apt-get update && apt-get install -y \
    unzip zip git curl \
    libzip-dev libpng-dev libonig-dev libxml2-dev \
    && docker-php-ext-install \
    pdo pdo_pgsql mbstring zip xml \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY . .

RUN cp .env.example .env \
    && mkdir -p storage/framework/sessions storage/framework/views storage/framework/cache storage/logs bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache \
    && composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev

EXPOSE 10000

CMD php artisan serve --host=0.0.0.0 --port=10000
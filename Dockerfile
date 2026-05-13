FROM php:8.4-cli

WORKDIR /var/www

RUN apt-get update && apt-get install -y \
    unzip zip git curl \
    libzip-dev libpng-dev libonig-dev libxml2-dev \
    && docker-php-ext-install \
    pdo pdo_mysql mbstring zip xml \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY . .

RUN cp .env.example .env

RUN composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev

RUN touch database/database.sqlite

RUN php artisan key:generate --force
RUN php artisan migrate --force

EXPOSE 10000

CMD php artisan serve --host=0.0.0.0 --port=10000

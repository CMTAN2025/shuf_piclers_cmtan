FROM php:8.4-cli

WORKDIR /var/www

RUN apt-get update && apt-get install -y \
    unzip zip git curl \
    libzip-dev libxml2-dev libpq-dev \
    && docker-php-ext-install \
    pdo pdo_pgsql zip xml \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY . .

RUN mkdir -p storage/framework/sessions storage/framework/views storage/framework/cache storage/logs bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache \
    && composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev

COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 10000

ENTRYPOINT ["docker-entrypoint.sh"]
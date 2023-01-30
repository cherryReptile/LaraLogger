FROM php:8.1-fpm

RUN apt-get update && apt-get install -y \
    libpq-dev \

RUN docker-php-ext-install pdo_pgsql

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

WORKDIR /var/www
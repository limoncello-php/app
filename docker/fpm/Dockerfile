FROM php:fpm

RUN mkdir /app && \
    apt-get update && docker-php-ext-install -j$(nproc) pdo_mysql && \
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /app

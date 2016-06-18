FROM php:fpm

RUN mkdir /app && \
    apt-get update && docker-php-ext-install -j$(nproc) pdo_mysql

WORKDIR /app

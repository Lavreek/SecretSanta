FROM php:8.4-fpm

RUN apt-get update && apt-get install -y \
      libzip-dev zip

RUN docker-php-ext-install zip

RUN curl -sS https://getcomposer.org/installer | \
    php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www/html
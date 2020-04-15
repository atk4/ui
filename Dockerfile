FROM php:apache

RUN apt-get update && apt-get install -y \
        libicu-dev \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl

RUN docker-php-ext-install pdo pdo_mysql

RUN apt-get install -y git

WORKDIR /var/www/html/
ADD composer.json .
#RUN rm demos/coverage.php
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
COPY . .
ADD demos/db.env.php demos/db.php

RUN composer install --no-dev



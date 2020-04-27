####################################################################
# # build command:                                                 #
# docker build -f Dockerfile ..                                    #
# # run command:                                                   #
# docker run --rm -p 80:80 -it $(docker build -q -f Dockerfile ..) #
####################################################################

FROM php:apache

RUN apt-get update && apt-get install -y \
        libicu-dev \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl

RUN docker-php-ext-install pdo pdo_mysql

RUN apt-get install -y git

WORKDIR /var/www/html/

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

ADD composer.json .
RUN composer install --no-dev

COPY src src
COPY template template
COPY demos demos

ADD demos/db.example.php demos/db.php
RUN sed -E "s/\('.+'\);/(\$_ENV['DSN']);/g" -i demos/db.php

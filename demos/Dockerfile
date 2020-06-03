####################################################################
# # build command:                                                 #
# docker build -f Dockerfile ..                                    #
# # run command:                                                   #
# docker run --rm -p 80:80 -it $(docker build -q -f Dockerfile ..) #
####################################################################

FROM php:apache

RUN apt-get update && apt-get install -y \
        libicu-dev git jq unzip npm \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl \
    && docker-php-ext-install pdo pdo_mysql

WORKDIR /var/www/html/

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN npm install -g less clean-css

COPY js js
COPY public public

RUN (cd js && npm install && npm run build)
RUN (cd public && lessc agileui.less agileui.css)

ADD composer.json .
RUN jq 'del(."require-release")|del(."require-dev")' < composer.json > tmp && mv tmp composer.json \
    && composer install --no-dev

COPY index.php index.php
COPY src src
COPY template template
COPY demos demos
COPY tests tests

ADD demos/db.example.php demos/db.php
RUN sed -E "s/\('.+'\);/(\$_ENV['DSN']);/g" -i demos/db.php

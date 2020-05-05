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

ADD composer.json .
RUN jq 'del(."require-release")|del(."require-dev")' < composer.json > tmp && mv tmp composer.json \
    && composer install --no-dev

COPY js js
COPY public public
RUN npm install -g less clean-css
RUN (cd js && npm install && npm run build)
RUN (cd public && lessc agileui.less agileui.css --clean-css="--s1 --advanced --compatibility=ie8")

COPY index.php index.php
COPY src src
COPY template template
COPY demos demos

ADD demos/db.example.php demos/db.php
RUN sed -E "s/\('.+'\);/(\$_ENV['DSN']);/g" -i demos/db.php

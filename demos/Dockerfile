###################################################
## # build command:                              ##
## docker build -f Dockerfile .. -t atk4_ui_demo ##
## # run command:                                ##
## docker run --rm -p 80:80 -it atk4_ui_demo     ##
###################################################

FROM php:8.3-apache

RUN apt-get update && apt-get install -y \
        git libicu-dev npm unzip \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl \
    && docker-php-ext-install pdo pdo_mysql

WORKDIR /var/www/html/

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN npm install -g less clean-css

COPY js js
COPY public public

RUN cd js && npm ci && npm run build
RUN cd public/css && lessc agileui.less agileui.css

ADD composer.json .
RUN composer require --no-update --no-interaction fzaninotto/faker:^1.6 \
    && composer install --no-dev

RUN echo 'disable_functions = pcntl_exec,exec,passthru,proc_open,shell_exec,system,popen/g' >> "$PHP_INI_DIR/php.ini"

COPY src src
COPY template template
COPY demos demos
RUN echo "<?php header('Location: /demos/');" > index.php
RUN sed -E "s/(\\\$minified = )true;/\\1false;/g" -i src/App.php

RUN php demos/_demo-data/create-db.php
RUN chown -R www-data:www-data demos/_demo-data
RUN sed -E "s/\(('sqlite:.+)\);/(\$_ENV['DB_DSN'] ?? \\1, \$_ENV['DB_USER'] ?? null, \$_ENV['DB_PASSWORD'] ?? null);/g" -i demos/db.default.php

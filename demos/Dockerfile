###################################################
## # build command:                              ##
## docker build -f Dockerfile .. -t atk4_ui_demo ##
## # run command:                                ##
## docker run --rm -p 80:80 -it atk4_ui_demo     ##
###################################################

FROM php:apache

RUN apt-get update && apt-get install -y \
        libicu-dev git jq unzip npm \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl \
    && docker-php-ext-install pdo pdo_mysql

RUN curl -sL https://deb.nodesource.com/setup_12.x  | bash - \
    && apt-get update && apt-get -y install nodejs \
    && npm install -g npm

WORKDIR /var/www/html/

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN npm install -g less clean-css

COPY js js
COPY public public

RUN (cd js && npm ci && npm run build)
RUN (cd public && lessc agileui.less agileui.css)

ADD composer.json .
RUN jq 'del(."require-release")|del(."require-dev")' < composer.json > tmp && mv tmp composer.json \
    && composer require --no-update fzaninotto/faker:^1.6 \
    && composer install --no-dev

RUN echo 'disable_functions = pcntl_exec,exec,passthru,proc_open,shell_exec,system,popen/g' >> "$PHP_INI_DIR/php.ini"

COPY index.php index.php
COPY src src
COPY template template
COPY demos demos

COPY demos/db.default.php demos
RUN php demos/_demo-data/create-db.php
RUN sed -E "s/\(('sqlite:.+)\);/(\$_ENV['DB_DSN'] ?? \\1, \$_ENV['DB_USER'] ?? null, \$_ENV['DB_PASSWORD'] ?? null);/g" -i demos/db.default.php

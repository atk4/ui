FROM atk4/image:latest

WORKDIR /var/www/html/
COPY . .
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN composer install --no-dev



FROM php:8.2.6-zts-alpine

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY ./app /app

WORKDIR /app

RUN rm -rf /app/vendor && composer install

ENTRYPOINT ["php", "/app/index.php"]
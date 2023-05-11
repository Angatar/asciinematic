FROM php:8-fpm-alpine3.17
LABEL org.opencontainers.image.authors="d3fk"
ENV PAGE_TITLE=ASCIINEMA

RUN apk add --no-cache figlet
#  A production environment should have a production configuration:
#    && mv /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini
# For a more efficient php we could have used opcache and JIT
# ... we are saving space and complexity for this simple training
#&& docker-php-ext-configure opcache --enable-opcache \
#&& docker-php-ext-install opcache \

COPY ./app/ /var/www/html/
VOLUME /var/www/html
WORKDIR /var/www/html/


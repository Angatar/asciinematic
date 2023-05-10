FROM php:8-apache-bullseye
LABEL org.opencontainers.image.authors="d3fk"
ENV PAGE_TITLE=ASCIINEMA
RUN apt-get update && apt-get install -y figlet \
    # Clean the packages and install script in /var/cache/apt/archives/
    && apt-get clean \
    # Remove the package lists (created by apt-get update)
    && rm -rf /var/lib/apt/lists/*

COPY ./app/ /var/www/html/
WORKDIR /var/www/html/


FROM php:8.5-fpm-alpine

ARG UID
ARG GID

# Create a user with the specified UID and GID
RUN addgroup -g $GID user && \
    adduser -D -s /bin/bash -u $UID -G user user

RUN apk update && apk add git curl unzip nano bash

# Install PHP extensions
ADD --chmod=0755 https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN install-php-extensions zip xdebug pdo_mysql

USER user

# Install Composer
COPY --from=composer:2.9 /usr/bin/composer /usr/local/bin/

# Copy the Xdebug configuration file
COPY conf.d/docker-php-ext-xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

WORKDIR /srv/app
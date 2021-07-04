# setup timezones properly
FROM alpine:3.10.3 AS timezone
MAINTAINER Robert Premar <robert.premar@gmail.com>

RUN apk add tzdata

# setup production php container
FROM php:8.0.8-fpm-alpine3.13 AS production
MAINTAINER Robert Premar <robert.premar@gmail.com>

ARG HOST_USER_ID
ARG HOST_GROUP_ID
ARG TIMEZONE

ARG DEBIAN_FRONTEND=noninteractive
ARG DEV_DEPS="libzip-dev libxml2-dev"

COPY --from=timezone /usr/share/zoneinfo/${TIMEZONE} /etc/localtime

# fix work iconv library with alphine
RUN apk add --no-cache --repository http://dl-cdn.alpinelinux.org/alpine/edge/community/ --allow-untrusted gnu-libiconv
ENV LD_PRELOAD /usr/lib/preloadable_libiconv.so php

RUN set -xe && \
    echo "${TIMEZONE}" > /etc/timezone && \
    apk add --no-cache --update zlib libzip git openssh-client pcre-dev && \
    apk add --no-cache --update --virtual .phpize-deps ${PHPIZE_DEPS} && \
    apk add --no-cache --update --virtual .dev-deps ${DEV_DEPS} && \
    # create app user
    addgroup -S -g ${HOST_GROUP_ID} app && \
    adduser -S -s /bin/sh -DS -u ${HOST_USER_ID} -G app app  && \
    # download and install composer
    php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && \
    php composer-setup.php --install-dir=/usr/bin --filename=composer && \
    php -r "unlink('composer-setup.php');" && \
    # install core extensions
    docker-php-ext-install pdo_mysql zip xml opcache pcntl && \
    # cleanup
    apk del --purge .phpize-deps .dev-deps && \
    rm -rf /tmp/* && \
    rm -rf /usr/share/php8 && \
    rm -rf /var/cache/apk/*

# setup dev php container - has few tools more
FROM production AS development

RUN set -xe && \
    apk add --no-cache --update --virtual .phpize-deps ${PHPIZE_DEPS} && \
    apk add --no-cache --update --virtual .dev-deps ${DEV_DEPS} && \
    # update pecl
    pecl channel-update pecl.php.net && \
    # install pecl extensions
    pecl install xdebug && \
    # enable extensions installed via pecl
    docker-php-ext-enable xdebug && \
    # cleanup
    apk del --purge .phpize-deps .dev-deps && \
    rm -rf /tmp/* && \
    rm -rf /usr/share/php8 && \
    rm -rf /var/cache/apk/*

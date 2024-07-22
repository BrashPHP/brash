FROM php:8.3-alpine

# Fixes some weird terminal issues such as broken clear / CTRL+L
ENV TERM=linux

RUN apk add -Uuv \
    git bash supervisor freetype-dev libjpeg-turbo-dev libzip-dev \
    libpng-dev postgresql-dev libxml2-dev icu-dev libtool \
    make grep pcre-dev \
    autoconf libmcrypt-dev yaml-dev \
    g++ php83-openssl linux-headers \
    && rm -rf /var/cache/apk/*

RUN docker-php-ext-install bcmath dom zip exif pcntl xml \ 
    pdo_mysql pdo_pgsql pgsql opcache 

RUN apk --no-cache add ${PHPIZE_DEPS} && pecl install ds \
    && pecl install mcrypt \
    && pecl install xdebug \
    && pecl install yaml \
    && docker-php-ext-enable ds \
    mcrypt xdebug yaml

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# recommended: install optional extensions ext-ev and ext-sockets
RUN apk --no-cache add ${PHPIZE_DEPS} libev linux-headers \ 
    && pecl install ev \
    && docker-php-ext-enable ev \
    && docker-php-ext-install sockets \
    && apk del ${PHPIZE_DEPS} linux-headers \
    && echo "memory_limit = -1" >> "$PHP_INI_DIR/conf.d/acme.ini"

WORKDIR /app

STOPSIGNAL SIGQUIT

COPY . .

ENV docker=true

RUN composer install

ENV X_LISTEN 0.0.0.0:8080

EXPOSE 8080

USER nobody:nobody

ENTRYPOINT ["php", "public/index.php"]
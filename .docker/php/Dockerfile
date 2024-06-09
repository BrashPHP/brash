FROM ubuntu:jammy AS cli

# Fixes some weird terminal issues such as broken clear / CTRL+L
ENV TERM=linux

# Ensure apt doesn't ask questions when installing stuff
ENV DEBIAN_FRONTEND=noninteractive

# Install Ondrej repos for Ubuntu jammy, PHP, composer and selected extensions - better selection than
# the distro's packages
RUN apt-get update \
    && apt-get install -y --no-install-recommends gnupg \
    && echo "deb http://ppa.launchpad.net/ondrej/php/ubuntu jammy main" > /etc/apt/sources.list.d/ondrej-php.list \
    && apt-key adv --keyserver keyserver.ubuntu.com --recv-keys 4F4EA0AAE5267A6C \
    && apt-get update \
    && apt-get -y --no-install-recommends install \
    ca-certificates \
    curl \
    unzip \
    openssl \
    php8.3-apcu \
    php8.3-cli \
    php8.3-curl \
    php8.3-mbstring \
    php8.3-opcache \
    php8.3-readline \
    php8.3-xml \
    php8.3-zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/* ~/.composer

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

### FPM ###

FROM cli AS fpm

# Install FPM
RUN apt-get update \
    && apt-get -y --no-install-recommends install php8.3-fpm \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

STOPSIGNAL SIGQUIT

# PHP-FPM packages need a nudge to make them docker-friendly
COPY .docker/php/overrides.conf /etc/php/8.3/fpm/pool.d/z-overrides.conf

RUN apt-get update \
    && apt-get -y --no-install-recommends install \
    git \ 
    php8.3-bcmath \ 
    php8.3-bz2 \ 
    php8.3-decimal \ 
    php8.3-ds \ 
    php8.3-http \ 
    php8.3-mcrypt \ 
    php8.3-mysql \ 
    php8.3-pgsql \ 
    php-raphf \
    php8.3-sqlite3 \ 
    php8.3-xdebug \ 
    php8.3-yaml \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

WORKDIR /app


# FOR REACTPHP
# RUN apt-get install ${PHPIZE_DEPS} libev \ 
#     && pecl install ev \
#     && docker-php-ext-enable ev \
#     && docker-php-ext-install sockets \
#     && echo "memory_limit = -1" >> "$PHP_INI_DIR/conf.d/acme.ini"

COPY . .

ENV X_LISTEN 0.0.0.0:9000

ENV docker=true

RUN composer install

CMD ["/usr/sbin/php-fpm8.3", "-O" ]

# Open up fcgi port
EXPOSE 9000



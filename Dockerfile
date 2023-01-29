FROM php:8.1-apache

# Install linux extensions.
RUN apt-get update && \
    apt-get install -y \
    wget \
    g++ \
    git \
    unzip \
    libicu-dev \
    libpng-dev \
    libxml2-dev \
    libzip-dev \
    libpq-dev \
    zlib1g-dev && \
    # Update here for a reason, please do not remove.
    apt-get update && \
    apt-get install -y \
    file \
    libgmp-dev \
    libmcrypt-dev \
    libmhash-dev \
    locales \
    re2c && \
    sed -i 's/# \(en_US.UTF-8 UTF-8\)/\1/' /etc/locale.gen && \
    sed -i 's/# \(de_DE.UTF-8 UTF-8\)/\1/' /etc/locale.gen && \
    locale-gen en_US.UTF-8 de_DE.UTF-8

    # Install php extensions.
RUN  docker-php-ext-configure gmp \
    && docker-php-ext-configure intl \
    && docker-php-ext-configure pdo_mysql \
    && docker-php-ext-install -j5 gmp exif gd intl pdo_mysql soap zip opcache

ARG ENVIRONMENT=dev
ENV ENVIRONMENT=${ENVIRONMENT}

#------- Copy configurations ---------#
# Set up production php configurations by default.
RUN mv ${PHP_INI_DIR}/php.ini-production ${PHP_INI_DIR}/php.ini
COPY .docker/apache2/*.conf ${APACHE_CONFDIR}/conf-enabled/
COPY .docker/php/*-php.ini .docker/php/*-${ENVIRONMENT}.ini ${PHP_INI_DIR}/conf.d/

# Install xdebug.
RUN if [ ${ENVIRONMENT} = dev ]; then \
    yes | pecl install xdebug && \
    docker-php-ext-enable xdebug \
;fi

# Enable apache mods.
RUN a2enmod headers \
    && a2enmod rewrite \
    # Set apache document root.
    && sed -ri -e 's!/var/www/html!/var/www/html/public!g' ${APACHE_CONFDIR}/sites-available/*.conf \
    && sed -i -e 's/LogFormat/# LogFormat/g' ${APACHE_CONFDIR}/apache2.conf

# Copy code.
# Copy composer.
COPY --chown=www-data ./composer.* /var/www/html/
COPY --from=composer:2.1.3 /usr/bin/composer /usr/bin/composer


COPY --chown=www-data ./migrations /var/www/html/migrations
COPY --chown=www-data ./translations /var/www/html/translations
COPY --chown=www-data ./tests /var/www/html/tests
COPY --chown=www-data ./templates /var/www/html/templates

EXPOSE 80

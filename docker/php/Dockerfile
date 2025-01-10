# Use the official PHP 7.4-FPM base image
FROM php:7.4-fpm

# [Optional] Prevent some interactive prompts during package installs
ENV DEBIAN_FRONTEND=noninteractive

# 1. Install system packages (including ImageMagick dependencies)
RUN apt-get update && apt-get install -y --no-install-recommends \
        cron \
        git \
        zip \
        unzip \
        libzip-dev \
        libwebp-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        libfreetype6-dev \
        libicu-dev \
        libmagickwand-dev \
        libmagickcore-dev \
        # [Optional] for pdo_pgsql or soap, uncomment:
        # libpq-dev \
        # libxml2-dev \
        && rm -rf /var/lib/apt/lists/*

# 2. Install Imagick via PECL
RUN pecl install imagick \
        && docker-php-ext-enable imagick

# 3. Configure and install other PHP extensions
RUN docker-php-ext-configure gd \
        --with-webp \
        --with-jpeg \
        --with-freetype \
        && docker-php-ext-configure intl \
        && docker-php-ext-install \
        zip \
        bcmath \
        gd \
        mysqli \
        pdo_mysql \
        intl
# [Optional] pdo_pgsql, soap, etc., if needed

# [Optional] For supervisor & Redis extension
# RUN apt-get install -y supervisor \
#     && pecl install -o -f redis \
#     && rm -rf /tmp/pear \
#     && docker-php-ext-enable redis

# 4. Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- \
        --install-dir=/usr/local/bin \
        --filename=composer

# 5. Set working directory
WORKDIR /var/www/html

# 6. Copy cron file and daemon script
COPY cron-file /etc/cron.d/cron-file
COPY run-deamons.sh /run-deamons.sh

# 7. Set the ENTRYPOINT
ENTRYPOINT ["/bin/bash", "/run-deamons.sh"]

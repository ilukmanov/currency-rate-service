# Using the base image of PHP 8.3
FROM php:8.3-fpm

# Installing necessary dependencies and extensions
RUN apt-get update && apt-get install -y \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libonig-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    htop \
    procps \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd mbstring zip pdo pdo_mysql pcntl

# Installing Redis extension
RUN pecl install redis \
    && docker-php-ext-enable redis

# Installing Xdebug
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

# Configuring Xdebug
RUN echo "xdebug.mode=coverage" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.start_with_request=yes" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_host=host.docker.internal" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_port=9003" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.log=/tmp/xdebug.log" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# Installing Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Setting the working directory
WORKDIR /var/www/webman

# Copying the entrypoint script
COPY ./docker/entrypoint.sh /usr/local/bin/entrypoint.sh

# Exposing the port
EXPOSE 8080

# Setting the entrypoint
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
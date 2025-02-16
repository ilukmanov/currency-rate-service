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

# Installing Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Setting the working directory
WORKDIR /var/www/webman

# Copying project files into the container
COPY . /var/www/webman

# Installing project dependencies
RUN composer install

# Exposing the port
EXPOSE 8080

# Starting Webman on container startup
CMD ["sh", "-c", "rm -f ./runtime/webman.pid && php start.php start -d && tail -f /dev/null"]
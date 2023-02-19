FROM php:apache

EXPOSE 80

# Enable required apache module(s)
RUN a2enmod rewrite
RUN service apache2 restart

# Install dependencies and php extensions
RUN apt-get update && apt-get install -y \
    zlib1g-dev \
    libzip-dev \
    unzip \
    graphviz

RUN docker-php-ext-install zip pdo pdo_mysql

# Set php settings
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

WORKDIR /var/www/html

# Install app dependencies using composer
COPY --from=composer/composer:2-bin /composer /usr/bin/composer

COPY composer.json composer.json
COPY composer.lock composer.lock

RUN composer install

COPY --chown=www-data:www-data . .

# Generate classmap
RUN composer dumpautoload -a

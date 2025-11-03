FROM php:7.4-fpm

# Installer les dépendances système nécessaires pour Composer
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    zip \
    libzip-dev \
    && docker-php-ext-install zip \
    && rm -rf /var/lib/apt/lists/*
    
# Installation de l'extension pecl pcov
RUN pecl install pcov \
    && docker-php-ext-enable pcov

       # Installer Composer 2.2.x (compatible PHP 7.4)
RUN curl -sS https://getcomposer.org/installer | php -- --version=2.2.21 && \
mv composer.phar /usr/local/bin/composer

# Vérification version Composer
RUN composer --version

EXPOSE 9000

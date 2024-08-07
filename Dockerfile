# Utiliser l'image officielle de PHP 
FROM php:8.3.9-fpm

# Définir le répertoire de travail
WORKDIR /var/www/html

# Installer les dépendances nécessaires
RUN apt update && apt install -y \
    libpng-dev \
    libjpeg-dev \
    libzip-dev \
    libfreetype6-dev \
    libonig-dev \
    libxslt1-dev \
    unzip \
    build-essential \
    git \
    nginx \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install pdo_mysql \
    && docker-php-ext-enable opcache \
    && docker-php-ext-install xsl \
    && docker-php-ext-install zip \
    && docker-php-ext-install intl \
    && docker-php-ext-install soap \
    && pecl install mongodb \
    && docker-php-ext-enable mongodb



# Installer Composer
COPY --from=composer:2.7.7 /usr/bin/composer /usr/bin/composer

# Copier les fichiers de l'application
WORKDIR /var/www/html

COPY . .

# on met la config nginx
COPY build/nginx/conf/default.conf /etc/nginx/conf.d/default.conf
COPY build/php/custom.ini /usr/local/etc/php/conf.d/

# on applique la configuration php de prod
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# Installer les dépendances Composer
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN composer install --no-interaction --optimize-autoloader

# Configurer les permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Exposer le port 80
EXPOSE 80

# Démarrer nginx que le serveur php
CMD nginx && php-fpm
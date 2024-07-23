# Utilisez l'image officielle PHP avec Apache
FROM php:7.4-apache

# Installez les extensions PHP requises
RUN docker-php-ext-install pdo pdo_mysql

# Activez les modules Apache nécessaires
RUN a2enmod rewrite

# Copiez votre code source dans le conteneur
COPY . /var/www/html/

# Définissez le répertoire de travail
WORKDIR /var/www/html

# Configurez Apache pour utiliser le répertoire public
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|' /etc/apache2/sites-available/000-default.conf

# Exposez le port 8080
EXPOSE 8080

# Démarrez Apache
CMD ["apache2-foreground"]
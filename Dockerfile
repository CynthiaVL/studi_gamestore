# Use the official PHP image with Apache
FROM php:7.4-apache

# Set working directory
WORKDIR /var/www/html

# Install dependencies
RUN apt-get update && apt-get install -y \
    libicu-dev \
    libpq-dev \
    && docker-php-ext-install \
    intl \
    opcache \
    pdo \
    pdo_mysql \
    pdo_pgsql

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Copy existing application directory contents
COPY . /var/www/html

# Copy Apache vhost file
COPY ./docker/apache2.conf /etc/apache2/sites-available/000-default.conf

# Ensure Apache listens on port 8080
RUN echo "Listen 8080" >> /etc/apache2/ports.conf

# Expose port 8080
EXPOSE 8080

# Start Apache in the foreground
CMD ["apache2-foreground"]

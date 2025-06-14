# Use official PHP image with Apache
FROM php:8.1-apache

# Enable required Apache modules
RUN a2enmod rewrite

# Set working directory inside the container
WORKDIR /var/www/html

# Copy Laravel files to the container
COPY . /var/www/html

# Install dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev libjpeg-dev libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql

# Set correct permissions for Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Expose Apache port
EXPOSE 80

# Start Apache in foreground
CMD ["apache2-foreground"]
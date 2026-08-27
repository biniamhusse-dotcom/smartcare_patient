FROM php:8.2-apache

# Install PDO MySQL extension
RUN docker-php-ext-install pdo pdo_mysql

# Enable Apache rewrite module
RUN a2enmod rewrite

# Set permissions
WORKDIR /var/www/html
RUN chown -R www-data:www-data /var/www/html
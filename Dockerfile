# Use the official PHP image with Apache
FROM php:8.2-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libicu-dev zlib1g-dev g++ make automake autoconf libzip-dev \
    libpng-dev libwebp-dev libjpeg62-turbo-dev libfreetype6-dev && \
    docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install gd zip mysqli pdo_mysql intl bcmath opcache exif

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Set the working directory
WORKDIR /var/www/html

# Copy the current directory contents into the container at /var/www/html
COPY . .

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set permissions for Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Expose the port
EXPOSE 80

# Copy the setup script into the container
COPY ./setup.sh /usr/local/bin/setup.sh

# Set file permissions
RUN chmod +x /usr/local/bin/setup.sh

# Run setup script after container is built
CMD ["bash", "/usr/local/bin/setup.sh"]

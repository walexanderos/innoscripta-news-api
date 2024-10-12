#!/bin/bash

service apache2 start

# Install composer dependencies
composer install

# Generate application key if not set
if [ -z "$APP_KEY" ]; then
  php artisan key:generate
fi

# Run database migrations
php artisan migrate --force


# Clear and cache configuration
php artisan optimize:clear

# Generate Swagger DOC
php artisan l5-swagger:generate

# Start the app
php artisan serve

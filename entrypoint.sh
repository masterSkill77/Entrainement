#!/bin/sh

set -e

# Wait for MySQL
while ! mysql -h mysql -u laravel -psecret -e "SELECT 1"; do
    sleep 1
done

# Run migrations
php artisan migrate --force

# Start PHP-FPM
exec php-fpm

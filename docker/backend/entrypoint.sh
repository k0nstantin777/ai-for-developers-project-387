#!/bin/sh
set -e

if [ ! -f "vendor/autoload.php" ]; then
    echo "Installing Composer dependencies..."
    composer install --no-interaction
fi

if [ ! -f "database/database.sqlite" ]; then
    touch database/database.sqlite
fi

if ! php artisan migrate:status --no-interaction > /dev/null 2>&1; then
    php artisan migrate --force --no-interaction
fi

exec php artisan serve --host=0.0.0.0 --port=8000

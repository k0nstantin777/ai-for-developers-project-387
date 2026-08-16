#!/bin/sh
set -e

if [ ! -f ".env" ]; then
    cp .env.example .env
fi

if [ -z "$APP_KEY" ] || ! grep -q "^APP_KEY=" .env || grep -q "^APP_KEY=$" .env; then
    php artisan key:generate --force --no-interaction
fi

if [ ! -f "database/database.sqlite" ]; then
    touch database/database.sqlite
    chown www-data:www-data database/database.sqlite
fi

php artisan migrate --force --no-interaction

php artisan config:cache
php artisan route:cache
php artisan view:cache

exec "$@"

#!/bin/sh
set -e

echo "Running migrations..."
php artisan migrate --force

echo "Linking storage..."
php artisan storage:link

echo "Optimizing application..."
php artisan optimize:clear
php artisan optimize

echo "Starting FrankenPHP..."
exec docker-php-entrypoint --config /Caddyfile --adapter caddyfile 2>&1

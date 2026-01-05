#!/bin/sh

set -e

if [ ! -f ".env" ]; then
    php artisan jodi:setup --force
fi

php artisan config:cache --quiet
php artisan migrate --force --quiet

exec "$@"

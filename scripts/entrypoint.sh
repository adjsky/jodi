#!/bin/sh

set -e

php artisan config:cache --quiet
php artisan migrate --force --quiet

exec "$@"

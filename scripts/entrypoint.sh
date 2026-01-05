#!/bin/sh

DB_PATH="database/database.sqlite"

if [ ! -f $DB_PATH ]; then
    touch $DB_PATH;
fi

php artisan migrate --force --quiet

multirun \
    "frankenphp run" \
    "php artisan queue:work --tries=3 --timeout=60 --backoff=30" \
    "php artisan schedule:work --quiet"

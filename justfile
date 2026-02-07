set quiet := true

default:
    just --list --unsorted

init:
    php artisan jodi:setup --seed

# -------------------------------- DEVELOPMENT ---------------------------------

[parallel]
dev: php-serve vite worker logs

dev-preview:
    npm run build && frankenphp run

[parallel]
dev-android: (php-serve "--host=0.0.0.0") (vite "--host") worker logs

# ---------------------------------- SERVICES ----------------------------------

php-serve args="":
    php artisan serve {{args}}

vite args="":
    npm run dev -- {{args}}

worker:
    php artisan queue:listen --tries=1

logs:
    php artisan pail --timeout=0

android-studio:
    npx cap open android

android-emulator:
    npx cap run android

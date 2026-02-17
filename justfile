set quiet := true

default:
    just --list --unsorted

init:
    php artisan jodi:setup --seed

# -------------------------------- DEVELOPMENT ---------------------------------

[parallel]
dev: php-serve vite worker logs

dev-preview:
    npm run build
    frankenphp run

[parallel]
dev-android: (php-serve "--host=0.0.0.0") (vite "--host") worker logs

gen-assets:
    npx pwa-assets-generator
    npx capacitor-assets generate --android --assetPath=public --iconBackgroundColor="#fdf3e2" --splashBackgroundColor="#fdf3e2" --splashBackgroundColorDark="#fdf3e2"

# ---------------------------------- SERVICES ----------------------------------

php-serve args="":
    php artisan serve {{args}}

vite args="":
    npm run dev -- {{args}}

worker:
    php artisan queue:listen --tries=1

logs:
    php artisan pail --timeout=0

cap *args:
    npx cap {{args}}

# https://just.systems

# move initialization logic to a custom artisan command
init:
    php -r "file_exists('.env') || copy('.env.example', '.env');"
    php -r "file_exists('database/database.sqlite') || touch('database/database.sqlite');"
    php artisan key:generate --ansi
    php artisan migrate --graceful --ansi --seed
    php artisan webpush:vapid

[parallel]
dev: dev-server dev-queue dev-logs dev-vite

dev-server:
    php artisan serve

dev-queue:
    php artisan queue:listen --tries=1

dev-logs:
    php artisan pail --timeout=0

dev-vite:
    npm run dev

preview:
    npm run build && frankenphp run

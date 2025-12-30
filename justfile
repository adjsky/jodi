# https://just.systems

setup:
    php artisan jodi:setup --seed

reset-db:
    php artisan migrate:fresh --seed

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

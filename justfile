# https://just.systems

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

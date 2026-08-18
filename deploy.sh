#!/bin/bash
set -e

echo "=== Deployment started at $(date) ==="
cd /DATA/AppData/raffle-app

# 1. Pull perubahan terbaru dari GitHub
echo "Pulling latest changes from git origin main..."
git pull origin main

# 2. Update dependencies composer (opsional/uncomment jika dibutuhkan)
if [ -f "composer.json" ]; then
    echo "Running composer install..."
    composer install --no-interaction --prefer-dist --optimize-autoloader || true
fi

# 3. Jalankan migrasi & optimize Laravel
if [ -f "artisan" ]; then
    echo "Running artisan migrations & optimize..."
    if docker ps --format '{{.Names}}' | grep -q "^raffle_app$"; then
        docker exec -u 0 raffle_app chmod -R 777 /var/www/html/storage /var/www/html/bootstrap/cache || true
        docker exec raffle_app php artisan migrate --force || true
        docker exec raffle_app php artisan optimize:clear || true
    else
        chmod -R 777 storage bootstrap/cache || true
        php artisan migrate --force || true
        php artisan optimize:clear || true
    fi
fi

# 4. Build assets Vite / Frontend (opsional/uncomment jika dibutuhkan)
if [ -f "package.json" ]; then
    echo "Building frontend assets..."
    npm install --no-audit || true
    npm run build || true
fi

# Pastikan permission folder storage tetap bisa ditulis oleh web server / container
chmod -R 777 storage bootstrap/cache 2>/dev/null || true

echo "=== Deployment finished successfully at $(date) ==="

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
    php artisan migrate --force || true
    php artisan optimize:clear || true
    php artisan config:cache || true
    php artisan route:cache || true
    php artisan view:cache || true
fi

# 4. Build assets Vite / Frontend (opsional/uncomment jika dibutuhkan)
if [ -f "package.json" ]; then
    echo "Building frontend assets..."
    npm install --no-audit || true
    npm run build || true
fi

echo "=== Deployment finished successfully at $(date) ==="

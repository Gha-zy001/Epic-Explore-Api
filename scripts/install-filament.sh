#!/usr/bin/env bash
# ============================================
# Filament Admin Panel — installation script
# Run from project root: bash scripts/install-filament.sh
# ============================================
set -e

echo "==> 1. Installing Filament v3"
composer require filament/filament:"^3.2" --no-interaction

echo "==> 2. Installing Spatie Laravel Permission (for admin roles)"
composer require spatie/laravel-permission:"^6.0" --no-interaction

echo "==> 3. Publishing Filament assets"
php artisan filament:install --panels --no-interaction

echo "==> 4. Publishing Spatie permission config + migrations"
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider" --no-interaction

echo "==> 5. Running migrations"
php artisan migrate --force

echo "==> 6. Seeding admin + roles + sample data"
php artisan db:seed --class=AdminSeeder --force
php artisan db:seed --class=GuiderSeeder --force

echo "==> 7. Generating Filament admin user (if not already)"
php artisan make:filament-user --no-interaction || true

echo "==> 8. Building storage symlink"
php artisan storage:link

echo "==> 9. Clearing caches"
php artisan optimize:clear

echo ""
echo "================================================="
echo " Filament admin panel installed!"
echo " Visit: /admin (or your custom ADMIN_PANEL_PATH)"
echo " Default admin: see .env ADMIN_EMAIL / ADMIN_PASSWORD"
echo "================================================="

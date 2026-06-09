# Filament Admin Panel — Installation (Windows PowerShell)

# 1. Install Filament v3 + Spatie Permission
composer require filament/filament:"^3.2" --no-interaction
composer require spatie/laravel-permission:"^6.0" --no-interaction

# 2. Publish Filament assets
php artisan filament:install --panels --no-interaction

# 3. Publish Spatie permission migrations
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider" --no-interaction

# 4. Run migrations
php artisan migrate --force

# 5. Seed admin + roles + sample data
php artisan db:seed --class=AdminSeeder --force
php artisan db:seed --class=GuiderSeeder --force

# 6. Build storage symlink
php artisan storage:link

# 7. Clear caches
php artisan optimize:clear

# 8. Visit the panel
# Open: http://localhost:8000/admin
# Default credentials come from your .env:
#   ADMIN_EMAIL (defaults to admin@epicexplore.test)
#   ADMIN_PASSWORD (defaults to "password" — CHANGE IN PRODUCTION)

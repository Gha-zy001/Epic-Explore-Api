<?php

namespace App\Filament\Pages;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SystemControl extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationGroup = 'System';

    protected static ?string $navigationLabel = 'System Control';

    protected static ?string $title = 'System Control';

    protected static ?string $slug = 'system-control';

    protected static ?int $navigationSort = 99;

    protected static string $view = 'filament.pages.system-control';

    public static function canAccess(): bool
    {
        return auth('admin')->user()?->hasRole(['super-admin', 'admin']) ?? false;
    }

    public function clearCache(): void
    {
        try {
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('route:clear');
            Artisan::call('view:clear');

            Notification::make()
                ->title('All caches cleared')
                ->body('Application, config, route, and view caches were cleared.')
                ->success()
                ->send();
        } catch (\Throwable $e) {
            Log::error('SystemControl@clearCache failed', ['error' => $e->getMessage()]);
            Notification::make()->title('Failed to clear cache')->body($e->getMessage())->danger()->send();
        }
    }

    public function optimizeApp(): void
    {
        try {
            Artisan::call('optimize');
            Notification::make()
                ->title('Application optimized')
                ->body('Config, routes, and views cached.')
                ->success()
                ->send();
        } catch (\Throwable $e) {
            Log::error('SystemControl@optimizeApp failed', ['error' => $e->getMessage()]);
            Notification::make()->title('Failed to optimize')->body($e->getMessage())->danger()->send();
        }
    }

    public function clearOtpCache(): void
    {
        try {
            Cache::flush();
            Notification::make()
                ->title('OTP / cache flushed')
                ->body('All cached values including OTP attempts were cleared.')
                ->success()
                ->send();
        } catch (\Throwable $e) {
            Log::error('SystemControl@clearOtpCache failed', ['error' => $e->getMessage()]);
            Notification::make()->title('Failed to flush cache')->body($e->getMessage())->danger()->send();
        }
    }

    public function runMigrations(): void
    {
        try {
            Artisan::call('migrate', ['--force' => true]);
            Notification::make()
                ->title('Migrations completed')
                ->body(Artisan::output())
                ->success()
                ->send();
        } catch (\Throwable $e) {
            Log::error('SystemControl@runMigrations failed', ['error' => $e->getMessage()]);
            Notification::make()->title('Migration failed')->body($e->getMessage())->danger()->send();
        }
    }

    public function seedDatabase(): void
    {
        try {
            Artisan::call('db:seed', ['--force' => true]);
            Notification::make()
                ->title('Database seeded')
                ->body(Artisan::output())
                ->success()
                ->send();
        } catch (\Throwable $e) {
            Log::error('SystemControl@seedDatabase failed', ['error' => $e->getMessage()]);
            Notification::make()->title('Seeding failed')->body($e->getMessage())->danger()->send();
        }
    }

    public function refreshScribeDocs(): void
    {
        try {
            Artisan::call('scribe:generate');
            Notification::make()
                ->title('API documentation regenerated')
                ->body('Scribe docs updated successfully.')
                ->success()
                ->send();
        } catch (\Throwable $e) {
            Log::error('SystemControl@refreshScribeDocs failed', ['error' => $e->getMessage()]);
            Notification::make()->title('Doc generation failed')->body($e->getMessage())->danger()->send();
        }
    }
}

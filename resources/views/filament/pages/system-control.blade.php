<x-filament-panels::page>
    <div class="space-y-6">
        <x-filament::section>
            <x-slot name="heading">Cache Management</x-slot>
            <x-slot name="description">Clear various application caches to free up memory and apply new configurations.</x-slot>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-filament::button wire:click="clearCache" color="warning" icon="heroicon-o-trash">
                    Clear all caches
                </x-filament::button>

                <x-filament::button wire:click="clearOtpCache" color="danger" icon="heroicon-o-key">
                    Flush OTP & Cache store
                </x-filament::button>
            </div>
        </x-filament::section>

        <x-filament::section>
            <x-slot name="heading">Application</x-slot>
            <x-slot name="description">Optimize the application for production performance.</x-slot>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-filament::button wire:click="optimizeApp" color="success" icon="heroicon-o-rocket-launch">
                    Optimize (cache config/routes/views)
                </x-filament::button>

                <x-filament::button wire:click="refreshScribeDocs" color="info" icon="heroicon-o-book-open">
                    Regenerate API documentation
                </x-filament::button>
            </div>
        </x-filament::section>

        <x-filament::section>
            <x-slot name="heading">Database</x-slot>
            <x-slot name="description">Run schema migrations and seeders. Be careful in production.</x-slot>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-filament::button wire:click="runMigrations" color="warning" icon="heroicon-o-circle-stack">
                    Run migrations
                </x-filament::button>

                <x-filament::button wire:click="seedDatabase" color="primary" icon="heroicon-o-squares-plus">
                    Seed database
                </x-filament::button>
            </div>
        </x-filament::section>

        <x-filament::section>
            <x-slot name="heading">System Info</x-slot>

            <dl class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                    <dt class="font-semibold">Laravel version</dt>
                    <dd>{{ app()->version() }}</dd>
                </div>
                <div>
                    <dt class="font-semibold">PHP version</dt>
                    <dd>{{ PHP_VERSION }}</dd>
                </div>
                <div>
                    <dt class="font-semibold">Environment</dt>
                    <dd>{{ app()->environment() }}</dd>
                </div>
                <div>
                    <dt class="font-semibold">Debug mode</dt>
                    <dd>{{ config('app.debug') ? 'ON' : 'OFF' }}</dd>
                </div>
                <div>
                    <dt class="font-semibold">Cache driver</dt>
                    <dd>{{ config('cache.default') }}</dd>
                </div>
                <div>
                    <dt class="font-semibold">Database</dt>
                    <dd>{{ config('database.default') }}</dd>
                </div>
                <div>
                    <dt class="font-semibold">Queue</dt>
                    <dd>{{ config('queue.default') }}</dd>
                </div>
                <div>
                    <dt class="font-semibold">Admin panel path</dt>
                    <dd>/{{ config('filament.path') }}</dd>
                </div>
            </dl>
        </x-filament::section>
    </div>
</x-filament-panels::page>

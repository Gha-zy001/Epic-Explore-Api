<?php

namespace App\Filament\Imports;

use App\Models\Restaurant;
use App\Models\State;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;

class RestaurantImporter extends Importer
{
    protected static ?string $model = Restaurant::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')->requiredMapping()->rules(['required', 'max:255']),
            ImportColumn::make('state')
                ->label('State name')
                ->requiredMapping()
                ->relationship(resolveUsing: 'name')
                ->rules(['required']),
            ImportColumn::make('address')->rules(['max:500']),
            ImportColumn::make('rate')->numeric()->rules(['nullable', 'numeric', 'min:0', 'max:5']),
        ];
    }

    public static function getOptions(): array
    {
        return [];
    }

    public function resolveRecord(): ?Restaurant
    {
        $stateName = $this->data['state'] ?? null;
        unset($this->data['state']);

        if ($stateName) {
            $state = State::firstOrCreate(['name' => $stateName]);
            $this->data['state_id'] = $state->id;
        }

        return Restaurant::firstOrNew([
            'name' => $this->data['name'] ?? null,
            'state_id' => $this->data['state_id'] ?? null,
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your restaurant import has completed and ' . Number::format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}

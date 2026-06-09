<?php

namespace App\Filament\Imports;

use App\Models\Place;
use App\Models\State;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;

class PlaceImporter extends Importer
{
    protected static ?string $model = Place::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->requiredMapping()
                ->rules(['required', 'max:255'])
                ->example('Pyramids of Giza'),
            ImportColumn::make('state')
                ->label('State name')
                ->requiredMapping()
                ->relationship(resolveUsing: 'name')
                ->rules(['required'])
                ->example('Cairo'),
            ImportColumn::make('description')
                ->rules(['max:5000'])
                ->example('Ancient wonder of the world'),
            ImportColumn::make('address')
                ->rules(['max:500'])
                ->example('Al Haram, Giza'),
        ];
    }

    public static function getOptions(): array
    {
        return [];
    }

    public function resolveRecord(): ?Place
    {
        $stateName = $this->data['state'] ?? null;
        unset($this->data['state']);

        if ($stateName) {
            $state = State::firstOrCreate(['name' => $stateName]);
            $this->data['state_id'] = $state->id;
        }

        return Place::firstOrNew([
            'name' => $this->data['name'] ?? null,
            'state_id' => $this->data['state_id'] ?? null,
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your place import has completed and ' . Number::format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}

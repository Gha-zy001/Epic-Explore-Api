<?php

namespace App\Filament\Imports;

use App\Models\State;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Number;

class StateImporter extends Importer
{
    protected static ?string $model = State::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->requiredMapping()
                ->rules(['required', 'max:255'])
                ->example('Cairo'),
            ImportColumn::make('description')
                ->rules(['max:1000'])
                ->example('Capital of Egypt'),
        ];
    }

    public static function getOptions(): array
    {
        return [];
    }

    public function resolveRecord(): ?State
    {
        return State::firstOrNew(['name' => $this->data['name']]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your state import has completed and ' . Number::format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}

<?php

namespace App\Filament\Resources\GuiderResource\Pages;

use App\Filament\Resources\GuiderResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewGuider extends ViewRecord
{
    protected static string $resource = GuiderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}

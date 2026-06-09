<?php

namespace App\Filament\Resources\GuiderResource\Pages;

use App\Filament\Resources\GuiderResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGuiders extends ListRecords
{
    protected static string $resource = GuiderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

<?php

namespace App\Filament\Resources\GuiderResource\Pages;

use App\Filament\Resources\GuiderResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGuider extends EditRecord
{
    protected static string $resource = GuiderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}

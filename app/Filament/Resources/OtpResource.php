<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OtpResource\Pages;
use App\Models\Otp;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class OtpResource extends Resource
{
    protected static ?string $model = Otp::class;

    protected static ?string $navigationIcon = 'heroicon-o-key';

    protected static ?string $navigationGroup = 'System';

    protected static ?int $navigationSort = 4;

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('identifier')->searchable()->copyable(),
                Tables\Columns\TextColumn::make('token'),
                Tables\Columns\IconColumn::make('valid')->boolean(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('valid'),
            ])
            ->headerActions([
                Tables\Actions\Action::make('clear_invalid')
                    ->label('Clear invalid OTPs')
                    ->icon('heroicon-o-trash')
                    ->color('warning')
                    ->action(function () {
                        $count = Otp::where('valid', false)
                            ->orWhere('created_at', '<', now()->subHours(1))
                            ->delete();
                        \Filament\Notifications\Notification::make()
                            ->title("Cleared {$count} OTP records")
                            ->success()
                            ->send();
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('invalidate')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (Otp $record) => $record->valid)
                    ->action(fn (Otp $record) => $record->update(['valid' => false])),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOtps::route('/'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('valid', true)->count() ?: null;
    }
}

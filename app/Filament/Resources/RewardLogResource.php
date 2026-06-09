<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RewardLogResource\Pages;
use App\Models\RewardLog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class RewardLogResource extends Resource
{
    protected static ?string $model = RewardLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-gift';

    protected static ?string $navigationLabel = 'Reward Logs';

    protected static ?string $navigationGroup = 'Gamification';

    protected static ?int $navigationSort = 3;

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('user.name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('reward_type')->badge()->searchable(),
                Tables\Columns\TextColumn::make('description')->limit(50)->searchable(),
                Tables\Columns\TextColumn::make('points')->numeric()->suffix(' XP')->sortable(),
                Tables\Columns\TextColumn::make('reference_type')->badge(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('reward_type')
                    ->options(['xp' => 'XP', 'badge' => 'Badge', 'item' => 'Item']),
            ])
            ->actions([
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
            'index' => Pages\ListRewardLogs::route('/'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}

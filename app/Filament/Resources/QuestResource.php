<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuestResource\Pages;
use App\Models\Quest;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class QuestResource extends Resource
{
    protected static ?string $model = Quest::class;

    protected static ?string $navigationIcon = 'heroicon-o-trophy';

    protected static ?string $navigationGroup = 'Gamification';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('title')->required()->maxLength(255),
            Forms\Components\Textarea::make('description')->rows(3),
            Forms\Components\TextInput::make('reward_xp')->numeric()->required()->minValue(0),
            Forms\Components\Select::make('requirement_type')
                ->options([
                    'visits' => 'Visits',
                    'reviews' => 'Reviews',
                    'favorites' => 'Favorites',
                    'trips' => 'Trips',
                ])
                ->required(),
            Forms\Components\TextInput::make('requirement_count')->numeric()->required()->minValue(1),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('title')->searchable()->sortable()->weight('bold'),
                Tables\Columns\TextColumn::make('description')->limit(50),
                Tables\Columns\TextColumn::make('reward_xp')->numeric()->sortable()->suffix(' XP'),
                Tables\Columns\TextColumn::make('requirement_type')->badge(),
                Tables\Columns\TextColumn::make('requirement_count')->numeric(),
                Tables\Columns\TextColumn::make('users_count')->counts('users')->label('Accepted'),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListQuests::route('/'),
            'create' => Pages\CreateQuest::route('/create'),
            'edit' => Pages\EditQuest::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}

<?php

namespace App\Filament\Resources;

use App\Filament\Imports\RestaurantImporter;
use App\Filament\Resources\RestaurantResource\Pages;
use App\Models\Restaurant;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class RestaurantResource extends Resource
{
    protected static ?string $model = Restaurant::class;

    protected static ?string $navigationIcon = 'heroicon-o-cake';

    protected static ?string $navigationGroup = 'Content';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')->required()->maxLength(255),
            Forms\Components\Select::make('state_id')
                ->label('State')
                ->relationship('state', 'name')
                ->searchable()
                ->preload()
                ->required()
                ->createOptionForm([
                    Forms\Components\TextInput::make('name')->required(),
                    Forms\Components\Textarea::make('description'),
                ]),
            Forms\Components\TextInput::make('address')->maxLength(500),
            Forms\Components\TextInput::make('rate')->numeric()->step(0.1)->minValue(0)->maxValue(5)->suffix('★'),
            Forms\Components\Repeater::make('images')
                ->relationship()
                ->schema([
                    Forms\Components\FileUpload::make('data')
                        ->label('Image')
                        ->image()
                        ->disk('public')
                        ->directory('restaurants')
                        ->required(),
                ])
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('name')->searchable()->sortable()->weight('bold'),
                Tables\Columns\TextColumn::make('state.name')->label('State')->badge()->searchable()->sortable(),
                Tables\Columns\TextColumn::make('address')->limit(30),
                Tables\Columns\TextColumn::make('rate')->numeric(decimalPlaces: 1)->suffix('★'),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('state_id')
                    ->label('State')
                    ->relationship('state', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->headerActions([
                Tables\Actions\ImportAction::make()
                    ->importer(RestaurantImporter::class)
                    ->fileRules(['mimes:csv,xlsx,xls']),
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
            'index' => Pages\ListRestaurants::route('/'),
            'create' => Pages\CreateRestaurant::route('/create'),
            'edit' => Pages\EditRestaurant::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}

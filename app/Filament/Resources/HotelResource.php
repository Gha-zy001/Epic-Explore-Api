<?php

namespace App\Filament\Resources;

use App\Filament\Imports\HotelImporter;
use App\Filament\Resources\HotelResource\Pages;
use App\Models\Hotel;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class HotelResource extends Resource
{
    protected static ?string $model = Hotel::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $navigationGroup = 'Content';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Basic Info')
                ->columns(2)
                ->schema([
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
                    Forms\Components\TextInput::make('address')->maxLength(500)->columnSpanFull(),
                    Forms\Components\TextInput::make('price')
                        ->numeric()
                        ->prefix('$')
                        ->step(0.01)
                        ->minValue(0),
                    Forms\Components\TextInput::make('rate')
                        ->numeric()
                        ->step(0.1)
                        ->minValue(0)
                        ->maxValue(5)
                        ->suffix('★'),
                ]),

            Forms\Components\Section::make('Images')
                ->schema([
                    Forms\Components\Repeater::make('images')
                        ->relationship()
                        ->schema([
                            Forms\Components\FileUpload::make('data')
                                ->label('Image')
                                ->image()
                                ->disk('public')
                                ->directory('hotels')
                                ->required(),
                        ])
                        ->columnSpanFull(),
                ]),
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
                Tables\Columns\TextColumn::make('price')->money('usd')->sortable(),
                Tables\Columns\TextColumn::make('rate')->numeric(decimalPlaces: 1)->suffix('★'),
                Tables\Columns\TextColumn::make('images_count')->counts('images')->label('Images'),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('state_id')
                    ->label('State')
                    ->relationship('state', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\Filter::make('min_price')
                    ->form([
                        Forms\Components\TextInput::make('min')->numeric(),
                    ])
                    ->query(fn ($query, $data) => $query->when($data['min'] ?? null, fn ($q, $v) => $q->where('price', '>=', $v))),
            ])
            ->headerActions([
                Tables\Actions\ImportAction::make()
                    ->importer(HotelImporter::class)
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
            'index' => Pages\ListHotels::route('/'),
            'create' => Pages\CreateHotel::route('/create'),
            'edit' => Pages\EditHotel::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}

<?php

namespace App\Filament\Resources;

use App\Filament\Imports\PlaceImporter;
use App\Filament\Resources\PlaceResource\Pages;
use App\Models\Image;
use App\Models\Place;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class PlaceResource extends Resource
{
    protected static ?string $model = Place::class;

    protected static ?string $navigationIcon = 'heroicon-o-map-pin';

    protected static ?string $navigationGroup = 'Content';

    protected static ?string $navigationLabel = 'Places';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Basic Info')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->maxLength(255),
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
                    Forms\Components\TextInput::make('address')
                        ->maxLength(500)
                        ->columnSpanFull(),
                    Forms\Components\RichEditor::make('description')
                        ->columnSpanFull()
                        ->maxLength(5000),
                ]),

            Forms\Components\Section::make('Images')
                ->schema([
                    Forms\Components\Repeater::make('images')
                        ->relationship()
                        ->schema([
                            Forms\Components\FileUpload::make('data')
                                ->label('Image')
                                ->image()
                                ->disk(config('filament.default_filesystem_disk', 'public'))
                                ->directory('places')
                                ->imageEditor()
                                ->required()
                                ->columnSpanFull(),
                        ])
                        ->columnSpanFull()
                        ->addActionLabel('Add image URL')
                        ->defaultItems(0),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\ImageColumn::make('images.data')
                    ->label('Image')
                    ->disk('public')
                    ->height(48)
                    ->width(48)
                    ->limit(1),
                Tables\Columns\TextColumn::make('name')->searchable()->sortable()->weight('bold'),
                Tables\Columns\TextColumn::make('state.name')
                    ->label('State')
                    ->badge()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('address')->limit(40),
                Tables\Columns\TextColumn::make('images_count')
                    ->counts('images')
                    ->label('Images'),
                Tables\Columns\TextColumn::make('visits_count')
                    ->counts('visits')
                    ->label('Visits'),
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
                    ->importer(PlaceImporter::class)
                    ->fileRules(['mimes:csv,xlsx,xls'])
                    ->visible(fn () => auth('admin')->user()?->can('import places') ?? true),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListPlaces::route('/'),
            'create' => Pages\CreatePlace::route('/create'),
            'view' => Pages\ViewPlace::route('/{record}'),
            'edit' => Pages\EditPlace::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}

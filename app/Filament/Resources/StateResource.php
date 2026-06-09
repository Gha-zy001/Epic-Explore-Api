<?php

namespace App\Filament\Resources;

use App\Filament\Imports\StateImporter;
use App\Filament\Resources\StateResource\Pages;
use App\Models\State;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class StateResource extends Resource
{
    protected static ?string $model = State::class;

    protected static ?string $navigationIcon = 'heroicon-o-map';

    protected static ?string $navigationGroup = 'Content';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->required()
                ->maxLength(255)
                ->unique(ignoreRecord: true),
            Forms\Components\Textarea::make('description')
                ->rows(3)
                ->maxLength(1000),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('description')->limit(60),
                Tables\Columns\TextColumn::make('places_count')
                    ->counts('places')
                    ->label('Places'),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->headerActions([
                Tables\Actions\ImportAction::make()
                    ->importer(StateImporter::class)
                    ->fileRules(['mimes:csv,xlsx,xls'])
                    ->visible(fn () => auth('admin')->user()?->can('import places') ?? true),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->before(function (State $record) {
                        if ($record->places()->exists()) {
                            Notification::make()
                                ->title('Cannot delete state with places')
                                ->body('This state has places linked to it.')
                                ->danger()
                                ->send();
                            return false;
                        }
                        return null;
                    }),
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
            'index' => Pages\ListStates::route('/'),
            'create' => Pages\CreateState::route('/create'),
            'edit' => Pages\EditState::route('/{record}/edit'),
        ];
    }
}

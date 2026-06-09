<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GuiderResource\Pages;
use App\Models\Guider;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;

class GuiderResource extends Resource
{
    protected static ?string $model = Guider::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';

    protected static ?string $navigationGroup = 'Community';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Account')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('name')->required()->maxLength(255),
                    Forms\Components\TextInput::make('email')->email()->required()->unique(ignoreRecord: true),
                    Forms\Components\TextInput::make('password')
                        ->password()
                        ->dehydrateStateUsing(fn ($state) => filled($state) ? Hash::make($state) : null)
                        ->dehydrated(fn ($state) => filled($state))
                        ->required(fn (string $context): bool => $context === 'create')
                        ->revealable(),
                    Forms\Components\Toggle::make('is_verified')->label('Email verified'),
                ]),

            Forms\Components\Section::make('Profile')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('phone_number')->tel()->maxLength(20),
                    Forms\Components\TextInput::make('national_id')->maxLength(20),
                    Forms\Components\Textarea::make('description')->rows(4)->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable()->weight('bold'),
                Tables\Columns\TextColumn::make('email')->searchable()->copyable(),
                Tables\Columns\TextColumn::make('phone_number'),
                Tables\Columns\TextColumn::make('national_id'),
                Tables\Columns\IconColumn::make('is_verified')->boolean()->label('Verified'),
                Tables\Columns\TextColumn::make('contacts_count')->counts('contacts')->label('Requests'),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_verified'),
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
            'index' => Pages\ListGuiders::route('/'),
            'create' => Pages\CreateGuider::route('/create'),
            'view' => Pages\ViewGuider::route('/{record}'),
            'edit' => Pages\EditGuider::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}

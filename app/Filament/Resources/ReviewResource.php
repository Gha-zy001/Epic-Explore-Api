<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReviewResource\Pages;
use App\Models\Review;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ReviewResource extends Resource
{
    protected static ?string $model = Review::class;

    protected static ?string $navigationIcon = 'heroicon-o-star';

    protected static ?string $navigationGroup = 'Community';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('user_id')->relationship('user', 'name')->required()->searchable(),
            Forms\Components\TextInput::make('reviewable_type')->required(),
            Forms\Components\TextInput::make('reviewable_id')->numeric()->required(),
            Forms\Components\Select::make('star_rating')
                ->options([
                    1 => '1 ★',
                    2 => '2 ★',
                    3 => '3 ★',
                    4 => '4 ★',
                    5 => '5 ★',
                ])
                ->required(),
            Forms\Components\Textarea::make('comments')->rows(4)->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('user.name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('reviewable_type')->badge(),
                Tables\Columns\TextColumn::make('reviewable_id'),
                Tables\Columns\TextColumn::make('star_rating')->suffix(' ★')->sortable(),
                Tables\Columns\TextColumn::make('comments')->limit(60),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->filters([
                Tables\Filters\Filter::make('star_rating')
                    ->form([
                        Forms\Components\Select::make('min')
                            ->options([1 => '1+', 2 => '2+', 3 => '3+', 4 => '4+', 5 => '5']),
                    ])
                    ->query(fn ($query, $data) => $query->when($data['min'] ?? null, fn ($q, $v) => $q->where('star_rating', '>=', $v))),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListReviews::route('/'),
            'view' => Pages\ViewReview::route('/{record}'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}

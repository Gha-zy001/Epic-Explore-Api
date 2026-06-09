<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use App\Models\Quest;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\AttachAction;
use Filament\Tables\Actions\DetachAction;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Community';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Profile')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('name')->required()->maxLength(150),
                    Forms\Components\TextInput::make('email')->email()->required()->unique(ignoreRecord: true),
                    Forms\Components\FileUpload::make('image')
                        ->image()
                        ->disk('public')
                        ->directory('users')
                        ->avatar(),
                    Forms\Components\TextInput::make('password')
                        ->password()
                        ->dehydrateStateUsing(fn ($state) => filled($state) ? Hash::make($state) : null)
                        ->dehydrated(fn ($state) => filled($state))
                        ->required(fn (string $context): bool => $context === 'create')
                        ->revealable(),
                ]),

            Forms\Components\Section::make('Gamification')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('exp')->numeric()->default(0)->minValue(0),
                    Forms\Components\TextInput::make('level')->numeric()->default(1)->minValue(1),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->disk('public')
                    ->height(40)
                    ->width(40)
                    ->circular()
                    ->defaultImageUrl(fn ($record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->name)),
                Tables\Columns\TextColumn::make('name')->searchable()->sortable()->weight('bold'),
                Tables\Columns\TextColumn::make('email')->searchable()->copyable(),
                Tables\Columns\TextColumn::make('exp')->numeric()->sortable(),
                Tables\Columns\TextColumn::make('level')->numeric()->sortable()->badge(),
                Tables\Columns\TextColumn::make('visits_count')->counts('visits')->label('Visits'),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->filters([
                Tables\Filters\Filter::make('level')
                    ->form([
                        Forms\Components\TextInput::make('min_level')->numeric(),
                        Forms\Components\TextInput::make('max_level')->numeric(),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['min_level'] ?? null, fn ($q, $v) => $q->where('level', '>=', $v))
                            ->when($data['max_level'] ?? null, fn ($q, $v) => $q->where('level', '<=', $v));
                    }),
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

    public static function getRelations(): array
    {
        return [
            QuestsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}

class QuestsRelationManager extends RelationManager
{
    protected static string $relationship = 'quests';

    protected static ?string $title = 'Assigned Quests';

    protected static ?string $inverseRelationship = 'users';

    public function form(Form $form): Form
    {
        return $form->schema([
            Select::make('quest_id')
                ->label('Quest')
                ->options(Quest::pluck('title', 'id'))
                ->searchable()
                ->required(),
            Forms\Components\TextInput::make('progress')
                ->numeric()
                ->default(0)
                ->minValue(0),
            Select::make('status')
                ->options([
                    'active' => 'Active',
                    'completed' => 'Completed',
                ])
                ->default('active')
                ->required(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->searchable()->sortable()->weight('bold'),
                TextColumn::make('description')->limit(50),
                TextColumn::make('reward_xp')->numeric()->sortable()->suffix(' XP'),
                TextColumn::make('requirement_type')->badge(),
                TextColumn::make('requirement_count')->numeric(),
                TextColumn::make('pivot.progress')->numeric()->label('Progress'),
                TextColumn::make('pivot.status')->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'warning',
                        'completed' => 'success',
                        default => 'gray',
                    }),
            ])
            ->headerActions([
                AttachAction::make()
                    ->label('Assign Quest')
                    ->form(fn (AttachAction $action) => [
                        Select::make('quest_id')
                            ->label('Quest')
                            ->options(Quest::pluck('title', 'id'))
                            ->searchable()
                            ->required(),
                        Forms\Components\TextInput::make('progress')
                            ->numeric()
                            ->default(0)
                            ->minValue(0),
                        Select::make('status')
                            ->options([
                                'active' => 'Active',
                                'completed' => 'Completed',
                            ])
                            ->default('active')
                            ->required(),
                    ]),
            ])
            ->actions([
                DetachAction::make()->label('Unassign'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}

<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Resources\Components\Tab;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\UserResource\Pages;
use Filament\Tables\Columns\IconColumn;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Shop';

    protected static ?int $navigationSort = 2;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutRole('super_admin')->orderByDesc('id');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Profile')
                    ->columns([
                        'sm' => 3,
                        'xl' => 6,
                        '2xl' => 8,
                    ])
                    ->schema([
                        Forms\Components\FileUpload::make('avatar')
                            ->image()
                            ->avatar()
                            ->disk('public')
                            ->directory('web/user/images')
                            ->columnSpan([
                                'sm' => 3,
                                'xl' => 6,
                                '2xl' => 8,
                            ])->alignCenter(),
                        Forms\Components\TextInput::make('name')
                            ->placeholder('John Doe')
                            ->required()
                            ->string()
                            ->maxLength(20)
                            ->minLength(3)
                            ->columnSpan([
                                'sm' => 2,
                                'xl' => 3,
                                '2xl' => 4,
                            ]),
                        Forms\Components\TextInput::make('email')
                            ->placeholder('example@example.co')
                            ->required()
                            ->unique()
                            ->email()
                            ->visibleOn('create')
                            ->columnSpan([
                                'sm' => 2,
                                'xl' => 3,
                                '2xl' => 4,
                            ]),
                        Forms\Components\Select::make('role')
                            ->placeholder('Select Role')
                            ->searchable()
                            ->relationship('roles', 'name',  function (Builder $query) {
                                $query->whereNot('name', 'super_admin');
                            })
                            ->preload()
                            ->columnSpan([
                                'sm' => 2,
                                'xl' => 3,
                                '2xl' => 4,
                            ]),
                        Forms\Components\TextInput::make('password')
                            ->placeholder('test@1234')
                            ->required()
                            ->minLength(6)
                            ->password()
                            ->visibleOn('create')
                            ->columnSpan([
                                'sm' => 2,
                                'xl' => 3,
                                '2xl' => 4,
                            ]),
                    ]),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('avatar')
                    ->defaultImageUrl('https://img.icons8.com/color/96/user-male-circle--v1.png'),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                IconColumn::make('status')
                    ->boolean(),

                TextColumn::make('role')->label('Role')
                    ->getStateUsing(function (User $record) {
                        return $record->getRoleNames()->implode(', ');
                    }),
            ])
            ->filters([
                SelectFilter::make('role')
                    ->relationship('roles', 'name', $modifyQueryUsing = function (Builder $query) {
                        $query->where('name', '!=', 'super_admin');
                    })
                    ->preload()
                    ->searchable()
                    ->native(false),
                SelectFilter::make('status')
                    ->options([
                        '1' => 'Active',
                        '0' => 'Inactive',

                    ])
                    ->native(false),
                Tables\Filters\TrashedFilter::make()
                    ->native(false),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\RestoreAction::make()

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            // 'create' => Pages\CreateUser::route('/create'),
            // 'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}

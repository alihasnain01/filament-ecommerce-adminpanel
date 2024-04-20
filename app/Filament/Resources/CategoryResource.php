<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Filament\Resources\CategoryResource\Pages\CreateCategory;
use App\Filament\Resources\CategoryResource\Pages\EditCategory;
use App\Filament\Resources\CategoryResource\Pages\ListCategories;
use App\Filament\Resources\CategoryResource\RelationManagers;
use App\Models\Category;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-list-bullet';

    protected static ?string $navigationGroup = 'Shop';

    protected static ?string $navigationParentItem = 'Products';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->orderByDesc('id');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Categories')
                    ->description('Update your category details and slug.')
                    ->schema([
                        Hidden::make('created_by')->default(auth()->user()->id),
                        TextInput::make('name')
                            ->required()
                            ->minLength(3)
                            ->maxLength(150)
                            ->placeholder('Category name')
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $operation, ?string $state, $set) {
                                $operation == 'create' ?  $set('slug', str($state)->slug()->toString()) : '';
                            }),
                        TextInput::make('slug')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->placeholder('Category Slug')
                            ->minLength(3)
                            ->maxLength(150)
                            ->readOnly(),
                        Select::make('parent_id')
                            ->options(Category::where('parent_id', null)->where('status', 1)->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->native(false),
                        Select::make('status')
                            ->native(false)
                            ->label('Status')
                            ->required()
                            ->options([
                                '1' => 'Active',
                                '0' => 'Inactive',
                            ]),
                        TextInput::make('description')
                            ->placeholder('Description')
                            ->maxLength(150),
                        FileUpload::make('image')
                            ->label('Image')
                            ->disk('public')
                            ->directory('web/categories'),
                    ])->columns([
                        'sm' => 1,
                        'md' => 2,
                        'lg' => 2,
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id'),
                ImageColumn::make('image'),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('slug'),
                TextColumn::make('description')
                    ->state(function (Category $record) {
                        if ($record->description == null) {
                            return '-';
                        }

                        return strlen($record->description) > 50
                            ? substr($record->description, 0, 50) . '...'
                            : $record->description;
                    }),
                TextColumn::make('parent.name')
                    ->state(function (Category $record) {
                        if ($record->parent == null) {
                            return '-';
                        }

                        return $record->parent->name;
                    }),
            ])
            ->filters([
                //
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCategories::route('/'),
            // 'create' => Pages\CreateCategory::route('/create'),
            // 'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}

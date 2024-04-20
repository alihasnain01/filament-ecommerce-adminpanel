<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\Pages\CreateProduct;
use App\Models\Category;
use App\Models\Product;
use Carbon\Carbon;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Support\Facades\Date;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $modelLable = 'Product';

    protected static ?string $navigationIcon = 'heroicon-o-bolt';

    protected static ?string $navigationGroup = 'Shop';

    public static function getEloquentQuery(): EloquentBuilder
    {
        return parent::getEloquentQuery()->orderByDesc('id');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()->schema([
                    Section::make('')
                        ->schema([
                            Hidden::make('created_by')->default(auth()->user()->id),
                            TextInput::make('name')
                                ->required()
                                ->minLength(3)
                                ->maxLength(150)
                                ->label('Product Title')
                                ->placeholder('Product Title')
                                ->live(onBlur: true)
                                ->afterStateUpdated(function (?string $state, $set, Component $component) {
                                    if ($component->getName() == 'name') {
                                        $set('slug', str($state)->slug()->toString());
                                    }
                                }),
                            TextInput::make('slug')
                                ->readOnly()
                                ->unique(ignoreRecord: true)
                                ->minLength(3)
                                ->maxLength(150)
                                ->label('Product Slug')
                                ->placeholder('Product Slug'),
                            Select::make('category_id')
                                ->options(Category::where([['status', 1], ['parent_id', '!=', null]])->pluck('name', 'id'))
                                ->searchable()
                                ->preload()
                                ->required()
                                ->live(onBlur: true)
                                ->afterStateUpdated(function (?string $state, $set, Component $component) {
                                    if ($component->getName() == 'category_id') {
                                        $category = Category::find($state);
                                        $category ? $set('sku', $category->slug . '-' . Product::count() + 1) : $set('sku', null);
                                    }
                                }),
                            TextInput::make('sku')
                                ->label('SKU')
                                ->placeholder('SKU')
                                ->readOnly()
                                ->unique(ignoreRecord: true),
                            TextInput::make('stock')
                                ->label('Stock Quantity')
                                ->placeholder('Stock Quantity')
                                ->numeric()
                                ->minValue(1)
                                ->maxValue(1000)
                                ->required(),
                            RichEditor::make('description')
                                ->label('Description')
                                ->placeholder('Description')
                                ->columnSpanFull(),
                        ])->columnSpan(2)->columns(2),

                    Section::make('Images')
                        ->schema([
                            FileUpload::make('images')
                                ->multiple()
                                ->disk('public')
                                ->directory('web/products/images')
                                ->maxFiles(4)
                                ->label('Other Images'),
                        ])->columnSpan(2),

                    Section::make('Pricing')
                        ->schema([
                            TextInput::make('price')
                                ->required()
                                ->label('Price')
                                ->placeholder('Price')
                                ->minValue(1)
                                ->numeric(),
                            TextInput::make('sale_price')
                                ->label('Sale Price')
                                ->placeholder('Sale Price')
                                ->minValue(1)
                                ->numeric(),
                            TextInput::make('cost_per_piece')
                                ->required()
                                ->label('Cost Per Piece')
                                ->placeholder('Cost Per Piece')
                                ->minValue(1)
                                ->numeric()
                                ->helperText("Customers won't see this price."),
                        ])->columns([
                            'sm' => 1,
                            'md' => 2
                        ]),
                ])->columnSpan(2),

                Group::make()->schema([
                    Section::make()
                        ->schema([
                            Toggle::make('is_visible')
                                ->label('Visibile')
                                ->onColor('success')
                                ->required()
                                ->helperText('Is product visibile on site?'),
                            Toggle::make('is_feature')
                                ->label('Is Featured')
                                ->onColor('success')
                                ->helperText('Is product featured on site?'),
                            DatePicker::make('available_start')
                                ->label('Availability')
                                ->placeholder('Select availability date')
                                ->required()
                                ->minDate(Carbon::today())
                                ->native(false)
                                ->closeOnDateSelection(),
                            TagsInput::make('tags')
                                ->label('Tags')
                                ->placeholder('Tags'),
                        ])->columnSpan(1),

                    Section::make('Featured Image')
                        ->schema([
                            FileUpload::make('image')
                                ->disk('public')
                                ->directory('web/products/images')
                                ->required()
                                ->label('Featured Image')
                                ->image(),
                        ])->columnSpan(1),

                    Section::make('Sales Date')
                        ->schema([
                            DatePicker::make('discount_start')
                                ->label('Discount Start Date')
                                ->placeholder('Discount Start Date')
                                ->minDate(Date::now())
                                ->native(false)
                                ->closeOnDateSelection(),
                            DatePicker::make('discount_end')
                                ->label('Discount End Date')
                                ->placeholder('Discount End Date')
                                ->minDate(Date::now())
                                ->native(false)
                                ->closeOnDateSelection(),
                        ]),
                ])->columnSpan(1)->columns(1),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image'),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('price')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('sale_price'),
                TextColumn::make('category.name')
                    ->searchable()
                    ->sortable(),
                IconColumn::make('is_visible')
                    ->boolean(),

                TextColumn::make('createdBy.name')
                    ->searchable(),
            ])
            ->filters([
                Filter::make('is_visible')
                    ->toggle(),
                SelectFilter::make('category')
                    ->searchable()
                    ->native(false)
                    ->preload()
                    ->multiple()
                    ->relationship('category', 'name', function (Builder $query): Builder {
                        return $query->where([['status', 1], ['parent_id', '!=', null]]);
                    }),
                Filter::make('created_at')->form([
                    DatePicker::make('created_from')
                        ->native(false)
                        ->placeholder('Created From')
                        ->closeOnDateSelection(),
                    DatePicker::make('created_until')
                        ->native(false)
                        ->placeholder('Created until')
                        ->closeOnDateSelection()
                        ->afterOrEqual('created_from'),
                ])->query(function (Builder $query, array $data): Builder {
                    return $query
                        ->when(
                            $data['created_from'],
                            fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                        )
                        ->when(
                            $data['created_until'],
                            fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                        );
                }),
            ])
            ->actions([
                ActionGroup::make([
                    EditAction::make(),
                    DeleteAction::make(),
                ])

            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
